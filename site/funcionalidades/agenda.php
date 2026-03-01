<?php require_once '../includes/site_header.php'; ?>

<!-- HERO AGENDA -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-primary mb-3">Agenda Inteligente</span>
                <h1 class="display-4 fw-bold mb-4">Acabe com as faltas e organize seu dia</h1>
                <p class="lead text-muted mb-4">
                    Uma agenda feita para dentistas. Confirmação automática, encaixes inteligentes e visualização
                    completa da produtividade da clínica.
                </p>
                <a href="../../cadastro.php"
                    class="btn btn-primary d-inline-flex align-items-center px-4 py-3 rounded-pill">
                    Testar Agenda Grátis <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="bg-white p-2 rounded shadow-lg rotate-md">
                    <img src="../assets/img/feature_agenda.png" alt="Agenda Visual" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RECURSOS -->
<section class="py-5">
    <div class="container my-5">
        <div class="row g-5">
            <div class="col-md-4 text-center">
                <div class="mb-3 text-primary"><i class="bi bi-whatsapp fs-1"></i></div>
                <h4>Confirmação via WhatsApp</h4>
                <p class="text-muted">O sistema envia mensagens automáticas lembrando o paciente 24h antes. Ele responde
                    e o status muda na agenda.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3 text-primary"><i class="bi bi-clock-history fs-1"></i></div>
                <h4>Lista de Espera</h4>
                <p class="text-muted">Surgiu uma desistência? O sistema avisa quem está na fila para preencher o horário
                    vago imediatamente.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3 text-primary"><i class="bi bi-globe fs-1"></i></div>
                <h4>Agendamento Online</h4>
                <p class="text-muted">Seus pacientes marcam consultas pelo link do seu site ou redes sociais, 24 horas
                    por dia.</p>
            </div>
        </div>
    </div>
</section>

<!-- PRINT/DEMO SECTION -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <h2 class="mb-4">Código de Cores e Status</h2>
                <p>Saiba batendo o olho quem confirmou, quem chegou, quem está em atendimento e quem já pagou.</p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2 d-flex align-items-center"><span class="badge bg-secondary me-2 p-2 rounded-circle">
                        </span> Agendado (Cinza)</li>
                    <li class="mb-2 d-flex align-items-center"><span class="badge bg-success me-2 p-2 rounded-circle">
                        </span> Confirmado (Verde)</li>
                    <li class="mb-2 d-flex align-items-center"><span class="badge bg-info me-2 p-2 rounded-circle">
                        </span> Em Atendimento (Azul)</li>
                    <li class="mb-2 d-flex align-items-center"><span class="badge bg-warning me-2 p-2 rounded-circle">
                        </span> Faltou (Laranja)</li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="bg-light p-5 rounded-4 text-center">
                    <i class="bi bi-palette fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Visualização intuitiva</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .rotate-md {
        transform: perspective(1000px) rotateY(-5deg) rotateX(2deg);
        transition: 0.5s;
    }

    .rotate-md:hover {
        transform: none;
    }
</style>

<?php require_once '../includes/site_footer.php'; ?>