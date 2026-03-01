<?php
$pageTitle = "Dashboard Administrativo";
require_once __DIR__ . '/../header.php';
checkPermission(['superadmin']);

// Stats Mockup (or Real Query)

// 1. Total Assets
$totalEmpresas = $conn->query("SELECT COUNT(*) as c FROM empresas")->fetch_assoc()['c'];
$newEmpresas = $conn->query("SELECT COUNT(*) as c FROM empresas WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['c'];

// 2. Total Users
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$newUsers = $conn->query("SELECT COUNT(*) as c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['c'];

// 3. Total Diagnostics
$totalDiags = $conn->query("SELECT COUNT(*) as c FROM gestao_diagnostico_resultados")->fetch_assoc()['c'];
$newDiags = $conn->query("SELECT COUNT(*) as c FROM gestao_diagnostico_resultados WHERE data_realizacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['c'];

// 4. Total Models
$totalModelos = $conn->query("SELECT COUNT(*) as c FROM gestao_diagnostico_modelos")->fetch_assoc()['c'];
$newModelos = $conn->query("SELECT COUNT(*) as c FROM gestao_diagnostico_modelos WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['c'];

// 5. Revenue Chart Data (Based on New Contracts/Subscriptions)
$months = [];
$revenues = [];

// Get last 6 months
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $monthLabel = date('M/Y', strtotime("-$i months"));

    // Sum subscription values created in this month
    // Note: Assuming 'valor' is the monthly recurring value.
    $sqlRevenue = "SELECT SUM(valor) as total 
                   FROM financeiro_assinaturas 
                   WHERE DATE_FORMAT(created_at, '%Y-%m') = '$date' 
                   AND status != 'CANCELLED'";

    $resRevenue = $conn->query($sqlRevenue);
    $val = $resRevenue ? $resRevenue->fetch_assoc()['total'] : 0;

    $months[] = $monthLabel;
    $revenues[] = $val ? floatval($val) : 0;
}

?>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Linha de Estatísticas (Stats Row) -->
    <div class="row">
        <!-- Empresas -->
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="bi bi-buildings"></i></span>
                        </div>
                        <?php if ($newEmpresas > 0): ?>
                            <span class="badge bg-label-success rounded-pill">+<?php echo $newEmpresas; ?> essa
                                semana</span>
                        <?php endif; ?>
                    </div>
                    <span class="fw-semibold d-block mb-1">Empresas</span>
                    <h3 class="card-title mb-2"><?php echo $totalEmpresas; ?></h3>
                </div>
            </div>
        </div>

        <!-- Usuários -->
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info"><i class="bi bi-people"></i></span>
                        </div>
                        <?php if ($newUsers > 0): ?>
                            <span class="badge bg-label-info rounded-pill">+<?php echo $newUsers; ?> essa semana</span>
                        <?php endif; ?>
                    </div>
                    <span class="fw-semibold d-block mb-1">Usuários</span>
                    <h3 class="card-title text-nowrap mb-1"><?php echo $totalUsers; ?></h3>
                </div>
            </div>
        </div>

        <!-- Diagnósticos Feitos -->
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bi bi-ui-checks"></i></span>
                        </div>
                        <?php if ($newDiags > 0): ?>
                            <span class="badge bg-label-primary rounded-pill">+<?php echo $newDiags; ?> essa semana</span>
                        <?php endif; ?>
                    </div>
                    <span class="fw-semibold d-block mb-1">Diag. Feitos</span>
                    <h3 class="card-title text-nowrap mb-1"><?php echo $totalDiags; ?></h3>
                </div>
            </div>
        </div>

        <!-- Modelos Ativos -->
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bi bi-list-task"></i></span>
                        </div>
                        <?php if ($newModelos > 0): ?>
                            <span class="badge bg-label-warning rounded-pill">+<?php echo $newModelos; ?> essa semana</span>
                        <?php endif; ?>
                    </div>
                    <span class="fw-semibold d-block mb-1">Modelos</span>
                    <h3 class="card-title mb-1"><?php echo $totalModelos; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="card-title m-0 fw-bold text-primary">
                        <i class="bi bi-graph-up-arrow me-2"></i>Evolução de Contratos (Assinaturas)
                    </h5>
                    <small class="text-muted">Valor total de novas assinaturas nos últimos 6 meses</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Linha Principal (Welcome + Feed) -->
    <div class="row">
        <!-- Welcome Card (Lado Esquerdo) -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="me-3">
                            <h5 class="card-title text-primary mb-2">Bem-vindo! 🎉</h5>
                            <p class="mb-0 text-muted">
                                Visão geral e controle do sistema.
                            </p>
                        </div>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bi bi-shield-lock-fill"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.7rem;">Acesso
                            Rápido</small>
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>admin/empresas"
                                class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center">
                                <i class="bi bi-buildings me-2"></i> Gerenciar Empresas
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/diagnosticos"
                                class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center">
                                <i class="bi bi-list-check me-2"></i> Diagnósticos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feed de Atividades (Lado Direito) -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="card-title m-0 fs-6">Últimas Atividades</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php
                        // Feed Unificado (UNION)
                        $sql_feed = "
                            (SELECT 
                                'user' as type, 
                                u.created_at as time, 
                                CONVERT(u.nome USING utf8mb4) as title, 
                                CONVERT(e.nome USING utf8mb4) as subtitle,
                                'bi-person-plus' as icon,
                                'success' as color
                            FROM users u
                            JOIN empresas e ON u.company_id = e.id
                            WHERE u.tipo = 'cliente')
                            
                            UNION
                            
                            (SELECT 
                                'diag' as type, 
                                r.data_realizacao as time, 
                                CONVERT(COALESCE(m.titulo, 'Diagnóstico Padrão') USING utf8mb4) as title, 
                                CONVERT(e.nome USING utf8mb4) as subtitle,
                                'bi-activity' as icon,
                                'primary' as color
                            FROM gestao_diagnostico_resultados r
                            JOIN empresas e ON r.company_id = e.id
                            LEFT JOIN gestao_diagnostico_modelos m ON r.modelo_id = m.id
                            WHERE r.data_realizacao IS NOT NULL)

                            ORDER BY time DESC 
                            LIMIT 5
                        ";

                        $feed = $conn->query($sql_feed);
                        $has_activity = false;

                        if ($feed && $feed->num_rows > 0) {
                            while ($item = $feed->fetch_assoc()) {
                                $has_activity = true;
                                // Format Date: "Hoje às 14:00" vs "12/05 14:00"
                                $ts = strtotime($item['time']);
                                $dateStr = date('d/m H:i', $ts);
                                if (date('Y-m-d') == date('Y-m-d', $ts)) {
                                    $dateStr = 'Hoje, ' . date('H:i', $ts);
                                }

                                echo '
                                <li class="list-group-item px-3 py-2 border-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-' . $item['color'] . ' bg-opacity-10 rounded-circle me-3" style="width: 38px; height: 38px;">
                                            <i class="bi ' . $item['icon'] . ' text-' . $item['color'] . ' fs-6"></i>
                                        </div>
                                        <div class="flex-grow-1" style="min-width:0;">
                                            <div class="d-flex justify-content-between align-items-baseline mb-0">
                                                <h6 class="mb-0 text-dark fw-bold text-truncate" style="font-size: 0.9rem;">' . htmlspecialchars($item['title']) . '</h6>
                                                <small class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">' . $dateStr . '</small>
                                            </div>
                                            <div class="text-muted small text-truncate" style="font-size: 0.8rem; opacity: 0.8;">' . htmlspecialchars($item['subtitle']) . '</div>
                                        </div>
                                    </div>
                                </li>';
                            }
                        } else {
                            echo '
                            <div class="text-center py-5">
                                <div class="mb-3 text-muted opacity-50">
                                    <i class="bi bi-inbox fs-1"></i>
                                </div>
                                <h6 class="text-muted small fw-bold">Nenhuma atividade recente</h6>
                            </div>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Gradient fill
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Faturamento de Novos Contratos (R$)',
                    data: <?php echo json_encode($revenues); ?>,
                    backgroundColor: gradient,
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            callback: function (value, index, values) {
                                return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumSignificantDigits: 3 }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>