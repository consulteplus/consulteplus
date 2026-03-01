<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Novo Diagnóstico";
require_once __DIR__ . '/../../../includes/header.php';
// checkPermission(['admin', 'cliente']); 

// Perguntas hardcoded para protótipo
// Map de configurações de UI (Icones, Cores, Passos)
// Map de configurações de UI (Icones, Cores, Passos)
$configSecoes = [
    'perfil' => [
        'titulo' => 'Perfil e Porte',
        'icon' => 'bi-building',
        'color' => 'primary',
        'step' => 1
    ],
    'modelo' => [
        'titulo' => 'Modelo de Negócio',
        'icon' => 'bi-shop',
        'color' => 'info',
        'step' => 2
    ],
    'financeiro' => [
        'titulo' => 'Gestão Financeira',
        'icon' => 'bi-currency-dollar',
        'color' => 'success',
        'step' => 3
    ],
    'operacional' => [
        'titulo' => 'Eficiência Operacional',
        'icon' => 'bi-gear-wide-connected',
        'color' => 'primary',
        'step' => 4
    ],
    'aquisicao' => [
        'titulo' => 'Aquisição e Vendas',
        'icon' => 'bi-megaphone',
        'color' => 'danger',
        'step' => 5
    ],
    'jornada' => [
        'titulo' => 'Jornada e Experiência',
        'icon' => 'bi-heart',
        'color' => 'danger',
        'step' => 6
    ],
    'equipe' => [
        'titulo' => 'Pessoas e Liderança',
        'icon' => 'bi-people',
        'color' => 'primary',
        'step' => 7
    ],
    'momento' => [
        'titulo' => 'Momento Atual',
        'icon' => 'bi-hourglass-split',
        'color' => 'warning',
        'step' => 8
    ]
];

// Buscar perguntas do banco
// Buscar perguntas do banco
$perguntas = [];
$sql = "SELECT * FROM gestao_diagnostico_perguntas ORDER BY ordem ASC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $secao = $row['secao'];
        if (!isset($configSecoes[$secao])) continue;
        if (!isset($perguntas[$secao])) {
            $perguntas[$secao] = $configSecoes[$secao];
            $perguntas[$secao]['questoes'] = [];
        }
        $perguntas[$secao]['questoes'][$row['id']] = [
            'texto' => $row['texto_pergunta'],
            'tipo' => $row['tipo'],
            'opcoes' => $row['opcoes']
        ];
    }
}

if (empty($perguntas)) {
    echo "<div class='alert alert-warning'>Nenhuma pergunta encontrada. Execute o seed.</div>";
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Avaliação de Maturidade & Estratégia</h2>
                <p class="text-muted">Passo <span id="current-step-display">1</span> de 8</p>

                <div class="progress" style="height: 10px;">
                    <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>

            <form action="<?php echo BASE_URL; ?>gestao/diagnostico/resultado" method="POST" id="diagnosisForm" novalidate>

                <?php 
                $stepCount = 1;
                foreach ($configSecoes as $key => $secaoConfig):
                    if (!isset($perguntas[$key])) continue;
                    $secao = $perguntas[$key];
                ?>
                    <div class="step-section" id="step-<?php echo $stepCount; ?>" style="<?php echo $stepCount > 1 ? 'display: none;' : ''; ?>">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-<?php echo $secaoConfig['color']; ?> text-white d-flex align-items-center py-3">
                                <i class="bi <?php echo $secaoConfig['icon']; ?> fs-3 me-3"></i>
                                <div>
                                    <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">Etapa <?php echo $stepCount; ?></small>
                                    <h4 class="mb-0 text-white"><?php echo $secaoConfig['titulo']; ?></h4>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <?php foreach ($secao['questoes'] as $id => $qData): 
                                    $texto = $qData['texto'];
                                    $tipo = $qData['tipo'];
                                    $opcoes = json_decode($qData['opcoes'] ?? '[]', true);
                                ?>
                                    <div class="mb-5 question-block">
                                        <label class="form-label fw-bold mb-3 fs-5 text-dark"><?php echo $texto; ?></label>


                                        <?php if ($tipo === 'numero'): ?>
                                            <!-- INPUT NUMÉRICO -->
                                            <div class="bg-light p-3 rounded-3">
                                                <input type="number" class="form-control form-control-lg" 
                                                       name="q<?php echo $id; ?>" placeholder="Digite o número..." required>
                                            </div>

                                        <?php elseif ($tipo === 'multipla'): ?>
                                            <!-- MÚLTIPLA ESCOLHA (CHECKBOX) -->
                                            <div class="d-flex flex-column gap-3">
                                                <?php foreach ($opcoes as $idx => $opt): ?>
                                                <div class="custom-option p-3 rounded-3 border position-relative d-flex align-items-center shadow-sm" style="background: #fff; cursor: pointer;">
                                                    <input class="form-check-input me-3 fs-5" type="checkbox" 
                                                           name="q<?php echo $id; ?>[]" 
                                                           id="q<?php echo $id; ?>_opt<?php echo $idx; ?>" 
                                                           value="<?php echo htmlspecialchars($opt); ?>" style="margin-top:0;">
                                                    <label class="form-check-label w-100 text-dark fs-6 stretched-link" for="q<?php echo $id; ?>_opt<?php echo $idx; ?>" style="cursor: pointer;">
                                                        <?php echo htmlspecialchars($opt); ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                                <small class="text-muted ms-1"><i class="bi bi-info-circle"></i> Selecione todas as opções que se aplicam.</small>
                                            </div>

                                        <?php else: // Seleção ou Escala ?>
                                            <!-- RADIO (ÚNICA ESCOLHA) -->
                                            <div class="d-flex flex-column gap-3">
                                                <?php foreach ($opcoes as $idx => $opt): ?>
                                                <div class="custom-option p-3 rounded-3 border position-relative d-flex align-items-center shadow-sm" style="background: #fff; cursor: pointer;">
                                                    <input class="form-check-input me-3 fs-5" type="radio" 
                                                           name="q<?php echo $id; ?>" 
                                                           id="q<?php echo $id; ?>_opt<?php echo $idx; ?>" 
                                                           value="<?php echo htmlspecialchars($opt); ?>" required style="margin-top:0;">
                                                    <label class="form-check-label w-100 text-dark fs-6 stretched-link" for="q<?php echo $id; ?>_opt<?php echo $idx; ?>" style="cursor: pointer;">
                                                        <?php echo htmlspecialchars($opt); ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <?php if ($id != array_key_last($secao['questoes'])): ?>
                                        <hr class="text-muted opacity-10 my-4">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <?php if ($stepCount > 1): ?>
                                <button type="button" class="btn btn-outline-secondary btn-lg px-4"
                                    onclick="prevStep(<?php echo $stepCount; ?>)">
                                    <i class="bi bi-arrow-left me-2"></i> Anterior
                                </button>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>gestao/diagnostico"
                                    class="btn btn-outline-secondary btn-lg px-4">Cancelar</a>
                            <?php endif; ?>

                            <?php if ($stepCount < 8): ?>
                                <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm"
                                    onclick="nextStep(<?php echo $stepCount; ?>)">
                                    Próximo <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-success btn-lg px-5 shadow fw-bold">
                                    Finalizar Diagnóstico <i class="bi bi-check-lg ms-2"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $stepCount++; ?>
                <?php endforeach; ?>

            </form>
        </div>
    </div>
</div>

<style>
    .custom-option {
        transition: all 0.2s ease;
    }
    .custom-option:hover {
        background-color: #f8f9fa !important;
        border-color: var(--bs-primary) !important;
        transform: translateY(-2px);
    }
    /* Quando Selecionado (Modern Browsers) */
    .custom-option:has(input:checked) {
        border-color: var(--bs-primary) !important;
        background-color: #e7f1ff !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
    }
    .custom-option:has(input:checked) label {
        color: var(--bs-primary) !important;
        font-weight: 700;
    }
    .form-check-input {
        cursor: pointer;
    }
</style>

<!-- ... Script Validation Update ... -->
<script>
    function nextStep(currentStep) {
        const currentSection = document.getElementById('step-' + currentStep);
        let valid = true;

        // Check Radios
        const radioGroups = new Set();
        currentSection.querySelectorAll('input[type="radio"]').forEach(r => radioGroups.add(r.name));
        radioGroups.forEach(name => {
            if (!currentSection.querySelector(`input[name="${name}"]:checked`)) valid = false;
        });

        // Check Checkboxes (At least one must be checked for 'multipla' types)
        // Group by name (e.g., q17[])
        const checkGroups = new Set();
        currentSection.querySelectorAll('input[type="checkbox"]').forEach(c => checkGroups.add(c.name));
        checkGroups.forEach(name => {
            // Only validate if we treat them as required. Assuming YES.
            if (!currentSection.querySelector(`input[name="${name}"]:checked`)) valid = false;
        });

        // Check Inputs
        currentSection.querySelectorAll('input[type="number"], input[type="text"]').forEach(input => {
            if (input.required && !input.value) {
                valid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!valid) {
            alert('Por favor, responda todas as perguntas obrigatórias.');
            return;
        }

        // Hide current, show next
        if(document.getElementById('step-' + (currentStep + 1))) {
            document.getElementById('step-' + currentStep).style.display = 'none';
            document.getElementById('step-' + (currentStep + 1)).style.display = 'block';
            updateProgress(currentStep + 1);
            window.scrollTo(0, 0);
        } else {
            // Se não tiver próximo passo, é submit (mas o btn submit já faz isso)
        }
    }


    function calcTicket() {
        const fat = parseFloat(document.getElementById('faturamento').value) || 0;
        const qtd = parseFloat(document.getElementById('atendimentos').value) || 0;

        if (qtd > 0) {
            const ticket = fat / qtd;
            document.getElementById('ticket_display').value = ticket.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            document.getElementById('ticket_medio_val').value = ticket.toFixed(2);
        } else {
            document.getElementById('ticket_display').value = 'R$ 0,00';
            document.getElementById('ticket_medio_val').value = 0;
        }
    }

    function prevStep(currentStep) {
        document.getElementById('step-' + currentStep).style.display = 'none';
        document.getElementById('step-' + (currentStep - 1)).style.display = 'block';

        updateProgress(currentStep - 1);
        window.scrollTo(0, 0);
    }

    function updateProgress(step) {
        document.getElementById('current-step-display').innerText = step;
        const percent = (step / 8) * 100;
        document.getElementById('progress-bar').style.width = percent + '%';
        document.getElementById('progress-bar').setAttribute('aria-valuenow', percent);
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>