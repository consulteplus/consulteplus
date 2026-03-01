-- Adicionar 'cliente' nas opções de tipo de usuário
ALTER TABLE `users` 
MODIFY COLUMN `tipo` ENUM('admin','medico','secretaria','cliente') NOT NULL DEFAULT 'cliente';
