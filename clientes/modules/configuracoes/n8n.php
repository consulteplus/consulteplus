<?php
$pageTitle = "Configurações n8n";
require_once __DIR__ . '/../../includes/header.php';

checkPermission(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $webhook_url = sanitize($_POST['webhook_url']);
    $api_token = sanitize($_POST['api_token']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Salvar configurações (em produção, usar tabela de configurações)
    $config_file = __DIR__ . '/../../config/n8n_config.json';
    $config = [
        'webhook_url' => $webhook_url,
        'roadmap_webhook_url' => sanitize($_POST['roadmap_webhook_url']),
        'analysis_webhook_url' => sanitize($_POST['analysis_webhook_url']),
        'mentoria_webhook_url' => sanitize($_POST['mentoria_webhook_url']),
        'roteiro_webhook_url' => sanitize($_POST['roteiro_webhook_url']), // Novo campo Roteiro IA
        'api_token' => $api_token,
        'ativo' => $ativo,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if (file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT))) {
        $_SESSION['success'] = "Configurações salvas com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao salvar configurações.";
    }
}

// Carregar configurações
$config_file = __DIR__ . '/../../config/n8n_config.json';
$defaults = [
    'webhook_url' => '',
    'roadmap_webhook_url' => '',
    'analysis_webhook_url' => '',
    'api_token' => '',
    'ativo' => 0
];
$config = file_exists($config_file) ? array_merge($defaults, json_decode(file_get_contents($config_file), true)) : $defaults;
?>

<div class="page-header">
    <h1><i class="bi bi-gear me-2"></i>Configurações do Sistema</h1>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo BASE_URL; ?>configuracoes">
            <i class="bi bi-sliders me-2"></i>Gerais
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo BASE_URL; ?>configuracoes/agendamento-online">
            <i class="bi bi-globe me-2"></i>Agendamento Online
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo BASE_URL; ?>especialidades">
            <i class="bi bi-list-ul me-2"></i>Especialidades
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="#">
            <i class="bi bi-link-45deg me-2"></i>Integrações
        </a>
    </li>
</ul>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-link-45deg me-2"></i>Webhooks n8n
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="webhook_url" class="form-label">URL do Webhook (Notificações) *</label>
                        <input type="url" class="form-control" id="webhook_url" name="webhook_url" 
                               value="<?php echo htmlspecialchars($config['webhook_url']); ?>" 
                               placeholder="https://seu-n8n.com/webhook/notificacoes" required>
                        <small class="text-muted">URL para envio de notificações (WhatsApp, Email)</small>
                    </div>

                    <div class="mb-3">
                        <label for="roadmap_webhook_url" class="form-label">URL do Webhook (Roadmap IA)</label>
                        <input type="url" class="form-control" id="roadmap_webhook_url" name="roadmap_webhook_url" 
                               value="<?php echo htmlspecialchars($config['roadmap_webhook_url'] ?? ''); ?>" 
                               placeholder="https://seu-n8n.com/webhook/roadmap-ia">
                        <small class="text-muted">URL para geração de tarefas (Roadmap)</small>
                    </div>

                    <div class="mb-3">
                        <label for="analysis_webhook_url" class="form-label">URL do Webhook (Análise Executiva IA)</label>
                        <input type="url" class="form-control" id="analysis_webhook_url" name="analysis_webhook_url" 
                               value="<?php echo htmlspecialchars($config['analysis_webhook_url'] ?? ''); ?>" 
                               placeholder="https://seu-n8n.com/webhook/analise-clinica">
                        <small class="text-muted">URL para geração do resumo executivo e análise de respostas</small>
                    </div>

                    <div class="mb-3">
                        <label for="mentoria_webhook_url" class="form-label">URL do Webhook (Gerador de Mentorias IA)</label>
                        <input type="url" class="form-control" id="mentoria_webhook_url" name="mentoria_webhook_url" 
                               value="<?php echo htmlspecialchars($config['mentoria_webhook_url'] ?? ''); ?>" 
                               placeholder="https://seu-n8n.com/webhook/gerar-mentoria">
                        <small class="text-muted">URL para criar produtos, trilhas e roteiros com IA</small>
                    </div>

                    <div class="mb-3">
                        <label for="roteiro_webhook_url" class="form-label">URL do Webhook (Gerador de Roteiros IA)</label>
                        <input type="url" class="form-control" id="roteiro_webhook_url" name="roteiro_webhook_url" 
                               value="<?php echo htmlspecialchars($config['roteiro_webhook_url'] ?? ''); ?>" 
                               placeholder="https://seu-n8n.com/webhook/gerar-roteiro">
                        <small class="text-muted">URL para criar o texto das aulas (roteiro de gravação)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="api_token" class="form-label">Token de API (opcional)</label>
                        <input type="text" class="form-control" id="api_token" name="api_token" 
                               value="<?php echo htmlspecialchars($config['api_token']); ?>" 
                               placeholder="seu-token-secreto">
                        <small class="text-muted">Token para autenticação (se configurado no n8n)</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                   <?php echo $config['ativo'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="ativo">
                                <strong>Integração Ativa</strong>
                            </label>
                        </div>
                        <small class="text-muted">Ative para permitir o funcionamento das integrações</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Salvar Configurações
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-code-square me-2"></i>Testar Integração
            </div>
            <div class="card-body">
                <p>Envie uma notificação de teste para verificar se a integração está funcionando:</p>
                <button type="button" class="btn btn-success" id="btnTestar">
                    <i class="bi bi-send me-2"></i>Enviar Teste
                </button>
                <div id="resultadoTeste" class="mt-3"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Como Configurar
            </div>
            <div class="card-body">
                <h6>1. Criar Workflow no n8n</h6>
                <p class="small">Crie um workflow que receba dados via webhook</p>
                
                <h6>2. Configurar Webhook</h6>
                <p class="small">Adicione um nó "Webhook" e copie a URL gerada</p>
                
                <h6>3. Cole a URL Aqui</h6>
                <p class="small">Cole a URL do webhook no campo acima</p>
                
                <h6>4. Ativar Integração</h6>
                <p class="small">Marque "Integração Ativa" e salve</p>
                
                <hr>
                
                <h6>Dados Enviados</h6>
                <p class="small">O sistema enviará:</p>
                <ul class="small">
                    <li>tipo (lembrete, confirmacao)</li>
                    <li>paciente_nome</li>
                    <li>paciente_telefone</li>
                    <li>data_hora</li>
                    <li>profissional_nome</li>
                    <li>tipo_procedimento</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnTestar').addEventListener('click', function() {
    const btn = this;
    const resultado = document.getElementById('resultadoTeste');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    
    fetch('<?php echo BASE_URL; ?>api/n8n/test.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultado.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + data.message + '</div>';
        } else {
            resultado.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>' + data.message + '</div>';
        }
    })
    .catch(error => {
        resultado.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>Erro: ' + error + '</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Teste';
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
