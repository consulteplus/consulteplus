# Documentação do Módulo Configurações

**Caminho**: `modules/configuracoes/`

## 1. Visão Geral
Centraliza todas as definições administrativas do sistema, permitindo ao Administrador ajustar parâmetros da clínica, integrações, agenda e perfil público dos profissionais.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Arquivo monolítico que gerencia 5 abas de configuração (Clínica, Agenda Interna, Agenda Online, Especialidades, Integrações). |
| `agendamento-online.php` | (Legado/Em desuso?) A lógica de agendamento online parece ter sido mergeada no `index.php`. |
| `financeiro.php` | (Provável desuso) Configurações financeiras parecem ser tratadas globalmente ou em outro local. |
| `n8n.php` | (Possível script auxiliar) Configuração do N8N também está presente no `index.php`. |

## 3. Áreas de Configuração (`index.php`)

### 3.1. Clínica e Sistema (Aba "Clínica")
-   Edita registros na tabela `configuracoes`.
-   **Tabela**: `configuracoes` (chave, valor, grupo, company_id).
-   **Grupos**: `clinica`, `sistema`, `backup`.

### 3.2. Agenda Interna
-   Define regras para o calendário interno (horários, intervalos).
-   **Grupo**: `agendamento` na tabela `configuracoes`.

### 3.3. Agendamento Online
-   Configura o perfil público dos profissionais para o site de agendamento.
-   **Tabelas**: `profissionais`, `config_agendamento_online` (slug, ativo_online, telemedicina).
-   Ativa/Desativa profissionais e define link de telemedicina/Zoom.

### 3.4. Especialidades
-   CRUD simples de especialidades médicas.
-   **Tabela**: `especialidades`.

### 3.5. Integrações (N8N)
-   Configura Webhooks para automação e tokens de API.
-   **Armazenamento**: Arquivo JSON `config/n8n_config.json`.
-   **Campos**: Webhook Notificações, Webhook Roadmap IA, Webhook Análise IA.

## 4. Observações Técnicas
-   **Dívida Técnica**: O arquivo `index.php` é muito extenso e mistura lógica de processamento de vários formulários diferentes com a exibição HTML. Idealmente deveria ser separado em `acoes.php`.
-   **Segurança**: Apenas usuários com permissão `['admin']` podem acessar.

## 5. Análise de Código e Dívida Técnica ("Código Sujo")

### Organização
- [ ] **Arquivo Monolítico**: `index.php` (425+ linhas) viola princípios CLEAN/SOLID. Ele gerencia roteamento de abas, processamento de 5 formulários diferentes e renderização HTML ao mesmo tempo.
- [ ] **Mistura de Responsabilidades**: Configurações de "Integração N8N" (JSON) e "Agendamento Online" (SQL) estão tratadas no mesmo fluxo.

### Segurança
- [ ] **CSRF**: Os forms POST não validam tokens anti-CSRF. Como este módulo altera configurações sensíveis, é um vetor de ataque crítico (um Admin poderia ser forçado a alterar o webhook do N8N clicando em um link malicioso).
- [ ] **Validação de Input**: O POST `agendamento_online` insere dados diretamente no banco com validação mínima.

### Refatorações Recomendadas
1.  Extrair a lógica de POST para `acoes.php` ou `ConfigController.php`.
2.  Implementar CSRF Token.
3.  Modularizar as abas em arquivos parciais (`views/clinica.php`, `views/n8n.php`).
