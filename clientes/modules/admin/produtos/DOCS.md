# Documentação do Módulo Produtos (Mentoria)

**Caminho**: `modules/admin/produtos/`

## 1. Visão Geral
Sistema de gestão de Produtos Digitais (Cursos e Mentorias). Permite criar estruturas curriculares (Módulos/Aulas), gerar conteúdo com IA e distribuir para empresas (tenants) como produtos white-label.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Listagem de produtos criados pelo Admin. |
| `ia_generator.php` | Wizard para criação de novos produtos usando IA (gera módulos e aulas). |
| `trilhas.php` | Gerenciamento da estrutura (arrastar e soltar módulos, criar aulas). |
| `conteudo.php` | Editor de conteúdo da aula (Vídeo, Texto, Roteiro, Tarefas). |
| `distribuir.php` | Concede acesso do produto a uma Empresa (similar a `atribuir.php` em outros módulos). |

## 3. Fluxo de Dados
1.  **Criação com IA (`ia_generator.php`)**:
    *   Usuário define tema e público.
    *   Chamada à OpenAI gera JSON estruturado.
    *   `ia_modulo_generator.php` e `salvar_ia.php` persistem no banco.
2.  **Edição (`conteudo.php`)**:
    *   Permite upload de vídeo (link YouTube/Vimeo), edição de roteiro (interno) e descrição (aluno).
    *   Configura tarefas/exercícios vinculados à aula.
3.  **Distribuição (`distribuir.php`)**:
    *   Cria vínculo na tabela `mentoria_acesso_empresas`.
    *   Pode gerar cobrança automática no Asaas se configurado.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança
- [ ] **Validação de ID**: Em `conteudo.php`, o ID da aula vem via GET. A verificação de permissão (`checkPermission`) é genérica, não valida se aquele ID de aula pertence a um produto que o usuário admin pode editar (embora admin veja tudo, em multi-tenant real isso seria falha).
- [ ] **XSS**: Em `ia_generator.php`, os inputs do usuário são passados para o prompt da IA. Se a IA retornar scripts maliciosos (prompt injection) e isso for renderizado sem escape na tela de revisão, há risco de XSS.

### Organização
- [ ] **JavaScript Inline Excessivo**: `ia_generator.php` tem centenas de linhas de JS no final do arquivo para controlar o wizard e chamadas AJAX. Deveria estar em `assets/js`.
- [ ] **Lógica de Cobrança Misturada**: `distribuir.php` contém lógica complexa de criação de assinatura no Asaas misturada com a lógica de insert no banco local.

### Performance
- [ ] **Queries em Loop**: Na listagem de trilhas (`trilhas.php`), pode haver queries N+1 ao buscar aulas de cada módulo se não estiver otimizado (necessário verificar arquivo `trilhas.php` mais a fundo).

### Refatorações Recomendadas
1.  Extrair lógica de integração com Asaas para um Service dedicado (já existe `AsaasService`, mas o uso deve ser padronizado).
2.  Mover JS complexo para arquivos externos.
3.  Implementar validação rigorosa do JSON retornado pela IA antes de salvar no banco.
