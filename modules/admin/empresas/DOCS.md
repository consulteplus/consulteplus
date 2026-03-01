# Documentação do Módulo Empresas

**Caminho**: `modules/admin/empresas/`

## 1. Visão Geral
Gerenciamento central de Tenants (Empresas). Permite ao SuperAdmin visualizar, ativar/desativar empresas e gerenciar seus usuários internos. É o coração do sistema Multi-Tenant.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Listagem geral de empresas cadastradas com status e ações rápidas. |
| `view.php` | Painel de controle de uma empresa específica. Gerencia usuários, vê mentorias e projetos associados. |

## 3. Fluxo de Dados
1.  **Listagem (`index.php`)**:
    *   Carrega todas as empresas ordenadas por criação.
    *   Usa *Subqueries* para buscar nome e email do usuário "admin" (dono) de cada empresa.
2.  **Detalhes (`view.php`)**:
    *   Exibe dados cadastrais (`documento`, `email`, etc.).
    *   **CRUD de Usuários**: Permite adicionar, editar e remover usuários vinculados àquela empresa.
    *   **Abas**: Carrega dados de outros módulos (`mentoria_acesso_empresas`, `gestao_projetos`) filtrados pelo `company_id`.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Performance
- [ ] **Subqueries no Select**: Em `index.php`, duas subqueries (`SELECT ... FROM users ... LIMIT 1`) são executadas para *cada* empresa listada. Isso causará lentidão com o crescimento da base. O ideal é usar `JOIN`.
- [ ] **Data Fetching Síncrono**: `view.php` carrega Mentorias, Projetos e Usuários tudo de uma vez no carregamento da página, o que pode ser pesado. Carregamento via AJAX nas abas seria melhor.

### Segurança
- [ ] **CSRF**: As ações de `activate/deactivate` e o formulário de usuários não parecem ter tokens CSRF, apenas verificação de sessão.
- [ ] **Delete User**: A função de deletar usuário (`delete_user`) em `view.php` deveria verificar se o usuário a ser deletado realmente pertence à empresa em questão no `WHERE` principal, embora o `company_id` seja passado.

### Organização
- [ ] **Lógica Gigante**: `view.php` tem mais de 700 linhas, misturando processamento de formulário (POST) no topo com lógica de visualização.
- [ ] **Actions via GET**: Ativar/Desativar via GET em `index.php` é prático mas inseguro (violação de padrões REST/HTTP para ações mutáveis).

### Refatorações Recomendadas
1.  Otimizar a query de listagem usando `LEFT JOIN` com a tabela de users.
2.  Separar a lógica de gerenciamento de usuários de `view.php` para um `UsersController` ou arquivo dedicado.
3.  Implementar validação de CSRF nos formulários POST.
