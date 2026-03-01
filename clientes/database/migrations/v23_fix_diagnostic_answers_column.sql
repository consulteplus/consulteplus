-- Corrige o tipo da coluna valor_escolhido para suportar texto
-- Anteriormente estava como INT, causando truncamento de respostas para 0
ALTER TABLE gestao_diagnostico_respostas MODIFY valor_escolhido TEXT;
