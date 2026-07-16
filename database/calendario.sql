CREATE DATABASE IF NOT EXISTS Calendario;
USE Calendario;

CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    tipo ENUM('feriado','recesso','uc','outro') NOT NULL DEFAULT 'outro',
    cor VARCHAR(20) DEFAULT '#f2f2f2',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO configuracoes (chave, valor) VALUES
('titulo_sistema', 'Calendário Acadêmico SENAC'),
('ano_referencia', '2026');

INSERT INTO eventos (titulo, descricao, data_inicio, data_fim, tipo, cor) VALUES
('Confraternização Universal', 'Feriado nacional', '2026-01-01', NULL, 'feriado', '#f8c8c8'),
('Carnaval', 'Carnaval', '2026-02-12', NULL, 'feriado', '#f8c8c8'),
('Tiradentes', 'Feriado nacional', '2026-04-21', NULL, 'feriado', '#f8c8c8'),
('Dia do Trabalho', 'Feriado nacional', '2026-05-01', NULL, 'feriado', '#f8c8c8'),
('Independência do Brasil', 'Feriado nacional', '2026-09-07', NULL, 'feriado', '#f8c8c8'),
('Nossa Senhora Aparecida', 'Feriado nacional', '2026-10-12', NULL, 'feriado', '#f8c8c8'),
('Finados', 'Feriado nacional', '2026-11-02', NULL, 'feriado', '#f8c8c8'),
('Proclamação da República', 'Feriado nacional', '2026-11-15', NULL, 'feriado', '#f8c8c8'),
('Natal', 'Feriado nacional', '2026-12-25', NULL, 'feriado', '#f8c8c8'),
('Recesso de Julho', 'Período de recesso escolar', '2026-07-01', '2026-07-15', 'recesso', '#f6e0b5'),
('Planejamento de Sistemas', 'Unidade curricular de Planejamento de Sistemas', '2026-01-10', '2026-02-28', 'uc', '#bdd7ee'),
('Banco de Dados', 'Unidade curricular de Banco de Dados', '2026-02-15', '2026-04-10', 'uc', '#d9d2e9'),
('Redes de Computadores', 'Unidade curricular de Redes de Computadores', '2026-04-06', '2026-05-25', 'uc', '#fff2cc'),
('Desenvolvimento Web', 'Unidade curricular de Desenvolvimento Web', '2026-06-01', '2026-07-20', 'uc', '#f2dcdb'),
('Projeto Integrador', 'Unidade curricular de Projeto Integrador', '2026-08-10', '2026-10-15', 'uc', '#d8e4bc');
