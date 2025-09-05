CREATE DATABASE IF NOT EXISTS auditoria;
USE auditoria;

CREATE TABLE IF NOT EXISTS auditorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artefato VARCHAR(100) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS checklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auditoria_id INT NOT NULL,
    checklist_id INT NOT NULL,
    resposta ENUM('Sim','Nao','NA') NOT NULL,
    FOREIGN KEY (auditoria_id) REFERENCES auditorias(id) ON DELETE CASCADE,
    FOREIGN KEY (checklist_id) REFERENCES checklist(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS nao_conformidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auditoria_id INT NOT NULL,
    checklist_id INT NOT NULL,
    descricao TEXT,
    responsavel VARCHAR(100),
    prazo DATE,
    status ENUM('Aberta','Em Andamento','Resolvida','Escalonada') DEFAULT 'Aberta',
    FOREIGN KEY (auditoria_id) REFERENCES auditorias(id) ON DELETE CASCADE,
    FOREIGN KEY (checklist_id) REFERENCES checklist(id) ON DELETE CASCADE
);

INSERT INTO checklist (pergunta) VALUES
('Os requisitos estão numerados?'),
('Existe rastreabilidade entre requisitos e casos de uso?'),
('Cada requisito está claro e sem ambiguidades?'),
('Existe aprovação formal do cliente?');
