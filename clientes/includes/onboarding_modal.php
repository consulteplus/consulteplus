<!-- Onboarding Modal Wizard -->
<!-- Estilos Inline para garantir isolamento e simplicidade -->
<style>
    /* Wizard Steps Progress */
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
    }

    .step-indicator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
        transform: translateY(-50%);
    }

    .step-dot {
        width: 10px;
        height: 10px;
        background: #e9ecef;
        border-radius: 50%;
        z-index: 2;
        transition: all 0.3s;
    }

    .step-dot.active {
        background: #28a745;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    }

    /* Card Selectable */
    .card-select {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid #e9ecef;
    }

    .card-select:hover {
        border-color: #28a745;
        background-color: #f8fff9;
    }

    .card-select.selected {
        border-color: #28a745;
        background-color: #e6ffec;
        font-weight: bold;
    }

    /* Welcome Image Animation */
    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .welcome-img {
        animation: float 3s ease-in-out infinite;
    }
</style>

<div class="modal fade" id="modalOnboarding" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <!-- Header Minimalista -->
            <div class="modal-header border-0 pb-0 justify-content-center pt-4">
                <div class="step-indicator w-50 d-none" id="onboardingSteps">
                    <div class="step-dot active" data-step="1"></div>
                    <div class="step-dot" data-step="2"></div>
                    <div class="step-dot" data-step="3"></div>
                    <div class="step-dot" data-step="4"></div>
                </div>
            </div>

            <div class="modal-body p-5">

                <!-- STEP 0: WELCOME -->
                <div class="step-content text-center" id="step0">
                    <div class="mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center welcome-img"
                            style="width: 100px; height: 100px;">
                            <span style="font-size: 3rem;">🎉</span>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-3">Seja muito bem-vindo(a)!</h2>
                    <p class="text-muted fs-5 mb-5 px-5">
                        Que ótimo ter você por aqui! Para personalizarmos sua experiência no sistema,
                        gostaríamos de te conhecer um pouquinho melhor. Prometemos que é rápido!
                    </p>
                    <button class="btn btn-success btn-lg px-5 rounded-pill fw-bold shadow-sm"
                        onclick="onboardingNextStep(1)">
                        COMEÇAR AGORA <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>

                <!-- STEP 1: TEMPO DE EMPRESA -->
                <div class="step-content d-none" id="step1">
                    <h3 class="fw-bold text-center mb-4">Há quanto tempo a empresa existe?</h3>
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-8">
                            <div class="list-group">
                                <label
                                    class="list-group-item list-group-item-action p-3 rounded mb-2 border hover-shadow card-select"
                                    onclick="selectRadio(this)">
                                    <input class="form-check-input me-3 d-none" type="radio" name="tempo_existencia"
                                        value="Nova / Em abertura">
                                    <span class="fs-5">🚀 Estamos abrindo agora</span>
                                </label>
                                <label
                                    class="list-group-item list-group-item-action p-3 rounded mb-2 border hover-shadow card-select"
                                    onclick="selectRadio(this)">
                                    <input class="form-check-input me-3 d-none" type="radio" name="tempo_existencia"
                                        value="Menos de 1 ano">
                                    <span class="fs-5">👶 Menos de 1 ano</span>
                                </label>
                                <label
                                    class="list-group-item list-group-item-action p-3 rounded mb-2 border hover-shadow card-select"
                                    onclick="selectRadio(this)">
                                    <input class="form-check-input me-3 d-none" type="radio" name="tempo_existencia"
                                        value="1 a 5 anos">
                                    <span class="fs-5">📈 Entre 1 e 5 anos</span>
                                </label>
                                <label
                                    class="list-group-item list-group-item-action p-3 rounded mb-2 border hover-shadow card-select"
                                    onclick="selectRadio(this)">
                                    <input class="form-check-input me-3 d-none" type="radio" name="tempo_existencia"
                                        value="Mais de 5 anos">
                                    <span class="fs-5">🏛️ Mais de 5 anos</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: GESTÃO ATUAL (Mantido, apenas visualmente revisado se necessário) -->
                <div class="step-content d-none" id="step2">
                    <h3 class="fw-bold text-center mb-4">Como você faz a gestão hoje?</h3>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-4 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'gestao', 'Papel / Agenda Google')">
                                <i class="bi bi-journal-bookmark fs-1 text-warning mb-3 d-block"></i>
                                <h5 class="fw-bold">Papel ou Agenda Comum</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'gestao', 'Planilhas Excel')">
                                <i class="bi bi-file-earmark-spreadsheet fs-1 text-success mb-3 d-block"></i>
                                <h5 class="fw-bold">Planilhas Excel</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'gestao', 'Outro Sistema')">
                                <i class="bi bi-laptop fs-1 text-primary mb-3 d-block"></i>
                                <h5 class="fw-bold">Outro Sistema</h5>
                                <input type="text" class="form-control mt-2 d-none" id="outroSistemaInput"
                                    placeholder="Qual sistema?" onclick="event.stopPropagation()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'gestao', 'Não faço gestão')">
                                <i class="bi bi-x-circle fs-1 text-danger mb-3 d-block"></i>
                                <h5 class="fw-bold">Não faço gestão ainda</h5>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="gestaoSelecionada">
                </div>

                <!-- STEP 3: TAMANHO EQUIPE -->
                <div class="step-content d-none" id="step3">
                    <h3 class="fw-bold text-center mb-2">Qual o tamanho da sua equipe?</h3>
                    <p class="text-center text-muted mb-4">Considere todos os colaboradores.</p>

                    <div class="row g-3 justify-content-center">
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'equipe', '1')">
                                <span class="fs-1">👤</span>
                                <h6 class="mt-2">Eu-quipe (1)</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'equipe', '2-5')">
                                <span class="fs-1">👥</span>
                                <h6 class="mt-2">2 a 5 pessoas</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'equipe', '6-10')">
                                <span class="fs-1">🏢</span>
                                <h6 class="mt-2">6 a 10 pessoas</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100 card-select"
                                onclick="selectCard(this, 'equipe', '10+')">
                                <span class="fs-1">🏥</span>
                                <h6 class="mt-2">10+ pessoas</h6>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="equipeSelecionada">
                </div>

                <!-- STEP 4: SEGMENTO DE ATUAÇÃO -->
                <div class="step-content d-none" id="step4">
                    <h3 class="fw-bold text-center mb-4">Qual o segmento do seu negócio?</h3>
                    <div class="row g-2 justify-content-center">
                        <?php
                        $areas = [
                            'Comércio / Varejo',
                            'Serviços',
                            'Indústria',
                            'Tecnologia',
                            'Saúde / Clínica',
                            'Educação',
                            'Alimentação',
                            'Consultoria',
                            'Outros'
                        ];
                        foreach ($areas as $area): ?>
                            <div class="col-md-6">
                                <label class="d-flex align-items-center p-3 border rounded card-select w-100"
                                    onclick="toggleCheckbox(this)">
                                    <input type="checkbox" class="form-check-input me-3 d-none" name="segmento"
                                        value="<?php echo $area; ?>">
                                    <span class="fw-bold">
                                        <?php echo $area; ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- NAVIGATION BUTTONS (Step 1+) -->
                <div class="d-flex justify-content-between mt-5 pt-3 border-top d-none" id="navButtons">
                    <button class="btn btn-outline-secondary" onclick="onboardingPrevStep()">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </button>
                    <button class="btn btn-success px-4 fw-bold" id="btnNext" onclick="onboardingNextStep()" disabled>
                        PRÓXIMA <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 0;
    const totalSteps = 4;
    const formData = {};

    document.addEventListener('DOMContentLoaded', function () {
        // Verificar se precisa abrir o modal
        checkOnboardingStatus();
    });

    function checkOnboardingStatus() {
        const BASE_URL = "<?php echo BASE_URL; ?>";
        console.log("Onboarding Check: Checking status at " + BASE_URL);

        fetch(BASE_URL + 'modules/onboarding/acoes.php?acao=check_status')
            .then(res => {
                if (!res.ok) throw new Error("HTTP " + res.status);
                return res.json();
            })
            .then(data => {
                console.log("Onboarding Check Result:", data);
                if (data.success && !data.onboarding_done) {
                    console.log("Onboarding needed! Opening modal...");
                    const modalEl = document.getElementById('modalOnboarding');
                    if (modalEl && window.bootstrap) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    } else {
                        console.error("Bootstrap or Modal Element missing!");
                    }
                } else {
                    console.log("Onboarding already done or skipped.");
                }
            })
            .catch(err => console.error('Erro ao verificar onboarding:', err));
    }

    function onboardingNextStep(targetStep) {
        // Validar passo atual antes de ir
        if (!targetStep) targetStep = currentStep + 1;

        // Coletar dados do passo atual
        if (currentStep === 1) {
            const el = document.querySelector('input[name="tempo_existencia"]:checked');
            if (!el) return; // Não deve acontecer pois botão fica disabled
            formData.tempo_existencia = el.value;
        }
        if (currentStep === 4) {
            // Final Step - Save
            salvarOnboarding();
            return;
        }

        // Hide Checkboxes/Inputs in Step 2 if specific logic needed
        if (currentStep === 2) {
            const sysInput = document.getElementById('outroSistemaInput');
            if (document.getElementById('gestaoSelecionada').value === 'Outro Sistema') {
                formData.nome_sistema_anterior = sysInput.value;
            }
            formData.metodo_gestao_anterior = document.getElementById('gestaoSelecionada').value;
        }

        if (currentStep === 3) {
            formData.tamanho_equipe = document.getElementById('equipeSelecionada').value;
        }

        // UI Transition
        // Check if elements exist (safety)
        const currEl = document.getElementById(`step${currentStep}`);
        const nextEl = document.getElementById(`step${targetStep}`);

        if (currEl) currEl.classList.add('d-none');
        if (nextEl) nextEl.classList.remove('d-none');

        // Update Dots
        if (targetStep > 0) {
            document.getElementById('onboardingSteps').classList.remove('d-none');
            document.getElementById('navButtons').classList.remove('d-none');
            document.querySelectorAll('.step-dot').forEach((dot, idx) => {
                dot.classList.toggle('active', idx < targetStep);
            });
        }

        // Button State Reset
        if (targetStep === 4) {
            document.getElementById('btnNext').innerHTML = 'FINALIZAR <i class="bi bi-check-lg ms-1"></i>';
            document.getElementById('btnNext').disabled = false;
        } else {
            document.getElementById('btnNext').innerHTML = 'PRÓXIMA <i class="bi bi-arrow-right ms-1"></i>';
            document.getElementById('btnNext').disabled = true;
        }

        currentStep = targetStep;
    }

    function onboardingPrevStep() {
        if (currentStep <= 0) return;
        const targetStep = currentStep - 1;
        document.getElementById(`step${currentStep}`).classList.add('d-none');
        document.getElementById(`step${targetStep}`).classList.remove('d-none');

        if (targetStep === 0) {
            document.getElementById('onboardingSteps').classList.add('d-none');
            document.getElementById('navButtons').classList.add('d-none');
        }

        document.querySelectorAll('.step-dot').forEach((dot, idx) => {
            dot.classList.toggle('active', idx < targetStep);
        });

        currentStep = targetStep;
        document.getElementById('btnNext').disabled = false;
    }

    // Helpers UI
    function selectRadio(element) {
        // Step 1
        const radio = element.querySelector('input[type="radio"]');
        radio.checked = true;

        // Visual
        element.closest('.list-group').querySelectorAll('.card-select').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        // Enable Next
        document.getElementById('btnNext').disabled = false;
    }

    function selectCard(element, type, value) {
        // Step 2 & 3
        const container = element.closest('.row');
        container.querySelectorAll('.card-select').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        if (type === 'gestao') {
            document.getElementById('gestaoSelecionada').value = value;
            const input = document.getElementById('outroSistemaInput');
            if (value === 'Outro Sistema') {
                input.classList.remove('d-none');
                input.focus();
            } else {
                input.classList.add('d-none');
            }
            document.getElementById('btnNext').disabled = false;
        }

        if (type === 'equipe') {
            document.getElementById('equipeSelecionada').value = value;
            document.getElementById('btnNext').disabled = false;
        }
    }

    function toggleCheckbox(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        element.classList.toggle('selected', checkbox.checked);
    }

    function salvarOnboarding() {
        const checkboxes = document.querySelectorAll('input[name="segmento"]:checked');
        const segmentos = Array.from(checkboxes).map(cb => cb.value);

        const payload = {
            ...formData,
            segmento: segmentos,
            acao: 'salvar_respostas'
        };

        const BASE_URL = "<?php echo BASE_URL; ?>";
        fetch(BASE_URL + 'modules/onboarding/acoes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Successo! 
                    const modalEl = document.getElementById('modalOnboarding');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    alert('Tudo pronto! Perfil configurado com sucesso.');
                } else {
                    alert('Erro ao salvar: ' + data.error);
                }
            })
            .catch(err => alert('Erro de conexão.'));
    }
</script>