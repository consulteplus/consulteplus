# Documentação do Módulo Ferramentas

**Caminho**: `modules/ferramentas/`

## 1. Visão Geral
Este módulo agrupa diversas ferramentas estratégicas de gestão (como SWOT, 5W2H, Canvas). Diferente de outros módulos, ele é **dinâmico**: sua página inicial (`index.php`) consulta o banco de dados (`ferramentas_tipos`) para listar as ferramentas ativas e verificar se os arquivos correspondentes existem no servidor.

## 2. Estrutura de Arquivos

| Arquivo/Diretório | Função Principal |
| :--- | :--- |
| `index.php` | Listagem dinâmica das ferramentas. Verifica permissões e existência de tabelas/arquivos. |
| `diagnostic_cli.php` | Script PHP CLI (Command Line Interface) provável para testes ou diagnósticos internos. |
| `diagnostic_gemini.php` | Script relacionado a diagnósticos usando IA (Gemini), possivelmente um protótipo ou ferramenta interna. |
| **Sub-diretórios** | Cada diretório representa uma ferramenta (slug). Exemplos: `5w2h`, `swot`, `canvas`, `pestel`. |

## 3. Padrão das Ferramentas (Sub-módulos)
A maioria das ferramentas nos subdiretórios (ex: `5w2h/`) segue um padrão CRUD simples:
-   `index.php`: Listagem dos registros criados (ex: planos de ação 5W2H).
-   `form.php`: Formulário para criar ou editar um registro.
-   `excluir.php`: Lógica para remoção de registros.

## 4. Dependências Importantes
-   **Banco de Dados**: Tabela `ferramentas_tipos` é crucial. Sem ela, nenhuma ferramenta é listada.
-   **Permissões**: Baseadas em `recursos_atribuicoes` se o usuário não for admin.

## 5. Observações Técnicas
-   O `index.php` possui tratamento de erro robusto (`try-catch`) para inclusão de arquivos e chamadas de função, o que é uma boa prática.
-   A verificação `file_exists($modulePath)` no loop de listagem previne links quebrados para ferramentas cadastradas no banco mas sem arquivos no servidor.

## 6. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **CSRF**: Formulários como `5w2h/form.php` não validam tokens CSRF, permitindo cross-site attacks.
- [ ] **Validação de ID**: Embora use Prepared Statements, a validação de que o ID pertence à empresa do usuário é feita, o que é um ponto positivo, mas deve ser garantido em *todas* as ferramentas.

### Organização
- [ ] **Lógica Duplicada**: Cada ferramenta (5w2h, swot, etc.) reimplementa a lógica de CRUD. Seria ideal ter uma classe `FerramentaBase` ou um Controller genérico.
- [ ] **HTML com PHP**: Mistura intensa de lógica de banco de dados com apresentação nos arquivos `form.php`.

### Refatorações Recomendadas
1.  Criar um `FerramentasController` para padronizar o CRUD.
2.  Adicionar verificação CSRF globalmente.
3.  Padronizar o armazenamento do conteúdo (atualmente JSON no banco, o que é flexível mas difícil de consultar via SQL se necessário).
