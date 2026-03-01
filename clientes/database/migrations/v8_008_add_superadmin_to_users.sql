-- Adicionar 'superadmin' nas opções de tipo de usuário
ALTER TABLE `users` 
MODIFY COLUMN `tipo` ENUM('superadmin','admin','medico','secretaria','cliente') NOT NULL DEFAULT 'admin';
