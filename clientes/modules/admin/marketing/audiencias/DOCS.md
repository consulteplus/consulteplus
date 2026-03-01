# Módulo de Audiências

## Visão Geral

O módulo de **Audiências** permite criar segmentações personalizadas de leads através de filtros dinâmicos, similar às listas da HubSpot e segmentações da RD Station.

## Funcionalidades

### 1. Tipos de Audiências

- **Dinâmicas**: Atualizam automaticamente conforme novos leads atendem aos critérios
- **Estáticas**: Congelam a lista de leads no momento da criação (snapshot)

### 2. Sistema de Filtros

Campos disponíveis para filtro:
- Nome (LIKE, =, !=)
- Email (LIKE, =, !=, IS NULL, IS NOT NULL)
- Telefone (LIKE, =, !=, IS NULL, IS NOT NULL)
- Origem (=, !=, IN, NOT IN)
- Status (=, !=, IN, NOT IN)
- Data de Criação (=, >, <, >=, <=)

Operadores suportados:
- `=` - Igual a
- `!=` - Diferente de
- `LIKE` - Contém
- `IN` - Está em (múltiplos valores)
- `NOT IN` - Não está em
- `>`, `<`, `>=`, `<=` - Comparações numéricas/datas
- `IS NULL` - Está vazio
- `IS NOT NULL` - Não está vazio

### 3. Condições Globais

- **AND**: Lead deve atender a TODOS os filtros
- **OR**: Lead deve atender a QUALQUER um dos filtros

## Estrutura de Arquivos

```
modules/admin/marketing/audiencias/
├── index.php          # Listagem de audiências
├── criar.php          # Criar nova audiência
├── editar.php         # Editar audiência existente
├── visualizar.php     # Ver leads da audiência
├── acoes.php          # API backend
├── exportar.php       # Exportar para CSV
└── DOCS.md            # Este arquivo
```

## Banco de Dados

### Tabela: `audiencias`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | ID único |
| company_id | INT | ID da empresa |
| nome | VARCHAR(255) | Nome da audiência |
| descricao | TEXT | Descrição opcional |
| filtros_json | TEXT | Configuração dos filtros em JSON |
| tipo | ENUM | 'dinamica' ou 'estatica' |
| total_leads | INT | Total de leads (cache) |
| criado_por | INT | ID do usuário criador |
| criado_em | DATETIME | Data de criação |
| atualizado_em | DATETIME | Data de atualização |

### Tabela: `audiencia_leads`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | ID único |
| audiencia_id | INT | ID da audiência |
| lead_id | INT | ID do lead |
| adicionado_em | DATETIME | Data de adição |

## API Endpoints

### GET `/acoes.php?acao=listar`
Lista todas as audiências da empresa.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Leads Qualificados Facebook",
      "tipo": "dinamica",
      "total_leads": 150,
      ...
    }
  ]
}
```

### POST `/acoes.php?acao=salvar`
Cria ou edita uma audiência.

**Request:**
```json
{
  "id": 0,
  "nome": "Leads Qualificados",
  "descricao": "Leads com status qualificado",
  "tipo": "dinamica",
  "filtros": {
    "condicao": "AND",
    "filtros": [
      {
        "campo": "status",
        "operador": "=",
        "valor": "qualificado"
      }
    ]
  }
}
```

### POST `/acoes.php?acao=excluir`
Exclui uma audiência.

**Request:**
```json
{
  "id": 1
}
```

### POST `/acoes.php?acao=preview`
Retorna preview de leads que atendem aos filtros.

**Request:**
```json
{
  "filtros": {
    "condicao": "AND",
    "filtros": [...]
  }
}
```

**Response:**
```json
{
  "success": true,
  "total": 150,
  "leads": [...]
}
```

### GET `/acoes.php?acao=leads&id=1`
Retorna todos os leads de uma audiência.

## Casos de Uso

### 1. Campanha de Remarketing
- Criar audiência estática com leads que não converteram
- Exportar para Facebook Ads/Google Ads
- Manter registro histórico da campanha

### 2. Nutrição de Leads
- Criar audiência dinâmica com leads novos
- Usar em automação de email
- Atualiza automaticamente com novos leads

### 3. Segmentação por Origem
- Filtrar leads por origem (Facebook, Google, etc.)
- Analisar performance por canal
- Criar estratégias específicas por origem

## Exportação

O módulo permite exportar audiências para CSV com as seguintes colunas:
- ID
- Nome
- Email
- Telefone
- Origem
- Status
- Data de Criação

Formato: UTF-8 com BOM, separador `;` (compatível com Excel)

## Permissões

Acesso restrito a usuários com permissão `admin` ou `superadmin`.

## Próximas Melhorias

1. Integração com automações de email
2. Integração com WhatsApp (envio em massa)
3. Sincronização com Facebook Ads / Google Ads
4. Audiências baseadas em comportamento
5. Audiências lookalike

---

*Documentação criada em: 24/01/2026*
