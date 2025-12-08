-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/12/2025 às 00:50
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

-- Remover e recriar o banco para evitar erros de tabelas já existentes
DROP DATABASE IF EXISTS `db_campeonato`;
CREATE DATABASE `db_campeonato` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_campeonato`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_campeonato`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_campeonatos`
--

CREATE TABLE `tbl_campeonatos` (
  `id_campeonato` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `data_inicio` datetime NOT NULL,
  `intervalo_minutos` int(11) NOT NULL DEFAULT 1440,
  `iniciado` tinyint(4) NOT NULL DEFAULT 0,
  `pontos_vitoria` int(11) DEFAULT 3,
  `pontos_empate` int(11) DEFAULT 1,
  `pontos_derrota` int(11) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_equipes`
--

CREATE TABLE `tbl_equipes` (
  `id_equipe` int(11) NOT NULL,
  `id_campeonato` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `cidade` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbl_jogos`
--

CREATE TABLE `tbl_jogos` (
  `id_jogo` int(11) NOT NULL,
  `id_campeonato` int(11) NOT NULL,
  `rodada` int(11) NOT NULL,
  `turno` tinyint(4) NOT NULL DEFAULT 1,
  `equipe_casa` int(11) DEFAULT NULL,
  `equipe_fora` int(11) DEFAULT NULL,
  `data_hora` datetime DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `gols_casa` int(11) DEFAULT NULL,
  `gols_fora` int(11) DEFAULT NULL,
  `jogado` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tbl_campeonatos`
--
ALTER TABLE `tbl_campeonatos`
  ADD PRIMARY KEY (`id_campeonato`);

--
-- Índices de tabela `tbl_equipes`
--
ALTER TABLE `tbl_equipes`
  ADD PRIMARY KEY (`id_equipe`),
  ADD KEY `id_campeonato` (`id_campeonato`);

--
-- Índices de tabela `tbl_jogos`
--
ALTER TABLE `tbl_jogos`
  ADD PRIMARY KEY (`id_jogo`),
  ADD KEY `id_campeonato` (`id_campeonato`),
  ADD KEY `equipe_casa` (`equipe_casa`),
  ADD KEY `equipe_fora` (`equipe_fora`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tbl_campeonatos`
--
ALTER TABLE `tbl_campeonatos`
  MODIFY `id_campeonato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_equipes`
--
ALTER TABLE `tbl_equipes`
  MODIFY `id_equipe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbl_jogos`
--
ALTER TABLE `tbl_jogos`
  MODIFY `id_jogo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tbl_equipes`
--
ALTER TABLE `tbl_equipes`
  ADD CONSTRAINT `tbl_equipes_ibfk_1` FOREIGN KEY (`id_campeonato`) REFERENCES `tbl_campeonatos` (`id_campeonato`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tbl_jogos`
--
ALTER TABLE `tbl_jogos`
  ADD CONSTRAINT `tbl_jogos_ibfk_1` FOREIGN KEY (`id_campeonato`) REFERENCES `tbl_campeonatos` (`id_campeonato`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_jogos_ibfk_2` FOREIGN KEY (`equipe_casa`) REFERENCES `tbl_equipes` (`id_equipe`) ON DELETE SET NULL,
  ADD CONSTRAINT `tbl_jogos_ibfk_3` FOREIGN KEY (`equipe_fora`) REFERENCES `tbl_equipes` (`id_equipe`) ON DELETE SET NULL;
--
-- Tabela e dados de usuários adicionados manualmente
--

CREATE TABLE tbl_usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(150) NOT NULL,
    cargo TINYINT(4) NOT NULL DEFAULT 0,
    email VARCHAR(150) UNIQUE
);


-- Inserir os 3 usuários existentes
INSERT INTO tbl_usuarios (id_usuario, username, password, cargo, name) VALUES 
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 2, 'Henri'),
(2, 'trainner', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 1, 'Aguiar'),
(3, 'player', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 0, 'Kemi');

-- Tabela de times
CREATE TABLE tbl_times (
  id_time INT AUTO_INCREMENT PRIMARY KEY,
  id_campeonato INT DEFAULT NULL,
  nome VARCHAR(120) NOT NULL,
  cidade VARCHAR(100) DEFAULT NULL,
  INDEX (id_campeonato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Mapeamento usuários → times (unificado: jogadores e treinadores)
-- A coluna 'cargo' em tbl_usuarios define o tipo (0=jogador, 1=treinador, 2=admin)
CREATE TABLE tbl_usuarios_time (
  id_usuario INT NOT NULL,
  id_time INT NOT NULL,
  PRIMARY KEY (id_usuario, id_time),
  INDEX (id_time),
  FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_time) REFERENCES tbl_times(id_time) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados de exemplo para teste
-- Criar um time de exemplo
INSERT INTO tbl_times (id_time, nome, cidade) VALUES (1, 'killers', 'Coroa de espinhos');

-- Associar o treinador Aguiar (id 2, cargo 1) ao time 1
-- O jogador Kemi (id 3) ficará disponível para ser contratado
INSERT INTO tbl_usuarios_time (id_usuario, id_time) VALUES (2, 1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
