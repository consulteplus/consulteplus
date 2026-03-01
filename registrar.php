<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// Se já estiver logado, redirecionar para dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "modules/dashboard/index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $nomeClinica = trim($_POST['nome_clinica'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($nomeClinica) || empty($email) || empty($whatsapp) || empty($senha)) {
        $error = 'Preencha todos os campos obrigatórios.';
    } elseif (strlen($senha) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        // 1. Verificar se email existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Este e-mail já está cadastrado.';
        } else {
            // 2. Criar Empresa
            $stmtClinica = $conn->prepare("INSERT INTO empresas (nome, ativo) VALUES (?, 1)");
            $stmtClinica->bind_param("s", $nomeClinica);

            if ($stmtClinica->execute()) {
                $companyId = $stmtClinica->insert_id;

                // 3. Criar Usuário (Admin)
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $tipo = 'admin';

                $stmtUser = $conn->prepare("INSERT INTO users (company_id, nome, email, telefone, senha, tipo, ativo) VALUES (?, ?, ?, ?, ?, ?, 1)");
                $stmtUser->bind_param("isssss", $companyId, $nome, $email, $whatsapp, $senhaHash, $tipo);

                if ($stmtUser->execute()) {
                    $newUserId = $stmtUser->insert_id;

                    // Auto-login
                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['nome'] = $nome;
                    $_SESSION['email'] = $email;
                    $_SESSION['tipo'] = $tipo;
                    $_SESSION['company_id'] = $companyId;

                    header("Location: " . BASE_URL . "modules/dashboard/index.php");
                    exit;
                } else {
                    $error = "Erro ao criar usuário: " . $conn->error;
                }
            } else {
                $error = "Erro ao criar empresa: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se -
        <?php echo (defined('SITE_NAME') ? SITE_NAME : 'Gestão'); ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .login-card {
            width: 100%;
            max-width: 500px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: white;
            overflow: hidden;
        }

        .login-header {
            background-color: #fff;
            padding: 2rem 2rem 1rem;
            text-align: center;
        }

        .login-icon {
            font-size: 3.5rem;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }

        .form-floating>.form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .btn-primary-custom {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-1px);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .login-card {
                max-width: 100%;
            }

            .login-header {
                padding: 1.5rem 1.5rem 1rem;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            .login-icon {
                font-size: 3rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .login-header {
                padding: 1.25rem 1rem 0.75rem;
            }

            .login-header h3 {
                font-size: 1.5rem;
            }

            .login-icon {
                font-size: 2.5rem;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .form-floating>.form-control {
                font-size: 0.95rem;
            }

            .btn-primary-custom {
                padding: 10px;
                font-size: 1rem;
            }

            /* Garantir que campos em grid empilhem */
            .col-md-6 {
                margin-bottom: 0.75rem;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 5px;
            }

            .login-header {
                padding: 1rem 0.75rem 0.5rem;
            }

            .card-body {
                padding: 1rem !important;
            }

            .login-header h3 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="login-header">
            <div class="mb-3">
                <i class="bi bi-briefcase text-primary fs-1"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Crie sua conta</h3>
            <p class="text-muted mb-0">Comece a gerenciar seu negócio hoje</p>
        </div>

        <div class="card-body p-4 pt-2">

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div>
                        <?php echo $error; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="row g-3">
                    <!-- Nome da Empresa -->
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nome_clinica" name="nome_clinica"
                                placeholder="Nome da Empresa" required
                                value="<?php echo htmlspecialchars($nomeClinica ?? ''); ?>">
                            <label for="nome_clinica">Nome da Empresa</label>
                        </div>
                    </div>

                    <!-- Nome Pessoal -->
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome"
                                required value="<?php echo htmlspecialchars($nome ?? ''); ?>">
                            <label for="nome">Seu Nome Completo</label>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="nome@exemplo.com" required
                                value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <label for="email">E-mail Profissional</label>
                        </div>
                    </div>

                    <!-- WhatsApp e Senha (em grid para economizar espaço visual) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" placeholder="WhatsApp"
                                required value="<?php echo htmlspecialchars($whatsapp ?? ''); ?>">
                            <label for="whatsapp">WhatsApp / Celular</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha"
                                required minlength="6">
                            <label for="senha">Senha</label>
                            <i class="bi bi-eye-slash password-toggle pe-3" id="togglePassword"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-3">
                    <button type="submit" class="btn btn-primary btn-primary-custom w-100 shadow-sm">
                        <i class="bi bi-rocket-takeoff me-2"></i> COMEÇAR TESTE GRÁTIS
                    </button>
                </div>

                <p class="text-center text-muted small mb-0">
                    Ao criar uma conta, você concorda com os <a href="#" class="text-decoration-none">Termos de Uso</a>.
                </p>

                <div class="text-center mt-4 pt-3 border-top">
                    <span class="text-muted">Já tem uma conta?</span>
                    <a href="login.php" class="fw-bold text-decoration-none ms-1">Fazer Login</a>
                </div>

            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password Toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#senha');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Simple Phone Mask
        const phoneInput = document.getElementById('whatsapp');
        phoneInput.addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>

</html>