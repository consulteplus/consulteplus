/**
 * Configuração de Tours por Página
 */

// Tour do Dashboard
const tourDashboard = [
    {
        element: '.page-header',
        title: 'Bem-vindo ao Dashboard! 👋',
        description: 'Esta é sua visão geral do sistema. Aqui você encontra estatísticas e informações importantes.',
        position: 'bottom'
    },
    {
        element: '.stat-card',
        title: 'Estatísticas Rápidas',
        description: 'Veja rapidamente o total de pacientes, agendamentos e profissionais ativos.',
        position: 'bottom'
    },
    {
        element: '.card',
        title: 'Informações do Sistema',
        description: 'Acompanhe salas, agendamentos e outras informações importantes em tempo real.',
        position: 'bottom'
    }
];

// Tour de Agendamentos
const tourAgendamentos = [
    {
        element: '.page-header',
        title: 'Gestão de Agendamentos 📅',
        description: 'Aqui você gerencia todos os agendamentos da clínica.',
        position: 'bottom'
    },
    {
        element: '.btn.btn-primary',
        title: 'Criar Agendamento',
        description: 'Clique aqui para criar um novo agendamento. O sistema vai guiá-lo pelo processo.',
        position: 'bottom'
    },
    {
        element: '.card',
        title: 'Lista de Agendamentos',
        description: 'Visualize todos os agendamentos. Você pode editar, alterar status ou criar prontuários.',
        position: 'top'
    }
];

// Tour de Pacientes
const tourPacientes = [
    {
        element: '.page-header',
        title: 'Cadastro de Pacientes 👥',
        description: 'Gerencie o cadastro completo de todos os pacientes.',
        position: 'bottom'
    },
    {
        element: '.btn.btn-primary',
        title: 'Adicionar Paciente',
        description: 'Cadastre novos pacientes com informações completas.',
        position: 'bottom'
    },
    {
        element: '.card',
        title: 'Lista de Pacientes',
        description: 'Visualize, edite ou exclua pacientes. Use a busca para encontrar rapidamente.',
        position: 'top'
    }
];

// Tour de Relatórios
const tourRelatorios = [
    {
        element: '.page-header h1',
        title: 'Relatórios Gerenciais 📊',
        description: 'Acesse relatórios detalhados sobre atendimentos, ocupação e produtividade.',
        position: 'bottom'
    },
    {
        element: '.row .col-md-4:first-child .card',
        title: 'Tipos de Relatórios',
        description: 'Escolha entre relatórios de atendimentos, ociosidade de salas ou produtividade.',
        position: 'bottom'
    },
    {
        element: '.card form',
        title: 'Filtros e Geração',
        description: 'Selecione o período e clique em "Gerar Relatório" para visualizar os dados.',
        position: 'bottom'
    }
];

// Tour de Configurações
const tourConfiguracoes = [
    {
        element: '.page-header h1',
        title: 'Configurações do Sistema ⚙️',
        description: 'Configure parâmetros gerais, notificações e integrações.',
        position: 'bottom'
    },
    {
        element: '.nav.nav-tabs',
        title: 'Abas de Configuração',
        description: 'Navegue entre diferentes categorias de configurações.',
        position: 'bottom'
    },
    {
        element: '.card form',
        title: 'Formulário de Configuração',
        description: 'Preencha os campos e não esqueça de salvar suas alterações!',
        position: 'top'
    }
];

// Mapa de tours por página
const toursConfig = {
    'dashboard': tourDashboard,
    'agendamentos': tourAgendamentos,
    'pacientes': tourPacientes,
    'relatorios': tourRelatorios,
    'configuracoes': tourConfiguracoes
};

/**
 * Inicializar tour automaticamente baseado na página
 */
function autoInitTour() {
    // Detectar página atual pela URL
    const path = window.location.pathname;
    let pageId = null;

    if (path.includes('/dashboard')) pageId = 'dashboard';
    else if (path.includes('/agendamentos')) pageId = 'agendamentos';
    else if (path.includes('/pacientes')) pageId = 'pacientes';
    else if (path.includes('/relatorios')) pageId = 'relatorios';
    else if (path.includes('/configuracoes')) pageId = 'configuracoes';

    // Iniciar tour se configurado
    if (pageId && toursConfig[pageId]) {
        initTour(pageId, toursConfig[pageId]);
    }
}

// Auto-inicializar
autoInitTour();
