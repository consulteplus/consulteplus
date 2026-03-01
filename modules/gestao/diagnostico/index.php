<?php
$pageTitle = "Diagnóstico Empresarial";
require_once __DIR__ . '/../../../includes/header.php';
checkPermission(['admin', 'cliente']);
?>
<!-- Chart.js for Mini Radar Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>


<?php
// Buscar histórico de diagnósticos do usuário
$userId = $_SESSION['user_id'] ?? 0;
$company_id = (int) ($_SESSION['company_id'] ?? 0);
$historico = [];

// JOIN para pegar o título do modelo
$sql = "SELECT r.*, m.titulo as modelo_titulo 
        FROM gestao_diagnostico_resultados r 
        LEFT JOIN gestao_diagnostico_modelos m ON r.modelo_id = m.id
        WHERE r.user_id = $userId AND r.company_id = $company_id 
        ORDER BY r.data_realizacao DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $historico[] = $row;
    }
}

// Verificar se tem modelos disponíveis para novo diagnóstico
$sqlModelos = "SELECT count(*) as total 
               FROM gestao_diagnostico_modelos m
               JOIN recursos_atribuicoes a ON m.id = a.recurso_id 
               WHERE a.empresa_id = $company_id 
               AND a.recurso_tipo = 'diagnostico' 
               AND a.ativo = 1
               AND (a.proxima_data IS NULL OR a.proxima_data <= CURDATE())";
$resModelos = $conn->query($sqlModelos);
$temModelosDisponiveis = $resModelos && $resModelos->fetch_assoc()['total'] > 0;

// Buscar Modelos Disponíveis para o Modal
$sqlModelosDisponiveis = "
    SELECT m.* 
    FROM gestao_diagnostico_modelos m
    JOIN recursos_atribuicoes a ON m.id = a.recurso_id
    WHERE m.ativo = 1 
    AND a.empresa_id = $company_id 
    AND a.recurso_tipo = 'diagnostico'
    AND a.ativo = 1
";
$resModelos = $conn->query($sqlModelosDisponiveis);
$modelosDisponiveis = [];
if ($resModelos) {
    while ($m = $resModelos->fetch_assoc()) {
        $modelosDisponiveis[] = $m;
    }
}
?>

<div class="container py-4">


    <!-- Header Padronizado (Sempre Visível) -->
    <!-- Header Padronizado (Boxed) -->
    <div class="mb-4">
        <div class="card premium-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold text-dark mb-1">Maturidade do Negócio</h4>
                        <p class="text-muted mb-0">Histórico de avaliações e evolução.</p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalNewDiagnostic">
                            <i class="bi bi-plus-lg me-2"></i>Novo Diagnóstico
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($historico)): ?>

        <?php if (empty($modelosDisponiveis)): ?>
            <!-- EMPTY STATE (Sem acesso a nenhum diagnóstico) -->
            <!-- EMPTY STATE (Sem acesso absoluto) -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5 px-4">
                            <div class="mb-4">
                                <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>

                            <h3 class="fw-bold mb-3">Nenhum Diagnóstico Habilitado</h3>

                            <p class="text-muted mb-4">
                                Sua conta ainda não possui diagnósticos liberados para preenchimento.
                            </p>

                            <div class="d-inline-block text-start bg-light rounded p-3 mb-4 mx-auto" style="max-width: 450px;">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <span>Entre em contato com seu mentor ou administrador para solicitar a liberação do
                                        acesso.</span>
                                </small>
                            </div>

                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="<?php echo BASE_URL; ?>dashboard" class="btn btn-primary px-4">
                                    <i class="bi bi-arrow-left me-2"></i>Voltar para o Início
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- EMPTY STATE (Boas Vindas - Com acesso disponível) -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h3 class="fw-bold mb-3">Faça Seu Primeiro Diagnóstico</h3>
                        <p class="text-muted" style="max-width: 700px; margin: 0 auto;">
                            O diagnóstico avalia a maturidade da sua empresa em áreas cruciais para o crescimento sustentável.
                        </p>
                    </div>

                    <!-- Pillars -->
                    <div class="row g-3 mb-5 row-cols-1 row-cols-md-2 row-cols-lg-5 justify-content-center">
                        <!-- Pilar 1 -->
                        <div class="col">
                            <div class="card assessment-card h-100 bg-light border-0">
                                <div class="card-body text-center p-3">
                                    <div class="pillar-icon bg-white text-primary shadow-sm mb-3">
                                        <i class="bi bi-gear-wide-connected"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary">Operação</h6>
                                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Recepção e processos.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pilar 2 -->
                        <div class="col">
                            <div class="card assessment-card h-100 bg-light border-0">
                                <div class="card-body text-center p-3">
                                    <div class="pillar-icon bg-white text-success shadow-sm mb-3">
                                        <i class="bi bi-graph-up-arrow"></i>
                                    </div>
                                    <h6 class="fw-bold text-success">Financeiro</h6>
                                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Caixa e Lucro.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pilar 3 -->
                        <div class="col">
                            <div class="card assessment-card h-100 bg-light border-0">
                                <div class="card-body text-center p-3">
                                    <div class="pillar-icon bg-white text-info shadow-sm mb-3">
                                        <i class="bi bi-megaphone"></i>
                                    </div>
                                    <h6 class="fw-bold text-info">Aquisição</h6>
                                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Marketing e Vendas.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pilar 4 -->
                        <div class="col">
                            <div class="card assessment-card h-100 bg-light border-0">
                                <div class="card-body text-center p-3">
                                    <div class="pillar-icon bg-white text-danger shadow-sm mb-3">
                                        <i class="bi bi-heart-pulse"></i>
                                    </div>
                                    <h6 class="fw-bold text-danger">Jornada</h6>
                                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Experiência e LTV.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pilar 5 -->
                        <div class="col">
                            <div class="card assessment-card h-100 bg-light border-0">
                                <div class="card-body text-center p-3">
                                    <div class="pillar-icon bg-white text-warning shadow-sm mb-3">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h6 class="fw-bold text-warning">Equipe</h6>
                                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Cultura e Liderança.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="text-center">
                        <button class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow hover-shadow" type="button"
                            data-bs-toggle="modal" data-bs-target="#modalNewDiagnostic">
                            Começar Diagnóstico Gratuito
                        </button>
                        <div class="mt-3">
                            <small class="text-muted"><i class="bi bi-clock me-1"></i> Tempo estimado: 5 minutos</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- HISTORY GRID -->
        <div class="row g-4">
            <!-- Card Novo Diagnóstico (atalho rápido) - APENAS SE TIVER MODELOS DISPONÍVEIS -->
            <?php if ($temModelosDisponiveis): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-2 border-dashed border-primary bg-light hover-shadow transition-all cursor-pointer"
                        data-bs-toggle="modal" data-bs-target="#modalNewDiagnostic" role="button">
                        <div class="card-body d-flex align-items-center justify-content-center p-4">
                            <div class="text-center">
                                <div class="mb-3 text-primary opacity-75">
                                    <i class="bi bi-plus-circle display-4"></i>
                                </div>
                                <h5 class="fw-bold text-primary">Novo Diagnóstico</h5>
                                <p class="small text-muted mb-0">Clique para iniciar</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($historico as $diag):
                $badges = [
                    'Iniciante' => 'danger',
                    'Em Crescimento' => 'warning',
                    'Líder de Mercado' => 'success'
                ];
                $cor = $badges[$diag['nivel_maturidade']] ?? 'secondary';
                ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-header bg-white border-bottom px-3 pt-3 pb-3"
                            style="border-color: rgba(0,0,0,0.05) !important;">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="text-primary fw-bold mb-0" style="font-size: 0.95rem; line-height: 1.2;">
                                    <?php echo htmlspecialchars($diag['modelo_titulo'] ?? 'Diagnóstico Padrão'); ?>
                                </h6>
                                <div class="dropdown ms-2">
                                    <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item"
                                                href="<?php echo BASE_URL; ?>gestao/diagnostico/resultado/<?php echo $diag['id']; ?>"><i
                                                    class="bi bi-eye me-2"></i>Ver Detalhes</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <!-- RADAR CHART - PRIMEIRO -->
                            <div class="px-3 pt-0 pb-2">
                                <div style="position: relative; height: 240px; margin: -10px auto 0;">
                                    <canvas id="miniRadar<?php echo $diag['id']; ?>" data-scores='<?php
                                       echo json_encode([
                                           'operacional' => intval($diag['score_operacao'] ?? 0),
                                           'financeiro' => intval($diag['score_financeiro'] ?? 0),
                                           'aquisicao' => intval($diag['score_aquisicao'] ?? 0),
                                           'jornada' => intval($diag['score_jornada'] ?? 0),
                                           'equipe' => intval($diag['score_equipe'] ?? 0)
                                       ]);
                                       ?>'></canvas>
                                </div>
                            </div>

                            <!-- Score Geral + Data - EMBAIXO -->
                            <div class="text-center pb-3 pt-2 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
                                <div class="h2 fw-bold text-dark mb-1" style="line-height: 1;">
                                    <?php echo intval($diag['score_geral']); ?>
                                </div>
                                <small class="text-muted d-block mb-2" style="font-size: 0.7rem;">Pontos Gerais</small>
                                <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($diag['data_realizacao'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 p-3">
                            <a href="<?php echo BASE_URL; ?>gestao/diagnostico/resultado/<?php echo $diag['id']; ?>"
                                class="btn btn-outline-primary w-100 btn-sm fw-bold">
                                Ver Plano de Ação
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    <?php endif; ?>
</div>

<!-- Modal Seleção de Diagnóstico -->
<div class="modal fade" id="modalNewDiagnostic" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Novo Diagnóstico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <p class="text-muted mb-4">Escolha qual avaliação deseja realizar neste momento:</p>

                <div class="row g-3">
                    <?php if (!empty($modelosDisponiveis)): ?>
                        <?php foreach ($modelosDisponiveis as $md): ?>
                            <div class="col-md-6">
                                <div class="card h-100 border hover-shadow transition-all">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                                <i class="bi bi-ui-checks fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">
                                                    <?php echo htmlspecialchars($md['titulo']); ?>
                                                </h6>
                                                <!-- <small class="text-muted">Geral</small> -->
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-3 text-truncate-3">
                                            <?php echo htmlspecialchars($md['descricao'] ?: 'Avalie os principais pilares do seu negócio.'); ?>
                                        </p>
                                        <a href="<?php echo BASE_URL; ?>gestao/diagnostico/novo?modelo_id=<?php echo $md['id']; ?>"
                                            class="btn btn-outline-primary w-100 fw-bold">
                                            Iniciar Avaliação <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-slash-circle text-muted fs-1"></i>
                            <h6 class="text-muted mt-3">Nenhum diagnóstico disponível para seu plano.</h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize all mini radar charts
        const canvases = document.querySelectorAll('canvas[id^="miniRadar"]');

        canvases.forEach(canvas => {
            const scoresData = JSON.parse(canvas.getAttribute('data-scores'));

            // Extract labels and values
            const labels = [];
            const values = [];

            const labelMap = {
                'operacional': 'Operação',
                'financeiro': 'Financeiro',
                'aquisicao': 'Aquisição',
                'jornada': 'Jornada',
                'equipe': 'Equipe'
            };

            for (const [key, value] of Object.entries(scoresData)) {
                if (value > 0) {
                    const labelName = labelMap[key] || key;
                    labels.push(labelName);
                    values.push(value);
                }
            }

            // Create mini radar chart
            new Chart(canvas, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        fill: true,
                        backgroundColor: function (context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;
                            if (!chartArea) return 'rgba(29, 47, 95, 0.1)';

                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
                            gradient.addColorStop(1, 'rgba(29, 47, 95, 0.05)');
                            return gradient;
                        },
                        borderColor: 'rgba(99, 102, 241, 0.8)',
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(99, 102, 241, 1)',
                        pointHoverBorderWidth: 3,
                        borderWidth: 2.5,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            min: 0,
                            ticks: {
                                stepSize: 20,
                                display: false,
                                backdropColor: 'transparent'
                            },
                            pointLabels: {
                                font: {
                                    size: 11,
                                    weight: '600',
                                    family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                                },
                                color: '#64748b',
                                padding: 15
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.15)',
                                lineWidth: 1.5,
                                circular: true
                            },
                            angleLines: {
                                color: 'rgba(148, 163, 184, 0.15)',
                                lineWidth: 1.5
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.r + '%';
                                },
                                title: function (context) {
                                    return context[0].label;
                                }
                            }
                        },
                        datalabels: false
                    },
                    animation: {
                        duration: 800,
                        easing: 'easeInOutQuart',
                        onComplete: function () {
                            const ctx = this.ctx;
                            const chart = this;

                            // Estilo refinado para os valores
                            ctx.font = '600 13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                            ctx.fillStyle = '#1e293b';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';

                            // Adicionar fundo branco semi-transparente atrás dos números
                            const meta = chart.getDatasetMeta(0);
                            meta.data.forEach((point, index) => {
                                const value = chart.data.datasets[0].data[index];
                                const text = value.toString();
                                const textWidth = ctx.measureText(text).width;

                                // Fundo arredondado menor
                                ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
                                ctx.beginPath();
                                ctx.roundRect(point.x - textWidth / 2 - 4, point.y - 15, textWidth + 8, 16, 4);
                                ctx.fill();

                                // Borda sutil
                                ctx.strokeStyle = 'rgba(99, 102, 241, 0.2)';
                                ctx.lineWidth = 1;
                                ctx.stroke();

                                // Texto menor
                                ctx.fillStyle = '#6366f1';
                                ctx.font = '600 10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                                ctx.fillText(text, point.x, point.y - 7);
                            });
                        }
                    }
                }
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>