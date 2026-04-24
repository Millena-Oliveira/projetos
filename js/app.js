/**
 * Roma Antiga - JavaScript Principal
 */

document.addEventListener('DOMContentLoaded', () => {
    const flashcardModal = new bootstrap.Modal(document.getElementById('flashcardModal'));
    let flashcardsData = [];
    let conteudosData = [];
    let currentIndex = 0;

    // Carregar total de flashcards para a seção "Sobre"
    carregarTotalFlashcards();

    // Eventos nos cards de categoria
    document.querySelectorAll('.categoria-card').forEach(card => {
        card.addEventListener('click', () => {
            // Exige login para estudar: se não logado, vai para a tela de login
            if (!window.USUARIO_LOGADO) {
                window.location.href = 'login.php';
                return;
            }
            const categoriaId = card.dataset.id;
            const categoriaNome = card.querySelector('.categoria-nome').textContent;
            abrirCategoria(categoriaId, categoriaNome);
        });
    });

    // Evento de virar flashcard (registra progresso quando vira para a resposta)
    document.getElementById('flashcard').addEventListener('click', () => {
        const el = document.getElementById('flashcard');
        el.classList.toggle('flipped');
        if (el.classList.contains('flipped')) {
            registrarProgresso();
        }
    });

    // Navegação
    document.getElementById('btnAnterior').addEventListener('click', () => navegarFlashcard(-1));
    document.getElementById('btnProximo').addEventListener('click', () => navegarFlashcard(1));

    // Navegação por teclado
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('flashcardModal');
        if (!modal.classList.contains('show')) return;

        if (e.key === 'ArrowLeft') navegarFlashcard(-1);
        if (e.key === 'ArrowRight') navegarFlashcard(1);
        if (e.key === ' ') {
            e.preventDefault();
            const el = document.getElementById('flashcard');
            el.classList.toggle('flipped');
            if (el.classList.contains('flipped')) {
                registrarProgresso();
            }
        }
    });

    /**
     * Registra no back-end que o flashcard atual foi estudado
     */
    async function registrarProgresso() {
        if (!window.USUARIO_LOGADO) return;
        const card = flashcardsData[currentIndex];
        if (!card || !card.id) return;
        try {
            await fetch('api/progresso.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ flashcard_id: card.id })
            });
        } catch (e) {
            // silencioso
        }
    }

    // Tabs
    document.querySelectorAll('#tabsConteudo .nav-link').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('#tabsConteudo .nav-link').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const tabName = tab.dataset.tab;
            document.getElementById('flashcardsContainer').style.display = tabName === 'flashcards' ? 'block' : 'none';
            document.getElementById('conteudoContainer').style.display = tabName === 'conteudo' ? 'block' : 'none';
        });
    });

    // Smooth scroll para links da navbar
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            // Ignorar âncoras vazias (ex.: href="#") para não quebrar outros handlers (Sair etc.)
            if (!href || href === '#') return;
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /**
     * Abre uma categoria: carrega flashcards e conteúdo
     */
    async function abrirCategoria(categoriaId, categoriaNome) {
        document.getElementById('modalCategoriaNome').textContent = categoriaNome;

        // Resetar para tab de flashcards
        document.querySelectorAll('#tabsConteudo .nav-link').forEach(t => t.classList.remove('active'));
        document.querySelector('#tabsConteudo .nav-link[data-tab="flashcards"]').classList.add('active');
        document.getElementById('flashcardsContainer').style.display = 'block';
        document.getElementById('conteudoContainer').style.display = 'none';

        // Loading
        document.getElementById('flashcardPergunta').textContent = 'Carregando...';
        document.getElementById('flashcardResposta').textContent = '';
        document.getElementById('conteudoTexto').innerHTML = '<div class="text-center py-4"><div class="spinner-roma"></div></div>';

        flashcardModal.show();

        // Carregar dados
        try {
            const [flashRes, contRes] = await Promise.all([
                fetch(`api/flashcards.php?categoria_id=${categoriaId}`),
                fetch(`api/conteudos.php?categoria_id=${categoriaId}`)
            ]);

            flashcardsData = await flashRes.json();
            conteudosData = await contRes.json();

            currentIndex = 0;
            document.getElementById('flashcard').classList.remove('flipped');
            atualizarFlashcard();
            renderizarConteudo();
        } catch (error) {
            document.getElementById('flashcardPergunta').textContent = 'Erro ao carregar dados.';
            document.getElementById('conteudoTexto').innerHTML = '<p>Erro ao carregar conteúdo.</p>';
        }
    }

    /**
     * Atualiza o flashcard exibido
     */
    function atualizarFlashcard() {
        if (flashcardsData.length === 0) {
            document.getElementById('flashcardPergunta').textContent = 'Nenhum flashcard disponível para esta categoria.';
            document.getElementById('flashcardResposta').textContent = '';
            document.getElementById('flashcardAtual').textContent = '0';
            document.getElementById('flashcardTotal').textContent = '0';
            document.getElementById('btnAnterior').disabled = true;
            document.getElementById('btnProximo').disabled = true;
            return;
        }

        const card = flashcardsData[currentIndex];
        document.getElementById('flashcardPergunta').textContent = card.pergunta;
        document.getElementById('flashcardResposta').textContent = card.resposta;
        document.getElementById('flashcardAtual').textContent = currentIndex + 1;
        document.getElementById('flashcardTotal').textContent = flashcardsData.length;

        document.getElementById('btnAnterior').disabled = currentIndex === 0;
        document.getElementById('btnProximo').disabled = currentIndex === flashcardsData.length - 1;

        // Resetar flip
        document.getElementById('flashcard').classList.remove('flipped');
    }

    /**
     * Navega entre flashcards
     */
    function navegarFlashcard(direcao) {
        const novoIndex = currentIndex + direcao;
        if (novoIndex >= 0 && novoIndex < flashcardsData.length) {
            currentIndex = novoIndex;
            atualizarFlashcard();
        }
    }

    /**
     * Renderiza o conteúdo detalhado
     */
    function renderizarConteudo() {
        const container = document.getElementById('conteudoTexto');

        if (conteudosData.length === 0) {
            container.innerHTML = '<p>Nenhum conteúdo disponível para esta categoria.</p>';
            return;
        }

        let html = '';
        conteudosData.forEach(conteudo => {
            html += `<h4>${escapeHtml(conteudo.titulo)}</h4>`;
            const paragrafos = conteudo.texto.split('\n').filter(p => p.trim());
            paragrafos.forEach(p => {
                html += `<p>${escapeHtml(p)}</p>`;
            });
        });

        container.innerHTML = html;
    }

    /**
     * Carrega o total de flashcards
     */
    async function carregarTotalFlashcards() {
        try {
            const res = await fetch('api/flashcards.php?total=1');
            const data = await res.json();
            const el = document.getElementById('totalFlashcards');
            if (el && data.total !== undefined) {
                el.textContent = data.total;
            }
        } catch (e) {
            // silencioso
        }
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
