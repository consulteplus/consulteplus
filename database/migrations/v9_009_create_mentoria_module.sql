-- 1. Tabela de Produtos (Mentorias/Cursos)
CREATE TABLE IF NOT EXISTS mentoria_produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagem_capa VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabela de Trilhas (Módulos)
CREATE TABLE IF NOT EXISTS mentoria_trilhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    ordem INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES mentoria_produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabela de Conteudos (Aulas)
CREATE TABLE IF NOT EXISTS mentoria_conteudos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trilha_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao LONGTEXT,
    tipo ENUM('video', 'texto') DEFAULT 'video',
    url_video VARCHAR(500),
    anexo_url VARCHAR(500),
    
    -- Integração com Tarefas
    tem_tarefa TINYINT(1) DEFAULT 0,
    tarefa_titulo VARCHAR(255),
    tarefa_descricao TEXT,
    tarefa_tipo_entrega ENUM('texto', 'arquivo', 'ambos') DEFAULT 'texto',
    
    ordem INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trilha_id) REFERENCES mentoria_trilhas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Matriculas (Quem tem acesso)
CREATE TABLE IF NOT EXISTS mentoria_matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produto_id INT NOT NULL,
    data_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES mentoria_produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Progresso (Tracking)
CREATE TABLE IF NOT EXISTS mentoria_progresso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    conteudo_id INT NOT NULL,
    concluido TINYINT(1) DEFAULT 0,
    data_conclusao DATETIME,
    
    -- Resposta da Tarefa
    resposta_texto TEXT,
    arquivo_anexo VARCHAR(255),
    data_resposta DATETIME,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (conteudo_id) REFERENCES mentoria_conteudos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
