-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 06/12/2025 às 01:50
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
-- Banco de dados: `fitness_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_exercicio`
--

CREATE TABLE `tipos_exercicio` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `calorias_por_minuto` int(11) DEFAULT NULL,
  `criado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_exercicio`
--

INSERT INTO `tipos_exercicio` (`id`, `descricao`, `calorias_por_minuto`, `criado_por`) VALUES
(1, 'Musculação', 6, NULL),
(2, 'Corrida de Rua', 10, NULL),
(3, 'Caminhada', 4, NULL),
(4, 'Natação', 8, NULL),
(5, 'Ciclismo', 7, NULL),
(6, 'CrossFit', 7, 1),
(7, 'Caminhada Rápida', 7, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos`
--

CREATE TABLE `treinos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_exercicio_id` int(11) NOT NULL,
  `duracao_minutos` int(11) NOT NULL,
  `data_treino` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `treinos`
--

INSERT INTO `treinos` (`id`, `usuario_id`, `tipo_exercicio_id`, `duracao_minutos`, `data_treino`, `observacoes`, `data_registro`) VALUES
(4, 1, 7, 10, '2025-12-05', '', '2025-12-05 22:57:02'),
(5, 1, 5, 30, '2025-12-06', '', '2025-12-06 00:10:22'),
(6, 1, 2, 60, '2025-12-05', '', '2025-12-06 00:29:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('Admin','Comum') NOT NULL DEFAULT 'Comum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `senha`, `nivel`) VALUES
(1, 'admin', 'admin@fitness.com', '$2y$10$7xBRdj53x2tbxAtoaouazuvRLslOtZk22IHi.qAbMIwhvSXpRj.uK', 'Admin'),
(2, 'João', 'joao@fitness.log.com', '$2y$10$K.M/PapE9elbkOIEY/.RiOaMCkA2NN7EmcyDGV88RsZ4McF381Hja', 'Comum');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tipos_exercicio`
--
ALTER TABLE `tipos_exercicio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criado_por` (`criado_por`);

--
-- Índices de tabela `treinos`
--
ALTER TABLE `treinos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `tipo_exercicio_id` (`tipo_exercicio_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tipos_exercicio`
--
ALTER TABLE `tipos_exercicio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `treinos`
--
ALTER TABLE `treinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tipos_exercicio`
--
ALTER TABLE `tipos_exercicio`
  ADD CONSTRAINT `tipos_exercicio_ibfk_1` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `treinos`
--
ALTER TABLE `treinos`
  ADD CONSTRAINT `treinos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `treinos_ibfk_2` FOREIGN KEY (`tipo_exercicio_id`) REFERENCES `tipos_exercicio` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
