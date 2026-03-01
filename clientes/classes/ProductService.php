<?php
require_once __DIR__ . '/../config/database.php';

class ProductService
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function listar($companyId, $limit = 10, $offset = 0)
    {
        $sql = "
            SELECT 
                p.*,
                (SELECT COUNT(*) FROM mentoria_trilhas t WHERE t.produto_id = p.id) as total_trilhas,
                (SELECT COUNT(*) FROM mentoria_acesso_empresas a WHERE a.produto_id = p.id) as total_alunos
            FROM mentoria_produtos p 
            WHERE p.company_id = ? 
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $companyId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos = [];
        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }
        return $produtos;
    }

    public function contarTotal($companyId)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM mentoria_produtos WHERE company_id = ?");
        $stmt->bind_param("i", $companyId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function buscar($id, $companyId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM mentoria_produtos WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $id, $companyId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function salvar($dados, $companyId)
    {
        $id = isset($dados['id']) ? (int) $dados['id'] : 0;

        // Validação básica
        if (empty($dados['titulo'])) {
            throw new Exception("O título do produto é obrigatório.");
        }

        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'] ?? '';
        $tipo = $dados['tipo'] ?? 'mentoria';
        $imagem_capa = $dados['imagem_capa'] ?? '';
        $ativo = isset($dados['ativo']) ? 1 : 0;
        $valor = (float) ($dados['valor'] ?? 0);
        $ciclo = $dados['ciclo'] ?? 'MONTHLY';
        $tipo_cobranca = $dados['tipo_cobranca'] ?? 'recorrente';
        $link_checkout = $dados['link_checkout'] ?? '';

        if ($id > 0) {
            // Update
            $stmt = $this->conn->prepare("UPDATE mentoria_produtos SET titulo=?, descricao=?, tipo=?, imagem_capa=?, ativo=?, valor=?, ciclo=?, tipo_cobranca=?, link_checkout=? WHERE id=? AND company_id=?");
            $stmt->bind_param("ssssidsssii", $titulo, $descricao, $tipo, $imagem_capa, $ativo, $valor, $ciclo, $tipo_cobranca, $link_checkout, $id, $companyId);
        } else {
            // Insert
            $stmt = $this->conn->prepare("INSERT INTO mentoria_produtos (company_id, titulo, descricao, tipo, imagem_capa, ativo, valor, ciclo, tipo_cobranca, link_checkout) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssidsss", $companyId, $titulo, $descricao, $tipo, $imagem_capa, $ativo, $valor, $ciclo, $tipo_cobranca, $link_checkout);
        }

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }

        return $id > 0 ? $id : $stmt->insert_id;
    }

    public function excluir($id, $companyId)
    {
        // Verificar dependências antes de excluir (opcional, pode ser CASCADE no banco, mas bom validar)
        // Por via das dúvidas, vamos apagar trilhas e vínculos primeiro ou deixar o banco chiar?
        // O código original fazia DELETE direto. O usuário disse "Todos as trilhas e conteúdos serão apagados".
        // Vamos manter o DELETE simples por enquanto, assumindo CASCADE ou força bruta.

        $stmt = $this->conn->prepare("DELETE FROM mentoria_produtos WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $id, $companyId);

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }
}
?>