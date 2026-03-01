# Módulo CRM

Este módulo gerencia o Pipeline de Vendas (Kanban), Negócios, Funis e Etapas.

## Arquitetura (Refatoração 2026)

O módulo foi refatorado para utilizar uma camada de serviço e separar a lógica de negócios da visualização.

### Estrutura
- **`../../classes/CrmService.php`**: Classe principal que encapsula todas as operações de banco de dados. Utiliza *Prepared Statements* para segurança.
- **`acoes.php`**: Endpoint API (JSON) que recebe requisições do frontend e chama o `CrmService`. Não contém queries SQL diretas.
- **`index.php`**: View principal (Kanban/Lista). Instancia `CrmService` apenas para carregamento inicial.
- **`assets/css/crm.css`**: Estilos extraídos e organizados (ex: Kanban, Cards, UI Premium).

### Como usar o CrmService

```php
require_once __DIR__ . '/path/to/classes/CrmService.php';
$service = new CrmService($conn);

// Listar Negócios com Filtros
$filtros = ['funil_id' => 1, 'busca' => 'João'];
$resultado = $service->listarNegocios($filtros, $company_id);

// Criar Negócio
$id = $service->criarNegocio($dados, $company_id, $user_id);
```

### Notas de Desenvolvimento
- **Aliases de Banco**: As queries retornam `cliente_nome` e também `paciente_nome` (alias) para manter compatibilidade com códigos legados que usam terminologias diferentes.
- **Segurança**: Nunca execute queries diretas (`$conn->query`) neste módulo. Use sempre o Service ou adicione novos métodos lá com `prepare()/bind_param()`.
- **Logs**: Logs de debug em arquivo de texto foram removidos em favor de `error_log` padrão e exceções tratadas.
