-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/07/2025 às 06:44
-- Versão do servidor: 8.0.42
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atp2webbordados`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clientes_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`) VALUES
(1, 'Maria Silva', 'maria@email.com', '11999999999'),
(2, 'João Souza', 'joao@email.com', '11888888888');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `descricao` text NOT NULL,
  `data_pedido` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pedidos_clientes` (`cliente_id`),
  CONSTRAINT `fk_pedidos_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `descricao`, `data_pedido`, `valor`) VALUES
(1, 1, 'Bordado em camisa polo', '2025-07-01', 150.00),
(2, 2, 'Bordado em boné', '2025-07-02', 80.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT '0.00',
  `imagem` varchar(255) DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `imagem`, `categoria`, `criado_em`) VALUES
(1, 'Bordado 8x8 frente', 'Bordado no peitoral com 8cm', 10.00, NULL, 'b2c', '2025-07-05 22:17:10'),
(2, 'Bordado Costas 26x8', 'Bordado costas de até 26x8', 50.00, NULL, 'b2c', '2025-07-05 22:19:40'),
(3, 'Bordado Nome', 'Bordado de nome com até 10cm largura', 5.00, NULL, 'b2c', '2025-07-05 22:23:19'),
(4, 'Vetorização (tirar o bordado a partir de uma imagem do cliente)', 'Valor inicial de 30, sujeito a análise do desenvolvedor (prazo de entrega pós feedback do cliente 36h)', 30.00, NULL, 'matriz', '2025-07-05 22:37:39'),
(6, 'bordados para B2B', 'Entre em contato direto com o vendedor para um atendimento personalizado (**)9XX3X-XXX7', 1.00, NULL, 'b2b', '2025-07-05 22:37:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('dono','gerente','funcionario') NOT NULL DEFAULT 'funcionario',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--
-- IMPORTANTE: As senhas devem ser armazenadas como HASH.
-- Os valores abaixo são exemplos de hashes bcrypt. Use a função password_hash() do PHP para gerá-los.
-- Exemplo: password_hash('sua_senha_aqui', PASSWORD_BCRYPT)

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel`) VALUES
(1, 'RAFAEL', 'rafal@rafael.com', '$2y$10$H7c.gaZk1aL8zWp9E4a5W.wG1uF3jVzE9bB2iC6dD0eF8gH9iJ0k', 'dono'),
(2, 'Joao', 'joao@exemplo.com', '$2y$10$kL9.mN8oPqR7sT6uV5wX4.yZ3aB1cD2eF5gH7iJ9kL0mN8oPqR7sT', 'gerente');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
