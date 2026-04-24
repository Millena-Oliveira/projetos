/**
 * Roma Antiga - JavaScript do Painel Administrativo
 */

const API = '../api';
let categoriasCache = [];

document.addEventListener('DOMContentLoaded', () => {
    // Proteção agora é feita server-side em admin/index.php via exigirAdmin().
    // Quando o script executa, o admin já está autenticado.
    iniciarPainel();
    configurarEventos();
});

/* ===== AUTENTICAÇÃO ===== */

function iniciarPainel() {
    carregarDashboard();
    carregarCategorias();
    carregarFlashcards();
    carregarConteudos();
}

function configurarEventos() {
    // Logout -> destrói sessão e volta para login unificado
    document.getElementById('btnLogout').addEventListener('click', async () => {
        await fetch(`${API}/auth.php`, { method: 'DELETE' });
        window.location.href = '../login.php';
    });

    // Sidebar toggle (mobile)
    document.getElementById('btnToggleSidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Navegação sidebar
    document.querySelectorAll('.sidebar-menu li').forEach(item => {
        item.addEventListener('click', () => {
            const secao = item.dataset.section;
            document.querySelectorAll('.sidebar-menu li').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            document.querySelectorAll('.admin-section').forEach(s => s.classList.add('d-none'));
            document.getElementById(`secao${capitalizar(secao)}`).classList.remove('d-none');
            document.getElementById('sidebar').classList.remove('show');
        });
    });

    // Formulários
    document.getElementById('formCategoria').addEventListener('submit', salvarCategoria);
    document.getElementById('formFlashcard').addEventListener('submit', salvarFlashcard);
    document.getElementById('formConteudo').addEventListener('submit', salvarConteudo);

    // Filtro de categoria nos flashcards
    document.getElementById('filtroCategoria').addEventListener('change', carregarFlashcards);
}

/* ===== DASHBOARD ===== */

async function carregarDashboard() {
    try {
        const [catRes, flashRes, contRes] = await Promise.all([
            fetch(`${API}/categorias.php`),
            fetch(`${API}/flashcards.php`),
            fetch(`${API}/conteudos.php`)
        ]);

        const categorias = await catRes.json();
        const flashcards = await flashRes.json();
        const conteudos = await contRes.json();

        document.getElementById('dashCategorias').textContent = categorias.length;
        document.getElementById('dashFlashcards').textContent = flashcards.length;
        document.getElementById('dashConteudos').textContent = conteudos.length;

        // Últimos flashcards
        const recentes = flashcards.slice(-5).reverse();
        const corpo = document.getElementById('corpoRecentes');

        if (recentes.length === 0) {
            corpo.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhum flashcard cadastrado</td></tr>';
            return;
        }

        corpo.innerHTML = recentes.map(f => `
            <tr>
                <td class="text-truncate-cell">${escapeHtml(f.pergunta)}</td>
                <td>${escapeHtml(f.categoria_nome || '-')}</td>
                <td>${formatarData(f.criado_em)}</td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Erro ao carregar dashboard:', err);
    }
}

/* ===== CATEGORIAS ===== */

async function carregarCategorias() {
    try {
        const res = await fetch(`${API}/categorias.php`);
        categoriasCache = await res.json();

        const corpo = document.getElementById('corpoCategorias');

        if (categoriasCache.length === 0) {
            corpo.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Nenhuma categoria cadastrada</td></tr>';
            return;
        }

        corpo.innerHTML = categoriasCache.map(c => `
            <tr>
                <td>${c.id}</td>
                <td><span class="cor-preview" style="background: ${escapeHtml(c.cor)}"></span></td>
                <td><strong>${escapeHtml(c.nome)}</strong></td>
                <td class="text-truncate-cell">${escapeHtml(c.descricao || '-')}</td>
                <td><i class="${escapeHtml(c.icone)}"></i> <small class="text-muted">${escapeHtml(c.icone)}</small></td>
                <td>${c.ativo == 1 ? '<span class="badge-ativo">Ativo</span>' : '<span class="badge-inativo">Inativo</span>'}</td>
                <td>
                    <button class="btn btn-acao btn-editar" onclick="editarCategoria(${c.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-acao btn-excluir" onclick="excluirCategoria(${c.id}, '${escapeHtml(c.nome)}')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');

        // Atualizar selects de categoria
        atualizarSelectsCategorias();
    } catch (err) {
        console.error('Erro ao carregar categorias:', err);
    }
}

function atualizarSelectsCategorias() {
    const options = '<option value="">Selecione...</option>' +
        categoriasCache.map(c => `<option value="${c.id}">${escapeHtml(c.nome)}</option>`).join('');

    document.getElementById('flashCategoria').innerHTML = options;
    document.getElementById('contCategoria').innerHTML = options;

    const filtroOptions = '<option value="">Todas as categorias</option>' +
        categoriasCache.map(c => `<option value="${c.id}">${escapeHtml(c.nome)}</option>`).join('');
    document.getElementById('filtroCategoria').innerHTML = filtroOptions;
}

function limparFormCategoria() {
    document.getElementById('tituloModalCategoria').textContent = 'Nova Categoria';
    document.getElementById('formCategoria').reset();
    document.getElementById('catId').value = '';
    document.getElementById('catCor').value = '#8B4513';
    document.getElementById('catIcone').value = 'fas fa-landmark';
}

function editarCategoria(id) {
    const cat = categoriasCache.find(c => c.id == id);
    if (!cat) return;

    document.getElementById('tituloModalCategoria').textContent = 'Editar Categoria';
    document.getElementById('catId').value = cat.id;
    document.getElementById('catNome').value = cat.nome;
    document.getElementById('catDescricao').value = cat.descricao || '';
    document.getElementById('catIcone').value = cat.icone || 'fas fa-landmark';
    document.getElementById('catCor').value = cat.cor || '#8B4513';
    document.getElementById('catOrdem').value = cat.ordem || 0;

    new bootstrap.Modal(document.getElementById('modalCategoria')).show();
}

async function salvarCategoria(e) {
    e.preventDefault();
    const id = document.getElementById('catId').value;
    const dados = {
        nome: document.getElementById('catNome').value,
        descricao: document.getElementById('catDescricao').value,
        icone: document.getElementById('catIcone').value,
        cor: document.getElementById('catCor').value,
        ordem: parseInt(document.getElementById('catOrdem').value) || 0
    };

    const method = id ? 'PUT' : 'POST';
    if (id) dados.id = parseInt(id);

    try {
        const res = await fetch(`${API}/categorias.php`, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });
        const data = await res.json();

        if (data.sucesso || data.id) {
            bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
            mostrarToast('Sucesso', `Categoria ${id ? 'atualizada' : 'criada'} com sucesso!`);
            carregarCategorias();
            carregarDashboard();
        } else {
            mostrarToast('Erro', data.erro || 'Erro ao salvar categoria');
        }
    } catch (err) {
        mostrarToast('Erro', 'Erro de conexão com o servidor');
    }
}

async function excluirCategoria(id, nome) {
    if (!confirm(`Deseja excluir a categoria "${nome}"?\nTodos os flashcards e conteúdos vinculados serão removidos.`)) return;

    try {
        const res = await fetch(`${API}/categorias.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const data = await res.json();

        if (data.sucesso) {
            mostrarToast('Sucesso', 'Categoria excluída com sucesso!');
            carregarCategorias();
            carregarFlashcards();
            carregarConteudos();
            carregarDashboard();
        }
    } catch (err) {
        mostrarToast('Erro', 'Erro ao excluir categoria');
    }
}

/* ===== FLASHCARDS ===== */

async function carregarFlashcards() {
    try {
        const filtro = document.getElementById('filtroCategoria').value;
        const url = filtro ? `${API}/flashcards.php?categoria_id=${filtro}` : `${API}/flashcards.php`;
        const res = await fetch(url);
        const flashcards = await res.json();

        const corpo = document.getElementById('corpoFlashcards');

        if (flashcards.length === 0) {
            corpo.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum flashcard encontrado</td></tr>';
            return;
        }

        corpo.innerHTML = flashcards.map(f => `
            <tr>
                <td>${f.id}</td>
                <td>${escapeHtml(f.categoria_nome || buscarNomeCategoria(f.categoria_id))}</td>
                <td class="text-truncate-cell">${escapeHtml(f.pergunta)}</td>
                <td class="text-truncate-cell">${escapeHtml(f.resposta)}</td>
                <td>${f.ativo == 1 ? '<span class="badge-ativo">Ativo</span>' : '<span class="badge-inativo">Inativo</span>'}</td>
                <td>
                    <button class="btn btn-acao btn-editar" onclick="editarFlashcard(${f.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-acao btn-excluir" onclick="excluirFlashcard(${f.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Erro ao carregar flashcards:', err);
    }
}

let flashcardsCache = [];

function limparFormFlashcard() {
    document.getElementById('tituloModalFlashcard').textContent = 'Novo Flashcard';
    document.getElementById('formFlashcard').reset();
    document.getElementById('flashId').value = '';
}

async function editarFlashcard(id) {
    try {
        const res = await fetch(`${API}/flashcards.php`);
        flashcardsCache = await res.json();
        const flash = flashcardsCache.find(f => f.id == id);
        if (!flash) return;

        document.getElementById('tituloModalFlashcard').textContent = 'Editar Flashcard';
        document.getElementById('flashId').value = flash.id;
        document.getElementById('flashCategoria').value = flash.categoria_id;
        document.getElementById('flashPergunta').value = flash.pergunta;
        document.getElementById('flashResposta').value = flash.resposta;
        document.getElementById('flashOrdem').value = flash.ordem || 0;

        new bootstrap.Modal(document.getElementById('modalFlashcard')).show();
    } catch (err) {
        mostrarToast('Erro', 'Erro ao carregar flashcard');
    }
}

async function salvarFlashcard(e) {
    e.preventDefault();
    const id = document.getElementById('flashId').value;
    const dados = {
        categoria_id: parseInt(document.getElementById('flashCategoria').value),
        pergunta: document.getElementById('flashPergunta').value,
        resposta: document.getElementById('flashResposta').value,
        ordem: parseInt(document.getElementById('flashOrdem').value) || 0
    };

    const method = id ? 'PUT' : 'POST';
    if (id) dados.id = parseInt(id);

    try {
        const res = await fetch(`${API}/flashcards.php`, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });
        const data = await res.json();

        if (data.sucesso || data.id) {
            bootstrap.Modal.getInstance(document.getElementById('modalFlashcard')).hide();
            mostrarToast('Sucesso', `Flashcard ${id ? 'atualizado' : 'criado'} com sucesso!`);
            carregarFlashcards();
            carregarDashboard();
        } else {
            mostrarToast('Erro', data.erro || 'Erro ao salvar flashcard');
        }
    } catch (err) {
        mostrarToast('Erro', 'Erro de conexão com o servidor');
    }
}

async function excluirFlashcard(id) {
    if (!confirm('Deseja excluir este flashcard?')) return;

    try {
        const res = await fetch(`${API}/flashcards.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const data = await res.json();

        if (data.sucesso) {
            mostrarToast('Sucesso', 'Flashcard excluído com sucesso!');
            carregarFlashcards();
            carregarDashboard();
        }
    } catch (err) {
        mostrarToast('Erro', 'Erro ao excluir flashcard');
    }
}

/* ===== CONTEÚDOS ===== */

let conteudosCache = [];

async function carregarConteudos() {
    try {
        const res = await fetch(`${API}/conteudos.php`);
        conteudosCache = await res.json();

        const corpo = document.getElementById('corpoConteudos');

        if (conteudosCache.length === 0) {
            corpo.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Nenhum conteúdo encontrado</td></tr>';
            return;
        }

        corpo.innerHTML = conteudosCache.map(c => `
            <tr>
                <td>${c.id}</td>
                <td>${escapeHtml(c.categoria_nome || buscarNomeCategoria(c.categoria_id))}</td>
                <td>${escapeHtml(c.titulo)}</td>
                <td class="text-truncate-cell">${escapeHtml(c.texto.substring(0, 100))}...</td>
                <td>${c.ativo == 1 ? '<span class="badge-ativo">Ativo</span>' : '<span class="badge-inativo">Inativo</span>'}</td>
                <td>
                    <button class="btn btn-acao btn-editar" onclick="editarConteudo(${c.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-acao btn-excluir" onclick="excluirConteudo(${c.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Erro ao carregar conteúdos:', err);
    }
}

function limparFormConteudo() {
    document.getElementById('tituloModalConteudo').textContent = 'Novo Conteúdo';
    document.getElementById('formConteudo').reset();
    document.getElementById('contId').value = '';
}

function editarConteudo(id) {
    const cont = conteudosCache.find(c => c.id == id);
    if (!cont) return;

    document.getElementById('tituloModalConteudo').textContent = 'Editar Conteúdo';
    document.getElementById('contId').value = cont.id;
    document.getElementById('contCategoria').value = cont.categoria_id;
    document.getElementById('contTitulo').value = cont.titulo;
    document.getElementById('contTexto').value = cont.texto;
    document.getElementById('contOrdem').value = cont.ordem || 0;

    new bootstrap.Modal(document.getElementById('modalConteudo')).show();
}

async function salvarConteudo(e) {
    e.preventDefault();
    const id = document.getElementById('contId').value;
    const dados = {
        categoria_id: parseInt(document.getElementById('contCategoria').value),
        titulo: document.getElementById('contTitulo').value,
        texto: document.getElementById('contTexto').value,
        ordem: parseInt(document.getElementById('contOrdem').value) || 0
    };

    const method = id ? 'PUT' : 'POST';
    if (id) dados.id = parseInt(id);

    try {
        const res = await fetch(`${API}/conteudos.php`, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });
        const data = await res.json();

        if (data.sucesso || data.id) {
            bootstrap.Modal.getInstance(document.getElementById('modalConteudo')).hide();
            mostrarToast('Sucesso', `Conteúdo ${id ? 'atualizado' : 'criado'} com sucesso!`);
            carregarConteudos();
            carregarDashboard();
        } else {
            mostrarToast('Erro', data.erro || 'Erro ao salvar conteúdo');
        }
    } catch (err) {
        mostrarToast('Erro', 'Erro de conexão com o servidor');
    }
}

async function excluirConteudo(id) {
    if (!confirm('Deseja excluir este conteúdo?')) return;

    try {
        const res = await fetch(`${API}/conteudos.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const data = await res.json();

        if (data.sucesso) {
            mostrarToast('Sucesso', 'Conteúdo excluído com sucesso!');
            carregarConteudos();
            carregarDashboard();
        }
    } catch (err) {
        mostrarToast('Erro', 'Erro ao excluir conteúdo');
    }
}

/* ===== UTILITÁRIOS ===== */

function capitalizar(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatarData(data) {
    if (!data) return '-';
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR');
}

function buscarNomeCategoria(id) {
    const cat = categoriasCache.find(c => c.id == id);
    return cat ? cat.nome : '-';
}

function mostrarToast(titulo, mensagem) {
    document.getElementById('toastTitulo').textContent = titulo;
    document.getElementById('toastMensagem').textContent = mensagem;
    const toast = new bootstrap.Toast(document.getElementById('toastNotificacao'));
    toast.show();
}
