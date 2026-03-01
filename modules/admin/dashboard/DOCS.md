# Documentação do Módulo Dashboard

**Caminho**: `modules/admin/dashboard/`

## 1. Visão Geral
Dashboard principal administrativo, restrito a usuários do tipo `superadmin`. Exibe KPIs gerais (total de empresas, usuários, diagnósticos) e um gráfico de receita baseado em novas assinaturas.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Único arquivo do módulo. Executa queries de agregação e renderiza os cards de métricas e gráficos Chart.js. |

## 3. Fluxo de Dados
1.  **Cálculo de Métricas**:
    *   Executa `COUNT(*)` nas tabelas `empresas`, `users`, `diagnosticos`, `modelos`.
2.  **Gráfico de Receita**:
    *   Itera sobre os últimos 6 meses (via PHP loop).
    *   Para cada mês, executa uma *subquery* somando o valor de `empresas` criadas naquele mês.
    *   **Atenção**: Assume que o campo `created_at` define a receita, o que pode não refletir faturamento recorrente real.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Performance
- [ ] **Queries em Loop**: O cálculo do gráfico de receita executa uma query SQL dentro de um loop `for` (6 iterações). Embora pequeno, é uma má prática. Deveria usar `GROUP BY MONTH(created_at)`.
- [ ] **Data Fetching na View**: Toda a lógica de acesso a dados está no início do arquivo de visualização (`index.php`), dificultando testes e manutenção.

### Organização
- [ ] **Chart.js Inline**: O script de configuração do Chart.js está misturado ao HTML.
- [ ] **Hardcoded Logic**: A lógica de "Receita" assume que o valor da empresa é uma assinatura mensal fixa baseada na data de criação. Isso ignora cancelamentos, upgrades ou recorrência real.

### Refatorações Recomendadas
1.  Extrair lógica de queries para um DashboardService.
2.  Otimizar a query de receita para uma única consulta SQL com agregação por mês no banco de dados.
3.  Modularizar os widgets (cards) em componentes reutilizáveis.
