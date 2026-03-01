-- Correções identificadas pelos testes automatizados
-- Execute cada bloco separadamente

-- ============================================
-- 1. ADICIONAR COLUNA created_by em agendamentos
-- ============================================

-- Primeiro adicionar a coluna
ALTER TABLE agendamentos 
ADD COLUMN created_by INT NULL;

-- Depois adicionar a constraint (execute separadamente)
ALTER TABLE agendamentos
ADD CONSTRAINT agendamentos_ibfk_created_by 
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- ============================================
-- 2. CORRIGIR TABELA prontuarios
-- ============================================

-- Verificar colunas atuais
DESCRIBE prontuarios;

-- A tabela usa 'anamnese' mas os testes esperam 'queixa_principal' e 'historia_doenca'
-- Adicionar colunas que faltam:
ALTER TABLE prontuarios
ADD COLUMN queixa_principal TEXT AFTER profissional_id,
ADD COLUMN historia_doenca TEXT AFTER queixa_principal,
ADD COLUMN historia_patologica TEXT AFTER historia_doenca;

-- ============================================
-- 3. CORRIGIR TABELA bloqueios_agenda
-- ============================================

-- Verificar colunas atuais
DESCRIBE bloqueios_agenda;

-- Adicionar colunas que faltam:
ALTER TABLE bloqueios_agenda
ADD COLUMN dia_completo TINYINT(1) DEFAULT 0 AFTER motivo;

ALTER TABLE bloqueios_agenda
ADD COLUMN hora_inicio TIME NULL AFTER dia_completo,
ADD COLUMN hora_fim TIME NULL AFTER hora_inicio;

-- ============================================
-- 4. VERIFICAR USUÁRIO ADMIN
-- ============================================

-- Verificar se existe
SELECT * FROM users WHERE email = 'admin@admin.com';

-- Se não existir, criar (substitua o hash pela senha correta):
-- Senha: admin123
-- Hash bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (nome, email, senha, tipo, ativo) 
VALUES ('Administrador', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1)
ON DUPLICATE KEY UPDATE nome=nome;
