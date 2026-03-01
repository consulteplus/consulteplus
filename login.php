<?php
require_once 'includes/auth.php';

// Se já estiver logado, redirecionar para dashboard
if (isset($_SESSION['user_id'])) {
    redirect('modules/dashboard/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        if (login($email, $password)) {
            // Login bem sucedido
            if ($_SESSION['tipo'] === 'superadmin') {
                redirect('modules/admin/dashboard/index.php');
            } else {
                redirect('modules/dashboard/index.php');
            }
        } else {
            $error = 'Email ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #ffffff;
        }

        /* Left Side - Branding */
        .brand-side {
            background: #223359;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .brand-side::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            top: -250px;
            right: -250px;
        }

        .brand-side::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            bottom: -200px;
            left: -200px;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }

        .brand-logo {
            max-width: 400px;
            width: 100%;
            height: auto;
            margin-bottom: 3rem;
        }

        .brand-title {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        /* Right Side - Login Form */
        .login-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: #f8f9fa;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1D2F5F;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
            font-size: 1rem;
        }

        .form-floating {
            margin-bottom: 1.25rem;
        }

        .form-floating>.form-control {
            height: 58px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-floating>.form-control:focus {
            border-color: #1D2F5F;
            box-shadow: 0 0 0 0.2rem rgba(29, 47, 95, 0.1);
        }

        .form-floating>label {
            color: #6c757d;
            padding: 1rem 0.75rem;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 8px;
            z-index: 10;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: #1D2F5F;
        }

        .btn-login {
            width: 100%;
            height: 56px;
            background: #1D2F5F;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            background: #2a4575;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 47, 95, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }

        .divider span {
            background: #f8f9fa;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
            position: relative;
        }

        .signup-link {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
        }

        .signup-link a {
            color: #1D2F5F;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .signup-link a:hover {
            color: #2a4575;
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            body {
                grid-template-columns: 1fr;
            }

            .brand-side {
                display: none;
            }

            .login-side {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .login-side {
                padding: 1.5rem 1rem;
            }

            .login-container {
                max-width: 100%;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .login-header p {
                font-size: 0.95rem;
            }

            .form-floating>.form-control {
                height: 52px;
                font-size: 0.95rem;
            }

            .btn-login {
                height: 50px;
                font-size: 1rem;
            }

            .brand-title {
                font-size: 1.5rem;
            }
        }

        /* Telas muito pequenas */
        @media (max-width: 400px) {
            .login-side {
                padding: 1rem 0.75rem;
            }

            .login-header {
                margin-bottom: 1.5rem;
            }

            .login-header h1 {
                font-size: 1.35rem;
            }

            .form-floating {
                margin-bottom: 1rem;
            }
        }

        /* Loading animation */
        .btn-login.loading {
            pointer-events: none;
            position: relative;
            color: transparent;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Left Side - Branding -->
    <div class="brand-side">
        <div class="brand-content">
            <img src="<?php echo BASE_URL; ?>assets/img/logo-white.png" alt="<?php echo SITE_NAME; ?>"
                class="brand-logo">
            <h2 class="brand-title">Bem-vindo de volta!</h2>
            <p class="brand-subtitle">Acesse sua conta e continue gerenciando seu negócio com excelência.</p>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-side">
        <div class="login-container">
            <div class="login-header">
                <h1>Entrar</h1>
                <p>Digite suas credenciais para acessar</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="seu@email.com"
                        required autofocus>
                    <label for="email">
                        <i class="bi bi-envelope me-2"></i>Email
                    </label>
                </div>

                <div class="form-floating password-wrapper">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Senha"
                        required>
                    <label for="password">
                        <i class="bi bi-lock me-2"></i>Senha
                    </label>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-login">
                    Entrar
                </button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="signup-link">
                Não tem uma conta? <a href="cadastro">Cadastre-se gratuitamente</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        // Add loading state to button on submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.querySelector('.btn-login');
            btn.classList.add('loading');
        });
    </script>
</body>

</html>