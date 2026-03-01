# Documentação do Módulo Usuários

**Caminho**: `modules/admin/usuarios/`

## 1. Visão Geral
Módulo restrito a SuperAdmins para gestão centralizada de todos os usuários do sistema, sejam eles administradores globais ou usuários vinculados a empresas (tenants).

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Listagem completa de usuários com DataTable e modais de edição. |
| `acoes.php` | API JSON para CRUD (Listar, Salvar, Excluir) e helper para listar empresas. |

## 3. Fluxo de Dados
1.  **Listagem (`acoes.php?acao=listar`)**:
    *   Retorna JSON com todos os usuários, fazendo `LEFT JOIN` com `empresas` para mostrar a qual tenant pertencem.
2.  **Criação/Edição (`acoes.php?acao=salvar`)**:
    *   Recebe JSON via POST.
    *   Verifica duplicidade de email (código de erro MySQL 1062).
    *   Faz o hash da senha com `password_hash` (Bcrypt por padrão no PHP moderno).

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **CSRF**: As chamadas AJAX em `index.php` para `acoes.php` não enviam token CSRF. Um site malicioso poderia forçar a exclusão de usuários se o admin estiver logado.
- [ ] **Exclusão de Si Mesmo**: Há uma trava para não excluir o próprio ID da sessão, o que é bom.

### Organização
- [ ] **Mistura de HTML/JS**: O arquivo `index.php` contém todo o JavaScript de controle da modal e do DataTable no final do arquivo.
- [ ] **Validação de Senha**: A validação `empty($senha)` para novos usuários é fraca. Não checa complexidade ou tamanho mínimo.

### Refatorações Recomendadas
1.  Adicionar verificação de complexidade de senha no backend.
2.  Implementar proteção CSRF nas rotas de API.
3.  Permitir filtrar usuários por Empresa na listagem (hoje carrega todos).
