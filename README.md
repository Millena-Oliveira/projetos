# Roma Antiga - Sistema de Flashcards

Sistema educacional para estudo de **História e Civilização da Roma Antiga** através de flashcards interativos. Desenvolvido para alunos do ensino fundamental e médio.

## Tecnologias

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend:** PHP 7.4+
- **Banco de Dados:** MySQL 5.7+
- **Ícones:** Font Awesome 6
- **Fontes:** Google Fonts (Cinzel, Lora, Inter)

## Estrutura de Arquivos

```
roma-antiga/
├── index.php              # Página principal (flashcards do aluno)
├── conexao.php            # Conexão com banco de dados MySQL
├── database.sql           # Script SQL para criar o banco de dados
├── README.md              # Este arquivo
│
├── css/
│   ├── estilo.css         # Estilos da interface do aluno
│   └── admin.css          # Estilos do painel administrativo
│
├── js/
│   ├── app.js             # JavaScript da interface do aluno
│   └── admin.js           # JavaScript do painel administrativo
│
├── api/
│   ├── categorias.php     # API REST para categorias
│   ├── flashcards.php     # API REST para flashcards
│   ├── conteudos.php      # API REST para conteúdos
│   └── login.php          # API de autenticação
│
└── admin/
    └── index.php          # Painel administrativo
```

## Instalação

### 1. Banco de Dados

1. Abra o phpMyAdmin ou terminal MySQL
2. Execute o arquivo `database.sql`:

```sql
SOURCE /caminho/para/database.sql;
```

Ou copie e cole o conteúdo do arquivo `database.sql` no phpMyAdmin.

### 2. Configurar Conexão

Edite o arquivo `conexao.php` com as credenciais do seu banco de dados:

```php
$host = 'localhost';
$dbname = 'roma_antiga';
$usuario = 'root';
$senha = '';  // Sua senha do MySQL
```

### 3. Servidor

Coloque os arquivos na pasta do seu servidor web (ex: `htdocs` no XAMPP ou `www` no WAMP).

Acesse:
- **Site do aluno:** `http://localhost/roma-antiga/`
- **Painel administrativo:** `http://localhost/roma-antiga/admin/`

## Credenciais Padrão do Admin

- **Email:** admin@romaantiga.com
- **Senha:** admin123

## Login e Cadastro

- A página `login.php` funciona como tela de **login unificada**: alunos e administradores usam o mesmo formulário.
- Ao fazer login, o usuário é redirecionado automaticamente:
  - **Administrador** → `admin/index.php` (painel administrativo)
  - **Aluno** → `painel.php` (painel do usuário)
- `registro.php` é a página **pública** de cadastro de alunos.
- Os **temas** (categorias) ficam visíveis em `index.php` para qualquer visitante, mas para **estudar flashcards é necessário estar logado** — ao clicar em um tema sem estar logado, o usuário é redirecionado para `login.php`.
- O **painel do aluno** (`painel.php`) mostra estatísticas de progresso, progresso por tema e histórico dos últimos flashcards estudados.

## Funcionalidades

### Interface do Aluno
- 8 categorias temáticas sobre Roma Antiga
- Flashcards interativos com animação de virar (pergunta/resposta)
- Conteúdo resumido e explicativo por tema
- Navegação por teclado (setas e espaço)
- Design responsivo para desktop e mobile
- Interface moderna e elegante (não infantil)

### Painel Administrativo
- Login com autenticação segura (bcrypt)
- Dashboard com estatísticas
- CRUD completo de categorias
- CRUD completo de flashcards
- CRUD completo de conteúdos
- Todas as informações são salvas no banco de dados MySQL
- Interface limpa e profissional

## Conteúdos Incluídos

1. **Fundação de Roma** - Rômulo e Remo, as Sete Colinas
2. **República Romana** - Senado, cônsules, guerras púnicas
3. **Império Romano** - Augusto, Pax Romana, imperadores
4. **Exército Romano** - Legiões, táticas, equipamento
5. **Sociedade e Cultura** - Classes sociais, educação, entretenimento
6. **Arquitetura e Engenharia** - Coliseu, aquedutos, estradas
7. **Religião e Mitologia** - Deuses romanos, cristianismo
8. **Queda do Império** - Invasões bárbaras, causas, legado
