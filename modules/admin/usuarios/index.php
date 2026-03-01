<?php
require_once __DIR__ . '/../header.php';

// Apenas SuperAdmin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'superadmin') {
    echo '<div class="alert alert-danger m-4">Acesso negado.</div>';
    require_once __DIR__ . '/../../../includes/footer.php';
    exit;
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Usuários do Sistema</h1>
            <p class="text-muted small mb-0">Gerencie os administradores, médicos e equipe.</p>
        </div>
        <button class="btn btn-primary shadow-sm" onclick="openModal()">
            <i class="bi bi-person-plus me-2"></i>Novo Usuário
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="tableUsers" class="table table-hover align-middle mb-0 w-100">
                    <thead class="bg-light text-uppercase text-muted small">
                        <tr>
                            <th class="ps-4">Nome / Email</th>
                            <th>Telefone</th>
                            <th>Empresa</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-end pe-4" width="100">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Novo Usuário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formUsuario">
                    <input type="hidden" id="userId" name="id">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Nome Completo</label>
                        <input type="text" class="form-control" id="userNome" name="nome" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase">Email</label>
                            <input type="email" class="form-control" id="userEmail" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase">Telefone</label>
                            <input type="text" class="form-control" id="userTelefone" name="telefone"
                                placeholder="(00) 00000-0000">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase">Tipo de Acesso</label>
                            <select class="form-select" id="userTipo" name="tipo" required>
                                <option value="superadmin">Super Admin</option>
                                <option value="admin">Administrador (Gestor)</option>
                                <option value="medico">Médico / Especialista</option>
                                <option value="secretaria">Secretária / Recepcionista</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-uppercase">Empresa</label>
                            <select class="form-select" id="userEmpresa" name="company_id" required>
                                <!-- Populate JS -->
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Senha</label>
                        <input type="password" class="form-control" id="userSenha" name="senha"
                            autocomplete="new-password">
                        <small class="text-muted d-block mt-1" id="passHelp">Preencha apenas se quiser alterar a
                            senha.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="salvarUsuario()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
    let modalEl;
    let modalUsuario;
    let empresasList = [];

    document.addEventListener('DOMContentLoaded', () => {
        modalEl = document.getElementById('modalUsuario');
        modalUsuario = new bootstrap.Modal(modalEl);

        // Carregar Empresas Dropdown
        fetch(BASE_URL + 'modules/admin/usuarios/acoes.php?acao=empresas')
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data) {
                    empresasList = res.data;
                    let opts = res.data.map(e => `<option value="${e.id}">${e.nome}</option>`).join('');
                    document.getElementById('userEmpresa').innerHTML = opts;
                }
            });

        // DataTable
        $('#tableUsers').DataTable({
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
            ajax: {
                url: BASE_URL + 'modules/admin/usuarios/acoes.php?acao=listar',
                dataSrc: function (json) {
                    if (!json.success) {
                         console.error(json.message);
                         // Opcional: Mostrar erro na tela
                         return [];
                    }
                    return json.data;
                }
            },
            order: [[5, 'desc']],
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        let iniciais = row.nome.substring(0, 2).toUpperCase();
                        let bg = 'bg-primary';
                        if (row.tipo === 'superadmin') bg = 'bg-danger';
                        if (row.tipo === 'admin') bg = 'bg-warning text-dark';

                        return `
                        <div class="d-flex align-items-center ps-2">
                            <div class="avatar-initials me-3 ${bg} text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px; font-weight:bold;">${iniciais}</div>
                            <div>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">${row.nome}</div>
                                <small class="text-muted">${row.email}</small>
                            </div>
                        </div>`;
                    }
                },
                { data: 'telefone', render: function (d) { return d || '-'; } },
                { data: 'empresa_nome', render: function (d) { return `<span class="badge bg-light text-dark border">${d || 'Global'}</span>`; } },
                {
                    data: 'tipo',
                    render: function (d) {
                        const map = {
                            'superadmin': { label: 'SUPER ADMIN', class: 'bg-danger' },
                            'admin': { label: 'ADMINISTRADOR', class: 'bg-dark' },
                            'medico': { label: 'MÉDICO', class: 'bg-info text-dark' },
                            'secretaria': { label: 'SECRETÁRIA', class: 'bg-warning text-dark' }
                        };
                        let conf = map[d] || { label: d.toUpperCase(), class: 'bg-secondary' };
                        return `<span class="badge ${conf.class} rounded-pill" style="font-size: 0.7rem;">${conf.label}</span>`;
                    }
                },
                {
                    data: 'ativo',
                    render: function (d) {
                        return d == 1
                            ? '<span class="badge bg-success bg-opacity-10 text-success">Ativo</span>'
                            : '<span class="badge bg-danger bg-opacity-10 text-danger">Inativo</span>';
                    }
                },
                {
                    data: 'created_at',
                    render: function (d) { return `<small class="text-muted">${new Date(d).toLocaleDateString('pt-BR')}</small>`; }
                },
                {
                    data: 'id',
                    className: 'text-end pe-4',
                    orderable: false,
                    render: function (d, type, row) {
                        // Passar objeto row como string para editar
                        // Obs: passar JSON em onclick pode ser tricky com aspas
                        let jsonRow = encodeURIComponent(JSON.stringify(row));
                        return `
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="editarUsuario('${jsonRow}')" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="excluirUsuario(${d})" title="Excluir">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                        `;
                    }
                }
            ]
        });
    });

    function openModal() {
        document.getElementById('formUsuario').reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerText = 'Novo Usuário';
        document.getElementById('passHelp').innerText = 'Senha obrigatória para novos usuários.';
        document.getElementById('userSenha').required = true;
        modalUsuario.show();
    }

    function editarUsuario(jsonRow) {
        let user = JSON.parse(decodeURIComponent(jsonRow));

        document.getElementById('userId').value = user.id;
        document.getElementById('userNome').value = user.nome;
        document.getElementById('userEmail').value = user.email;
        document.getElementById('userTelefone').value = user.telefone;
        document.getElementById('userTipo').value = user.tipo;
        document.getElementById('userEmpresa').value = user.company_id;

        document.getElementById('modalTitle').innerText = 'Editar Usuário';
        document.getElementById('passHelp').innerText = 'Deixe em branco para manter a senha atual.';
        document.getElementById('userSenha').required = false;
        document.getElementById('userSenha').value = '';

        modalUsuario.show();
    }

    function salvarUsuario() {
        let form = document.getElementById('formUsuario');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let formData = new FormData(form);
        let data = Object.fromEntries(formData.entries());

        fetch(BASE_URL + 'modules/admin/usuarios/acoes.php?acao=salvar', {
            method: 'POST',
            body: JSON.stringify(data)
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Sucesso', 'Usuário salvo com sucesso!', 'success');
                    modalUsuario.hide();
                    $('#tableUsers').DataTable().ajax.reload();
                } else {
                    Swal.fire('Erro', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Erro', 'Erro de conexão', 'error'));
    }

    function excluirUsuario(id) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Remover este usuário é irreversível.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(BASE_URL + 'modules/admin/usuarios/acoes.php?acao=excluir', {
                    method: 'POST',
                    body: JSON.stringify({ id: id })
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Excluído!', 'Usuário removido.', 'success');
                            $('#tableUsers').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Erro', res.message, 'error');
                        }
                    });
            }
        })
    }
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>