/**
 * Loading States para Formulários
 * Gerencia estados visuais de loading em botões de submit
 */

class FormLoadingManager {
    constructor() {
        this.originalStates = new Map();
    }

    /**
     * Inicia estado de loading em um botão
     * @param {HTMLElement} button - Botão a ser modificado
     * @param {string} loadingText - Texto durante loading (padrão: "Processando...")
     */
    start(button, loadingText = 'Processando...') {
        if (!button) return;

        // Salvar estado original
        this.originalStates.set(button, {
            html: button.innerHTML,
            disabled: button.disabled,
            classes: button.className
        });

        // Aplicar estado de loading
        button.disabled = true;
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            ${loadingText}
        `;
    }

    /**
     * Mostra estado de sucesso
     * @param {HTMLElement} button - Botão a ser modificado
     * @param {string} successText - Texto de sucesso (padrão: "Sucesso!")
     * @param {number} duration - Duração em ms (padrão: 1500)
     */
    success(button, successText = 'Sucesso!', duration = 1500) {
        if (!button) return;

        button.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>
            ${successText}
        `;
        button.classList.remove('btn-primary', 'btn-secondary');
        button.classList.add('btn-success');

        // Restaurar após duração
        setTimeout(() => this.reset(button), duration);
    }

    /**
     * Mostra estado de erro
     * @param {HTMLElement} button - Botão a ser modificado
     * @param {string} errorText - Texto de erro (padrão: "Erro!")
     * @param {number} duration - Duração em ms (padrão: 2000)
     */
    error(button, errorText = 'Erro!', duration = 2000) {
        if (!button) return;

        button.innerHTML = `
            <i class="bi bi-x-circle me-2"></i>
            ${errorText}
        `;
        button.classList.remove('btn-primary', 'btn-secondary');
        button.classList.add('btn-danger');

        // Restaurar após duração
        setTimeout(() => this.reset(button), duration);
    }

    /**
     * Restaura estado original do botão
     * @param {HTMLElement} button - Botão a ser restaurado
     */
    reset(button) {
        if (!button || !this.originalStates.has(button)) return;

        const original = this.originalStates.get(button);
        button.innerHTML = original.html;
        button.disabled = original.disabled;
        button.className = original.classes;

        this.originalStates.delete(button);
    }
}

// Instância global
const formLoading = new FormLoadingManager();

/**
 * Adiciona loading automático a todos os formulários
 */
document.addEventListener('DOMContentLoaded', function () {
    // Interceptar todos os submits de formulários
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // Pegar botão de submit
            const submitBtn = form.querySelector('button[type="submit"]');

            if (submitBtn && !submitBtn.hasAttribute('data-no-loading')) {
                // Texto customizado ou padrão
                const loadingText = submitBtn.getAttribute('data-loading-text') || 'Processando...';
                formLoading.start(submitBtn, loadingText);
            }
        });
    });
});

/**
 * Função auxiliar para AJAX forms
 * @param {HTMLFormElement} form - Formulário
 * @param {string} url - URL do endpoint
 * @param {Function} onSuccess - Callback de sucesso
 * @param {Function} onError - Callback de erro
 */
function submitFormAjax(form, url, onSuccess, onError) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    const loadingText = submitBtn?.getAttribute('data-loading-text') || 'Enviando...';

    if (submitBtn) {
        formLoading.start(submitBtn, loadingText);
    }

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (submitBtn) {
                    formLoading.success(submitBtn, 'Sucesso!');
                }
                if (onSuccess) onSuccess(data);
            } else {
                if (submitBtn) {
                    formLoading.error(submitBtn, 'Erro!');
                }
                if (onError) onError(data);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            if (submitBtn) {
                formLoading.error(submitBtn, 'Erro de conexão');
            }
            if (onError) onError(error);
        });
}

// Exportar para uso global
window.formLoading = formLoading;
window.submitFormAjax = submitFormAjax;
