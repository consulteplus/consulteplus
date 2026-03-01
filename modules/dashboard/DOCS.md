# Documentação do Módulo Dashboard

**Caminho**: `modules/dashboard/`

## 1. Visão Geral
Este é a página inicial do sistema após o login (`index.php`). Seu objetivo é dar boas-vindas ao usuário, mostrar um resumo dos produtos adquiridos (cursos/mentorias) e oferecer atalhos para ações rápidas.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Único arquivo do módulo. Renderiza a dashboard. |

## 3. Funcionalidades
1.  **Boas-vindas**: Exibe o nome do usuário.
2.  **Meus Produtos**:
    *   Lista até 6 produtos onde o usuário está matriculado ou a empresa dele tem acesso.
    *   Consulta tabelas `mentoria_produtos`, `mentoria_matriculas` e `mentoria_acesso_empresas`.
    *   Exibe card com imagem, título e progresso (placeholder de 0%).
3.  **Ações Rápidas**:
    *   Links diretos para "Diagnóstico" (`gestao/diagnostico`), "Loja" (`produtos?view=loja`) e "Projetos" (`gestao/projetos`).

## 4. Dependências
-   **Banco de Dados**: Requer acesso às tabelas do módulo de Mentoria/Produtos para listar o conteúdo do usuário.

## 5. Análise de Código e Dívida Técnica ("Código Sujo")

### Organização
- [ ] **Lógica na View**: `index.php` contém queries SQL complexas misturadas com o HTML de exibição.

### Refatorações Recomendadas
1.  Mover queries para um Service ou Repository dedicado.
