<?php require_once 'includes/site_header.php'; ?>

<!-- HEADER -->
<section class="bg-light py-5 text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Funcionalidades Poderosas</h1>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">
            Tudo o que sua clínica precisa para crescer de forma organizada e eficiente.
            Conheça cada detalhe do nosso ecossistema.
        </p>
    </div>
</section>

<!-- GRID DE FUNCIONALIDADES -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Cards grandes -->
            <div class="col-lg-6">
                <a href="funcionalidades/agenda.php" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm hover-up p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary-subtle text-primary p-3 rounded-4 me-3">
                                <i class="bi bi-calendar-check fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1 feature-title">Agenda Inteligente</h3>
                                <p class="text-muted mb-0">Evite faltas e organize horários</p>
                            </div>
                        </div>
                        <p class="text-secondary">Confirmações automáticas, lista de espera, agendamento online e
                            visualização flexível (dia, semana, mês).</p>
                        <span class="text-primary fw-bold mt-auto d-inline-block">Saiba mais <i
                                class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>

            <div class="col-lg-6">
                <a href="funcionalidades/prontuario.php" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm hover-up p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success-subtle text-success p-3 rounded-4 me-3">
                                <i class="bi bi-file-medical fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1 feature-title">Prontuário Digital</h3>
                                <p class="text-muted mb-0">Segurança e praticidade clínica</p>
                            </div>
                        </div>
                        <p class="text-secondary">Anamnese personalizável, odontograma, prescrições digitais e histórico
                            completo do paciente na nuvem.</p>
                        <span class="text-success fw-bold mt-auto d-inline-block">Saiba mais <i
                                class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>

            <div class="col-lg-6">
                <a href="funcionalidades/financeiro.php" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm hover-up p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning-subtle text-warning p-3 rounded-4 me-3">
                                <i class="bi bi-cash-stack fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1 feature-title">Gestão Financeira</h3>
                                <p class="text-muted mb-0">Controle total do caixa</p>
                            </div>
                        </div>
                        <p class="text-secondary">Fluxo de caixa, DRE automática, repasse de profissionais, contas a
                            pagar/receber e emissão de boletos.</p>
                        <span class="text-warning fw-bold mt-auto d-inline-block">Saiba mais <i
                                class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>

            <div class="col-lg-6">
                <a href="funcionalidades/marketing.php" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm hover-up p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-info-subtle text-info p-3 rounded-4 me-3">
                                <i class="bi bi-megaphone fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1 feature-title">Marketing e CRM</h3>
                                <p class="text-muted mb-0">Venda mais e fidelize</p>
                            </div>
                        </div>
                        <p class="text-secondary">Campanhas de SMS/WhatsApp, funil de vendas (pipeline), automações de
                            pós-venda e pesquisa de satisfação.</p>
                        <span class="text-info fw-bold mt-auto d-inline-block">Saiba mais <i
                                class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-5 bg-dark text-white text-center">
    <div class="container my-4">
        <h2>Pronto para ver na prática?</h2>
        <p class="lead opacity-75 mb-4">Crie sua conta gratuita agora e explore todas as funcionalidades.</p>
        <a href="../cadastro.php" class="btn btn-accent px-5 py-3 fw-bold">Começar Teste Grátis</a>
    </div>
</section>

<style>
    .hover-up {
        transition: transform 0.2s;
        cursor: pointer;
    }

    .hover-up:hover {
        transform: translateY(-5px);
    }

    .feature-title {
        color: var(--text-dark);
    }
</style>

<?php require_once 'includes/site_footer.php'; ?>