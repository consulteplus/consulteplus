<?php
$pageTitle = "Gestão Empresarial";
require_once __DIR__ . '/../../includes/header.php';

checkPermission(['admin', 'cliente']);

if (!isModuleEnabled('gestao')) {
    redirect('dashboard?modulo_bloqueado=gestao');
    exit;
}
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><i class="bi bi-briefcase"></i> Gestão Estratégica</h1>
        <p class="text-muted mb-0">Ferramentas para administração, planejamento e diagnóstico do negócio</p>
    </div>
</div>

<div class="row g-4">
    <!-- Bloco 1: Operacional (Tarefas) -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <span
                        class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle"
                        style="width: 64px; height: 64px;">
                        <i class="bi bi-kanban display-6"></i>
                    </span>
                </div>
                <h4 class="card-title">Tarefas & Atividades</h4>
                <p class="card-text text-muted small">Gerencie demandas da equipe, pendências e acompanhe o fluxo de
                    trabalho com quadro Kanban.</p>
                <a href="<?php echo BASE_URL; ?>gestao/tarefas" class="btn btn-outline-primary w-100 mt-3">
                    <i class="bi bi-arrow-right-circle me-1"></i> Acessar
                </a>
            </div>
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <small class="text-muted"><i class="bi bi-check-circle text-success me-1"></i>Organizacional</small>
            </div>
        </div>
    </div>

    <!-- Bloco 2: Diagnóstico (SWOT) -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <span
                        class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle"
                        style="width: 64px; height: 64px;">
                        <i class="bi bi-activity display-6"></i>
                    </span>
                </div>
                <h4 class="card-title">Diagnóstico Empresarial</h4>
                <p class="card-text text-muted small">Realize análises SWOT (Forças, Fraquezas) e PDCA para entender a
                    saúde do negócio.</p>
                <a href="<?php echo BASE_URL; ?>gestao/diagnostico" class="btn btn-outline-success w-100 mt-3">
                    <i class="bi bi-arrow-right-circle me-1"></i> Acessar
                </a>
            </div>
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <small class="text-muted"><i class="bi bi-graph-up-arrow text-success me-1"></i>Estratégico</small>
            </div>
        </div>
    </div>

    <!-- Bloco 3: Planejamento (Metas/OKRs) -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <span
                        class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-circle"
                        style="width: 64px; height: 64px;">
                        <i class="bi bi-bullseye display-6"></i>
                    </span>
                </div>
                <h4 class="card-title">Planejamento & Metas</h4>
                <p class="card-text text-muted small">Defina OKRs, metas anuais e acompanhe o progresso dos planos de
                    ação.</p>
                <a href="<?php echo BASE_URL; ?>gestao/planejamento" class="btn btn-outline-info w-100 mt-3">
                    <i class="bi bi-arrow-right-circle me-1"></i> Acessar
                </a>
            </div>
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <small class="text-muted"><i class="bi bi-signpost-2 text-success me-1"></i>Diretivo</small>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>