<?php
require_once 'auth_check.php';

if (adminLogado()) {
    header('Location: admin/index.php');
    exit;
}
if (usuarioLogado()) {
    header('Location: painel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Roma Antiga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        body.auth-body {
            font-family: 'Lora', Georgia, serif;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #2C1810 0%, #5C2E0A 50%, #8B4513 100%);
        }
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .auth-header i {
            font-size: 2.8rem;
            color: #DAA520;
            margin-bottom: 0.5rem;
        }
        .auth-header h3 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: #5C2E0A;
            margin-bottom: 0.3rem;
        }
        .auth-header p {
            color: #7A6A5A;
            font-size: 0.9rem;
            margin: 0;
        }
        .btn-auth {
            background: linear-gradient(135deg, #DAA520, #B8860B);
            color: #2C1810;
            font-family: 'Cinzel', serif;
            font-weight: 600;
            padding: 0.7rem 1.2rem;
            border: none;
            border-radius: 8px;
            letter-spacing: 1px;
            transition: all 0.2s ease;
        }
        .btn-auth:hover {
            background: linear-gradient(135deg, #B8860B, #8B6914);
            color: #2C1810;
            transform: translateY(-1px);
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #7A6A5A;
        }
        .auth-footer a {
            color: #8B4513;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-footer a:hover {
            color: #DAA520;
            text-decoration: underline;
        }
        .input-group-text {
            background: #F5EDE0;
            border-color: #D4C4A8;
            color: #8B4513;
        }
        .form-control:focus {
            border-color: #DAA520;
            box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-user-plus"></i>
                <h3>Criar Conta</h3>
                <p>Cadastre-se para estudar com flashcards</p>
            </div>
            <form id="formRegistro">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome completo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="nome" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha <small class="text-muted">(mín. 6 caracteres)</small></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="senha" minlength="6" required>
                    </div>
                </div>
                <div id="erroRegistro" class="alert alert-danger d-none"></div>
                <button type="submit" class="btn btn-auth w-100">
                    <i class="fas fa-user-plus me-2"></i>Cadastrar
                </button>
            </form>
            <div class="auth-footer">
                <p class="mb-1">Já tem conta? <a href="login.php">Entrar</a></p>
                <p class="mb-0"><a href="index.php"><i class="fas fa-arrow-left me-1"></i>Voltar ao site</a></p>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('formRegistro').addEventListener('submit', async (e) => {
        e.preventDefault();
        const erro = document.getElementById('erroRegistro');
        erro.classList.add('d-none');

        try {
            const res = await fetch('api/registro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    nome: document.getElementById('nome').value,
                    email: document.getElementById('email').value,
                    senha: document.getElementById('senha').value
                })
            });
            const data = await res.json();

            if (data.sucesso) {
                window.location.href = data.redirect;
            } else {
                erro.textContent = data.erro || 'Erro ao cadastrar';
                erro.classList.remove('d-none');
            }
        } catch (err) {
            erro.textContent = 'Erro de conexão com o servidor';
            erro.classList.remove('d-none');
        }
    });
    </script>
</body>
</html>
