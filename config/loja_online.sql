-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/11/2023 às 00:25  -- Corrigido: Data ajustada para 2023 (assumindo erro de digitação)
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loja_online`
--
-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS loja_online;
USE loja_online;
-- --------------------------------------------------------

--
-- Estrutura para tabela `cancelamentos`
--

CREATE TABLE `cancelamentos` (
  `id` int(11) NOT NULL,
  `id_encomenda` int(11) NOT NULL,
  `motivo` text DEFAULT NULL,
  `data_cancelamento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cancelamentos`
--

INSERT INTO `cancelamentos` (`id`, `id_encomenda`, `motivo`, `data_cancelamento`) VALUES
(1, 3, 'Cliente solicitou cancelamento devido a mudança de ideia', '2023-09-21 11:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `capacidades`
--

CREATE TABLE `capacidades` (
  `id` int(11) NOT NULL,
  `ml` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `capacidades`
--

INSERT INTO `capacidades` (`id`, `ml`) VALUES
(1, 50),
(2, 100),
(3, 30),
(4, 75),
(5, 150),
(6, 200);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(1, 'Perfumes masculino'),
(2, 'Cosméticos'),
(3, 'Maquiagem'),
(4, 'Cabelos'),
(5, 'Perfumes feminino'),
(6, 'Perfumes infantil'),
(7, 'Corpo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes_dados`
--

CREATE TABLE `clientes_dados` (
  `id` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `nif` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes_dados`
--

INSERT INTO `clientes_dados` (`id`, `id_utilizador`, `nome`, `telefone`, `nif`) VALUES
(1, 1, 'João Silva', '912345678', '123456789');

-- --------------------------------------------------------

--
-- Estrutura para tabela `concentracoes`
--

CREATE TABLE `concentracoes` (
  `id` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `concentracoes` (`id`, `tipo`) VALUES
(1, 'EDP'),
(2, 'EDT'),
(3, 'Parfum'),
(4, 'Eau de Cologne'),
(5, 'Body Mist');

-- --------------------------------------------------------

--
-- Estrutura para tabela `encomendas`
--

CREATE TABLE `encomendas` (
  `id` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_pagamento` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `data` datetime DEFAULT current_timestamp(),
  `criado_em` datetime DEFAULT current_timestamp(),
  `motivo_cancelamento` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `encomendas`
--

INSERT INTO `encomendas` (`id`, `id_utilizador`, `total`, `id_pagamento`, `id_estado`, `data`, `criado_em`, `motivo_cancelamento`) VALUES
(1, 1, 170.00, 1, 4, '2023-10-01 10:00:00', '2023-10-01 10:00:00', NULL),
(2, 2, 45.00, 2, 1, '2023-10-05 14:30:00', '2023-10-05 14:30:00', NULL),
(3, 1, 120.00, 1, 5, '2023-09-20 09:15:00', '2023-09-20 09:15:00', 'Cliente solicitou cancelamento devido a mudança de ideia');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estados_encomenda`
--

CREATE TABLE `estados_encomenda` (
  `id` int(11) NOT NULL,
  `estado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estados_encomenda`
--

INSERT INTO `estados_encomenda` (`id`, `estado`) VALUES
(1, 'Pendente'),
(2, 'Processando'),
(3, 'Enviado'),
(4, 'Entregue'),
(5, 'Cancelado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_stock`
--

CREATE TABLE `historico_stock` (
  `id` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `fornecedor` varchar(255) DEFAULT NULL,
  `nota` text DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `historico_stock`
--

INSERT INTO `historico_stock` (`id`, `id_produto`, `quantidade`, `tipo`, `fornecedor`, `nota`, `data`) VALUES
(1, 1, 50, 'entrada', 'Fornecedor A', 'Entrega inicial', '2023-10-01 10:00:00'),
(2, 2, 100, 'entrada', 'Fornecedor B', 'Reabastecimento', '2023-10-01 10:00:00'),
(3, 3, 30, 'entrada', 'Fornecedor C', 'Novo lote', '2023-10-01 10:00:00'),
(4, 4, 40, 'entrada', 'Fornecedor A', 'Entrega especial', '2023-10-01 10:00:00'),
(5, 5, 60, 'entrada', 'Fornecedor D', 'Promoção', '2023-10-01 10:00:00'),
(6, 1, -1, 'saida', NULL, 'Venda online', '2023-10-01 10:30:00'),
(7, 2, -1, 'saida', NULL, 'Venda na home', '2023-10-05 14:30:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_admins`
--

CREATE TABLE `log_admins` (
  `id` int(11) NOT NULL,
  `acao` varchar(255) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `alvo_id` int(11) NOT NULL,
  `data` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `marcas`
--

INSERT INTO `marcas` (`id`, `nome`) VALUES
(1, 'Avon'),
(2, 'Eudora'),
(3, 'Natura'),
(4, 'O Boticário'),
(5, 'Lancôme'),
(6, 'Nivea');

-- --------------------------------------------------------

--
-- Estrutura para tabela `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `inscrito_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`, `inscrito_em`) VALUES
(1, 'cliente1@example.com', CURRENT_TIMESTAMP),  -- Corrigido: Data ajustada para 2023
(2, 'cliente2@example.com', CURRENT_TIMESTAMP);  -- Corrigido: Data ajustada para 2023

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `metodo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagamentos`
--

INSERT INTO `pagamentos` (`id`, `nome`, `metodo`) VALUES
(1, 'Cartão de Crédito', 'credit_card'),
(2, 'PayPal', 'paypal');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,  -- CORREÇÃO: Coluna 'tipo' adicionada para alinhar com o INSERT
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `eliminado` int(11) NOT NULL DEFAULT 0,
  `id_categoria` int(11) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `em_promocao` tinyint(1) DEFAULT 0,
  `criado_em` datetime DEFAULT current_timestamp(),
  `desconto` int(11) DEFAULT 0,
  `id_marca` int(11) DEFAULT NULL,
  `id_capacidade` int(11) DEFAULT NULL,
  `id_concentracao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `tipo`, `descricao`, `preco`, `stock`, `eliminado`, `id_categoria`, `imagem`, `em_promocao`, `criado_em`, `desconto`, `id_marca`, `id_capacidade`, `id_concentracao`) VALUES
(1, 'Perfume Chanel No. 5', 'Perfume feminino', 'Clássico perfume feminino', 150.00, 50, 0, 5, 'product1.jpg', 1, '2023-10-01 10:00:00', 10, 3, 2, 1),
(2, 'Kaiak Vibe Infantil Miniatura', 'Perfume infantil', 'Desodorante Colônia Kaiak Vibe Infantil Miniatura 25 ml', 20.00, 100, 0, 6, 'product2.jpg', 0, '2023-10-01 10:00:00', 0, 3, 3, NULL),
(3, 'Perfume Marca A Especial', 'Perfume Masculino', 'Perfume especial para homens', 25.00, 30, 0, 1, 'product3.jpg', 1, '2023-10-01 10:00:00', 15, 5, 2, NULL),
(4, 'Perfume Marca A Especial', 'Perfume Masculino', 'Perfume especial para homens', 80.00, 40, 0, 1, 'product4.jpg', 0, '2023-10-01 10:00:00', 0, 1, 3, 2),
(5, 'Cosmético Promoção', 'Produto em promoção', 'Produto em promoção', 120.00, 60, 0, 1, 'product5.jpg', 1, '2023-10-01 10:00:00', 20, 2, 4, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos_encomenda`
--

CREATE TABLE `produtos_encomenda` (
  `id` int(11) NOT NULL,
  `id_encomenda` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos_encomenda`
--

INSERT INTO `produtos_encomenda` (`id`, `id_encomenda`, `id_produto`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 1, 150.00),
(2, 1, 2, 1, 20.00),
(3, 2, 3, 1, 25.00),
(4, 2, 2, 1, 20.00),
(5, 3, 5, 1, 120.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  -- Adicionado AUTO_INCREMENT para chave primária
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,  -- Adicionado UNIQUE para evitar emails duplicados
  `palavra_passe` varchar(255) NOT NULL,
  `tipo` varchar(50) DEFAULT 'cliente',
  `tentativas_login` int(11) NOT NULL DEFAULT 0,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0,
  `ultima_tentativa` datetime DEFAULT NULL,
  `eliminado` int(11) NOT NULL DEFAULT 0,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,  -- Usado CURRENT_TIMESTAMP para dinamismo
  `criado_por` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX idx_email (`email`)  -- Índice para otimizar buscas por email
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Despejando dados para a tabela `utilizadores`
-- NOTA: Em produção, gere hashes únicos para senhas. Use uma ferramenta como password_hash() para criar hashes seguros.
INSERT INTO `utilizadores` (`id`, `nome`, `email`, `palavra_passe`, `tipo`, `tentativas_login`, `bloqueado`, `ultima_tentativa`, `eliminado`, `criado_em`, `criado_por`) VALUES
(1, 'João Silva', 'joao@email.com', '$2y$10$ULY5YDkmJw5x08MIBs1tW.s8/he8iojnQEb1PuI2ckUFVqFHNl.06', 'cliente', 0, 0, NULL, 0, CURRENT_TIMESTAMP, NULL),  -- Hash para 'password' (teste) - ALTERE EM PRODUÇÃO
(2, 'Maria Santos', 'maria@email.com', '$2y$10$ULY5YDkmJw5x08MIBs1tW.s8/he8iojnQEb1PuI2ckUFVqFHNl.06', 'cliente', 0, 0, NULL, 0, CURRENT_TIMESTAMP, NULL),  -- Hash para 'password' (teste) - ALTERE EM PRODUÇÃO
(3, 'Admin User', 'admin@home.com', '$2y$10$ULY5YDkmJw5x08MIBs1tW.s8/he8iojnQEb1PuI2ckUFVqFHNl.06', 'admin', 0, 0, NULL, 0, CURRENT_TIMESTAMP, NULL);  -- Hash para 'password' (teste) - ALTERE EM PRODUÇÃO

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cancelamentos`
--
ALTER TABLE `cancelamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_encomenda` (`id_encomenda`);

--
-- Índices de tabela `capacidades`
--
ALTER TABLE `capacidades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes_dados`
--
ALTER TABLE `clientes_dados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices de tabela `concentracoes`
--
ALTER TABLE `concentracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `encomendas`
--
ALTER TABLE `encomendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilizador` (`id_utilizador`),
  ADD KEY `id_pagamento` (`id_pagamento`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Índices de tabela `estados_encomenda`
--
ALTER TABLE `estados_encomenda`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_stock`
--
ALTER TABLE `historico_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `log_admins`
--
ALTER TABLE `log_admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `alvo_id` (`alvo_id`);

--
-- Índices de tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_marca` (`id_marca`),
  ADD KEY `id_capacidade` (`id_capacidade`),
  ADD KEY `id_concentracao` (`id_concentracao`);

--
-- Índices de tabela `produtos_encomenda`
--
ALTER TABLE `produtos_encomenda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_encomenda` (`id_encomenda`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `criado_por` (`criado_por`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cancelamentos`
--
ALTER TABLE `cancelamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `capacidades`
--
ALTER TABLE `capacidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `clientes_dados`
--
ALTER TABLE `clientes_dados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `concentracoes`
--
ALTER TABLE `concentracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `encomendas`
--
ALTER TABLE `encomendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `estados_encomenda`
--
ALTER TABLE `estados_encomenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `historico_stock`
--
ALTER TABLE `historico_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `log_admins`
--
ALTER TABLE `log_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `produtos_encomenda`
--
ALTER TABLE `produtos_encomenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `cancelamentos`
--
ALTER TABLE `cancelamentos`
  ADD CONSTRAINT `cancelamentos_ibfk_1` FOREIGN KEY (`id_encomenda`) REFERENCES `encomendas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `clientes_dados`
--
ALTER TABLE `clientes_dados`
  ADD CONSTRAINT `clientes_dados_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `encomendas`
--
ALTER TABLE `encomendas`
  ADD CONSTRAINT `encomendas_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `encomendas_ibfk_2` FOREIGN KEY (`id_pagamento`) REFERENCES `pagamentos` (`id`),
  ADD CONSTRAINT `encomendas_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `estados_encomenda` (`id`),
  ADD COLUMN `eliminada` tinyint(1) NOT NULL DEFAULT 0;
--
-- Restrições para tabelas `historico_stock`
--
ALTER TABLE `historico_stock`
  ADD CONSTRAINT `historico_stock_ibfk_1` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `log_admins`
--
ALTER TABLE `log_admins`
  ADD CONSTRAINT `log_admins_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `log_admins_ibfk_2` FOREIGN KEY (`alvo_id`) REFERENCES `utilizadores` (`id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id`),
  ADD CONSTRAINT `produtos_ibfk_3` FOREIGN KEY (`id_capacidade`) REFERENCES `capacidades` (`id`),
  ADD CONSTRAINT `produtos_ibfk_4` FOREIGN KEY (`id_concentracao`) REFERENCES `concentracoes` (`id`);

--
-- Restrições para tabelas `produtos_encomenda`
--
ALTER TABLE `produtos_encomenda`
  ADD CONSTRAINT `produtos_encomenda_ibfk_1` FOREIGN KEY (`id_encomenda`) REFERENCES `encomendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produtos_encomenda_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD CONSTRAINT `utilizadores_ibfk_1` FOREIGN KEY (`criado_por`) REFERENCES `utilizadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
