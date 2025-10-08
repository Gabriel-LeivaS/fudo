<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Login - Panel FUDO</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #b08c6a;
            --accent-2: #a3c06b;
            --muted: #6c6c6c;
            --bg-light: #fbf8f6;
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-header p {
            font-size: 14px;
            font-weight: 400;
            margin: 0;
            opacity: 0.95;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-2);
            box-shadow: 0 0 0 0.2rem rgba(163, 192, 107, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            color: white;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(176, 140, 106, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(176, 140, 106, 0.4);
            background: linear-gradient(135deg, #9a7757 0%, #8fb55a 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-danger {
            background: #fee;
            color: #c33;
        }

        .input-group-text {
            border: 2px solid #e9ecef;
            border-right: none;
            background: transparent;
            border-radius: 10px 0 0 10px;
            padding: 12px 16px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            font-size: 18px;
            z-index: 10;
        }

        .form-floating {
            position: relative;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
        }

        .demo-credentials h6 {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 10px;
            color: #333;
        }

        .demo-credentials code {
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: var(--accent);
            font-weight: 600;
        }

        .spinner-border-sm {
            display: none;
        }

        .btn-login.loading .spinner-border-sm {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>🍽️ FUDO</h1>
                <p>Panel de Administración</p>
            </div>

            <div class="login-body">
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        ❌ <?= $this->session->flashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('login/acceder') ?>" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">👤 Usuario</label>
                        <input 
                            type="text" 
                            name="usuario" 
                            class="form-control" 
                            placeholder="Ingresa tu usuario"
                            required
                            autocomplete="username"
                            id="usuarioInput">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">🔒 Contraseña</label>
                        <div class="form-floating position-relative">
                            <input 
                                type="password" 
                                name="contrasena" 
                                class="form-control" 
                                placeholder="Ingresa tu contraseña"
                                required
                                autocomplete="current-password"
                                id="passwordInput">
                            <span class="password-toggle" onclick="togglePassword()">
                                <span id="eyeIcon">👁️</span>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        <span class="btn-text">Iniciar Sesión</span>
                    </button>
                </form>

                <div class="demo-credentials">
                    <h6>🔑 Credenciales de Prueba:</h6>
                    <div class="mb-2">
                        <strong>Super Admin:</strong> <code>admin</code> / <code>admin123</code>
                    </div>
                    <div>
                        <strong>Admin Sucursal:</strong> <code>admin_centro</code> / <code>centro123</code>
                    </div>
                </div>

                <p class="footer-text">
                    FUDO © <?= date('Y') ?> | Sistema Multi-Sucursal
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // Enfocar automáticamente el campo de usuario
        window.addEventListener('load', function() {
            document.getElementById('usuarioInput').focus();
        });

        // Permitir Enter para enviar
        document.getElementById('passwordInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>
