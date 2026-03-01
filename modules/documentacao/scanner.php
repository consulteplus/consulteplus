<?php
/**
 * ModuleScanner - Escaneia módulos e encontra arquivos DOCS.md
 */
class ModuleScanner
{
    private $modulesPath;

    public function __construct($modulesPath)
    {
        $this->modulesPath = rtrim($modulesPath, '/\\');
    }

    /**
     * Escaneia todos os módulos e retorna lista estruturada
     * @param bool $isAdmin Se true, inclui módulos admin
     * @return array Módulos categorizados
     */
    public function scan($isAdmin = false)
    {
        $modules = [
            'public' => [],
            'user' => [],
            'admin' => []
        ];

        // Lista de diretórios a escanear
        $dirs = glob($this->modulesPath . '/*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $moduleName = basename($dir);
            $docsFile = $dir . '/DOCS.md';

            if (!file_exists($docsFile)) {
                continue; // Pula se não tem DOCS.md
            }

            // Categoriza o módulo
            $category = $this->categorizeModule($moduleName);

            // Se não é admin, pula módulos admin
            if ($category === 'admin' && !$isAdmin) {
                continue;
            }

            $modules[$category][] = [
                'name' => $moduleName,
                'path' => $docsFile,
                'title' => $this->getModuleTitle($moduleName),
                'icon' => $this->getModuleIcon($moduleName)
            ];

            // Se for o módulo admin, escanear submódulos
            if ($moduleName === 'admin' && $isAdmin) {
                $this->scanAdminSubmodules($dir, $modules['admin']);
            }
        }

        // Ordena alfabeticamente
        foreach ($modules as &$category) {
            usort($category, function ($a, $b) {
                return strcmp($a['title'], $b['title']);
            });
        }

        return $modules;
    }

    /**
     * Escaneia submódulos dentro de /admin/
     */
    private function scanAdminSubmodules($adminDir, &$adminModules)
    {
        $subDirs = glob($adminDir . '/*', GLOB_ONLYDIR);

        foreach ($subDirs as $subDir) {
            $subModuleName = basename($subDir);
            $docsFile = $subDir . '/DOCS.md';

            if (!file_exists($docsFile)) {
                continue;
            }

            $adminModules[] = [
                'name' => 'admin/' . $subModuleName,
                'path' => $docsFile,
                'title' => $this->getModuleTitle($subModuleName),
                'icon' => $this->getModuleIcon($subModuleName)
            ];
        }
    }

    /**
     * Categoriza módulo baseado no nome
     */
    private function categorizeModule($moduleName)
    {
        $adminModules = ['admin', 'deploy', 'configuracoes'];
        $publicModules = ['documentacao', 'onboarding'];

        if (in_array($moduleName, $adminModules)) {
            return 'admin';
        }

        if (in_array($moduleName, $publicModules)) {
            return 'public';
        }

        return 'user';
    }

    /**
     * Retorna título amigável do módulo
     */
    private function getModuleTitle($moduleName)
    {
        $titles = [
            'gestao' => 'Gestão',
            'ferramentas' => 'Ferramentas',
            'financeiro' => 'Financeiro',
            'produtos' => 'Produtos (LMS)',
            'perfil' => 'Perfil',
            'dashboard' => 'Dashboard',
            'loja' => 'Loja',
            'documentacao' => 'Documentação',
            'onboarding' => 'Onboarding',
            'minhas-notificacoes' => 'Notificações',
            'admin' => 'Administração',
            'deploy' => 'Deploy',
            'configuracoes' => 'Configurações',
            'banco-de-dados' => 'Banco de Dados',
            'includes-css' => 'Includes & CSS',

            // Submódulos do Admin
            'crm' => 'CRM (Pipeline)',
            'marketing' => 'Marketing',
            'diagnosticos' => 'Diagnósticos',
            'empresas' => 'Empresas',
            'usuarios' => 'Usuários',
            'ferramentas-admin' => 'Ferramentas'
        ];

        return $titles[$moduleName] ?? ucfirst($moduleName);
    }

    /**
     * Retorna ícone Bootstrap Icons do módulo
     */
    private function getModuleIcon($moduleName)
    {
        $icons = [
            'gestao' => 'kanban',
            'ferramentas' => 'tools',
            'financeiro' => 'cash-coin',
            'produtos' => 'mortarboard',
            'perfil' => 'person-circle',
            'dashboard' => 'speedometer2',
            'loja' => 'shop',
            'documentacao' => 'book',
            'onboarding' => 'flag',
            'minhas-notificacoes' => 'bell',
            'admin' => 'shield-lock',
            'deploy' => 'rocket-takeoff',
            'configuracoes' => 'gear',
            'banco-de-dados' => 'database',
            'includes-css' => 'file-code',

            // Submódulos do Admin
            'crm' => 'kanban',
            'marketing' => 'megaphone',
            'diagnosticos' => 'list-check',
            'empresas' => 'buildings',
            'usuarios' => 'people-fill',
            'ferramentas-admin' => 'tools'
        ];

        return $icons[$moduleName] ?? 'file-text';
    }
}
