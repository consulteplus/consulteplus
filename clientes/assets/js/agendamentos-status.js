/**
 * Funções para gerenciar status de agendamentos
 */

// Obter BASE_URL do caminho atual
const getBaseUrl = () => {
    const path = window.location.pathname;
    const parts = path.split('/');
    // Remove 'agendamentos' e tudo depois
    const baseIndex = parts.indexOf('agendamentos');
    if (baseIndex > 0) {
        return parts.slice(0, baseIndex).join('/') + '/';
    }
    // Fallback: pegar até o penúltimo /
    return path.substring(0, path.lastIndexOf('/') + 1).replace(/agendamentos.*/, '');
};

function alterarStatus(agendamentoId, novoStatus, mensagemConfirmacao) {
    if (!confirm(mensagemConfirmacao)) {
        return;
    }

    // Mostrar loading
    const btn = event.target.closest('button') || event.target.closest('a');
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processando...';

    const baseUrl = getBaseUrl();

    fetch(`${baseUrl}api/agendamentos/status.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            agendamento_id: agendamentoId,
            status: novoStatus
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensagem de sucesso
                showAlert('success', data.message);

                // Recarregar página após 1 segundo
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert('danger', data.error);
                btn.disabled = false;
                btn.innerHTML = textoOriginal;
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('danger', 'Erro ao processar requisição');
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
}

function iniciarAtendimento(id) {
    alterarStatus(id, 'em_atendimento', 'Iniciar atendimento deste paciente?');
}

function finalizarAtendimento(id) {
    alterarStatus(id, 'concluido', 'Finalizar este atendimento?');
}

function marcarFalta(id) {
    alterarStatus(id, 'faltou', 'Marcar este paciente como faltou?');
}

function cancelarAgendamento(id) {
    alterarStatus(id, 'cancelado', 'Cancelar este agendamento?');
}

function showAlert(type, message) {
    // Criar alert Bootstrap
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Remover após 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
