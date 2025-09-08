CREATE DATABASE auditoria;
USE auditoria;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE checklists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    auditor VARCHAR(100) NOT NULL,
    usuario_id INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE checklist_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    FOREIGN KEY (checklist_id) REFERENCES checklists(id) ON DELETE CASCADE
);

CREATE TABLE auditorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT NOT NULL,
    usuario_id INT NOT NULL,
    resultado FLOAT NOT NULL,
    realizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario_auditoria INT NOT NULL DEFAULT 0,
    FOREIGN KEY (checklist_id) REFERENCES checklists(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE auditoria_respostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auditoria_id INT NOT NULL,
    item_id INT NOT NULL,
    resposta ENUM('SIM','NAO','NA') NOT NULL,
    FOREIGN KEY (auditoria_id) REFERENCES auditorias(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES checklist_itens(id) ON DELETE CASCADE
);

CREATE TABLE nao_conformidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auditoria_id INT NOT NULL,
    item_id INT NOT NULL,
    status ENUM('ABERTA','EM ANDAMENTO','RESOLVIDA') DEFAULT 'ABERTA',
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auditoria_id) REFERENCES auditorias(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES checklist_itens(id) ON DELETE CASCADE
);