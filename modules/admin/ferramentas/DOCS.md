# Documentação do Módulo Ferramentas

**Caminho**: `modules/admin/ferramentas/`

## 1. Visão Geral
Gerencia o catálogo de "Ferramentas" (normalmente calculadoras ou simuladores interativos) disponíveis no sistema. O Admin pode criar/editar tipos de ferramentas e controlar quais empresas têm acesso a elas.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Listagem das ferramentas cadastradas (`ferramentas_tipos`). Permite ativar/desativar status global. |
| `editar.php` | Formulário para alterar nome, descrição, ícone e ordem de uma ferramenta. |
| `atribuir.php` | Controle de acesso específico. Vincula uma ferramenta a uma empresa na tabela `recursos_atribuicoes`. |

## 3. Fluxo de Dados
1.  **Catálogo (`index.php`)**:
    *   Exibe itens da tabela `ferramentas_tipos`.
    *   O campo `slug` é usado para gerar a URL amigável na área do cliente (ex: `/ferramentas/calculadora-roi`).
2.  **Atribuição (`atribuir.php`)**:
    *   Insere registros em `recursos_atribuicoes` com `recurso_tipo = 'ferramenta'`.
    *   Isso permite que o menu da área do cliente filtre apenas ferramentas liberadas.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **SQL Injection**: Em `atribuir.php`, a exclusão (`DELETE FROM ... WHERE id=$id`) concatena a variável diretamente na string SQL sem preparo.
- [ ] **Validação de Input**: Em `editar.php`, `intval` é usado para ordem, mas outros campos como `icone` e `nome` são passados apenas com sanitização básica implícita no bind (melhor, mas validação de tipo seria ideal).

### Organização
- [ ] **Redirecionamento**: O uso de `echo "<script>window.location..."` para "Refresh" após o POST em `atribuir.php` é uma prática ruim (hacky). Deveria usar o padrão PRG (Post-Redirect-Get) com `header('Location: ...')` antes de qualquer output HTML.
- [ ] **Lógica de View no Controller**: Os arquivos misturam a lógica de processamento de formulário no topo com o HTML abaixo. Padrão em todo o legado, mas dificulta testes.

### Refatorações Recomendadas
1.  Corrigir imediatamente a query de Delete em `atribuir.php` para usar `prepare/bind_param`.
2.  Padronizar os redirecionamentos para evitar scripts JS inline.
