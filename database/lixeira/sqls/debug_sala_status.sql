-- Verificar agendamentos da Sala 1 hoje
SELECT 
    a.id,
    a.data_hora,
    a.status,
    a.duracao_minutos,
    pac.nome as paciente,
    u.nome as profissional,
    s.nome as sala,
    DATE_ADD(a.data_hora, INTERVAL a.duracao_minutos MINUTE) as hora_fim,
    NOW() as agora,
    CASE 
        WHEN a.status = 'em_atendimento' 
            AND a.data_hora <= NOW() 
            AND DATE_ADD(a.data_hora, INTERVAL a.duracao_minutos MINUTE) >= NOW() 
        THEN 'DEVERIA APARECER COMO OCUPADO'
        ELSE 'NÃO APARECE COMO OCUPADO'
    END as status_dashboard
FROM agendamentos a
JOIN pacientes pac ON a.paciente_id = pac.id
JOIN profissionais p ON a.profissional_id = p.id
JOIN users u ON p.user_id = u.id
JOIN salas s ON a.sala_id = s.id
WHERE s.nome = 'Sala 1'
AND DATE(a.data_hora) = CURDATE()
ORDER BY a.data_hora;

-- Para mudar o status do agendamento para "Em Atendimento":
-- UPDATE agendamentos SET status = 'em_atendimento' WHERE id = [ID_DO_AGENDAMENTO];
