<?php
require_once 'auth_check.php';
exigirUsuario('login.php');

$nomeUsuario = $_SESSION['usuario_nome'];
$emailUsuario = $_SESSION['usuario_email'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - Roma Antiga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link href="css/estilo.css" rel="stylesheet">
    <link href="css/painel.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-landmark me-2"></i>
                <span class="brand-text">Roma Antiga</span>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-light me-3 d-none d-md-inline">
                    <i class="fas fa-user-circle me-1"></i>
                    <?= htmlspecialchars($nomeUsuario) ?>
                </span>
                <a href="index.php" class="btn btn-sm btn-outline-light me-2">
                    <i class="fas fa-th-large me-1"></i> Temas
                </a>
                <button class="btn btn-sm btn-outline-light" id="btnSair">
                    <i class="fas fa-sign-out-alt me-1"></i> Sair
                </button>
            </div>
        </div>
    </nav>

    <main class="painel-main">
        <div class="container">
            <div class="painel-header">
                <h1>Olá, <?= htmlspecialchars(explode(' ', $nomeUsuario)[0]) ?>!</h1>
                <p>Acompanhe seu progresso nos estudos sobre a Roma Antiga.</p>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="painel-stat">
                        <div class="painel-stat-icon bg-primary-roma">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="painel-stat-valor" id="totalEstudados">0</div>
                            <div class="painel-stat-label">Flashcards estudados</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="painel-stat">
                        <div class="painel-stat-icon bg-secondary-roma">
                            <i class="fas fa-clone"></i>
                        </div>
                        <div>
                            <div class="painel-stat-valor" id="totalGeral">0</div>
                            <div class="painel-stat-label">Total disponível</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="painel-stat">
                        <div class="painel-stat-icon bg-success-roma">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div>
                            <div class="painel-stat-valor" id="percentual">0%</div>
                            <div class="painel-stat-label">Progresso total</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="painel-bloco mb-4">
                <h4 class="painel-titulo"><i class="fas fa-layer-group me-2"></i>Progresso por Tema</h4>
                <div id="progressoCategorias">
                    <p class="text-muted">Carregando...</p>
                </div>
            </div>

            <div class="painel-bloco">
                <h4 class="painel-titulo"><i class="fas fa-history me-2"></i>Últimos flashcards estudados</h4>
                <div id="recentes">
                    <p class="text-muted">Carregando...</p>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('btnSair').addEventListener('click', async () => {
        await fetch('api/auth.php', { method: 'DELETE' });
        window.location.href = 'index.php';
    });

    function escapeHtml(text) {
        if (!text) return '';
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function formatarData(data) {
        if (!data) return '';
        const d = new Date(data.replace(' ', 'T'));
        return d.toLocaleString('pt-BR');
    }

    async function carregar() {
        try {
            const res = await fetch('api/progresso.php');
            const data = await res.json();

            document.getElementById('totalEstudados').textContent = data.total_estudados;
            document.getElementById('totalGeral').textContent = data.total_geral;
            const pct = data.total_geral > 0
                ? Math.round((data.total_estudados / data.total_geral) * 100)
                : 0;
            document.getElementById('percentual').textContent = pct + '%';

            const catContainer = document.getElementById('progressoCategorias');
            if (!data.por_categoria || data.por_categoria.length === 0) {
                catContainer.innerHTML = '<p class="text-muted">Nenhum tema disponível.</p>';
            } else {
                catContainer.innerHTML = data.por_categoria.map(c => {
                    const p = c.total_categoria > 0
                        ? Math.round((c.estudados / c.total_categoria) * 100)
                        : 0;
                    return `
                        <div class="categoria-progresso">
                            <div class="categoria-progresso-header">
                                <span><i class="${escapeHtml(c.icone)} me-2" style="color: ${escapeHtml(c.cor)}"></i>${escapeHtml(c.nome)}</span>
                                <span class="text-muted">${c.estudados} / ${c.total_categoria}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: ${p}%; background: ${escapeHtml(c.cor)}"
                                     aria-valuenow="${p}" aria-valuemin="0" aria-valuemax="100">${p}%</div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            const recContainer = document.getElementById('recentes');
            if (!data.recentes || data.recentes.length === 0) {
                recContainer.innerHTML = '<p class="text-muted">Você ainda não estudou nenhum flashcard. <a href="index.php">Começar agora</a></p>';
            } else {
                recContainer.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pergunta</th>
                                    <th>Tema</th>
                                    <th>Visto em</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.recentes.map(r => `
                                    <tr>
                                        <td>${escapeHtml(r.pergunta)}</td>
                                        <td><span class="badge" style="background: ${escapeHtml(r.cor)}">${escapeHtml(r.categoria_nome)}</span></td>
                                        <td>${formatarData(r.visualizado_em)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
        } catch (err) {
            console.error(err);
        }
    }

    carregar();
    </script>
</body>
</html>
