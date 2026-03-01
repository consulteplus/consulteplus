<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Force URL root
$baseUrl = '/site'; // Adjust based on deployment
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica v5 - Gestão Inteligente</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $baseUrl; ?>/assets/css/site.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top site-navbar">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $baseUrl; ?>/index.php">
                <div class="bg-primary text-white rounded p-1 me-2"
                    style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <span class="fw-bold text-primary">Clínica v5</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Inner -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

                    <!-- Mega Menu Trigger -->
                    <li class="nav-item dropdown has-megamenu">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Funcionalidades</a>
                        <div class="dropdown-menu megamenu fade-down">
                            <div class="container">
                                <div class="row g-4">
                                    <!-- Col 1 -->
                                    <div class="col-lg-4 col-md-6">
                                        <a href="<?php echo $baseUrl; ?>/funcionalidades/agenda.php"
                                            class="text-decoration-none">
                                            <div class="megamenu-item">
                                                <div class="megamenu-icon"><i class="bi bi-calendar-check"></i></div>
                                                <div>
                                                    <span class="megamenu-title">Agenda Online</span>
                                                    <p class="megamenu-desc">Organize atendimentos sem conflito e com
                                                        confirmação automática.</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <!-- Col 2 -->
                                    <div class="col-lg-4 col-md-6">
                                        <a href="<?php echo $baseUrl; ?>/funcionalidades/prontuario.php"
                                            class="text-decoration-none">
                                            <div class="megamenu-item">
                                                <div class="megamenu-icon"><i class="bi bi-file-medical"></i></div>
                                                <div>
                                                    <span class="megamenu-title">Prontuário Digital</span>
                                                    <p class="megamenu-desc">Histórico completo do paciente com
                                                        segurança e acesso rápido.</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <!-- Col 3 -->
                                    <div class="col-lg-4 col-md-6">
                                        <a href="<?php echo $baseUrl; ?>/funcionalidades/financeiro.php"
                                            class="text-decoration-none">
                                            <div class="megamenu-item">
                                                <div class="megamenu-icon"><i class="bi bi-cash-coin"></i></div>
                                                <div>
                                                    <span class="megamenu-title">Gestão Financeira</span>
                                                    <p class="megamenu-desc">Controle de fluxo de caixa, repasses e
                                                        relatórios.</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <!-- Col 4 (Gestão - Novo) -->
                                    <div class="col-lg-4 col-md-6">
                                        <a href="<?php echo $baseUrl; ?>/funcionalidades/gestao.php"
                                            class="text-decoration-none">
                                            <div class="megamenu-item">
                                                <div class="megamenu-icon"><i class="bi bi-kanban"></i></div>
                                                <div>
                                                    <span class="megamenu-title">Gestão Integrada</span>
                                                    <p class="megamenu-desc">Diagnóstico, OKRs e Kanban para escalar sua
                                                        clínica.</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <!-- Col 5 (Marketing) -->
                                    <div class="col-lg-4 col-md-6">
                                        <a href="<?php echo $baseUrl; ?>/funcionalidades/marketing.php"
                                            class="text-decoration-none">
                                            <div class="megamenu-item">
                                                <div class="megamenu-icon"><i class="bi bi-megaphone"></i></div>
                                                <div>
                                                    <span class="megamenu-title">Marketing e CRM</span>
                                                    <p class="megamenu-desc">Atraia mais pacientes e fidelize sua base.
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <!-- Col 6 (IA - Resized) -->
                                    <div class="col-lg-4 col-md-12 bg-light rounded p-3">
                                        <h6 class="mb-1 text-primary"><i class="bi bi-stars me-1"></i> Inteligência
                                            Artificial</h6>
                                        <p class="small mb-2 text-muted">Use IA para agilizar diagnósticos e
                                            comunicação.</p>
                                        <a href="<?php echo $baseUrl; ?>/funcionalidades.php"
                                            class="btn btn-sm btn-outline-primary w-100">Ver
                                            tudo <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/planos.php">Planos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?php echo $baseUrl; ?>/../login.php"
                        class="btn btn-outline-primary rounded-pill px-4">Entrar</a>
                    <a href="<?php echo $baseUrl; ?>/../cadastro.php" class="btn btn-accent rounded-pill px-4">Testar
                        Grátis</a>
                </div>
            </div>
        </div>
    </nav>
    <div style="height: 76px;"></div> <!-- Spacer for fixed navbar -->