/**
 * Sistema de Tour Guiado
 * Exibe tour interativo na primeira visita de cada página
 */

class TourGuiado {
    constructor(pageId, steps) {
        this.pageId = pageId;
        this.steps = steps;
        this.currentStep = 0;
        this.storageKey = `tour_completed_${pageId}`;
    }

    /**
     * Verificar se tour já foi completado
     */
    isCompleted() {
        return localStorage.getItem(this.storageKey) === 'true';
    }

    /**
     * Marcar tour como completado
     */
    markCompleted() {
        localStorage.setItem(this.storageKey, 'true');
    }

    /**
     * Resetar tour (para testes)
     */
    reset() {
        localStorage.removeItem(this.storageKey);
    }

    /**
     * Iniciar tour se não foi completado
     */
    start() {
        if (this.isCompleted()) {
            return;
        }

        this.createOverlay();
        this.showStep(0);
    }

    /**
     * Criar overlay escuro
     */
    createOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'tour-overlay';
        overlay.className = 'tour-overlay';
        document.body.appendChild(overlay);
    }

    /**
     * Remover overlay
     */
    removeOverlay() {
        const overlay = document.getElementById('tour-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    /**
     * Mostrar step específico
     */
    showStep(index) {
        if (index >= this.steps.length) {
            this.finish();
            return;
        }

        this.currentStep = index;
        const step = this.steps[index];

        // Verificar se elemento existe
        const element = document.querySelector(step.element);
        if (!element) {
            console.warn(`Tour: Elemento não encontrado: ${step.element}. Pulando para próximo step.`);
            // Pular para próximo step automaticamente
            this.showStep(index + 1);
            return;
        }

        // Remover tooltip anterior
        this.removeTooltip();

        // Destacar elemento
        this.highlightElement(step.element);

        // Criar tooltip
        this.createTooltip(step);
    }

    /**
     * Destacar elemento
     */
    highlightElement(selector) {
        // Remover destaque anterior
        document.querySelectorAll('.tour-highlight').forEach(el => {
            el.classList.remove('tour-highlight');
        });

        // Adicionar destaque ao elemento atual
        const element = document.querySelector(selector);
        if (element) {
            element.classList.add('tour-highlight');
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    /**
     * Criar tooltip
     */
    createTooltip(step) {
        const element = document.querySelector(step.element);
        if (!element) return;

        const tooltip = document.createElement('div');
        tooltip.id = 'tour-tooltip';
        tooltip.className = 'tour-tooltip';

        const rect = element.getBoundingClientRect();
        const position = step.position || 'bottom';

        // Conteúdo
        tooltip.innerHTML = `
            <div class="tour-tooltip-header">
                <span class="tour-step-counter">${this.currentStep + 1}/${this.steps.length}</span>
                <button class="tour-close" onclick="tourGuiado.skip()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="tour-tooltip-body">
                <h5>${step.title}</h5>
                <p>${step.description}</p>
            </div>
            <div class="tour-tooltip-footer">
                ${this.currentStep > 0 ? '<button class="btn btn-sm btn-outline-secondary" onclick="tourGuiado.prev()">Anterior</button>' : ''}
                <button class="btn btn-sm btn-primary" onclick="tourGuiado.next()">
                    ${this.currentStep === this.steps.length - 1 ? 'Concluir' : 'Próximo'}
                </button>
            </div>
        `;

        document.body.appendChild(tooltip);

        // Posicionar tooltip
        this.positionTooltip(tooltip, rect, position);
    }

    /**
     * Posicionar tooltip
     */
    positionTooltip(tooltip, rect, position) {
        const tooltipRect = tooltip.getBoundingClientRect();
        const margin = 10;

        let top, left;

        switch (position) {
            case 'top':
                top = rect.top - tooltipRect.height - margin;
                left = rect.left + (rect.width - tooltipRect.width) / 2;
                break;
            case 'bottom':
                top = rect.bottom + margin;
                left = rect.left + (rect.width - tooltipRect.width) / 2;
                break;
            case 'left':
                top = rect.top + (rect.height - tooltipRect.height) / 2;
                left = rect.left - tooltipRect.width - margin;
                break;
            case 'right':
                top = rect.top + (rect.height - tooltipRect.height) / 2;
                left = rect.right + margin;
                break;
            default:
                top = rect.bottom + margin;
                left = rect.left + (rect.width - tooltipRect.width) / 2;
        }

        // Ajustar se sair da tela
        if (left < 10) left = 10;
        if (left + tooltipRect.width > window.innerWidth - 10) {
            left = window.innerWidth - tooltipRect.width - 10;
        }
        if (top < 10) top = 10;

        tooltip.style.top = top + window.scrollY + 'px';
        tooltip.style.left = left + 'px';
    }

    /**
     * Remover tooltip
     */
    removeTooltip() {
        const tooltip = document.getElementById('tour-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    /**
     * Próximo step
     */
    next() {
        this.showStep(this.currentStep + 1);
    }

    /**
     * Step anterior
     */
    prev() {
        if (this.currentStep > 0) {
            this.showStep(this.currentStep - 1);
        }
    }

    /**
     * Pular tour
     */
    skip() {
        if (confirm('Deseja pular o tour guiado? Você pode reiniciá-lo a qualquer momento.')) {
            this.finish();
        }
    }

    /**
     * Finalizar tour
     */
    finish() {
        this.removeTooltip();
        this.removeOverlay();
        document.querySelectorAll('.tour-highlight').forEach(el => {
            el.classList.remove('tour-highlight');
        });
        this.markCompleted();
    }
}

// Variável global para controle
let tourGuiado = null;

/**
 * Inicializar tour para uma página
 */
function initTour(pageId, steps) {
    // Aguardar carregamento completo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            tourGuiado = new TourGuiado(pageId, steps);
            setTimeout(() => tourGuiado.start(), 500);
        });
    } else {
        tourGuiado = new TourGuiado(pageId, steps);
        setTimeout(() => tourGuiado.start(), 500);
    }
}

/**
 * Resetar todos os tours (admin)
 */
function resetAllTours() {
    if (confirm('Resetar todos os tours guiados? Eles serão exibidos novamente na próxima visita.')) {
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('tour_completed_')) {
                localStorage.removeItem(key);
            }
        });
        alert('Tours resetados com sucesso!');
        location.reload();
    }
}
