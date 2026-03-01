# 💰 Campo de Valor em Agendamentos

## Versão: 2.1.0
**Data:** 14/12/2024

---

## 📋 Descrição

Implementação do campo `valor` na tabela de agendamentos para armazenar o valor cobrado pela consulta/procedimento.

---

## 🗄️ Alterações no Banco de Dados

### Migration

Execute o script de migração:

```bash
mysql -u root -p clinica_db < database/migrations/add_valor_agendamentos.sql
```

Ou execute manualmente:

```sql
ALTER TABLE agendamentos 
ADD COLUMN valor DECIMAL(10,2) NULL COMMENT 'Valor da consulta em reais' 
AFTER observacoes;

ALTER TABLE agendamentos 
ADD INDEX idx_valor (valor);
```

### Estrutura do Campo

- **Nome:** `valor`
- **Tipo:** `DECIMAL(10,2)`
- **Nulo:** SIM (opcional)
- **Descrição:** Valor da consulta em reais
- **Índice:** Sim (para relatórios financeiros)

---

## 📝 Funcionalidades Implementadas

### 1. Novo Agendamento (`modules/agendamentos/novo.php`)

- ✅ Campo de valor com máscara monetária (R$)
- ✅ Validação de número decimal (2 casas)
- ✅ Campo opcional (pode ficar em branco)
- ✅ Placeholder: "0,00"
- ✅ Dica: "Deixe em branco se não houver cobrança"

### 2. Editar Agendamento (`modules/agendamentos/editar.php`)

- ✅ Campo de valor editável
- ✅ Exibe valor atual se existir
- ✅ Permite alterar ou remover valor

### 3. Schema Atualizado (`database/schema.sql`)

- ✅ Campo incluído no schema principal
- ✅ Índice adicionado para performance

---

## 💻 Exemplos de Uso

### Criar Agendamento com Valor

```php
$valor = 150.00; // R$ 150,00

$stmt = $conn->prepare("
    INSERT INTO agendamentos (..., valor, ...)
    VALUES (..., ?, ...)
");
$stmt->bind_param("...d...", ..., $valor, ...);
```

### Criar Agendamento sem Valor

```php
$valor = null; // Sem cobrança

$stmt = $conn->prepare("
    INSERT INTO agendamentos (..., valor, ...)
    VALUES (..., ?, ...)
");
$stmt->bind_param("...d...", ..., $valor, ...);
```

### Consultar Agendamentos com Valor

```sql
-- Agendamentos com valor definido
SELECT * FROM agendamentos WHERE valor IS NOT NULL;

-- Agendamentos gratuitos
SELECT * FROM agendamentos WHERE valor IS NULL OR valor = 0;

-- Soma total de valores
SELECT SUM(valor) as total FROM agendamentos WHERE status = 'concluido';
```

---

## 📊 Relatórios Financeiros (Futuro)

Este campo permite criar relatórios como:

- **Faturamento por período**
- **Faturamento por profissional**
- **Faturamento por tipo de procedimento**
- **Agendamentos pagos vs gratuitos**
- **Ticket médio por consulta**

---

## 🎨 Interface

### Campo no Formulário

```html
<div class="col-md-6 mb-3">
    <label for="valor" class="form-label">Valor da Consulta</label>
    <div class="input-group">
        <span class="input-group-text">R$</span>
        <input type="number" class="form-control" id="valor" name="valor" 
               step="0.01" min="0" placeholder="0,00">
    </div>
    <small class="text-muted">Deixe em branco se não houver cobrança</small>
</div>
```

---

## ✅ Checklist de Implementação

- [x] Criar migration SQL
- [x] Atualizar schema.sql
- [x] Adicionar campo no formulário de novo agendamento
- [x] Adicionar campo no formulário de edição
- [x] Atualizar processamento POST (novo)
- [x] Atualizar processamento POST (edição)
- [x] Adicionar índice para performance
- [x] Documentar funcionalidade
- [ ] Exibir valor na listagem de agendamentos
- [ ] Exibir valor na visualização de agendamento
- [ ] Criar relatório financeiro
- [ ] Adicionar filtro por valor na listagem

---

## 🔄 Próximos Passos

1. **Exibir valor na listagem** (`modules/agendamentos/index.php`)
2. **Exibir valor na visualização** (`modules/agendamentos/visualizar.php`)
3. **Criar relatório financeiro** (`modules/relatorios/financeiro.php`)
4. **Adicionar gráficos de faturamento** no dashboard
5. **Exportar relatórios em PDF/Excel**

---

## 🐛 Troubleshooting

### Erro ao salvar agendamento

**Problema:** Coluna 'valor' não existe

**Solução:** Execute a migration:
```bash
mysql -u root -p clinica_db < database/migrations/add_valor_agendamentos.sql
```

### Valor não aparece no formulário

**Problema:** Cache do navegador

**Solução:** Limpe o cache (Ctrl + Shift + Delete) ou force reload (Ctrl + F5)

---

**Versão:** 2.1.0  
**Autor:** Sistema Clínica Cinco  
**Data:** 14/12/2024
