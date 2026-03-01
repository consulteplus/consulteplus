# Documentação do Módulo Diagnósticos

**Caminho**: `modules/admin/diagnosticos/`

## 1. Visão Geral
Módulo central para criação e gestão de Diagnósticos (Assessments). Permite criar modelos de formulários, cadastrar perguntas divididas em seções, usar IA para gerar conteúdo automaticamente e atribuir diagnósticos a empresas para execução recorrente.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Lista os Modelos de Diagnóstico existentes e permite criar novos (título/descrição). |
| `novo.php` | Wizard para criação assistida por IA ou manual. Coleta parâmetros (setor, público, áreas de foco). |
| `perguntas.php` | Gerenciamento (CRUD) de perguntas e seções de um modelo específico. |
| `atribuir.php` | Gerencia a distribuição dos diagnósticos para as empresas (recorrência, vínculo). |
| `criar_ia.php` | Processa a solicitação de geração via OpenAI/LLM e retorna estrutura JSON. |
| `salvar_perguntas_ia.php` | Persiste a estrutura gerada pela IA no banco de dados. |

## 3. Fluxo de Dados
1.  **Criação (`novo.php` -> `criar_ia.php`)**:
    *   Usuário define parâmetros.
    *   IA gera JSON com seções, perguntas e pontuação.
    *   `criar_completo.php` salva o modelo e as perguntas.
2.  **Edição (`perguntas.php`)**:
    *   Permite refinar o que a IA gerou ou criar do zero.
    *   Usa tabelas `gestao_diagnostico_perguntas`.
3.  **Distribuição (`atribuir.php`)**:
    *   Cria registros em `recursos_atribuicoes` vinculando `empresa_id` e `modelo_id`.
    *   Define frequência (mensal, trimestral, etc.) que o cronjob (fora deste módulo) observará.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **SQL Injection**: Em `perguntas.php`, há trechos de delete (`DELETE FROM ... WHERE id=$id`) sem prepared statements, concatenando a variável diretamente. Risco alto.
- [ ] **Validação de ID**: Em vários arquivos, IDs vindos de GET/POST são convertidos para `intval` (bom), mas nem sempre validados se pertencem ao contexto correto antes de ações de exclusão.

### Organização
- [ ] **Lógica de Ordenação**: A ordenação de perguntas e seções (`reordenar_*.php`) é feita via scripts avulsos que recebem AJAX. Poderiam estar centralizados em um Controller API.
- [ ] **Mistura de UI e Lógica**: `novo.php` tem uma lógica complexa de UI (wizard) misturada com chamadas JavaScript inline para APIs de IA.

### Funcionalidade
- [ ] **Recorrência**: A lógica de "Próxima Execução" em `atribuir.php` é apenas visual/informativa. Não vi o script "Worker" que realmente cria as novas instâncias de diagnósticos baseado nessas datas. (Pode estar fora do diretório admin).

### Refatorações Recomendadas
1.  **Sanitização Urgente**: Corrigir todas as queries manuais (`query("DELETE...")`) para `prepare/bind_param`.
2.  **Abstração de IA**: Isolar a lógica de prompt/chamada de IA em uma classe `DiagnosticGeneratorService`.
3.  **Padronizar API**: Transformar os scripts soltos (`salvar_perguntas_ia.php`, `reordenar_...`) em rotas de uma API estruturada.
