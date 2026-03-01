# Documentação do Módulo Marketing

**Caminho**: `modules/admin/marketing/`

## 1. Visão Geral
Módulo composto por duas funcionalidades principais:
1.  **Base de Leads (`leads/`)**: Gestão de contatos comerciais (CRM leve pre-venda).
2.  **Gerador de Leads (`gerador_leads/`)**: Integração com Robôs de Scraping (via n8n) para buscar contatos no Google Maps, LinkedIn, etc.

## 2. Estrutura de Arquivos

### Submódulo `leads/`
| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Listagem principal de Leads cadastrados. |
| `acoes.php` | API para CRUD de Leads (Listar, Salvar, Excluir). |

### Submódulo `gerador_leads/`
| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Interface de controle do robô. Abas para iniciar busca e revisar resultados. |
| `acoes.php` | Backend complexo que dispara o Webhook do n8n (CURL) e recebe/processa aprovações. |
| `webhook_receiver.php` | Endpoint passivo para receber os leads encontrados pelo n8n de forma assíncrona. |

## 3. Fluxo de Dados
1.  **Scraping (`gerador_leads`)**:
    *   Usuário preenche termo e local.
    *   `acoes.php` envia POST para o n8n via CURL (síncrono/blocante no MVP).
    *   n8n retorna JSON com lista de empresas.
    *   Sistema salva em `leads_scraped` com status 'pendente'.
2.  **Aprovação**:
    *   Usuário seleciona leads na aba "Resultados".
    *   `acoes.php` move dados de `leads_scraped` para a tabela definitiva `leads`.
3.  **Gestão (`leads`)**:
    *   Exibe leads aprovados ou criados manualmente.
    *   Permite marcar como "Cliente" (embora a lógica de migração para tabela `users` não esteja explícita neste arquivo).

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Performance
- [ ] **CURL Síncrono**: O envio para o n8n (`trigger_scraping`) aguarda a resposta com `set_time_limit(300)`. Se o robô demorar mais que 5min, o PHP mata o processo e o usuário recebe erro 504/Timeout.
- [ ] **Memory Limit**: Há um `ini_set('memory_limit', '512M')` hardcore no código.

### Segurança
- [ ] **Exposição de Arquivos**: O arquivo `last_response.json` é gravado na pasta pública para debug. Se contiver dados sensíveis, qualquer um pode baixar.
- [ ] **Validação de Webhook**: O endpoint `webhook_receiver.php` (se usado) não parece ter validação de assinatura/token para garantir que vem do n8n legítimo.

### Organização
- [ ] **Lógica Duplicada**: Há lógica de inserção de leads tanto em `leads/acoes.php` quanto em `gerador_leads/acoes.php`.
- [ ] **Redirecionamento**: `marketing/index.php` é apenas um redirect fixo.

### Refatorações Recomendadas
1.  Transformar o acionamento do Scraping em **Assíncrono** (Fire-and-forget), recebendo os dados apenas pelo Webhook Receiver.
2.  Remover a gravação de `last_response.json` ou movê-lo para pasta temporária não pública.
3.  Unificar a lógica de criação de Lead em um Service comum.
