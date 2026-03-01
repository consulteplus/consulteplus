<?php
// Visualizar Retorno do n8n
// Lê o arquivo last_response.json e exibe formatado

$file = __DIR__ . '/last_response.json';
$content = file_exists($file) ? file_get_contents($file) : '{}';
$json = json_decode($content, true);

// Tentar normalizar para exibição de lista
$leads = [];
$is_list = false;
if (is_array($json)) {
    if (isset($json[0])) {
        $is_list = true;
    }
}

if ($is_list) {
    $leads = $json;
} elseif (isset($json['leads']) && is_array($json['leads'])) {
    $leads = $json['leads'];
} elseif (isset($json['results']) && is_array($json['results'])) {
    $leads = $json['results'];
} elseif (isset($json['data']) && is_array($json['data'])) {
    $leads = $json['data'];
} elseif ($json) {
    $leads = [$json];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug do Retorno n8n</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            padding: 20px;
        }

        .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }

        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            max-height: 500px;
            overflow: auto;
        }

        .badge-status {
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-bug"></i> Debug: Retorno do Scraping</h3>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </div>

        <!-- Alerta sobre Quantidade -->
        <div class="alert alert-info">
            <strong>Status:</strong> O arquivo JSON bruto tem tamanho de
            <?php echo strlen($content); ?> bytes.
            <br>
            <strong>Leads Identificados:</strong>
            <?php echo count($leads); ?>
            <?php if (count($leads) === 1): ?>
                <br><span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> Atenção: O n8n retornou
                    apenas um Objeto Único, não uma Lista. Verifique se o nó final do n8n está consolidando os itens em um
                    array.</span>
            <?php endif; ?>
        </div>

        <!-- Tabela de Pré-visualização -->
        <div class="card">
            <div class="card-header bg-white fw-bold">
                Pré-visualização dos Dados Recebidos
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Endereço</th>
                                <th>Site</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $i => $lead):
                                $nome = $lead['nome'] ?? $lead['name'] ?? $lead['title'] ?? '-';
                                $phone = $lead['telefone'] ?? $lead['phone_number'] ?? $lead['phone'] ?? '-';
                                $addr = $lead['endereco'] ?? $lead['full_address'] ?? $lead['address'] ?? '-';
                                $site = $lead['site'] ?? $lead['website'] ?? '-';
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $i + 1; ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($nome); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($phone); ?>
                                    </td>
                                    <td><small>
                                            <?php echo htmlspecialchars($addr); ?>
                                        </small></td>
                                    <td><small>
                                            <?php echo htmlspecialchars($site); ?>
                                        </small></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($leads)): ?>
                                <tr>
                                    <td colspan="5" class="text-center p-3">Nenhum dado interpretado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- JSON Bruto -->
        <div class="card">
            <div class="card-header bg-white fw-bold d-flex justify-content-between">
                <span>JSON Bruto (Raw Response)</span>
                <button class="btn btn-sm btn-outline-primary" onclick="copyJson()">Copiar JSON</button>
            </div>
            <div class="card-body">
                <pre
                    id="jsonContent"><?php echo htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
            </div>
        </div>
    </div>

    <script>
        function copyJson() {
            const content = document.getElementById('jsonContent').innerText;
            navigator.clipboard.writeText(content).then(() => {
                alert('JSON copiado para a área de transferência!');
            });
        }
    </script>
</body>

</html>