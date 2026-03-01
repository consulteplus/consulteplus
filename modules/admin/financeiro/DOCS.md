# Documentação do Módulo Financeiro

**Caminho**: `modules/admin/financeiro/`

## 1. Visão Geral
Gerencia as assinaturas recorrentes das empresas (tenants). O foco atual é a integração com Gateway de Pagamento (Asaas) para criar e monitorar cobranças.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Lista todas as assinaturas registradas no banco local (`financeiro_assinaturas`). Exibe status (Ativa/Atrasada) e valores. |
| `nova_assinatura.php` | Formulário para criar uma nova assinatura. Cria/Atualiza cliente no Asaas e gera a assinatura via API. |

## 3. Fluxo de Dados
1.  **Criação (`nova_assinatura.php`)**:
    *   Coleta dados do form (Empresa, Valor, Ciclo).
    *   Verifica se a empresa já tem `asaas_customer_id`. Se não, cria no Asaas.
    *   Chama `AsaasService::createSubscription`.
    *   Salva o retorno (ID da assinatura, status) na tabela local.
2.  **Listagem (`index.php`)**:
    *   Exibe dados da tabela local.
    *   *Nota*: Não parece haver um mecanismo automático de sync de status (Webhook) visível nos arquivos listados aqui.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **CSRF**: Formulário de criação de assinatura sem token CSRF.
- [ ] **Validação de Valor**: O tratamento de vírgula/ponto (`str_replace`) é básico. Inputs monetários deveriam ter validação mais rígida.

### Funcionalidade
- [ ] **Sync em Tempo Real**: A listagem depende apenas do banco local. Se o cliente pagar no Asaas, o status aqui não muda a menos que um Webhook (fora deste diretório) atualize a tabela.
- [ ] **Funcionalidades Faltantes**: Botões de "Cancelar" e "Editar" estão presentes na UI mas com links `javascript:void(0)` e label "(Em breve)".

### Refatorações Recomendadas
1.  Implementar edição e cancelamento de assinaturas chamando a API do Asaas.
2.  Adicionar listener de Webhooks para atualizar status de pagamento (Pendente -> Pago).
3.  Melhorar sanitização de inputs numéricos.
