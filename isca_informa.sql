-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15-Jan-2026 às 18:12
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `isca_informa`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `artigos`
--

CREATE TABLE `artigos` (
  `artigo_id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `conteudo` text NOT NULL,
  `resumo` varchar(300) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `imagem_url` varchar(500) NOT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `tempo_leitura` varchar(20) NOT NULL,
  `visualizacoes` int(11) DEFAULT 0,
  `data_publicacao` date NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `autor` varchar(255) DEFAULT '',
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `artigos`
--

INSERT INTO `artigos` (`artigo_id`, `titulo`, `conteudo`, `resumo`, `categoria_id`, `imagem_url`, `destaque`, `tempo_leitura`, `visualizacoes`, `data_publicacao`, `data_criacao`, `data_atualizacao`, `autor`, `ativo`) VALUES
(1, 'CICA 2025 - Congresso Internacional de Contabilidade e Administração', 'A edição de 2025 do CICA reunirá investigadores internacionais para debater a sustentabilidade no relato financeiro e as novas normas de ESG. O evento decorrerá no auditório principal do ISCA-UA.', 'O congresso de referência do ISCA-UA regressa em 2025 focado na sustentabilidade e ética empresarial.', 14, 'https://www.ua.pt/file/78229', 1, '6 min', 3, '2025-10-15', '2025-12-18 11:49:16', '2026-01-14 17:02:52', '', 1),
(2, 'IV Conferência Internacional em Sistemas de Informação', 'Análise das tendências tecnológicas para 2025, com foco em Cibersegurança e Governação de Dados nas organizações públicas e privadas.', 'Especialistas debatem a segurança de dados e a transformação digital no ISCA em maio de 2025.', 14, 'https://www.ua.pt/file/75000', 0, '4 min', 1, '2025-05-20', '2025-12-18 11:49:16', '2026-01-14 17:02:55', '', 1),
(3, 'ISCA Career Days 2025', 'A feira de emprego anual onde as \"Big Four\" e outras empresas parceiras recrutam diretamente alunos de Contabilidade, Gestão e Marketing.', 'O maior evento de empregabilidade do ISCA acontece em março de 2025. Prepare o seu CV.', 14, 'https://www.ua.pt/file/76000', 1, '5 min', 0, '2025-03-12', '2025-12-18 11:49:50', '2025-12-18 11:49:50', '', 1),
(4, 'Semana Aberta ISCA 2025', 'O ISCA abre as portas aos alunos do ensino secundário para dar a conhecer a oferta formativa de licenciaturas e CTeSP.', 'Venha conhecer o ISCA-UA. Workshops, visitas guiadas e contacto com o mercado de trabalho.', 14, 'https://www.ua.pt/file/74000', 0, '3 min', 0, '2025-04-05', '2025-12-18 11:49:50', '2025-12-18 11:49:50', '', 1),
(5, 'Prémio de Mérito Académico 2024/2025', 'Cerimónia de distinção dos estudantes com as melhores médias de curso e prémios por disciplina patrocinados por empresas parceiras.', 'Reconhecimento da excelência: o ISCA premeia os melhores alunos do ano letivo.', 13, 'https://www.ua.pt/file/73000', 0, '2 min', 1, '2025-11-20', '2025-12-18 11:50:22', '2026-01-14 16:39:34', '', 1),
(6, 'Abertura de Candidaturas: Bolsas de Mérito Fundação ISCA', 'Estão abertas as candidaturas para as bolsas que premeiam o esforço e dedicação dos alunos com aproveitamento excecional.', 'Candidaturas abertas para o programa de bolsas 2025. Informe-se na secretaria.', 13, 'https://www.ua.pt/file/72000', 0, '2 min', 1, '2025-09-15', '2025-12-18 11:50:22', '2026-01-14 14:13:45', '', 1),
(7, 'Workshop: Soft Skills', 'Sessão de preparação para o mercado.', 'Melhore as suas competências.', 11, 'https://www.ua.pt/file/71000', 0, '3 min', 3, '2025-02-15', '2025-12-19 18:44:17', '2026-01-14 20:19:01', '', 1),
(10, 'Jantar de Gala Alumni', 'Evento de networking anual.', 'O reencontro da família ISCA.', 18, 'https://www.ua.pt/file/68000', 0, '4 min', 10, '2025-06-15', '2025-12-19 18:44:17', '2026-01-14 23:35:06', '', 1),
(11, 'ISCA Solidário', 'Recolha de bens alimentares.', 'Participe na recolha de bens.', 12, 'https://www.ua.pt/file/67000', 0, '2 min', 19, '2025-11-10', '2025-12-19 18:44:17', '2026-01-14 23:27:48', '', 1),
(12, 'Workshop Investigação Marketing', 'Análise de dados com GOVCOPP.', 'Aprenda tendências de análise.', 19, 'https://www.ua.pt/file/66000', 1, '5 min', 0, '2025-06-05', '2025-12-19 18:44:17', '2025-12-19 18:44:17', '', 1),
(13, 'Receção Novos Alunos', 'Boas-vindas institucionais.', 'Bem-vindos ao ISCA-UA!', 17, 'https://www.ua.pt/file/65000', 1, '3 min', 1, '2025-09-12', '2025-12-19 18:44:17', '2026-01-14 17:14:01', '', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `nome_categoria` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `cor_hex` varchar(7) DEFAULT '#c00',
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`categoria_id`, `nome`, `nome_categoria`, `descricao`, `cor_hex`, `ativo`, `data_criacao`) VALUES
(11, 'Eventos Académicos', 'Eventos Académicos', 'Workshops, seminários e eventos educacionais do ISCA', '#1E88E5', 1, '2025-12-11 12:11:55'),
(12, 'Eventos Sociais', 'Eventos Sociais', 'Festas, convívios e atividades de integração', '#43A047', 1, '2025-12-11 12:11:55'),
(13, 'Prémios e Reconhecimentos', 'Prémios e Reconhecimentos', 'Prémios académicos e profissionais de contabilidade e administração', '#FF5722', 1, '2025-12-11 12:11:55'),
(14, 'Conferências', 'Conferências', 'Conferências profissionais e académicas do ISCA', '#8E24AA', 1, '2025-12-11 12:11:55'),
(15, 'Oportunidades de Carreira', 'Oportunidades de Carreira', 'Estágios, emprego e oportunidades profissionais', '#00ACC1', 1, '2025-12-11 12:11:55'),
(16, 'Inovação e Tecnologia', 'Inovação e Tecnologia', 'Novas tecnologias aplicadas à contabilidade e gestão', '#FDD835', 1, '2025-12-11 12:11:55'),
(17, 'Notícias Institucionais', 'Notícias Institucionais', 'Notícias oficiais e comunicados do ISCA', '#546E7A', 1, '2025-12-11 12:11:55'),
(18, 'Alumni', 'Alumni', 'Notícias sobre ex-alunos e rede de contactos', '#5D4037', 1, '2025-12-11 12:11:55'),
(19, 'Investigação', 'Investigação', 'Projetos de investigação em contabilidade e gestão', '#7B1FA2', 1, '2025-12-11 12:11:55'),
(20, 'Parcerias Empresariais', 'Parcerias Empresariais', 'Acordos e colaborações com empresas', '#388E3C', 1, '2025-12-11 12:11:55');

-- --------------------------------------------------------

--
-- Estrutura da tabela `conferencias`
--

CREATE TABLE `conferencias` (
  `conferencia_id` int(11) NOT NULL,
  `artigo_id` int(11) NOT NULL,
  `tema_principal` varchar(200) NOT NULL,
  `area_tematica` enum('Contabilidade','Auditoria','Fiscalidade','Gestão Empresarial','Finanças','Marketing','Recursos Humanos','Empreendedorismo','Tecnologias de Informação','Ética nos Negócios') NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `local_conferencia` varchar(200) NOT NULL,
  `tipo_conferencia` enum('Anual do ISCA','Nacional','Internacional','Parceria') NOT NULL,
  `oradores_principais` text DEFAULT NULL,
  `organizador_interno` varchar(150) DEFAULT NULL,
  `departamento_responsavel` varchar(100) DEFAULT NULL,
  `link_inscricao` varchar(500) DEFAULT NULL,
  `publico_alvo` enum('Estudantes','Alumni','Docentes','Profissionais','Empresas','Todos') DEFAULT 'Estudantes',
  `credito_profissional` tinyint(1) DEFAULT 0,
  `idioma_principal` enum('Português','Inglês','Bilingue') DEFAULT 'Português',
  `custo_inscricao` decimal(8,2) DEFAULT 0.00,
  `patrocinadores` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `conferencias`
--

INSERT INTO `conferencias` (`conferencia_id`, `artigo_id`, `tema_principal`, `area_tematica`, `data_inicio`, `data_fim`, `local_conferencia`, `tipo_conferencia`, `oradores_principais`, `organizador_interno`, `departamento_responsavel`, `link_inscricao`, `publico_alvo`, `credito_profissional`, `idioma_principal`, `custo_inscricao`, `patrocinadores`) VALUES
(1, 1, 'Contabilidade e Administração: Sustentabilidade e ESG', 'Contabilidade', '2026-01-16', '2026-01-18', 'Auditório do ISCA-UA', 'Internacional', 'Investigadores e Profissionais do Setor Financeiro', 'Direção do ISCA-UA', 'Contabilidade', 'https://www.ua.pt/pt/isca/cica', 'Todos', 1, 'Bilingue', 0.00, 'Ordem dos Contabilistas Certificados (OCC)'),
(2, 2, 'Cibersegurança e Governação de Dados nas Organizações', 'Tecnologias de Informação', '2026-02-06', '2026-02-14', 'Auditório do ISCA-UA', 'Nacional', 'Especialistas em Segurança Informática', 'Coordenação de Sistemas de Informação', 'Sistemas de Informação', 'https://www.ua.pt/pt/isca/conferencia-si', 'Todos', 0, 'Português', 0.00, 'Empresas de Tecnologia Parceiras');

-- --------------------------------------------------------

--
-- Estrutura da tabela `contactos`
--

CREATE TABLE `contactos` (
  `contacto_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(50) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `lido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `enquetes`
--

CREATE TABLE `enquetes` (
  `id` int(11) NOT NULL,
  `pergunta` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `ativa` tinyint(1) DEFAULT 1,
  `apenas_logados` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `enquetes`
--

INSERT INTO `enquetes` (`id`, `pergunta`, `descricao`, `data_inicio`, `data_fim`, `ativa`, `apenas_logados`, `created_at`) VALUES
(1, 'Qual o maior desafio dos estudantes do ISCA em 2026?', 'Partilha a tua opinião sobre os desafios atuais dos estudantes', '2026-01-15', '2026-01-22', 1, 1, '2026-01-15 03:50:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `evento_id` int(11) NOT NULL,
  `artigo_id` int(11) NOT NULL,
  `data_evento` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `local_evento` varchar(200) NOT NULL,
  `tipo_evento` enum('Workshop','Seminário','Palestra','Networking','Social','Formação','Outro') NOT NULL,
  `publico_alvo` enum('Estudantes','Alumni','Docentes','Profissionais','Todos') DEFAULT 'Estudantes',
  `organizador` varchar(150) DEFAULT NULL,
  `departamento_responsavel` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`evento_id`, `artigo_id`, `data_evento`, `hora_inicio`, `hora_fim`, `local_evento`, `tipo_evento`, `publico_alvo`, `organizador`, `departamento_responsavel`) VALUES
(10, 7, '2025-02-15', '14:30:00', '17:30:00', 'Sala de Atos - ISCA', 'Workshop', 'Estudantes', 'Gabinete de Saídas Profissionais', 'ISCA-UA'),
(11, 10, '2025-06-15', '19:30:00', '23:30:00', 'Hotel em Aveiro', 'Social', 'Alumni', 'Associação Alumni ISCA-UA', 'Relações Externas'),
(12, 11, '2025-11-15', '09:00:00', '19:00:00', 'Átrio Principal', 'Social', 'Todos', 'Núcleo de Estudantes', 'Ação Social'),
(13, 12, '2025-06-05', '14:00:00', '18:00:00', 'Lab de Informática 2', 'Formação', 'Docentes', 'Unidade GOVCOPP', 'Marketing'),
(14, 13, '2025-09-12', '10:00:00', '13:00:00', 'Auditório Exterior', 'Social', 'Estudantes', 'Direção do ISCA', 'Secretariado Académico');

-- --------------------------------------------------------

--
-- Estrutura da tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `favorito_id` int(11) NOT NULL,
  `usuario_email` varchar(255) NOT NULL,
  `artigo_id` int(11) NOT NULL,
  `data_favorito` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `opcoes_enquete`
--

CREATE TABLE `opcoes_enquete` (
  `id` int(11) NOT NULL,
  `enquete_id` int(11) NOT NULL,
  `texto_opcao` varchar(100) NOT NULL,
  `cor_hex` varchar(7) DEFAULT '#667eea'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `opcoes_enquete`
--

INSERT INTO `opcoes_enquete` (`id`, `enquete_id`, `texto_opcao`, `cor_hex`) VALUES
(1, 1, 'Acesso a estágios profissionais', '#FF6B6B'),
(2, 1, 'Propinas e custos de educação', '#4ECDC4'),
(3, 1, 'Qualidade das infraestruturas', '#FFD166'),
(4, 1, 'Conciliar estudos com trabalho', '#06D6A0'),
(5, 1, 'Acesso a materiais de estudo', '#118AB2');

-- --------------------------------------------------------

--
-- Estrutura da tabela `premios`
--

CREATE TABLE `premios` (
  `premio_id` int(11) NOT NULL,
  `artigo_id` int(11) NOT NULL,
  `nome_premio` varchar(150) NOT NULL,
  `tipo_premio` enum('Excelência Académica','Melhor Tese','Inovação Empresarial','Empreendedorismo','Responsabilidade Social','Desempenho Profissional') NOT NULL,
  `area_premio` enum('Contabilidade','Auditoria','Fiscalidade','Gestão','Marketing','Finanças','RH') NOT NULL,
  `instituicao_concedente` varchar(200) NOT NULL,
  `ano_concessao` year(4) NOT NULL,
  `valor_monetario` decimal(10,2) DEFAULT NULL,
  `parceiros` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `premios`
--

INSERT INTO `premios` (`premio_id`, `artigo_id`, `nome_premio`, `tipo_premio`, `area_premio`, `instituicao_concedente`, `ano_concessao`, `valor_monetario`, `parceiros`) VALUES
(1, 5, '', 'Inovação Empresarial', 'Contabilidade', '', '1999', NULL, NULL),
(2, 5, 'Prémio de Mérito Escolar UA', 'Excelência Académica', 'Contabilidade', 'Universidade de Aveiro', '2025', 1000.00, 'Banco Santander Totta'),
(3, 5, 'Prémio Melhor Aluno de Contabilidade', 'Excelência Académica', 'Contabilidade', 'ISCA-UA', '2025', 500.00, 'Ordem dos Contabilistas Certificados'),
(4, 6, 'Bolsas de Estudo Fundação ISCA', 'Excelência Académica', 'Gestão', 'Fundação ISCA', '2025', NULL, 'Empresas Associadas da Fundação');

-- --------------------------------------------------------

--
-- Estrutura da tabela `subscritores`
--

CREATE TABLE `subscritores` (
  `subscritor_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `tipo_subscritor` enum('Estudante ISCA','Ex-Aluno ISCA','Docente ISCA','Profissional Setor','Empresa','Outro') DEFAULT 'Estudante ISCA',
  `curso_ano` varchar(150) DEFAULT NULL,
  `empresa` varchar(200) DEFAULT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1,
  `preferencias` text DEFAULT NULL,
  `aceitou_termos` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nome_completo` varchar(100) DEFAULT NULL,
  `tipo` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `username`, `password`, `email`, `nome_completo`, `tipo`) VALUES
(1, 'admin', '$2y$10$82fatrukZ.xe0JPLSewUFuf6rmtGjdxhciMnWf/U7HtJUTm/HM3/a', 'admin@isca.ua.pt', 'Administrador ISCA', 'admin'),
(2, 'aluno', '$2y$10$JVX54pmHOYBPgwgKgU/bR.itgaQfETOBq8POQ64kTPoAXqtPAlcDi', 'aluno@isca.ua.pt', 'Aluno Teste', 'user');

-- --------------------------------------------------------

--
-- Estrutura da tabela `visualizacoes`
--

CREATE TABLE `visualizacoes` (
  `visualizacao_id` int(11) NOT NULL,
  `usuario_email` varchar(255) NOT NULL,
  `artigo_id` int(11) NOT NULL,
  `data_visualizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `votos_enquete`
--

CREATE TABLE `votos_enquete` (
  `id` int(11) NOT NULL,
  `enquete_id` int(11) NOT NULL,
  `opcao_id` int(11) NOT NULL,
  `user_identifier` varchar(100) NOT NULL,
  `data_voto` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `artigos`
--
ALTER TABLE `artigos`
  ADD PRIMARY KEY (`artigo_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`categoria_id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices para tabela `conferencias`
--
ALTER TABLE `conferencias`
  ADD PRIMARY KEY (`conferencia_id`),
  ADD KEY `artigo_id` (`artigo_id`);

--
-- Índices para tabela `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`contacto_id`);

--
-- Índices para tabela `enquetes`
--
ALTER TABLE `enquetes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`evento_id`),
  ADD KEY `artigo_id` (`artigo_id`);

--
-- Índices para tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`favorito_id`),
  ADD UNIQUE KEY `unique_favorito` (`usuario_email`,`artigo_id`),
  ADD KEY `artigo_id` (`artigo_id`);

--
-- Índices para tabela `opcoes_enquete`
--
ALTER TABLE `opcoes_enquete`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_enquete` (`enquete_id`);

--
-- Índices para tabela `premios`
--
ALTER TABLE `premios`
  ADD PRIMARY KEY (`premio_id`),
  ADD KEY `artigo_id` (`artigo_id`);

--
-- Índices para tabela `subscritores`
--
ALTER TABLE `subscritores`
  ADD PRIMARY KEY (`subscritor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Índices para tabela `visualizacoes`
--
ALTER TABLE `visualizacoes`
  ADD PRIMARY KEY (`visualizacao_id`),
  ADD KEY `artigo_id` (`artigo_id`);

--
-- Índices para tabela `votos_enquete`
--
ALTER TABLE `votos_enquete`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voto_unico` (`enquete_id`,`user_identifier`),
  ADD KEY `opcao_id` (`opcao_id`),
  ADD KEY `idx_enquete_user` (`enquete_id`,`user_identifier`),
  ADD KEY `idx_user` (`user_identifier`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `artigos`
--
ALTER TABLE `artigos`
  MODIFY `artigo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `categoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `conferencias`
--
ALTER TABLE `conferencias`
  MODIFY `conferencia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `contactos`
--
ALTER TABLE `contactos`
  MODIFY `contacto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `enquetes`
--
ALTER TABLE `enquetes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `evento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `favorito_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `opcoes_enquete`
--
ALTER TABLE `opcoes_enquete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `premios`
--
ALTER TABLE `premios`
  MODIFY `premio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `subscritores`
--
ALTER TABLE `subscritores`
  MODIFY `subscritor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `visualizacoes`
--
ALTER TABLE `visualizacoes`
  MODIFY `visualizacao_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `votos_enquete`
--
ALTER TABLE `votos_enquete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `artigos`
--
ALTER TABLE `artigos`
  ADD CONSTRAINT `artigos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `conferencias`
--
ALTER TABLE `conferencias`
  ADD CONSTRAINT `conferencias_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`artigo_id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`artigo_id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`artigo_id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `opcoes_enquete`
--
ALTER TABLE `opcoes_enquete`
  ADD CONSTRAINT `opcoes_enquete_ibfk_1` FOREIGN KEY (`enquete_id`) REFERENCES `enquetes` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `premios`
--
ALTER TABLE `premios`
  ADD CONSTRAINT `premios_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`artigo_id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `visualizacoes`
--
ALTER TABLE `visualizacoes`
  ADD CONSTRAINT `visualizacoes_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`artigo_id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `votos_enquete`
--
ALTER TABLE `votos_enquete`
  ADD CONSTRAINT `votos_enquete_ibfk_1` FOREIGN KEY (`enquete_id`) REFERENCES `enquetes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votos_enquete_ibfk_2` FOREIGN KEY (`opcao_id`) REFERENCES `opcoes_enquete` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
