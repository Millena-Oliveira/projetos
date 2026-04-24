<?php
require_once 'conexao.php';
require_once 'auth_check.php';

// Evita que o navegador mostre a página em cache depois do logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Buscar categorias ativas
$stmt = $pdo->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY ordem ASC");
$categorias = $stmt->fetchAll();

$logado = usuarioLogado();
$nomeLogado = $logado ? $_SESSION['usuario_nome'] : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roma Antiga - Flashcards de História</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <!-- CSS personalizado -->
    <link href="css/estilo.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-landmark me-2"></i>
                <span class="brand-text">Roma Antiga</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#inicio"><i class="fas fa-home me-1"></i> Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categorias"><i class="fas fa-th-large me-1"></i> Temas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sobre"><i class="fas fa-info-circle me-1"></i> Sobre</a>
                    </li>
                    <?php if ($logado): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="painel.php"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars(explode(' ', $nomeLogado)[0]) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Entrar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registro.php"><i class="fas fa-user-plus me-1"></i> Cadastrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="inicio" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1 class="hero-titulo">História e Civilização da Roma Antiga</h1>
            <p class="hero-subtitulo">Explore a maior civilização do mundo antigo através de flashcards interativos</p>
            <a href="#categorias" class="btn btn-hero">
                <i class="fas fa-scroll me-2"></i>Começar a Estudar
            </a>
        </div>
    </header>

    <!-- Categorias -->
    <section id="categorias" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-titulo">Escolha um Tema</h2>
                <p class="section-subtitulo">Clique em um tema para acessar os flashcards e o conteúdo resumido</p>
            </div>

            <div class="row g-4">
                <?php foreach ($categorias as $cat): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="categoria-card" 
                         data-id="<?= $cat['id'] ?>" 
                         style="--card-color: <?= htmlspecialchars($cat['cor']) ?>">
                        <div class="categoria-icone">
                            <i class="<?= htmlspecialchars($cat['icone']) ?>"></i>
                        </div>
                        <h5 class="categoria-nome"><?= htmlspecialchars($cat['nome']) ?></h5>
                        <p class="categoria-desc"><?= htmlspecialchars($cat['descricao']) ?></p>
                        <span class="categoria-btn">
                            <i class="fas fa-arrow-right me-1"></i> Explorar
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Modal de Flashcards -->
    <div class="modal fade" id="flashcardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-flashcard">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">
                        <i class="fas fa-landmark me-2"></i>
                        <span id="modalCategoriaNome">Categoria</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4" id="tabsConteudo">
                        <li class="nav-item">
                            <button class="nav-link active" data-tab="flashcards">
                                <i class="fas fa-clone me-1"></i> Flashcards
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-tab="conteudo">
                                <i class="fas fa-book-open me-1"></i> Conteúdo
                            </button>
                        </li>
                    </ul>

                    <!-- Flashcards Container -->
                    <div id="flashcardsContainer" class="tab-content-area">
                        <div class="flashcard-wrapper">
                            <div class="flashcard" id="flashcard">
                                <div class="flashcard-inner">
                                    <div class="flashcard-front">
                                        <div class="flashcard-label">PERGUNTA</div>
                                        <p id="flashcardPergunta">Carregando...</p>
                                        <div class="flashcard-hint">
                                            <i class="fas fa-hand-pointer me-1"></i> Clique para ver a resposta
                                        </div>
                                    </div>
                                    <div class="flashcard-back">
                                        <div class="flashcard-label">RESPOSTA</div>
                                        <p id="flashcardResposta">Carregando...</p>
                                        <div class="flashcard-hint">
                                            <i class="fas fa-hand-pointer me-1"></i> Clique para voltar à pergunta
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navegação dos flashcards -->
                        <div class="flashcard-nav">
                            <button class="btn btn-nav" id="btnAnterior" disabled>
                                <i class="fas fa-chevron-left me-1"></i> Anterior
                            </button>
                            <span class="flashcard-counter">
                                <span id="flashcardAtual">1</span> / <span id="flashcardTotal">1</span>
                            </span>
                            <button class="btn btn-nav" id="btnProximo">
                                Próximo <i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Conteúdo Container -->
                    <div id="conteudoContainer" class="tab-content-area" style="display: none;">
                        <div id="conteudoTexto" class="conteudo-texto">
                            Carregando conteúdo...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sobre -->
    <section id="sobre" class="py-5 bg-sobre">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-titulo text-start">Sobre o Projeto</h2>
                    <p class="sobre-texto">
                        Este sistema foi desenvolvido para auxiliar alunos do ensino fundamental e médio
                        no estudo da História e Civilização da Roma Antiga. Através de flashcards interativos
                        e conteúdos resumidos, o aprendizado se torna mais dinâmico e eficiente.
                    </p>
                    <p class="sobre-texto">
                        Cada tema conta com flashcards de perguntas e respostas, além de textos explicativos
                        que resumem os principais aspectos de cada tópico da história romana.
                    </p>
                    <div class="sobre-stats">
                        <div class="stat-item">
                            <i class="fas fa-layer-group"></i>
                            <span class="stat-numero"><?= count($categorias) ?></span>
                            <span class="stat-label">Temas</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clone"></i>
                            <span class="stat-numero" id="totalFlashcards">--</span>
                            <span class="stat-label">Flashcards</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="sobre-ilustracao">
                        <i class="fas fa-university"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container text-center">
            <p class="mb-1"><i class="fas fa-landmark me-1"></i> Roma Antiga - Sistema de Estudo</p>
            <p class="mb-0 footer-sub">Desenvolvido para fins educacionais</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flag de autenticação do usuário -->
    <script>
        window.USUARIO_LOGADO = <?= $logado ? 'true' : 'false' ?>;
    </script>
    <!-- JavaScript personalizado -->
    <script src="js/app.js"></script>
</body>
</html>
