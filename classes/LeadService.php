<?php

class LeadService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Listar Leads com Filtros Básicos
     */
    public function listar($filtros = [], $companyId = null, $limit = 100)
    {
        $where = ["1=1"];
        $params = [];
        $types = "";

        if ($companyId) {
            $where[] = "company_id = ?";
            $params[] = $companyId;
            $types .= "i";
        }

        if (!empty($filtros['status'])) {
            $where[] = "status = ?";
            $params[] = $filtros['status'];
            $types .= "s";
        }

        if (!empty($filtros['telefone'])) {
            // Busca exata ou LIKE? Para API geralmente exata ou limpa é melhor.
            // Vamos usar LIKE para flexibilidade ou exato se preferir. 
            // Como o numero pode vir formatado diferente, vou limpar no SQL ou assumir que quem chama manda limpo.
            $where[] = "telefone LIKE ?";
            $params[] = "%" . $filtros['telefone'] . "%";
            $types .= "s";
        }

        $whereSql = implode(" AND ", $where);

        $sql = "SELECT id, nome, email, telefone, origem, status, created_at, company_id 
                FROM leads 
                WHERE $whereSql 
                ORDER BY created_at DESC 
                LIMIT ?";
        $params[] = $limit;
        $types .= "i";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        $leads = [];
        while ($row = $res->fetch_assoc()) {
            $leads[] = $row;
        }

        return $leads;
    }

    /**
     * Criar ou Atualizar Lead (Upsert based on email)
     */
    public function upsertLead($dados, $companyId)
    {
        // Se vier ID, atualiza direto
        if (!empty($dados['id'])) {
            return $this->atualizarLead($dados['id'], $dados, $companyId);
        }

        // Se não, busca por email
        if (!empty($dados['email'])) {
            $stmt = $this->conn->prepare("SELECT id FROM leads WHERE email = ? AND (company_id = ? OR company_id IS NULL)");
            // Correção: company_id pode ser null. Ajuste de logica SQL para null-safe se necessario ou assumir null na query.
            // Para simplificar e evitar erro "company_id = NULL", usamos prepared statement null-safe ou logica variavel.
            // Assumindo que companyId aqui pode ser null, o bind "i" vai falhar se nao tratar.
            // Mas o PHP bind_param aceita null se variavel for null? Sim, mas treated as value. "company_id = ?" com null vira "company_id = NULL" que é false em SQL (IS NULL é o certo).
            // Vamos simplificar: Se companyId é null, busca global.

            $query = "SELECT id FROM leads WHERE email = ?";
            $types = "s";
            $params = [$dados['email']];

            if ($companyId !== null) {
                $query .= " AND company_id = ?";
                $types .= "i";
                $params[] = $companyId;
            } else {
                $query .= " AND company_id IS NULL";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            if ($row = $stmt->get_result()->fetch_assoc()) {
                return $this->atualizarLead($row['id'], $dados, $companyId);
            }
        }

        // Se não achou por email (ou não tem email), busca por telefone
        if (!empty($dados['telefone'])) {
            // Limpeza básica para match (assumindo que salvo limpo ou envio limpo)
            $query = "SELECT id FROM leads WHERE telefone = ?";
            $types = "s";
            $params = [$dados['telefone']];

            if ($companyId !== null) {
                $query .= " AND company_id = ?";
                $types .= "i";
                $params[] = $companyId;
            } else {
                $query .= " AND company_id IS NULL";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            if ($row = $stmt->get_result()->fetch_assoc()) {
                return $this->atualizarLead($row['id'], $dados, $companyId);
            }
        }

        // Criar Novo
        return $this->criarLead($dados, $companyId);
    }

    public function criarLead($dados, $companyId)
    {
        $nome = $dados['nome'] ?? 'Lead Sem Nome';
        $email = $dados['email'] ?? null;
        $telefone = $dados['telefone'] ?? null;
        $origem = $dados['origem'] ?? 'API';
        $status = $dados['status'] ?? 'novo';
        $score = isset($dados['score']) ? (int) $dados['score'] : 0;

        $stmt = $this->conn->prepare("INSERT INTO leads (company_id, nome, email, telefone, origem, status, ativo, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->bind_param("isssss", $companyId, $nome, $email, $telefone, $origem, $status);

        if ($stmt->execute()) {
            return [
                'action' => 'created',
                'lead_id' => $stmt->insert_id,
                'telefone' => $telefone
            ];
        }
        throw new Exception($this->conn->error);
    }

    public function atualizarLead($id, $dados, $companyId)
    {
        $campos = [];
        $params = [];
        $types = "";

        if (isset($dados['nome'])) {
            $campos[] = "nome=?";
            $params[] = $dados['nome'];
            $types .= "s";
        }
        if (isset($dados['email'])) {
            $campos[] = "email=?";
            $params[] = $dados['email'];
            $types .= "s";
        }
        if (isset($dados['telefone'])) {
            $campos[] = "telefone=?";
            $params[] = $dados['telefone'];
            $types .= "s";
        }
        if (isset($dados['origem'])) {
            $campos[] = "origem=?";
            $params[] = $dados['origem'];
            $types .= "s";
        }
        if (isset($dados['status'])) {
            $campos[] = "status=?";
            $params[] = $dados['status'];
            $types .= "s";
        }


        if (empty($campos))
            return [
                'action' => 'no_changes',
                'lead_id' => $id
            ];

        $campos[] = "updated_at=NOW()";

        $sql = "UPDATE leads SET " . implode(", ", $campos) . " WHERE id=? AND company_id=?";
        $params[] = $id;
        $params[] = $companyId;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return [
                'action' => 'updated',
                'lead_id' => $id,
                'telefone' => $dados['telefone'] ?? null
            ];
        }

        throw new Exception($this->conn->error);
    }

    public function buscarLead($id, $companyId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM leads WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $id, $companyId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Converter Lead em Negócio (Integração com CRM)
     */
    public function converterEmNegocio($leadId, $companyId, $userId, $funilId, $etapaId)
    {
        $lead = $this->buscarLead($leadId, $companyId);
        if (!$lead)
            throw new Exception("Lead não encontrado");

        // 1. Criar ou Vincular User (Cliente)
        // Lógica simplificada: Verifica se user com email existe
        $clienteId = null;
        if ($lead['email']) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND company_id = ?");
            $stmt->bind_param("si", $lead['email'], $companyId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $clienteId = $row['id'];
            }
        }

        // Se não existe user, cria um cliente rápido
        if (!$clienteId) {
            // Mock user creation logic or use CrmService if available context
            // Aqui no LeadService idealmente não deveriamos duplicar logica de User
            // Mas vamos inserir basico na users
            $senhaHash = password_hash(time(), PASSWORD_DEFAULT);
            $emailUser = $lead['email'] ?: 'lead_' . $leadId . '@temp.com';

            $stmtU = $this->conn->prepare("INSERT INTO users (company_id, nome, email, telefone, senha, tipo, ativo) VALUES (?, ?, ?, ?, ?, 'cliente', 1)");
            $stmtU->bind_param("issss", $companyId, $lead['nome'], $emailUser, $lead['telefone'], $senhaHash);
            if ($stmtU->execute()) {
                $clienteId = $stmtU->insert_id;
            }
        }

        // 2. Criar Negócio (Precisamos instanciar CrmService ou inserir direto)
        // Para manter desacoplamento, ideal é retornar os dados para o controller chamar CrmService
        // Mas como é um Service Method de acao de negocio, podemos fazer aqui se injetarmos CrmService
        // Ou fazemos INSERT direto na crm_negocios para performance

        $titulo = "Oportunidade: " . $lead['nome'];
        $valor = 0.00;

        $stmtN = $this->conn->prepare("INSERT INTO crm_negocios (company_id, funil_id, etapa_id, cliente_id, responsavel_id, titulo, valor_estimado, origem, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'aberto', NOW())");
        $stmtN->bind_param("iiiisds", $companyId, $funilId, $etapaId, $clienteId, $userId, $titulo, $valor, $lead['origem']);

        if ($stmtN->execute()) {
            $negocioId = $stmtN->insert_id;

            // Atualizar status do lead
            $this->atualizarLead($leadId, ['status' => 'convertido'], $companyId);

            return $negocioId;
        }

        throw new Exception("Erro ao criar negócio a partir do lead");
    }
}
