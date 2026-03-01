<?php require_once '../includes/site_header.php'; ?>

<section class="py-5 bg-light position-relative overflow-hidden">
    <!-- Gradient Background -->
    <div
        style="position: absolute; top: -50%; right: -20%; width: 80%; height: 200%; background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, rgba(255,255,255,0) 70%); z-index: 1;">
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-3">Gestão
                    Estratégica & IA</span>
                <h1 class="display-3 fw-bold mb-4 text-dark" style="letter-spacing: -1px;">Transforme dados soltos em
                    <span class="text-primary">decisões inteligentes</span></h1>
                <p class="lead text-secondary mb-4" style="line-height: 1.8;">
                    Diga adeus às planilhas complexas. Obtenha um diagnóstico 360º da sua clínica, defina OKRs claros e
                    acompanhe a produtividade da equipe em tempo real.
                </p>
                <div class="d-flex gap-3">
                    <a href="../../cadastro.php"
                        class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold hover-scale">
                        Começar Diagnóstico Grátis
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <!-- Image Wrapper with nice shadow and tilt -->
                <div class="position-relative">
                    <div class="bg-primary position-absolute rounded-circle opacity-25"
                        style="width: 300px; height: 300px; filter: blur(80px); top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    </div>
                    <img src="../assets/img/clinic_dashboard_hero.png" alt="Painel de Gestão Clínica v5"
                        class="img-fluid rounded-4 shadow-lg position-relative border border-white border-5"
                        style="transform: perspective(1000px) rotateY(-5deg) rotateX(2deg); transition: transform 0.3s ease;">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold">O tripé da clínica organizada</h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-light p-4">
                    <h4 class="mb-3 text-primary"><i class="bi bi-activity me-2"></i> Diagnóstico 360º</h4>
                    <p class="small text-secondary">Descubra exatamente onde sua clínica está falhando. Receba uma nota
                        de maturidade baseada em 5 pilares fundamentais.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-light p-4">
                    <h4 class="mb-3 text-danger"><i class="bi bi-bullseye me-2"></i> Metas & OKRs</h4>
                    <p class="small text-secondary">Defina objetivos claros (ex: Faturar R$ 100k) e quebre em resultados
                        chave mensuráveis para toda a equipe.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-light p-4">
                    <h4 class="mb-3 text-success"><i class="bi bi-kanban me-2"></i> Quadro de Tarefas</h4>
                    <p class="small text-secondary">Organize a rotina com um Kanban visual (A Fazer, Fazendo, Feito).
                        Delegue tarefas e nunca mais perca prazos.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/site_footer.php'; ?>