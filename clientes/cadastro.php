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

                // 3. Criar Usuário (Cliente/Normal)
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $tipo = 'cliente';

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
    <title>Criar Conta - <?php echo (defined('SITE_NAME') ? SITE_NAME : 'Gestão'); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --sidebar-bg: #1e293b;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1f2937;
            position: relative;
            overflow: hidden;
            /* Trava a rolagem principal */
        }

        /* --- DASHBOARD SIMULADO (BACKGROUND) --- */
        .dashboard-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            display: flex;
            filter: blur(8px) brightness(0.95);
            /* O Desfoque Mágico */
            user-select: none;
            pointer-events: none;
            background: #f1f5f9;
        }

        .bg-sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .bg-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .bg-header {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .bg-main {
            padding: 30px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        /* Elementos Fictícios */
        .fake-logo {
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .fake-menu-item {
            height: 48px;
            margin-bottom: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding-left: 15px;
        }

        .fake-menu-item.active {
            background: var(--primary-gradient);
            opacity: 0.9;
        }

        .fake-menu-item:not(.active) {
            background: rgba(255, 255, 255, 0.03);
        }

        .fake-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 140px;
        }

        .fake-card.large {
            grid-column: span 2;
            height: 300px;
        }

        .fake-card.tall {
            grid-row: span 2;
            height: auto;
        }

        .fake-line {
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            margin-bottom: 10px;
            width: 60%;
        }

        .fake-line.full {
            width: 100%;
        }

        .fake-line.title {
            height: 16px;
            width: 40%;
            margin-bottom: 20px;
            background: #cbd5e1;
        }

        .fake-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e0e7ff;
            margin-bottom: 15px;
        }

        /* Responsividade do Background */
        @media (max-width: 900px) {
            .bg-sidebar {
                display: none;
            }

            .bg-main {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            /* Reduzir blur em mobile para performance */
            .dashboard-bg {
                filter: blur(4px) brightness(0.97);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .login-card {
                max-width: 100%;
                border-radius: 16px;
            }

            .login-header {
                padding: 2rem 1.5rem 1rem;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            /* Garantir que campos em grid empilhem */
            .col-md-6 {
                margin-bottom: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .login-card {
                border-radius: 12px;
            }

            .login-header h3 {
                font-size: 1.5rem;
            }

            .login-header p {
                font-size: 0.95rem;
            }

            .brand-logo {
                width: 56px;
                height: 56px;
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
            }

            .form-floating>.form-control {
                height: 3.25rem;
                font-size: 0.95rem;
            }

            .btn-primary-custom {
                padding: 0.875rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 5px;
            }

            .login-header {
                padding: 1.5rem 1rem 0.75rem;
            }

            .card-body {
                padding: 1rem !important;
            }

            .login-header h3 {
                font-size: 1.35rem;
            }
        }

        /* --- FIM BACKGROUND --- */

        /* Glassmorphism Card (Ajuste z-index) */
        .login-card {
            width: 100%;
            max-width: 520px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: rgba(255, 255, 255, 0.85);
            /* Mais transparente */
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            position: relative;
            z-index: 10;
        }

        .login-header {
            padding: 2.5rem 2.5rem 1rem;
            text-align: center;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            background: var(--primary-gradient);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            transform: rotate(-5deg);
        }

        .login-header h3 {
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        /* Form Styling */
        .form-floating>.form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding-left: 1rem;
            height: 3.5rem;
            line-height: 1.25;
            transition: all 0.2s ease;
            font-weight: 500;
            background-color: #f9fafb;
        }

        .form-floating>.form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
            background-color: #fff;
        }

        .form-floating>label {
            padding-left: 1rem;
            color: #6b7280;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 12px;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            background: var(--hover-gradient);
        }

        .btn-primary-custom:active {
            transform: translateY(0);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            z-index: 10;
            padding: 5px;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #4f46e5;
        }

        .terms-text {
            font-size: 0.85rem;
            color: #6b7280;
            line-height: 1.5;
        }

        .terms-text a {
            color: #4f46e5;
            font-weight: 500;
            text-decoration: none;
            position: relative;
        }

        .terms-text a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 1px;
            bottom: -1px;
            left: 0;
            background-color: currentColor;
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.25s ease-out;
        }

        .terms-text a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        .login-footer {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }

        .login-footer a {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: #4338ca;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <!-- DASHBOARD SIMULADO (BACKGROUND) -->
    <div class="dashboard-bg">
        <!-- Sidebar -->
        <div class="bg-sidebar">
            <div class="fake-logo"></div>
            <div class="fake-menu-item active"></div>
            <div class="fake-menu-item"></div>
            <div class="fake-menu-item"></div>
            <div class="fake-menu-item"></div>
            <div class="fake-menu-item"></div>
            <div class="fake-menu-item"></div>
            <div style="margin-top: auto;">
                <div class="fake-menu-item"></div>
            </div>
        </div>

        <!-- Conteudo -->
        <div class="bg-content">
            <div class="bg-header">
                <div class="fake-line" style="width: 200px; height: 12px; margin: 0;"></div>
                <div class="fake-circle" style="width: 40px; height: 40px; margin: 0;"></div>
            </div>

            <div class="bg-main">
                <!-- Cards Topo -->
                <div class="fake-card">
                    <div class="fake-circle" style="background: #dbeafe; color: #3b82f6;"></div>
                    <div class="fake-line title"></div>
                    <div class="fake-line full" style="height: 24px; width: 60%;"></div>
                </div>
                <div class="fake-card">
                    <div class="fake-circle" style="background: #dcfce7; color: #22c55e;"></div>
                    <div class="fake-line title"></div>
                    <div class="fake-line full" style="height: 24px; width: 60%;"></div>
                </div>
                <div class="fake-card">
                    <div class="fake-circle" style="background: #fce7f3; color: #ec4899;"></div>
                    <div class="fake-line title"></div>
                    <div class="fake-line full" style="height: 24px; width: 60%;"></div>
                </div>

                <!-- Gráfico Grande -->
                <div class="fake-card large">
                    <div class="fake-line title" style="width: 200px;"></div>
                    <div style="flex:1; display:flex; align-items:flex-end; gap:10px; padding-top:20px;">
                        <div style="width:100%; height:40%; background:#e2e8f0; border-radius:4px;"></div>
                        <div style="width:100%; height:70%; background:#818cf8; border-radius:4px;"></div>
                        <div style="width:100%; height:50%; background:#e2e8f0; border-radius:4px;"></div>
                        <div style="width:100%; height:85%; background:#818cf8; border-radius:4px;"></div>
                        <div style="width:100%; height:60%; background:#e2e8f0; border-radius:4px;"></div>
                    </div>
                </div>

                <!-- Lista Lateral -->
                <div class="fake-card tall">
                    <div class="fake-line title"></div>
                    <div class="fake-line full"></div>
                    <div class="fake-line full"></div>
                    <div class="fake-line full"></div>
                    <div class="fake-line full"></div>
                    <div class="fake-line full"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- FIM DASHBOARD BACKGROUND -->

    <div class="login-card">
        <div class="login-header">
            <h3 class="mb-1">Crie sua conta</h3>
            <p class="text-muted mb-0">Junte-se a centenas de clínicas de sucesso</p>
        </div>

        <div class="card-body p-4 pt-1 px-sm-5">

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm" role="alert"
                    style="background-color: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div class="fw-medium small"><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="row g-3">

                    <!-- Nome Pessoal -->
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome"
                                required value="<?php echo htmlspecialchars($nome ?? ''); ?>">
                            <label for="nome"><i class="bi bi-person me-2"></i>Seu Nome Completo</label>
                        </div>
                    </div>

                    <!-- Nome da Empresa -->
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nome_clinica" name="nome_clinica"
                                placeholder="Nome da Empresa" required
                                value="<?php echo htmlspecialchars($nomeClinica ?? ''); ?>">
                            <label for="nome_clinica"><i class="bi bi-building me-2"></i>Nome da Empresa</label>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="nome@exemplo.com" required
                                value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <label for="email"><i class="bi bi-envelope me-2"></i>E-mail Profissional</label>
                        </div>
                    </div>

                    <!-- WhatsApp e Senha -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" placeholder="WhatsApp"
                                required value="<?php echo htmlspecialchars($whatsapp ?? ''); ?>">
                            <label for="whatsapp"><i class="bi bi-whatsapp me-2"></i>Celular</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha"
                                required minlength="6">
                            <label for="senha"><i class="bi bi-lock me-2"></i>Senha</label>
                            <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-primary-custom w-100">
                        Criar minha conta grátis
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>

                <div class="login-footer">
                    <span class="text-muted">Já possui cadastro?</span>
                    <a href="login.php" class="ms-1">Fazer Login</a>
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