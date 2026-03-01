<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/services/AsaasService.php';

class ProductDistributionService
{
    private $conn;
    private $asaasService;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
        $this->asaasService = new AsaasService();
    }

    public function listarAcessos($produtoId)
    {
        $sql = "SELECT a.id as access_id, a.created_at, e.nome as company_name, e.id as company_id 
                FROM mentoria_acesso_empresas a 
                JOIN empresas e ON a.company_id = e.id 
                WHERE a.produto_id = ? 
                ORDER BY e.nome ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $result = $stmt->get_result();

        $acessos = [];
        while ($row = $result->fetch_assoc()) {
            $acessos[] = $row;
        }
        return $acessos;
    }

    public function listarEmpresasDisponiveis($produtoId)
    {
        // Empresas ativas que NÃO tem acesso ainda
        $sql = "SELECT id, nome FROM empresas WHERE ativo = 1 
                AND id NOT IN (SELECT company_id FROM mentoria_acesso_empresas WHERE produto_id = ?) 
                ORDER BY nome ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $result = $stmt->get_result();

        $empresas = [];
        while ($row = $result->fetch_assoc()) {
            $empresas[] = $row;
        }
        return $empresas;
    }

    public function concederAcesso($produtoId, $companyId)
    {
        // 1. Verificar se já existe
        $check = $this->conn->prepare("SELECT id FROM mentoria_acesso_empresas WHERE produto_id = ? AND company_id = ?");
        $check->bind_param("ii", $produtoId, $companyId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            throw new Exception("Esta empresa já possui acesso.");
        }

        // 2. Inserir Acesso
        $stmt = $this->conn->prepare("INSERT INTO mentoria_acesso_empresas (produto_id, company_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $produtoId, $companyId);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao conceder acesso: " . $this->conn->error);
        }

        // 3. Processar Cobrança (Se aplicável)
        // Buscar dados do produto
        $stmtProd = $this->conn->prepare("SELECT * FROM mentoria_produtos WHERE id = ?");
        $stmtProd->bind_param("i", $produtoId);
        $stmtProd->execute();
        $produto = $stmtProd->get_result()->fetch_assoc();

        $msg = "Acesso concedido!";

        if ($produto['valor'] > 0) {
            $msg .= $this->processarCobranca($produto, $companyId);
        }

        return $msg;
    }

    private function processarCobranca($produto, $companyId)
    {
        $price = $produto['valor'];
        $cycle = $produto['ciclo'] ?? 'MONTHLY'; // Fallback
        $tipoCobranca = $produto['tipo_cobranca'] ?? 'recorrente';
        $desc = "Produto: " . $produto['titulo'];
        $nextDueDate = date('Y-m-d', strtotime('+3 days'));

        // Buscar Empresa
        $compStmt = $this->conn->prepare("SELECT nome, documento, asaas_customer_id FROM empresas WHERE id = ?");
        $compStmt->bind_param("i", $companyId);
        $compStmt->execute();
        $compData = $compStmt->get_result()->fetch_assoc();

        $customerId = $compData['asaas_customer_id'];

        // Criar Cliente Asaas se não existir
        if (empty($customerId)) {
            // Email fallback logic
            $emailRes = $this->conn->query("SELECT email FROM users WHERE company_id = $companyId LIMIT 1");
            $email = ($emailRes->num_rows > 0) ? $emailRes->fetch_assoc()['email'] : 'financeiro@consulteplus.com.br';

            $cResult = $this->asaasService->createCustomer($compData['nome'], $compData['documento'], $email);
            if ($cResult['success']) {
                $customerId = $cResult['id'];
                $this->conn->query("UPDATE empresas SET asaas_customer_id = '$customerId' WHERE id = $companyId");
            } else {
                return " (Erro ao criar cliente Asaas: " . ($cResult['error'] ?? 'Desconhecido') . ")";
            }
        }

        // Pagamento Recorrente
        if ($tipoCobranca === 'recorrente') {
            // Verificar duplicidade
            $checkSub = $this->conn->query("SELECT id FROM financeiro_assinaturas WHERE company_id = $companyId AND descricao = '$desc' AND status = 'ACTIVE'");
            if ($checkSub->num_rows > 0) {
                return " (Assinatura já existia)";
            }

            $subResult = $this->asaasService->createSubscription($customerId, $price, $cycle, $nextDueDate, $desc, 'BOLETO');
            if ($subResult['success']) {
                $asaasId = $subResult['data']['id'];
                $status = strtoupper($subResult['data']['status']);

                $ins = $this->conn->prepare("INSERT INTO financeiro_assinaturas (company_id, asaas_id, status, valor, ciclo, next_due_date, billing_type, descricao) VALUES (?, ?, ?, ?, ?, ?, 'BOLETO', ?)");
                $ins->bind_param("issdsss", $companyId, $asaasId, $status, $price, $cycle, $nextDueDate, $desc);
                $ins->execute();
                return " (Assinatura gerada)";
            } else {
                return " (Erro na assinatura: " . ($subResult['error'] ?? 'N/A') . ")";
            }
        }
        // Pagamento Único
        else {
            $payResult = $this->asaasService->createPayment($customerId, $price, $nextDueDate, $desc, 'BOLETO');
            if ($payResult['success']) {
                $asaasId = $payResult['data']['id'];
                $status = 'PENDENTE';

                $ins = $this->conn->prepare("INSERT INTO financeiro_lancamentos (company_id, tipo, titulo, descricao, valor, data_vencimento, status, forma_pagamento, asaas_payment_id) VALUES (?, 'AVULSO', ?, ?, ?, ?, ?, 'BOLETO', ?)");
                $ins->bind_param("isssdsss", $companyId, $desc, $desc, $price, $nextDueDate, $status, $asaasId);
                $ins->execute();
                return " (Cobrança gerada)";
            } else {
                return " (Erro na cobrança: " . ($payResult['error'] ?? 'N/A') . ")";
            }
        }
    }

    public function removerAcesso($acessoId, $produtoId)
    {
        $stmt = $this->conn->prepare("DELETE FROM mentoria_acesso_empresas WHERE id = ? AND produto_id = ?");
        $stmt->bind_param("ii", $acessoId, $produtoId);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao remover acesso: " . $this->conn->error);
        }
        return true;
    }
}
?>