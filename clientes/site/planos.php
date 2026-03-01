<?php require_once 'includes/site_header.php'; ?>

<!-- HEADER PLANOS -->
<section class="bg-primary text-white py-5 text-center position-relative overflow-hidden">
    <div class="container position-relative z-1">
        <h1 class="display-4 fw-bold mb-3">Escolha o plano ideal para sua fase</h1>
        <p class="lead opacity-75 mx-auto" style="max-width: 700px;">
            Sem contratos de fidelidade. Mude de plano quando quiser.
            Experimente grátis por 7 dias.
        </p>
    </div>
    <!-- Decorative Circle -->
    <div class="position-absolute bg-white opacity-10 rounded-circle"
        style="width: 500px; height: 500px; top: -250px; left: -100px;"></div>
</section>

<!-- TABELA DE PREÇOS -->
<section class="py-5" style="margin-top: -60px;">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <!-- PLANO START -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden pt-4">
                    <div class="card-body text-center">
                        <h4 class="fw-bold text-secondary mb-2">Essencial</h4>
                        <p class="text-muted small">Para quem está começando</p>
                        <div class="my-4">
                            <span class="h2 fw-bold">R$ 89</span><span class="text-muted">/mês</span>
                        </div>
                        <a href="../cadastro.php" class="btn btn-outline-primary rounded-pill px-4 w-100 mb-4">Começar
                            Grátis</a>
                        <hr class="text-secondary opacity-25">
                        <ul class="list-unstyled text-start small mt-4">
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> 1 Usuário (Dentista/Admin)
                            </li>
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> Agenda Online</li>
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> Prontuário Básico</li>
                            <li class="mb-3"><i class="bi bi-x-lg text-muted me-2 opacity-50"></i> <span
                                    class="text-decoration-line-through text-muted">Financeiro Completo</span></li>
                            <li class="mb-3"><i class="bi bi-x-lg text-muted me-2 opacity-50"></i> <span
                                    class="text-decoration-line-through text-muted">Marketing e CRM</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- PLANO PRO (DESTAQUE) -->
            <div class="col-lg-4 col-md-6">
                <div
                    class="card h-100 border border-primary shadow rounded-4 overflow-hidden position-relative pt-4 transform-scale">
                    <div class="position-absolute top-0 start-50 translate-middle-x bg-warning text-dark fw-bold px-3 py-1 rounded-bottom shadow-sm"
                        style="font-size: 0.8rem;">MAIS POPULAR</div>
                    <div class="card-body text-center bg-primary-subtle bg-opacity-10">
                        <h4 class="fw-bold text-primary mb-2">Profissional</h4>
                        <p class="text-muted small">Gestão completa para crescer</p>
                        <div class="my-4">
                            <span class="h2 fw-bold text-primary">R$ 159</span><span class="text-muted">/mês</span>
                        </div>
                        <a href="../cadastro.php" class="btn btn-primary rounded-pill px-4 w-100 mb-4 shadow-sm">Começar
                            Grátis</a>
                        <hr class="text-primary opacity-25">
                        <ul class="list-unstyled text-start small mt-4">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> <strong>Até 3
                                    Usuários</strong></li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Agenda com
                                Confirmação WhatsApp</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Prontuário
                                Completo + Odontograma</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Financeiro +
                                Boletos + NFS-e</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Disparo de
                                Campanhas Marketing</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- PLANO CLÍNICA -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden pt-4">
                    <div class="card-body text-center">
                        <h4 class="fw-bold text-secondary mb-2">Clínica Premium</h4>
                        <p class="text-muted small">Para grandes volumes</p>
                        <div class="my-4">
                            <span class="h2 fw-bold">R$ 299</span><span class="text-muted">/mês</span>
                        </div>
                        <a href="../cadastro.php" class="btn btn-outline-dark rounded-pill px-4 w-100 mb-4">Falar com
                            Consultor</a>
                        <hr class="text-secondary opacity-25">
                        <ul class="list-unstyled text-start small mt-4">
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> Usuários Ilimitados</li>
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> Tudo do plano Profissional
                            </li>
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> Gerente de Contas Dedicado
                            </li>
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> Treinamento de Equipe</li>
                            <li class="mb-3"><i class="bi bi-check-lg text-success me-2"></i> API para Integrações</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="text-center fw-bold mb-5">Perguntas Frequentes</h3>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq1">
                                Preciso cadastrar cartão de crédito para testar?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Não! O teste de 7 dias é totalmente gratuito e sem compromisso. Você só escolhe um plano
                                se decidir continuar.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq2">
                                Posso cancelar quando quiser?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sim, não temos fidelidade. Você pode cancelar sua assinatura a qualquer momento direto
                                pelo painel.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq3">
                                Meus dados estão seguros?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutamente. Utilizamos criptografia de ponta a ponta e backups diários automáticos.
                                Seus dados são seus e de mais ninguém.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .transform-scale {
        transition: transform 0.3s;
        z-index: 10;
    }

    .transform-scale:hover {
        transform: scale(1.03);
    }
</style>

<?php require_once 'includes/site_footer.php'; ?>