<?php
require_once __DIR__ . '/../auth_check.php';
// Se não estiver logado como admin, redireciona para a tela de login unificada
exigirAdmin('../login.php');

$adminNome = $_SESSION['admin_nome'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Roma Antiga</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS do Admin -->
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>

    <!-- Tela de Login (mantida como fallback, mas proteção é server-side via exigirAdmin) -->
    <div id="telaLogin" class="login-container d-none">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-shield-alt"></i>
                <h4>Painel Administrativo</h4>
                <p>Roma Antiga - Sistema de Flashcards</p>
            </div>
            <form id="formLogin">
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="loginEmail" placeholder="admin@romaantiga.com" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="loginSenha" class="form-label">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="loginSenha" placeholder="Sua senha" required>
                    </div>
                </div>
                <div id="loginErro" class="alert alert-danger d-none"></div>
                <button type="submit" class="btn btn-admin-primary w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                </button>
            </form>
            <div class="login-footer">
                <small>Credenciais padrão: admin@romaantiga.com / admin123</small>
            </div>
        </div>
    </div>

    <!-- Painel Principal -->
    <div id="painelAdmin">
        <!-- Sidebar -->
        <nav class="admin-sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-landmark"></i>
                <span>Roma Antiga</span>
                <small>Painel Admin</small>
            </div>
            <ul class="sidebar-menu">
                <li class="active" data-section="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </li>
                <li data-section="categorias">
                    <i class="fas fa-layer-group"></i>
                    <span>Categorias</span>
                </li>
                <li data-section="flashcards">
                    <i class="fas fa-clone"></i>
                    <span>Flashcards</span>
                </li>
                <li data-section="conteudos">
                    <i class="fas fa-book-open"></i>
                    <span>Conteúdos</span>
                </li>
            </ul>
            <div class="sidebar-footer">
                <button class="btn btn-sm btn-outline-light w-100" id="btnLogout">
                    <i class="fas fa-sign-out-alt me-1"></i> Sair
                </button>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="admin-main">
            <!-- Topbar -->
            <div class="admin-topbar">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="btnToggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-info">
                    <span>Olá, <strong id="adminNome"><?= htmlspecialchars($adminNome) ?></strong></span>
                    <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-primary ms-3">
                        <i class="fas fa-external-link-alt me-1"></i> Ver Site
                    </a>
                </div>
            </div>

            <!-- Dashboard -->
            <section id="secaoDashboard" class="admin-section">
                <h4 class="section-title"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card stat-categorias">
                            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                            <div class="stat-info">
                                <span class="stat-value" id="dashCategorias">0</span>
                                <span class="stat-label">Categorias</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card stat-flashcards">
                            <div class="stat-icon"><i class="fas fa-clone"></i></div>
                            <div class="stat-info">
                                <span class="stat-value" id="dashFlashcards">0</span>
                                <span class="stat-label">Flashcards</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card stat-conteudos">
                            <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                            <div class="stat-info">
                                <span class="stat-value" id="dashConteudos">0</span>
                                <span class="stat-label">Conteúdos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Últimos flashcards adicionados</h6>
                        <div class="table-responsive">
                            <table class="table table-sm" id="tabelaRecentes">
                                <thead>
                                    <tr>
                                        <th>Pergunta</th>
                                        <th>Categoria</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoRecentes">
                                    <tr><td colspan="3" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Categorias -->
            <section id="secaoCategorias" class="admin-section d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="section-title mb-0"><i class="fas fa-layer-group me-2"></i>Categorias</h4>
                    <button class="btn btn-admin-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="limparFormCategoria()">
                        <i class="fas fa-plus me-1"></i> Nova Categoria
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cor</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Ícone</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoCategorias">
                                    <tr><td colspan="7" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Flashcards -->
            <section id="secaoFlashcards" class="admin-section d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="section-title mb-0"><i class="fas fa-clone me-2"></i>Flashcards</h4>
                    <button class="btn btn-admin-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalFlashcard" onclick="limparFormFlashcard()">
                        <i class="fas fa-plus me-1"></i> Novo Flashcard
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <select class="form-select form-select-sm w-auto" id="filtroCategoria">
                                <option value="">Todas as categorias</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Categoria</th>
                                        <th>Pergunta</th>
                                        <th>Resposta</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoFlashcards">
                                    <tr><td colspan="6" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Conteúdos -->
            <section id="secaoConteudos" class="admin-section d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="section-title mb-0"><i class="fas fa-book-open me-2"></i>Conteúdos</h4>
                    <button class="btn btn-admin-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalConteudo" onclick="limparFormConteudo()">
                        <i class="fas fa-plus me-1"></i> Novo Conteúdo
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Categoria</th>
                                        <th>Título</th>
                                        <th>Texto (resumo)</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoConteudos">
                                    <tr><td colspan="6" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal Categoria -->
    <div class="modal fade" id="modalCategoria" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalCategoria">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCategoria">
                    <div class="modal-body">
                        <input type="hidden" id="catId">
                        <div class="mb-3">
                            <label for="catNome" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="catNome" required>
                        </div>
                        <div class="mb-3">
                            <label for="catDescricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="catDescricao" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="catIcone" class="form-label">Ícone (FontAwesome)</label>
                                <input type="text" class="form-control" id="catIcone" value="fas fa-landmark" placeholder="fas fa-landmark">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="catCor" class="form-label">Cor</label>
                                <input type="color" class="form-control form-control-color" id="catCor" value="#8B4513">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="catOrdem" class="form-label">Ordem</label>
                                <input type="number" class="form-control" id="catOrdem" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-admin-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Flashcard -->
    <div class="modal fade" id="modalFlashcard" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalFlashcard">Novo Flashcard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formFlashcard">
                    <div class="modal-body">
                        <input type="hidden" id="flashId">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="flashCategoria" class="form-label">Categoria *</label>
                                <select class="form-select" id="flashCategoria" required>
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="flashOrdem" class="form-label">Ordem</label>
                                <input type="number" class="form-control" id="flashOrdem" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="flashPergunta" class="form-label">Pergunta *</label>
                            <textarea class="form-control" id="flashPergunta" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="flashResposta" class="form-label">Resposta *</label>
                            <textarea class="form-control" id="flashResposta" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-admin-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Conteúdo -->
    <div class="modal fade" id="modalConteudo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalConteudo">Novo Conteúdo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formConteudo">
                    <div class="modal-body">
                        <input type="hidden" id="contId">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="contCategoria" class="form-label">Categoria *</label>
                                <select class="form-select" id="contCategoria" required>
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contOrdem" class="form-label">Ordem</label>
                                <input type="number" class="form-control" id="contOrdem" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contTitulo" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="contTitulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contTexto" class="form-label">Texto *</label>
                            <textarea class="form-control" id="contTexto" rows="8" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-admin-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast de notificação -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toastNotificacao" class="toast" role="alert">
            <div class="toast-header">
                <i class="fas fa-bell me-2 text-primary"></i>
                <strong class="me-auto" id="toastTitulo">Notificação</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMensagem"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript do Admin -->
    <script src="../js/admin.js"></script>
</body>
</html>
