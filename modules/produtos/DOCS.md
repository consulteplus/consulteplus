# Documentação do Módulo Produtos (Mentoria/EAD)

**Caminho**: `modules/produtos/`

## 1. Visão Geral
Core da plataforma de ensino e venda de conteúdo (Cursos, Mentorias, Ebooks). Gerencia a visualização do catálogo, matrículas e acesso às aulas.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Catálogo de produtos (Loja) e lista de "Meus Produtos". |
| `produto.php` | Página de detalhes de vendas (Landing Page interna do produto). |
| `aula.php` | Player de vídeo / Leitor de conteúdo da aula. |
| `install.php` | Script de instalação das tabelas do módulo (Setup inicial). |

## 3. Dependências
-   **Tabelas**:
    -   `mentoria_produtos`: Cadastro dos cursos.
    -   `mentoria_modulos`: Módulos do curso.
    -   `mentoria_aulas`: Conteúdo das aulas.
    -   `mentoria_matriculas`: Vínculo Usuário <-> Produto.
    -   `mentoria_acesso_empresas`: Vínculo Empresa <-> Produto (B2B).

## 4. Integração
-   O `dashboard` puxa os dados daqui para mostrar o "Meus Produtos".
-   A `loja` é na verdade uma *view* deste módulo (`index.php?view=loja`).
