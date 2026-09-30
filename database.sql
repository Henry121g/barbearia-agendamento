-- Estrutura do banco do Sistema de Agendamento para Barbearia
-- Uso: mysql -u root -p < database.sql
-- Contém apenas dados de EXEMPLO (serviços e barbeiros fictícios), nenhum dado pessoal.

-- Garante que acentos sejam gravados corretamente (sem isso o Windows grava "PigmentaÃ§Ã£o")
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS barbearia
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE barbearia;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    usuario    VARCHAR(50)  NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS servicos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nome             VARCHAR(100)  NOT NULL,
    preco            DECIMAL(8, 2) NOT NULL,
    duracao_minutos  INT           NOT NULL,
    ativo            TINYINT(1)    NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS barbeiros (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(100) NOT NULL,
    ativo TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS agendamentos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nome     VARCHAR(100) NOT NULL,
    cliente_telefone VARCHAR(20)  NOT NULL,
    servico_id       INT          NOT NULL,
    barbeiro_id      INT          NOT NULL,
    data             DATE         NOT NULL,
    horario          TIME         NOT NULL,
    status           ENUM('agendado', 'concluido', 'cancelado') NOT NULL DEFAULT 'agendado',
    criado_em        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_agendamento_servico  FOREIGN KEY (servico_id)  REFERENCES servicos (id),
    CONSTRAINT fk_agendamento_barbeiro FOREIGN KEY (barbeiro_id) REFERENCES barbeiros (id),
    INDEX idx_barbeiro_data (barbeiro_id, data)
) ENGINE=InnoDB;

-- Dados de exemplo
INSERT INTO servicos (nome, preco, duracao_minutos) VALUES
    ('Corte de cabelo', 40.00, 30),
    ('Barba',           30.00, 30),
    ('Corte + barba',   65.00, 60),
    ('Pigmentação',     50.00, 60);

INSERT INTO barbeiros (nome) VALUES
    ('Barbeiro 1'),
    ('Barbeiro 2');

-- O usuário administrador NÃO é criado aqui (para não publicar uma senha).
-- Crie o seu com: php scripts/criar_admin.php seu_usuario sua_senha
