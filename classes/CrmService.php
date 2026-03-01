<?php

class CrmService
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listarFunis($companyId)
    {
        $funis = [];
        $stmt = $this->conn->prepare("SELECT id, nome, padrao FROM crm_funis WHERE company_id = ? AND ativo = 1 ORDER BY padrao DESC, nome ASC");
        $stmt->bind_param("i", $companyId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $funis[] = $row;
        }
        return $funis;
    }

    public function listarEtapas($funilId, $companyId)
    {
        $etapas = [];
        $stmt = $this->conn->prepare("SELECT * FROM crm_etapas WHERE funil_id = ? AND company_id = ? ORDER BY ordem ASC");
        $stmt->bind_param("ii", $funilId, $companyId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $etapas[$row['id']] = $row;
            $etapas[$row['id']]['deals'] = []; // Inicializa array de deals
        }
        return $etapas;
    }

    public function listarNegocios($filtros, $companyId, $limit = 10, $offset = 0)
    {
        $where = ["n.status != 'cancelado'", "n.company_id = ?"];
        $params = [$companyId];
        $types = "i";

        if (!empty($filtros['funil_id'])) {
            $where[] = "n.funil_id = ?";
            $params[] = $filtros['funil_id'];
            $types .= "i";
        }

        if (!empty($filtros['etapa_id'])) {
            $where[] = "n.etapa_id = ?";
            $params[] = $filtros['etapa_id'];
            $types .= "i";
        }

        if (!empty($filtros['responsavel_id'])) {
            $where[] = "n.responsavel_id = ?";
            $params[] = $filtros['responsavel_id'];
            $types .= "i";
        }

        if (!empty($filtros['origem'])) {
            $where[] = "LOWER(n.origem) = LOWER(?)";
            $params[] = $filtros['origem'];
            $types .= "s";
        }

        if (!empty($filtros['busca'])) {
            $busca = "%{$filtros['busca']}%";
            $where[] = "(n.titulo LIKE ? OR u.nome LIKE ?)";
            $params[] = $busca;
            $params[] = $busca;
            $types .= "ss";
        }

        $whereSql = implode(" AND ", $where);

        $sql = "
            SELECT 
                n.*, 
                COALESCE(l.nome, u.nome) as cliente_nome, 
                COALESCE(l.nome, u.nome) as paciente_nome,
                COALESCE(l.email, u.email) as paciente_email,
                COALESCE(l.telefone, u.telefone) as paciente_telefone,
                resp.nome as responsavel_nome,
                e.nome as etapa_nome,
                e.cor as etapa_cor,
                l.id as lead_id_vinculado
            FROM crm_negocios n
            LEFT JOIN users u ON n.cliente_id = u.id
            LEFT JOIN leads l ON n.lead_id = l.id
            LEFT JOIN users resp ON n.responsavel_id = resp.id
            LEFT JOIN crm_etapas e ON n.etapa_id = e.id
            WHERE $whereSql
            ORDER BY n.updated_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit + 1; // +1 para verificar has_more
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();

        $deals = [];
        $hasMore = false;
        $count = 0;

        while ($row = $res->fetch_assoc()) {
            if ($count < $limit) {
                $deals[] = $row;
            } else {
                $hasMore = true;
            }
            $count++;
        }

        return ['deals' => $deals, 'has_more' => $hasMore];
    }

    public function buscarNegocio($id, $companyId)
    {
        $stmt = $this->conn->prepare("
            SELECT n.*, 
                COALESCE(l.nome, u.nome) as cliente_nome, 
                COALESCE(l.email, u.email) as cliente_email,
                COALESCE(l.nome, u.nome) as paciente_nome, 
                COALESCE(l.telefone, u.telefone) as paciente_telefone,
                n.cliente_id as paciente_id,
                n.lead_id,
                l.origem as lead_origem,
                emp.nome as empresa_nome, emp.documento as empresa_documento,
                e.nome as etapa_nome, e.cor as etapa_cor,
                f.nome as funil_nome
            FROM crm_negocios n
            LEFT JOIN users u ON n.cliente_id = u.id
            LEFT JOIN leads l ON n.lead_id = l.id
            LEFT JOIN empresas emp ON n.empresa_cliente_id = emp.id
            LEFT JOIN crm_etapas e ON n.etapa_id = e.id
            LEFT JOIN crm_funis f ON n.funil_id = f.id
            WHERE n.id = ? AND n.company_id = ?
        ");
        $stmt->bind_param("ii", $id, $companyId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function criarNegocio($dados, $companyId, $userId)
    {
        // ... lógica de criação (cliente rápido, etc) ...
        // Simplificado para usar Prepared Statements

        $titulo = $dados['titulo'];
        $valor = (float) str_replace(['.', ','], ['', '.'], $dados['valor'] ?? '0');
        $etapaId = (int) $dados['etapa_id'];
        $funilId = (int) ($dados['funil_id'] ?? 0);
        $clienteId = !empty($dados['cliente_id']) ? (int) $dados['cliente_id'] : null;
        $leadId = !empty($dados['lead_id']) ? (int) $dados['lead_id'] : null;
        $empresaId = !empty($dados['empresa_cliente_id']) ? (int) $dados['empresa_cliente_id'] : null;
        $origem = $dados['origem'] ?? 'Manual';

        // Se cliente rápido (apenas se não tiver lead nem cliente selecionado)
        if (is_null($clienteId) && is_null($leadId) && is_null($empresaId) && !empty($dados['novo_cliente_nome'])) {
            // Alterado para criar LEAD rápido ao invés de USUÁRIO
            $leadId = $this->criarLeadRapido($dados['novo_cliente_nome'], $dados['novo_cliente_email'] ?? '', $companyId);
        }

        $stmt = $this->conn->prepare("
            INSERT INTO crm_negocios (company_id, funil_id, etapa_id, cliente_id, lead_id, empresa_cliente_id, responsavel_id, titulo, valor_estimado, origem, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'aberto', NOW(), NOW())
        ");

        $stmt->bind_param("iiiiiiisds", $companyId, $funilId, $etapaId, $clienteId, $leadId, $empresaId, $userId, $titulo, $valor, $origem);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        Logger::error("Erro ao criar negócio (SQL): " . $stmt->error, ['dados' => $dados]);
        throw new Exception("Erro ao criar negócio. Tente novamente.");
    }

    private function criarLeadRapido($nome, $telefone, $companyId)
    {
        // $telefone aqui vem do campo que era email/telefone no form. Vamos tentar discernir ou salvar no campo telefone se parecer numero.
        // O form original passava telefone no campo 'novo_cliente_email' (ver JS).
        // Vamos salvar como telefone se tiver numeros, senao email? Ou salvar ambos?
        // O metodo recebe ($nome, $contato, $companyId).

        $email = null;
        $tel = null;

        if (strpos($telefone, '@') !== false) {
            $email = $telefone;
        } else {
            $tel = $telefone;
        }

        $stmt = $this->conn->prepare("INSERT INTO leads (company_id, nome, email, telefone, status, origem, ativo) VALUES (?, ?, ?, ?, 'novo', 'Criação Rápida CRM', 1)");
        $stmt->bind_param("isss", $companyId, $nome, $email, $tel);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        Logger::error("Erro ao criar lead rápido: " . $stmt->error, ['nome' => $nome]);
        throw new Exception("Erro ao criar lead rápido.");
    }

    // Mantido para compatibilidade se necessário, mas não usado no criarNegocio novo
    private function criarClienteRapido($nome, $telefone, $companyId)
    {
        $email = 'cli_' . time() . '_' . rand(100, 999) . '@sememail.com';
        $senha = password_hash(time(), PASSWORD_DEFAULT); // Senha aleatória

        $stmt = $this->conn->prepare("INSERT INTO users (company_id, nome, email, telefone, senha, tipo, ativo) VALUES (?, ?, ?, ?, ?, 'cliente', 1)");
        $stmt->bind_param("issss", $companyId, $nome, $email, $telefone, $senha);

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        Logger::error("Erro ao criar cliente rápido: " . $stmt->error, ['nome' => $nome]);
        throw new Exception("Erro ao criar cliente rápido.");
    }

    public function atualizarNegocio($id, $dados, $companyId)
    {
        $campos = [];
        $params = [];
        $types = "";

        if (isset($dados['titulo'])) {
            $campos[] = "titulo = ?";
            $params[] = $dados['titulo'];
            $types .= "s";
        }

        if (isset($dados['valor'])) {
            $campos[] = "valor_estimado = ?";
            $params[] = (float) str_replace(['.', ','], ['', '.'], $dados['valor']);
            $types .= "d";
        }

        if (isset($dados['etapa_id'])) {
            $campos[] = "etapa_id = ?";
            $params[] = (int) $dados['etapa_id'];
            $types .= "i";
        }

        if (isset($dados['lead_id'])) {
            $campos[] = "lead_id = ?";
            $params[] = (int) $dados['lead_id'];
            $types .= "i";
        }

        if (isset($dados['status'])) {
            $campos[] = "status = ?";
            $params[] = $dados['status'];
            $types .= "s";
        }

        if (empty($campos))
            return true;

        $campos[] = "updated_at = NOW()";

        $sql = "UPDATE crm_negocios SET " . implode(", ", $campos) . " WHERE id = ? AND company_id = ?";
        $params[] = $id;
        $params[] = $companyId;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function moverEtapa($dealId, $novaEtapaId, $companyId, $userId)
    {
        // Buscar etapa atual
        $negocio = $this->buscarNegocio($dealId, $companyId);
        if (!$negocio)
            throw new Exception("Negócio não encontrado");

        $etapaAnterior = $negocio['etapa_id'];

        if ($etapaAnterior == $novaEtapaId)
            return true;

        // Atualizar
        $stmt = $this->conn->prepare("UPDATE crm_negocios SET etapa_id = ?, updated_at = NOW() WHERE id = ? AND company_id = ?");
        $stmt->bind_param("iii", $novaEtapaId, $dealId, $companyId);

        if ($stmt->execute()) {
            // Histórico
            $stmtH = $this->conn->prepare("INSERT INTO crm_movimentacoes (negocio_id, etapa_anterior_id, etapa_nova_id, usuario_id, company_id) VALUES (?, ?, ?, ?, ?)");
            $stmtH->bind_param("iiiii", $dealId, $etapaAnterior, $novaEtapaId, $userId, $companyId);
            $stmtH->execute();
            return true;
        }
        return false;
    }

    public function buscarUsuarios($termo, $companyId)
    {
        $termoLike = "%$termo%";
        $stmt = $this->conn->prepare("SELECT id, nome, email, 'user' as tipo FROM users WHERE (nome LIKE ? OR email LIKE ?) AND company_id = ? AND tipo = 'cliente' LIMIT 5");
        $stmt->bind_param("ssi", $termoLike, $termoLike, $companyId);
        $stmt->execute();
        $res = $stmt->get_result();
        $usuarios = [];
        while ($row = $res->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    public function buscarEmpresas($termo, $companyId)
    {
        $termoLike = "%$termo%";
        // Tentativa 1: Tabela 'empresas' (Sem company_id aparentemente)
        $stmt = $this->conn->prepare("SELECT id, nome, documento, 'empresa' as tipo FROM empresas WHERE (nome LIKE ? OR documento LIKE ?) LIMIT 5");
        $stmt->bind_param("ss", $termoLike, $termoLike);
        $stmt->execute();
        $res = $stmt->get_result();
        $empresas = [];
        while ($row = $res->fetch_assoc()) {
            $empresas[] = $row;
        }
        return $empresas;
    }

    public function buscarLeads($termo)
    {
        $termoLike = "%$termo%";
        $stmt = $this->conn->prepare("SELECT id, nome, email, telefone, 'lead' as tipo FROM leads WHERE (nome LIKE ? OR email LIKE ? OR telefone LIKE ?) LIMIT 5");
        $stmt->bind_param("sss", $termoLike, $termoLike, $termoLike);
        $stmt->execute();
        $res = $stmt->get_result();
        $leads = [];
        while ($row = $res->fetch_assoc()) {
            $leads[] = $row;
        }
        return $leads;
    }

    public function listarResponsaveis($companyId)
    {
        $stmt = $this->conn->prepare("SELECT id, nome FROM users WHERE company_id = ? ORDER BY nome");
        $stmt->bind_param("i", $companyId);
        $stmt->execute();
        $res = $stmt->get_result();
        $users = [];
        while ($row = $res->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    // --- ADMINISTRATIVE METHODS ---

    public function criarFunil($companyId, $nome, $descricao)
    {
        $stmt = $this->conn->prepare("INSERT INTO crm_funis (company_id, nome, descricao, ativo) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("iss", $companyId, $nome, $descricao);
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        throw new Exception($this->conn->error);
    }

    public function editarFunil($id, $companyId, $nome, $descricao)
    {
        $stmt = $this->conn->prepare("UPDATE crm_funis SET nome=?, descricao=? WHERE id=? AND company_id=?");
        $stmt->bind_param("ssii", $nome, $descricao, $id, $companyId);
        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function excluirFunil($id, $companyId)
    {
        // Verificar se tem negócios
        $stmtCheck = $this->conn->prepare("SELECT COUNT(*) as c FROM crm_negocios WHERE funil_id = ? AND company_id = ?");
        $stmtCheck->bind_param("ii", $id, $companyId);
        $stmtCheck->execute();
        $count = $stmtCheck->get_result()->fetch_assoc()['c'];
        if ($count > 0) {
            throw new Exception("Não é possível excluir funil com negócios ativos.");
        }

        // Excluir etapas primeiro
        $stmtEtapas = $this->conn->prepare("DELETE FROM crm_etapas WHERE funil_id = ? AND company_id = ?");
        $stmtEtapas->bind_param("ii", $id, $companyId);
        $stmtEtapas->execute();

        $stmt = $this->conn->prepare("DELETE FROM crm_funis WHERE id=? AND company_id=?");
        $stmt->bind_param("ii", $id, $companyId);
        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function criarEtapa($companyId, $funilId, $nome, $ordem, $cor)
    {
        $stmt = $this->conn->prepare("INSERT INTO crm_etapas (company_id, funil_id, nome, ordem, cor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisis", $companyId, $funilId, $nome, $ordem, $cor);
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        throw new Exception($this->conn->error);
    }

    public function editarEtapa($id, $companyId, $nome, $ordem, $cor)
    {
        $stmt = $this->conn->prepare("UPDATE crm_etapas SET nome=?, ordem=?, cor=? WHERE id=? AND company_id=?");
        $stmt->bind_param("sisii", $nome, $ordem, $cor, $id, $companyId);
        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function excluirEtapa($id, $companyId)
    {
        $stmtCheck = $this->conn->prepare("SELECT COUNT(*) as c FROM crm_negocios WHERE etapa_id = ? AND company_id = ?");
        $stmtCheck->bind_param("ii", $id, $companyId);
        $stmtCheck->execute();
        $count = $stmtCheck->get_result()->fetch_assoc()['c'];
        if ($count > 0) {
            throw new Exception("Etapa possui negócios. Mova-os antes de excluir.");
        }
        $stmt = $this->conn->prepare("DELETE FROM crm_etapas WHERE id=? AND company_id=?");
        $stmt->bind_param("ii", $id, $companyId);
        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function reordenarEtapas($etapas, $companyId)
    {
        $stmt = $this->conn->prepare("UPDATE crm_etapas SET ordem = ? WHERE id = ? AND company_id = ?");
        foreach ($etapas as $etapa) {
            $ordem = (int) $etapa['ordem'];
            $id = (int) $etapa['id'];
            $stmt->bind_param("iii", $ordem, $id, $companyId);
            $stmt->execute();
        }
        return true;
    }

    public function salvarAnotacao($companyId, $negocioId, $usuarioId, $tipo, $descricao)
    {
        $stmt = $this->conn->prepare("INSERT INTO crm_atividades (company_id, negocio_id, realizado_por, tipo, descricao, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiss", $companyId, $negocioId, $usuarioId, $tipo, $descricao);
        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function vincularEmpresa($companyId, $negocioId, $empresaId, $usuarioId)
    {
        // Buscar nome da empresa (com prepared statement)
        $stmtEmp = $this->conn->prepare("SELECT nome FROM empresas WHERE id = ?");
        $stmtEmp->bind_param("i", $empresaId);
        $stmtEmp->execute();
        $empNome = $stmtEmp->get_result()->fetch_assoc()['nome'] ?? '';

        $stmt = $this->conn->prepare("UPDATE crm_negocios SET empresa_cliente_id = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param("iii", $empresaId, $negocioId, $companyId);

        if ($stmt->execute()) {
            $texto = "Vinculou a empresa: " . $empNome;
            $this->salvarAnotacao($companyId, $negocioId, $usuarioId, 'movimentacao', $texto);
            return true;
        }
        throw new Exception($this->conn->error);
    }

    public function excluirNegocio($id, $companyId)
    {
        $stmtAts = $this->conn->prepare("DELETE FROM crm_atividades WHERE negocio_id = ? AND company_id = ?");
        $stmtAts->bind_param("ii", $id, $companyId);
        $stmtAts->execute();

        $stmt = $this->conn->prepare("DELETE FROM crm_negocios WHERE id=? AND company_id=?");
        $stmt->bind_param("ii", $id, $companyId);

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function listarAtividades($negocioId, $companyId)
    {
        $stmt = $this->conn->prepare("
            (SELECT a.id, a.tipo, a.descricao as conteudo, a.created_at as data, u.nome as responsavel
            FROM crm_atividades a
            LEFT JOIN users u ON a.realizado_por = u.id
            WHERE a.negocio_id = ? AND a.company_id = ?)
            UNION ALL
            (SELECT m.id, 'movimentacao' as tipo, 
            CONCAT('Moveu de <b>', IFNULL(e1.nome, 'Início'), '</b> para <b>', IFNULL(e2.nome, 'Etapa'), '</b>') as conteudo, 
            m.created_at as data, u.nome as responsavel
            FROM crm_movimentacoes m
            LEFT JOIN users u ON m.usuario_id = u.id
            LEFT JOIN crm_etapas e1 ON m.etapa_anterior_id = e1.id
            LEFT JOIN crm_etapas e2 ON m.etapa_nova_id = e2.id
            WHERE m.negocio_id = ? AND m.company_id = ?)
            ORDER BY data DESC
        ");
        $stmt->bind_param("iiii", $negocioId, $companyId, $negocioId, $companyId);
        $stmt->execute();
        $res = $stmt->get_result();

        $atividades = [];
        while ($row = $res->fetch_assoc()) {
            $atividades[] = $row;
        }
        return $atividades;
    }
}
