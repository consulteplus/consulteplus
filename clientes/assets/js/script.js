$(document).ready(function () {
    // Inicializar DataTables
    if ($('.datatable').length) {
        $('.datatable').DataTable({
            language: {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }

    // Confirmação de exclusão
    $('.btn-delete').on('click', function (e) {
        if (!confirm('Tem certeza que deseja excluir este registro?')) {
            e.preventDefault();
        }
    });

    // Máscaras de input
    $('[data-mask="phone"]').on('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
        this.value = value;
    });

    $('[data-mask="cpf"]').on('input', function () {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
        this.value = value;
    });

    $('[data-mask="date"]').on('input', function () {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d{2})(\d{0,4})/, '$1/$2/$3');
        this.value = value;
    });

    // Toggle de senha
    $('.toggle-password').on('click', function () {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Auto-hide de alertas
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 3000);

    // Auto-expandir categoria ativa do menu ao carregar a página
    const activeLink = document.querySelector('.sidebar .nav-link.active');
    if (activeLink) {
        const collapseParent = activeLink.closest('.collapse');
        if (collapseParent) {
            const bsCollapse = new bootstrap.Collapse(collapseParent, {
                toggle: true
            });
        }
    }
});
