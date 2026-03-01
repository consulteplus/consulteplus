// Funções auxiliares para templates e conectores
$(document).ready(function () {

    window.aplicarTemplate = function (tipo) {
        // Limpar filtros existentes
        $('#filtrosContainer').empty();
        filtroCount = 0;

        const templates = {
            'novos_30_dias': {
                condicao: 'AND',
                filtros: [
                    { campo: 'created_at', operador: '>=', valor: getDataMenos30Dias() }
                ]
            },
            'qualificados': {
                condicao: 'AND',
                filtros: [
                    { campo: 'status', operador: '=', valor: 'qualificado' }
                ]
            },
            'sem_email': {
                condicao: 'AND',
                filtros: [
                    { campo: 'email', operador: 'IS NULL', valor: '' }
                ]
            },
            'facebook': {
                condicao: 'AND',
                filtros: [
                    { campo: 'origem', operador: '=', valor: 'Facebook' }
                ]
            }
        };

        const template = templates[tipo];
        if (template) {
            $('#condicaoGlobal').val(template.condicao);
            $('#condicaoBadge').text(template.condicao);
            template.filtros.forEach(filtro => {
                adicionarFiltro(filtro);
            });
            atualizarPreview();
        }
    };

    function getDataMenos30Dias() {
        const data = new Date();
        data.setDate(data.getDate() - 30);
        return data.toISOString().split('T')[0];
    }

    window.adicionarConector = function (filtroId) {
        const condicao = $('#condicaoGlobal').val();
        const conector = `
        <div class="filter-connector" data-conector="${filtroId}">
            <span class="filter-connector-badge">${condicao}</span>
        </div>
    `;
        $(`#${filtroId}`).before(conector);
    };

    // Atualizar badge de condição ao mudar select
    $('#condicaoGlobal').on('change', function () {
        const condicao = $(this).val();
        $('#condicaoBadge').text(condicao);

        // Atualizar conectores visuais
        $('.filter-connector-badge').text(condicao);

        atualizarPreview();
    });

});
