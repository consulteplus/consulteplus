# Documentação do Módulo Deploy

**Caminho**: `modules/deploy/`

## 1. Visão Geral
Ferramenta administrativa para realizar deploy (implantação) de arquivos do sistema via FTP/SFTP diretamente pelo painel. Permite atualizar o código-fonte em ambientes de produção.

## 2. Estrutura de Arquivos

| Arquivo | Função Principal |
| :--- | :--- |
| `index.php` | Interface principal do deployer. |
| `ftp_worker.php` | Script que executa a transferência de arquivos via FTP. |
| `processar.php` | Controller que recebe a requisição de deploy. |
| `migrate.php` | Script para rodar migrações de banco de dados após deploy. |
| `execute_db.php` | Executa comandos SQL diretos. |
| `save_config.php` | Salva credenciais de FTP (provavelmente em arquivo ou BD). |

## 3. Riscos
-   **Segurança**: Este módulo é crítico. O acesso deve ser estritamente restrito a Super Admins.
-   **Credenciais**: Verifica onde `save_config.php` armazena as senhas. Se for em arquivo plano, é um risco.

## 4. Análise de Código e Dívida Técnica ("Código Sujo")

### Segurança (CRÍTICO)
- [ ] **FALHA GRAVE DE ACESSO**: O arquivo `save_config.php` inicia a sessão mas **NÃO VERIFICA** se o usuário é Admin (`checkPermission` não é chamado). Qualquer usuário logado (inclusive clientes) pode enviar um POST para este arquivo e alterar as credenciais de FTP de produção.
- [ ] **Armazenamento em Texto Plano**: As senhas de FTP são salvas em `config/ftp_deploy.json` sem criptografia. Se o servidor web for mal configurado e servir arquivos `.json`, as credenciais vazam.
- [ ] **CSRF**: Não há proteção CSRF.

### Refatorações Recomendadas
1.  **URGENTE**: Adicionar `checkPermission(['admin'])` ou `['superadmin']` em `save_config.php`.
2.  Mover `ftp_deploy.json` para fora do diretório público (`public_html` ou `htdocs`) se possível, ou negar acesso via `.htaccess`.
3.  Implementar CSRF Token.
