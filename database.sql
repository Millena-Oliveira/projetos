-- =====================================================
-- BANCO DE DADOS: Roma Antiga - Sistema de Flashcards
-- =====================================================

CREATE DATABASE IF NOT EXISTS roma_antiga
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE roma_antiga;

-- =====================================================
-- TABELA: categorias
-- Armazena os temas/categorias de estudo
-- =====================================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    icone VARCHAR(50) DEFAULT 'fas fa-landmark',
    cor VARCHAR(7) DEFAULT '#8B4513',
    ativo TINYINT(1) DEFAULT 1,
    ordem INT DEFAULT 0,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: flashcards
-- Armazena os flashcards com pergunta e resposta
-- =====================================================
CREATE TABLE IF NOT EXISTS flashcards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    pergunta TEXT NOT NULL,
    resposta TEXT NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    ordem INT DEFAULT 0,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: conteudos
-- Armazena o conteúdo detalhado de cada categoria
-- =====================================================
CREATE TABLE IF NOT EXISTS conteudos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    texto TEXT NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    ordem INT DEFAULT 0,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: administradores
-- Armazena os usuários do painel administrativo
-- =====================================================
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DADOS INICIAIS
-- =====================================================

-- Administrador padrão (senha: admin123)
INSERT INTO administradores (nome, email, senha) VALUES
('Administrador', 'admin@romaantiga.com', '$2y$10$AAcJniGQTjfLsnQoZoDyvuk/Mo08QmCPdLu0JeYw1szzhfRMbu8om');

-- Categorias
INSERT INTO categorias (nome, descricao, icone, cor, ordem) VALUES
('Fundação de Roma', 'A lendária fundação de Roma, de Rômulo e Remo até a formação da cidade que dominaria o mundo antigo.', 'fas fa-archway', '#8B4513', 1),
('República Romana', 'O período republicano, suas instituições políticas, o Senado e as lutas entre patrícios e plebeus.', 'fas fa-balance-scale', '#B8860B', 2),
('Império Romano', 'A era dos imperadores, de Augusto à queda de Roma, passando pela Pax Romana e as grandes conquistas.', 'fas fa-crown', '#DAA520', 3),
('Exército Romano', 'A máquina militar mais poderosa do mundo antigo: legiões, táticas, armas e conquistas territoriais.', 'fas fa-shield-alt', '#CD5C5C', 4),
('Sociedade e Cultura', 'O cotidiano romano: classes sociais, educação, religião, arte, literatura e entretenimento.', 'fas fa-theater-masks', '#9370DB', 5),
('Arquitetura e Engenharia', 'Coliseu, aquedutos, estradas e templos: as obras que definiram a engenharia romana.', 'fas fa-gopuram', '#2E8B57', 6),
('Religião e Mitologia', 'Os deuses romanos, rituais, templos e a transição para o cristianismo no Império.', 'fas fa-sun', '#FF8C00', 7),
('Queda do Império', 'As causas da queda de Roma: invasões bárbaras, crises internas e o fim de uma era.', 'fas fa-skull-crossbones', '#8B0000', 8);

-- Flashcards
INSERT INTO flashcards (categoria_id, pergunta, resposta, ordem) VALUES
-- Fundação de Roma
(1, 'Quem foram Rômulo e Remo?', 'Rômulo e Remo foram irmãos gêmeos, filhos do deus Marte e da vestal Reia Sílvia. Segundo a lenda, foram abandonados no rio Tibre e amamentados por uma loba (Lupa Capitolina). Rômulo fundou Roma em 753 a.C. após matar Remo em uma disputa.', 1),
(1, 'Quando Roma foi fundada segundo a tradição?', 'Roma foi fundada em 21 de abril de 753 a.C., de acordo com a tradição romana. A data é chamada de "Natale di Roma" (Nascimento de Roma) e era celebrada anualmente pelos romanos.', 2),
(1, 'O que eram as Sete Colinas de Roma?', 'As Sete Colinas de Roma são: Palatino, Aventino, Capitolino, Quirinal, Viminal, Esquilino e Célio. O Palatino era a mais importante, pois segundo a lenda, foi ali que Rômulo fundou a cidade. A palavra "palácio" vem de Palatino.', 3),
(1, 'Quem foram os sete reis de Roma?', 'Os sete reis lendários foram: Rômulo (fundador), Numa Pompílio (religioso), Tulo Hostílio (guerreiro), Anco Márcio (construtor), Tarquínio Prisco (etrusco), Sérvio Túlio (reformador) e Tarquínio, o Soberbo (último rei, derrubado em 509 a.C.).', 4),

-- República Romana
(2, 'O que era o Senado Romano?', 'O Senado era a principal instituição política da República Romana, composto inicialmente por 300 membros (depois 600). Os senadores eram escolhidos entre ex-magistrados e tinham poder de aconselhar, aprovar leis e controlar finanças públicas.', 1),
(2, 'Qual a diferença entre patrícios e plebeus?', 'Os patrícios eram a aristocracia romana, famílias ricas e tradicionais que controlavam o poder político. Os plebeus eram o restante da população — agricultores, artesãos e comerciantes. Após séculos de luta, os plebeus conquistaram direitos políticos, como os Tribunos da Plebe.', 2),
(2, 'Quem foi Júlio César?', 'Caio Júlio César (100-44 a.C.) foi um general e político romano. Conquistou a Gália (atual França), cruzou o Rubicão com seu exército e se tornou ditador perpétuo de Roma. Foi assassinado no Senado nos Idos de Março (15 de março de 44 a.C.) por senadores liderados por Bruto e Cássio.', 3),
(2, 'O que foram as Guerras Púnicas?', 'As Guerras Púnicas foram três conflitos entre Roma e Cartago (264-146 a.C.) pelo controle do Mediterrâneo. Na Segunda Guerra Púnica, o general cartaginês Aníbal cruzou os Alpes com elefantes. Roma venceu as três guerras e destruiu Cartago em 146 a.C.', 4),

-- Império Romano
(3, 'Quem foi o primeiro imperador de Roma?', 'Otávio Augusto (63 a.C. - 14 d.C.) foi o primeiro imperador romano. Sobrinho-neto e filho adotivo de Júlio César, derrotou Marco Antônio na Batalha de Ácio (31 a.C.) e recebeu o título de "Augusto" do Senado em 27 a.C., iniciando o Principado.', 1),
(3, 'O que foi a Pax Romana?', 'A Pax Romana ("Paz Romana") foi um período de aproximadamente 200 anos (27 a.C. - 180 d.C.) de relativa paz e estabilidade no Império Romano. Durante esse tempo, o comércio floresceu, cidades cresceram e a cultura romana se espalhou por todo o Mediterrâneo.', 2),
(3, 'Quem foi Nero?', 'Nero (37-68 d.C.) foi o quinto imperador romano, conhecido por sua tirania e extravagância. É famoso pelo Grande Incêndio de Roma em 64 d.C., após o qual perseguiu os cristãos. Mandou matar sua própria mãe, Agripina, e se suicidou em 68 d.C.', 3),
(3, 'O que foi a divisão do Império Romano?', 'Em 285 d.C., o imperador Diocleciano dividiu o Império em Ocidente e Oriente para facilitar a administração. Em 395 d.C., Teodósio I oficializou a divisão. O Império do Ocidente caiu em 476 d.C., enquanto o Império do Oriente (Bizantino) sobreviveu até 1453 d.C.', 4),

-- Exército Romano
(4, 'O que era uma legião romana?', 'A legião era a principal unidade militar romana, composta por cerca de 5.000 a 6.000 soldados (legionários). Era dividida em coortes (10 por legião) e centúrias (6 por coorte, cada uma com 80 homens). A disciplina e organização das legiões foram fundamentais para as conquistas de Roma.', 1),
(4, 'Quais eram as principais armas dos legionários?', 'O legionário romano usava: gladius (espada curta), pilum (lança de arremesso), scutum (grande escudo retangular), lorica segmentata (armadura de placas) e galea (capacete). O pilum era projetado para entortar ao atingir o escudo inimigo, inutilizando-o.', 2),
(4, 'O que era a formação tartaruga (testudo)?', 'A testudo ("tartaruga") era uma formação defensiva na qual os legionários se agrupavam e erguiam seus escudos acima das cabeças e nas laterais, formando uma carapaça protetora. Era usada para se aproximar de muralhas inimigas sob chuva de flechas e pedras.', 3),

-- Sociedade e Cultura
(5, 'Como era a educação em Roma?', 'A educação romana era dividida em três fases: o ludus (escola primária, dos 7 aos 11 anos), o grammaticus (literatura e gramática, dos 12 aos 15) e o rhetor (retórica e oratória, dos 16 aos 18). Apenas famílias ricas podiam pagar pela educação completa. Os professores muitas vezes eram escravos gregos.', 1),
(5, 'O que eram os gladiadores?', 'Os gladiadores eram combatentes que lutavam em arenas para entretenimento público. A maioria era composta por escravos, prisioneiros de guerra ou condenados, mas alguns eram voluntários. Lutavam em pares ou grupos, usando diferentes tipos de armas e armaduras. Os combates eram realizados em anfiteatros como o Coliseu.', 2),
(5, 'Como era organizada a sociedade romana?', 'A sociedade romana era dividida em: patrícios (aristocracia), equestres (cavaleiros, classe média alta), plebeus (cidadãos comuns), libertos (ex-escravos) e escravos (sem direitos). A cidadania romana dava direitos políticos e legais. O imperador Caracala estendeu a cidadania a todos os homens livres do Império em 212 d.C.', 3),

-- Arquitetura e Engenharia
(6, 'O que foi o Coliseu?', 'O Coliseu (Anfiteatro Flávio) é o maior anfiteatro já construído, com capacidade para 50.000 a 80.000 espectadores. Inaugurado em 80 d.C. pelo imperador Tito, sediava combates de gladiadores, caças a animais e simulações de batalhas navais. Tinha um sistema de cobertura (velarium) e corredores subterrâneos (hipogeu).', 1),
(6, 'Como funcionavam os aquedutos romanos?', 'Os aquedutos eram estruturas para transportar água de fontes distantes até as cidades, usando apenas a gravidade (sem bombas). Roma tinha 11 aquedutos que forneciam cerca de 1 milhão de metros cúbicos de água por dia. O Aqua Appia, construído em 312 a.C., foi o primeiro. Os aquedutos abasteciam termas, fontes e casas.', 2),
(6, 'Por que as estradas romanas eram tão importantes?', 'As estradas romanas formavam uma rede de mais de 400.000 km conectando todo o Império. Eram construídas em camadas (cascalho, areia, pedras) com drenagem lateral. A Via Ápia, construída em 312 a.C., foi a primeira grande estrada. A frase "Todos os caminhos levam a Roma" reflete a importância dessa rede.', 3),

-- Religião e Mitologia
(7, 'Quais eram os principais deuses romanos?', 'Os principais deuses romanos eram: Júpiter (rei dos deuses, equivalente a Zeus), Juno (rainha dos deuses, esposa de Júpiter), Marte (deus da guerra), Vênus (deusa do amor), Minerva (deusa da sabedoria), Neptuno (deus do mar), Mercúrio (mensageiro dos deuses) e Apolo (deus do sol e das artes).', 1),
(7, 'Como o cristianismo se espalhou no Império Romano?', 'O cristianismo surgiu na Judeia no século I d.C. e se espalhou pelas rotas comerciais romanas. Inicialmente perseguido (Nero, Diocleciano), foi legalizado pelo Édito de Milão (313 d.C.) do imperador Constantino. Em 380 d.C., Teodósio I tornou o cristianismo a religião oficial do Império, proibindo os cultos pagãos.', 2),
(7, 'O que eram os templos romanos?', 'Os templos romanos eram dedicados a deuses específicos e serviam como morada da divindade. O Panteão, construído por Adriano (125 d.C.), é o mais bem preservado — sua cúpula de concreto com 43 metros de diâmetro e o óculo central são obras-primas da engenharia. Os templos tinham sacerdotes (pontífices) e vestais.', 3),

-- Queda do Império
(8, 'Quando caiu o Império Romano do Ocidente?', 'O Império Romano do Ocidente caiu em 4 de setembro de 476 d.C., quando o líder germânico Odoacro depôs o último imperador, Rômulo Augusto (chamado ironicamente de "Augustinho"). Este evento marca tradicionalmente o fim da Idade Antiga e o início da Idade Média.', 1),
(8, 'Quais foram as principais causas da queda de Roma?', 'As principais causas foram: invasões bárbaras (visigodos, vândalos, hunos), crises econômicas e inflação, enfraquecimento do exército (uso de mercenários bárbaros), instabilidade política (muitos imperadores assassinados), divisão do Império, epidemias e o declínio da moral e valores cívicos romanos.', 2),
(8, 'Quem foram os principais invasores de Roma?', 'Os principais invasores foram: Alarico I (visigodo, saqueou Roma em 410 d.C.), Genserico (vândalo, saqueou Roma em 455 d.C.), Átila (huno, "Flagelo de Deus", invadiu a Itália em 452 d.C.) e Odoacro (hérulo/germânico, depôs o último imperador em 476 d.C.).', 3);

-- Conteúdos detalhados por categoria
INSERT INTO conteudos (categoria_id, titulo, texto, ordem) VALUES
(1, 'A Lenda de Rômulo e Remo', 'Segundo a mitologia romana, Rômulo e Remo eram filhos gêmeos do deus Marte e da vestal Reia Sílvia, princesa da cidade de Alba Longa. O rei Amúlio, tio de Reia Sílvia, ordenou que os bebês fossem jogados no rio Tibre para eliminar ameaças ao seu trono.\n\nOs gêmeos sobreviveram e foram encontrados por uma loba (Lupa Capitolina), que os amamentou em uma gruta chamada Lupercal, no monte Palatino. Mais tarde, foram encontrados e criados pelo pastor Fáustulo e sua esposa Aca Larência.\n\nAo crescerem e descobrirem sua verdadeira origem, os irmãos mataram o usurpador Amúlio e restauraram o trono para seu avô, Numitor. Decidiram então fundar uma nova cidade. Porém, uma disputa sobre o local da fundação levou Rômulo a matar Remo. Rômulo fundou Roma em 21 de abril de 753 a.C. no monte Palatino e se tornou seu primeiro rei.', 1),

(2, 'A República Romana e suas Instituições', 'A República Romana (509-27 a.C.) nasceu quando os romanos expulsaram o último rei etrusco, Tarquínio, o Soberbo. O sistema republicano foi criado para evitar a tirania de um único governante.\n\nAs principais instituições eram:\n\n• Senado: Conselho de anciãos (inicialmente 300 membros) que aconselhava os magistrados, controlava finanças e política externa. Era o verdadeiro centro de poder da República.\n\n• Cônsules: Dois cônsules eleitos anualmente detinham o poder executivo e militar. Tinham poder de veto um sobre o outro, evitando abuso de poder.\n\n• Tribunos da Plebe: Criados em 494 a.C. após a "Secessão da Plebe", protegiam os direitos dos plebeus e podiam vetar decisões do Senado.\n\n• Assembleias Populares: Comícios Centuriatos (elegiam cônsules e votavam sobre guerra), Comícios Tributos (votavam leis) e Concílio da Plebe.\n\n• Ditador: Magistrado extraordinário nomeado em emergências, com poderes absolutos por no máximo 6 meses. Júlio César quebrou essa regra ao se declarar ditador perpétuo.', 1),

(3, 'O Principado e a Era dos Imperadores', 'O Império Romano começou quando Otávio recebeu o título de "Augusto" em 27 a.C. e se tornou o primeiro imperador. Ao contrário de César, Augusto manteve a aparência da República enquanto concentrava todo o poder real em suas mãos.\n\nPrincipais dinastias e períodos:\n\n• Dinastia Júlio-Claudiana (27 a.C. - 68 d.C.): Augusto, Tibério, Calígula, Cláudio e Nero. Período de consolidação do Império.\n\n• Dinastia Flaviana (69-96 d.C.): Vespasiano, Tito e Domiciano. Construção do Coliseu.\n\n• Os Cinco Bons Imperadores (96-180 d.C.): Nerva, Trajano, Adriano, Antonino Pio e Marco Aurélio. Auge do Império Romano, maior extensão territorial sob Trajano.\n\n• Crise do Século III (235-284 d.C.): Período de instabilidade com mais de 50 imperadores em 50 anos, invasões bárbaras e crise econômica.\n\n• Dominato (284-476 d.C.): Diocleciano reorganizou o Império com a Tetrarquia. Constantino legalizou o cristianismo e fundou Constantinopla.', 1),

(4, 'A Máquina Militar Romana', 'O exército romano foi a força militar mais organizada e eficiente do mundo antigo. Sua disciplina, treinamento e inovação tática permitiram a Roma conquistar e manter um vasto império por séculos.\n\nOrganização:\n• Legião: ~5.000 soldados, dividida em 10 coortes\n• Coorte: ~480 soldados, dividida em 6 centúrias\n• Centúria: ~80 soldados, comandada por um centurião\n• Contubernium: grupo de 8 soldados que compartilhavam uma barraca\n\nEquipamento do legionário:\n• Gladius: espada curta e letal para combate corpo a corpo\n• Pilum: lança de arremesso projetada para entortar ao impacto\n• Scutum: grande escudo retangular curvo\n• Lorica segmentata: armadura de placas metálicas articuladas\n• Caligae: sandálias militares resistentes\n\nTáticas famosas:\n• Testudo (tartaruga): formação defensiva com escudos\n• Triplex acies: três linhas de combate\n• Cerco: uso de catapultas, torres de assédio e aríetes\n\nOs legionários também eram engenheiros — construíam estradas, pontes, aquedutos e acampamentos fortificados (castra) em cada parada.', 1),

(5, 'Vida Cotidiana em Roma', 'A sociedade romana era complexa e estratificada. O dia a dia variava enormemente conforme a classe social.\n\nClasses sociais:\n• Patrícios: aristocracia, grandes proprietários de terras\n• Equestres: classe média alta, comerciantes ricos\n• Plebeus: cidadãos comuns, artesãos, pequenos comerciantes\n• Libertos: ex-escravos que conquistaram a liberdade\n• Escravos: sem direitos, propriedade de seus donos\n\nMoradia:\n• Domus: casas particulares dos ricos, com átrio e peristilo\n• Insulae: prédios de apartamentos (até 7 andares) dos plebeus\n\nAlimentação:\n• Café da manhã (ientaculum): pão, queijo, frutas\n• Almoço (prandium): leve, frio\n• Jantar (cena): principal refeição, podendo durar horas nos banquetes\n• Garum: molho de peixe fermentado, condimento favorito\n\nEntretenimento:\n• Termas: banhos públicos, centros de socialização\n• Anfiteatros: combates de gladiadores\n• Circo Máximo: corridas de bigas (250.000 espectadores)\n• Teatro: comédias e tragédias\n\nA frase "Panem et circenses" (Pão e circo) refletia a política de manter a plebe satisfeita com comida gratuita e espetáculos.', 1),

(6, 'As Grandes Obras da Engenharia Romana', 'Os romanos foram os maiores engenheiros da antiguidade. Suas obras resistiram milênios e muitas ainda estão em uso.\n\nConcreto Romano:\nOs romanos inventaram o opus caementicium, mistura de cal, pozolana (cinza vulcânica) e pedras. Este concreto é tão resistente que estruturas como o Panteão permanecem intactas após 2.000 anos. Pesquisadores descobriram que a água do mar fortalece o concreto romano.\n\nAquedutos:\nRoma tinha 11 aquedutos que transportavam ~1 milhão de m³ de água diariamente usando apenas a gravidade. O Pont du Gard (França) tem 49 metros de altura. A água abastecia termas, fontes públicas e casas dos ricos.\n\nEstradas:\nA rede de estradas romanas cobria mais de 400.000 km. Eram construídas em camadas: statumen (fundação), rudus (cascalho), nucleus (areia e cal) e summa crusta (pedras de pavimentação). Muitas estradas europeias modernas seguem traçados romanos.\n\nEdifícios notáveis:\n• Coliseu: anfiteatro para 50.000-80.000 pessoas\n• Panteão: cúpula de 43m, maior cúpula de concreto não armado do mundo\n• Termas de Caracala: complexo de banhos para 1.600 pessoas\n• Fórum Romano: centro político, religioso e comercial', 1),

(7, 'Deuses e Religião em Roma', 'A religião romana evoluiu de práticas animistas primitivas para um panteão complexo influenciado pelos gregos, culminando na adoção do cristianismo.\n\nPrincipais deuses (equivalentes gregos):\n• Júpiter (Zeus): rei dos deuses, deus do céu e do trovão\n• Juno (Hera): rainha dos deuses, protetora do casamento\n• Marte (Ares): deus da guerra, pai de Rômulo e Remo\n• Vênus (Afrodite): deusa do amor e da beleza\n• Minerva (Atena): deusa da sabedoria e estratégia\n• Neptuno (Poseidon): deus dos mares\n• Mercúrio (Hermes): mensageiro dos deuses\n• Apolo: deus do sol, música e artes (mesmo nome em grego)\n• Diana (Ártemis): deusa da caça e da lua\n• Vulcano (Hefesto): deus do fogo e da forja\n\nPráticas religiosas:\n• Os romanos acreditavam em augúrios — sinais dos deuses (voo de pássaros, entranhas de animais)\n• Vestais: sacerdotisas virgens que mantinham o fogo sagrado de Vesta\n• Pontifex Maximus: sumo sacerdote, título depois adotado pelo papa cristão\n\nTransição para o cristianismo:\n313 d.C. — Édito de Milão (Constantino legaliza o cristianismo)\n380 d.C. — Édito de Tessalônica (Teodósio torna o cristianismo religião oficial)\n391 d.C. — Proibição dos cultos pagãos', 1),

(8, 'O Fim de Roma e seu Legado', 'A queda do Império Romano do Ocidente em 476 d.C. foi resultado de uma combinação de fatores internos e externos que se acumularam ao longo de séculos.\n\nCausas internas:\n• Instabilidade política: entre 235 e 284 d.C., mais de 50 imperadores governaram, muitos assassinados\n• Crise econômica: inflação descontrolada, aumento de impostos, declínio do comércio\n• Enfraquecimento militar: dependência crescente de mercenários bárbaros\n• Epidemias: a Peste Antonina (165 d.C.) e a Peste de Cipriano (250 d.C.) dizimaram a população\n• Divisão do Império: a separação entre Ocidente e Oriente em 395 d.C. enfraqueceu o lado ocidental\n\nCausas externas:\n• Invasões germânicas: visigodos, ostrogodos, vândalos, francos, saxões\n• Hunos: sob Átila, pressionaram tribos germânicas para dentro do Império\n• Saque de Roma: 410 d.C. (Alarico/visigodos) e 455 d.C. (Genserico/vândalos)\n\n476 d.C.: Odoacro depõe Rômulo Augusto, o último imperador do Ocidente.\n\nLegado de Roma:\n• Direito romano: base dos sistemas jurídicos ocidentais\n• Latim: origem das línguas românicas (português, espanhol, francês, italiano)\n• Arquitetura e engenharia\n• Conceitos de cidadania e república\n• O alfabeto latino\n• O calendário (Júlio César/calendário juliano)', 1);
