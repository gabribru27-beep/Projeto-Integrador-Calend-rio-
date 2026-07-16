CREATE DATABASE IF NOT EXISTS `Calendario` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `Calendario`;

-- Tabela para receber os eventos cadastrados pela página de eventos
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    marcacao VARCHAR(100) NULL,
    tipo ENUM('feriado','recesso','uc','outro') NOT NULL DEFAULT 'outro',
    cor VARCHAR(20) DEFAULT '#f2f2f2',
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
