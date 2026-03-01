# Documentação do Módulo Perfil

**Caminho**: `modules/perfil/`

## 1. Visão Geral
Módulo responsável pela gestão da conta do usuário logado. Permite alteração de dados pessoais (nome, email, senha) e, para clientes, visualização de dados financeiros (assinatura e compras).

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Único arquivo. Contém abas para "Dados Pessoais", "Minha Assinatura" e "Minhas Compras". |

## 3. Funcionalidades
1.  **Dados Pessoais**:
    *   Formulário para atualizar `users`.
    *   Validação de senha/confirmação no backend.
    *   Atualiza sessão PHP após salvar.
2.  **Minha Assinatura** (Apenas Clientes):
    *   Exibe plano ativo (`financeiro_assinaturas`) e faturas pendentes (integração Asaas via `AsaasService`).
    *   Botão "Pagar" redireciona para `modules/financeiro/minha_assinatura.php`.
3.  **Minhas Compras** (Apenas Clientes):
    *   Lista histórico de produtos adquiridos via `mentoria_matriculas`.

## 4. Observações Técnicas
-   **Segurança**: Usa `password_hash` para salvar senhas.
-   **Integração**: Depende de `AsaasService` para exibir faturas na aba de assinatura.

## 5. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **CSRF**: O formulário de atualização de perfil não possui proteção CSRF.
- [ ] **Senhas Fracas**: O sistema aceita qualquer senha (ex: "123") se não estiver vazia. Falta validação de complexidade e tamanho mínimo.

### Refatorações Recomendadas
1.  Implementar validação de senha forte (`regex` ou biblioteca).
2.  Adicionar CSRF Token.
