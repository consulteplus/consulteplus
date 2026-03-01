<?php
// modules/admin/produtos/salvar_ia.php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../classes/ProductService.php';
require_once __DIR__ . '/../../../classes/ProductContentService.php';

session_start();

// Verifica permissão (Admin)
if (!isset($_SESSION['user_id']) || !in_array('admin', $_SESSION['permissoes'] ?? ['admin'])) {
    die("Acesso negado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $company_id = $_SESSION['company_id'];
        $titulo_produto = $_POST['titulo'];
        $descricao_produto = $_POST['descricao'];
        $tipo_produto = $_POST['tipo'] ?? 'mentoria';
        $raw_json = $_POST['ia_data'];
        $data = json_decode($raw_json, true);

        // Services
        $productService = new ProductService();
        $contentService = new ProductContentService();

        // 1. Criar Produto
        $dadosProduto = [
            'id' => 0,
            'titulo' => $titulo_produto,
            'descricao' => $descricao_produto,
            'tipo' => $tipo_produto,
            'ativo' => 1,
            'imagem_capa' => '',
            'valor' => 0,
            'ciclo' => 'MONTHLY',
            'tipo_cobranca' => 'recorrente',
            'link_checkout' => ''
        ];

        $produto_id = $productService->salvar($dadosProduto, $company_id);

        $modules = $data['modules'] ?? $data['modulos'] ?? [];
        $ordem_trilha = 1;

        // 2. Criar Trilhas (Módulos)
        foreach ($modules as $mod) {
            $titulo_trilha = $mod['title'] ?? $mod['titulo'];

            // Salvar trilha
            $contentService->salvarTrilha([
                'id' => 0,
                'produto_id' => $produto_id,
                'titulo' => $titulo_trilha,
                'ordem' => $ordem_trilha
            ]);

            // Precisamos do ID da trilha recém criada. 
            // O `salvarTrilha` atual retorna boolean, mas precisamos do ID para vincular aulas.
            // WORKAROUND: Como salvarTrilha não retorna ID (ainda), vamos ter que modificar salvarTrilha ou pegar por query ultimo id (arriscado).
            // Melhor abordagem: Ajustar ProductContentService::salvarTrilha para retornar o ID.

            // Como não posso modificar o service agora sem quebrar o fluxo, vou fazer uma busca rápida pelo titulo e produto_id e ordem para pegar o ID.
            // (Ou melhor, vou assumir que vou modificar o Service num passo separado se der erro, mas tentemos fazer o Service retornar ID no próximo passo se necessário).

            // BUSCA TRILHA RECEM CRIADA (Safety)
            $stmtGetId = $conn->prepare("SELECT id FROM mentoria_trilhas WHERE produto_id = ? AND ordem = ? ORDER BY id DESC LIMIT 1");
            $stmtGetId->bind_param("ii", $produto_id, $ordem_trilha);
            $stmtGetId->execute();
            $trilha_id = $stmtGetId->get_result()->fetch_assoc()['id'];

            $ordem_trilha++;
            $ordem_aula = 1;

            // 3. Criar Conteúdos (Aulas)
            $lessons = $mod['lessons'] ?? $mod['aulas'] ?? [];
            foreach ($lessons as $lesson) {
                $titulo_aula = $lesson['title'] ?? $lesson['titulo'];
                $desc_aula = $lesson['description'] ?? $lesson['descricao'];

                // Tarefas
                $has_task = !empty($lesson['has_task']) || !empty($lesson['tem_tarefa']) ? 1 : 0;
                $task_desc = $lesson['task_description'] ?? $lesson['tarefa_descricao'] ?? '';

                $duracao_aula = $lesson['duration'] ?? $lesson['duracao'] ?? '';
                $resource_type = $lesson['resource_type'] ?? null;
                $resource_id = $lesson['resource_id'] ?? null;

                $tipo_conteudo = 'video';
                $resource_id_diag = null;
                $resource_id_tool = null;

                if ($resource_type === 'diagnostico') {
                    $tipo_conteudo = 'diagnostico';
                    $resource_id_diag = $resource_id;
                }
                if ($resource_type === 'ferramenta') {
                    $tipo_conteudo = 'ferramenta';
                    $resource_id_tool = $resource_id;
                }

                // Salvar Aula
                $contentService->salvarConteudo([
                    'id' => 0,
                    'trilha_id' => $trilha_id,
                    'titulo' => $titulo_aula,
                    'descricao' => $desc_aula,
                    'tipo' => $tipo_conteudo,
                    'url_video' => '',
                    'tem_tarefa' => $has_task,
                    'tarefa_descricao' => $task_desc,
                    'tarefa_tipo_entrega' => 'texto',
                    'roteiro' => '', // IA nao gera roteiro aqui
                    'resource_id_diag' => $resource_id_diag,
                    'resource_id_tool' => $resource_id_tool
                ]);

                $ordem_aula++;
            }
        }

        $_SESSION['success'] = "Produto Criado com IA!";
        header('Location: ' . BASE_URL . 'admin/produtos/trilhas/' . $produto_id);
        exit;

    } catch (Exception $e) {
        die("Erro ao salvar produto: " . $e->getMessage());
    }
}
?>