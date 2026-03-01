# Documentação do Módulo Financeiro

**Caminho**: `modules/financeiro/`

## 1. Visão Geral
Este módulo **não** é uma gestão financeira empresarial genérica para o usuário final. Ele é focado exclusivamente na **Minha Assinatura** (gestão da assinatura do sistema SaaS pelo cliente). Permite visualizar faturas e realizar pagamentos via Asaas.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `minha_assinatura.php` | Página principal. Exibe detalhes do plano atual e lista faturas pendentes buscadas da API Asaas. |
| `acoes_financeiro.php` | API Backend para processar pagamentos. Gera Pix, Boleto e processa Cartão de Crédito. |

## 3. Integrações Externas
-   **Asaas API**: O módulo depende fortemente da classe `includes/services/AsaasService.php`.
    -   Lista pagamentos pendentes (`listPendingPayments`).
    -   Gera QR Code Pix.
    -   Gera linha digitável de Boleto.
    -   Processa transações de Cartão de Crédito.

## 4. Dependências Internas
-   **Tabela `empresas`**: Busca `asaas_customer_id` para consultar a API.
-   **Tabela `financeiro_assinaturas`** (Opcional): Usada para mostrar detalhes do plano ativo localmente.

## 5. Fluxo de Pagamento
1.  O front-end (`minha_assinatura.php`) abre um modal.
2.  JS chama `acoes_financeiro.php`.
3.  Backend chama `AsaasService`.
4.  Retorna dados (Linha digitável, imagem base64 do Pix) para o modal.

## 6. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **CSRF**: As ações críticas (ex: pagar com cartão) em `acoes_financeiro.php` não possuem token CSRF.
- [ ] **Dados Sensíveis**: O endpoint `pay_credit_card` recebe dados completos do cartão de crédito (Número, CVV) como JSON e passa para a classe de serviço. Embora não salve no banco, manusear esses dados exige cuidado redobrado (Logs, PCI Compliance).
- [ ] **Validação de Input**: O backend confia cegamente no JSON recebido (`json_decode`) sem sanitização rigorosa antes de enviar para a API Asaas.

### Refatorações Recomendadas
1.  Implementar CSRF Token urgentemente.
2.  Considerar usar Tokenização de Cartão (Credit Card Tokenization) no frontend se o Asaas suportar, para evitar trafegar CVV/Número pelo seu servidor PHP.
3.  Adicionar Log estruturado de erros de pagamento (sem salvar dados do cartão).
