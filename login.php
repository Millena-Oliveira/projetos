<?php
require_once 'auth_check.php';

// Se já estiver logado, redireciona direto
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
    <title>Entrar - Roma Antiga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link href="css/estilo.css" rel="stylesheet">
    <link href="css/auth.css" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-landmark"></i>
                <h3>Roma Antiga</h3>
                <p>Entre para acessar os flashcards</p>
            </div>
            <form id="formLogin">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="senha" required>
                    </div>
                </div>
                <div id="erroLogin" class="alert alert-danger d-none"></div>
                <button type="submit" class="btn btn-auth w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                </button>
            </form>
            <div class="auth-footer">
                <p class="mb-1">Não tem conta? <a href="registro.php">Cadastre-se</a></p>
                <p class="mb-0"><a href="index.php"><i class="fas fa-arrow-left me-1"></i>Voltar ao site</a></p>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('formLogin').addEventListener('submit', async (e) => {
        e.preventDefault();
        const erro = document.getElementById('erroLogin');
        erro.classList.add('d-none');

        try {
            const res = await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    senha: document.getElementById('senha').value
                })
            });
            const data = await res.json();

            if (data.sucesso) {
                window.location.href = data.redirect;
            } else {
                erro.textContent = data.erro || 'Erro ao fazer login';
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
