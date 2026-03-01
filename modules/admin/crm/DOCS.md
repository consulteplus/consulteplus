# Documentação do Módulo CRM

**Caminho**: `modules/admin/crm/`

## 1. Visão Geral
O módulo CRM (Customer Relationship Management) gerencia o "Pipeline de Vendas", permitindo a visualização de negócios em formato Kanban ou Lista. Ele lida com Etapas de Funil, Negócios (Deals), Vínculos com Clientes/Empresas e Histórico de Atividades.

**Última Atualização**: Janeiro/2026 (Refatoração de Segurança e Arquitetura)

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | View principal. Instancia o `CrmService` para buscar dados iniciais e renderiza o layout. (Lógica limpa). |
| `acoes.php` | Controller API (JSON). Recebe requisições do frontend, valida sessão e delega para `CrmService`. |
| `detalhes.php` | Visão detalhada de um negócio. Consome CSS externo. |
| `config.php` | Gerenciamento de Funis e Etapas. |
| `../../classes/CrmService.php` | **Service Layer**. Contém TODA a lógica de banco de dados e regras de negócio. |

## 3. Fluxo de Dados (Nova Arquitetura)
1.  **Frontend (`index.php` / JS)**:
    *   Faz chamadas ao `acoes.php` via `fetch()` para operações dinâmicas.
    *   Carregamento inicial feito via PHP chamando métodos do Service diretamente (ex: `listarFunis()`).
2.  **Controller (`acoes.php`)**:
    *   Recebe request JSON/GET.
    *   Verifica permissões (`auth.php`).
    *   Chama método correspondente em `CrmService`.
    *   Retorna JSON para o frontend.
3.  **Service (`CrmService.php`)**:
    *   Executa queries seguras com **Prepared Statements**.
    *   Retorna arrays de dados puros ou booleanos.
    *   Lida com lógica de criação de clientes "on-the-fly".

## 4. Estado Atual e Qualidade do Código

### Melhorias Implementadas (Refatoração 2026)
- [x] **Segurança**: SQL Injection eliminado. Todas as queries utilizam `bind_param`.
- [x] **Arquitetura**: Separação clara entre View (`index.php`), Controller (`acoes.php`) e Model/Service (`CrmService`).
- [x] **Organização**: CSS extraído para `assets/css/crm.css`.
- [x] **Logs**: Removido logging inseguro em arquivo de texto. Utiliza-se handlers de erro padrão.

### Pontos de Atenção (Roadmap Futuro)
- [ ] **Paginação Real**: O Kanban carrega 10 items por vez, mas a paginação completa na View Lista poderia ser otimizada no Service (retornar `total_count`).
- [ ] **Validação Exaustiva**: Implementar validador de schemas para entradas JSON complexas.
