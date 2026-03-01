<?php
require_once __DIR__ . '/../config/database.php';

class ProductContentService
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // --- TRILHAS (MÓDULOS) ---

    public function listarTrilhas($produtoId)
    {
        $stmt = $this->conn->prepare("
            SELECT t.*, 
            (SELECT COUNT(*) FROM mentoria_conteudos c WHERE c.trilha_id = t.id) as qtd_aulas 
            FROM mentoria_trilhas t 
            WHERE t.produto_id = ? 
            ORDER BY t.ordem ASC, t.id ASC
        ");
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $result = $stmt->get_result();

        $trilhas = [];
        while ($row = $result->fetch_assoc()) {
            $trilhas[] = $row;
        }
        return $trilhas;
    }

    public function salvarTrilha($dados)
    {
        $produtoId = (int) $dados['produto_id'];
        $titulo = $dados['titulo'];
        $ordem = isset($dados['ordem']) ? (int) $dados['ordem'] : 1;
        $id = isset($dados['id']) ? (int) $dados['id'] : 0;

        if (empty($titulo)) {
            throw new Exception("Título é obrigatório.");
        }

        if ($id > 0) {
            $stmt = $this->conn->prepare("UPDATE mentoria_trilhas SET titulo = ?, ordem = ? WHERE id = ?");
            $stmt->bind_param("sii", $titulo, $ordem, $id);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO mentoria_trilhas (produto_id, titulo, ordem) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $produtoId, $titulo, $ordem);
        }

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function excluirTrilha($id)
    {
        // Conteúdos serão apagados via CASCADE ou devem ser tratados. 
        // Assumindo delete direto conforme original.
        $stmt = $this->conn->prepare("DELETE FROM mentoria_trilhas WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function reordenarTrilhas($ordemIds)
    {
        if (!is_array($ordemIds))
            return false;

        $sql = "UPDATE mentoria_trilhas SET ordem = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        foreach ($ordemIds as $index => $id) {
            $ordem = $index + 1;
            $stmt->bind_param("ii", $ordem, $id);
            $stmt->execute();
        }
        return true;
    }

    // --- CONTEÚDOS (AULAS) ---

    public function listarConteudos($trilhaId)
    {
        $stmt = $this->conn->prepare("
            SELECT c.*, 
            d.titulo as diag_titulo, 
            f.nome as tool_nome 
            FROM mentoria_conteudos c 
            LEFT JOIN gestao_diagnostico_modelos d ON c.resource_id = d.id AND c.tipo = 'diagnostico'
            LEFT JOIN ferramentas_tipos f ON c.resource_id = f.id AND c.tipo = 'ferramenta'
            WHERE c.trilha_id = ? 
            ORDER BY c.ordem ASC
        ");
        $stmt->bind_param("i", $trilhaId);
        $stmt->execute();
        $result = $stmt->get_result();

        $conteudos = [];
        while ($row = $result->fetch_assoc()) {
            $conteudos[] = $row;
        }
        return $conteudos;
    }

    public function buscarConteudo($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM mentoria_conteudos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function salvarConteudo($dados)
    {
        $id = isset($dados['id']) ? (int) $dados['id'] : 0;
        $trilha_id = isset($dados['trilha_id']) ? (int) $dados['trilha_id'] : 0;

        $titulo = $dados['titulo'];
        $tipo = $dados['tipo'];
        $url_video = $dados['url_video'] ?? null;
        $descricao = $dados['descricao'] ?? '';
        $roteiro = $dados['roteiro'] ?? '';

        $tem_tarefa = !empty($dados['tem_tarefa']) ? 1 : 0;
        $tarefa_descricao = $dados['tarefa_descricao'] ?? '';
        $tarefa_tipo_entrega = $dados['tarefa_tipo_entrega'] ?? 'texto';

        $resource_id = null;
        if ($tipo === 'diagnostico') {
            $resource_id = !empty($dados['resource_id_diag']) ? (int) $dados['resource_id_diag'] : null;
        } elseif ($tipo === 'ferramenta') {
            $resource_id = !empty($dados['resource_id_tool']) ? (int) $dados['resource_id_tool'] : null;
        }

        if ($id > 0) {
            $stmt = $this->conn->prepare("UPDATE mentoria_conteudos SET titulo=?, tipo=?, url_video=?, descricao=?, roteiro=?, tem_tarefa=?, tarefa_descricao=?, tarefa_tipo_entrega=?, resource_id=? WHERE id=?");
            $stmt->bind_param("sssssissii", $titulo, $tipo, $url_video, $descricao, $roteiro, $tem_tarefa, $tarefa_descricao, $tarefa_tipo_entrega, $resource_id, $id);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO mentoria_conteudos (trilha_id, titulo, descricao, tipo, url_video, tem_tarefa, tarefa_descricao, tarefa_tipo_entrega, resource_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssissi", $trilha_id, $titulo, $descricao, $tipo, $url_video, $tem_tarefa, $tarefa_descricao, $tarefa_tipo_entrega, $resource_id);
        }

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    public function excluirConteudo($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM mentoria_conteudos WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception($this->conn->error);
        }
        return true;
    }

    // --- RECURSOS (Auxiliares) ---

    public function listarDiagnosticos()
    {
        $result = $this->conn->query("SELECT id, titulo FROM gestao_diagnostico_modelos WHERE ativo = 1 ORDER BY titulo ASC");
        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
        return $dados;
    }

    public function listarFerramentas()
    {
        $result = $this->conn->query("SELECT id, nome FROM ferramentas_tipos WHERE ativo = 1 ORDER BY nome ASC");
        $dados = [];
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
        return $dados;
    }
}
?>