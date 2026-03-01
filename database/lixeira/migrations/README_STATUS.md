# 🔄 Sistema de Mudança de Status - Implementação

## Versão: 2.1.0
**Data:** 14/12/2024

---

## ✅ **Arquivos Criados:**

1. **`database/migrations/create_agendamentos_log.sql`** - Tabela de log
2. **`api/agendamentos/status.php`** - API de mudança de status
3. **`assets/js/agendamentos-status.js`** - Funções JavaScript
4. **Atualizado:** `modules/agendamentos/index.php` - Botões de ação

---

## 🔧 **Passo a Passo para Finalizar:**

### **1. Executar Migration do Log:**

```sql
USE clinica_db;

CREATE TABLE IF NOT EXISTS agendamentos_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    status_anterior VARCHAR(20),
    status_novo VARCHAR(20) NOT NULL,
    alterado_por INT NOT NULL,
    observacao TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (alterado_por) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_agendamento (agendamento_id),
    INDEX idx_status (status_novo),
    INDEX idx_data (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **2. Adicionar Script no Final de `index.php`:**

Adicione antes do `<?php require_once ... footer.php; ?>`:

```html
<script src="<?php echo BASE_URL; ?>assets/js/agendamentos-status.js"></script>
```

---

## 🎯 **Funcionalidades Implementadas:**

### **Botões de Ação por Status:**

| Status | Botões Disponíveis |
|--------|-------------------|
| **Agendado** | ▶️ Iniciar • ❌ Marcar Falta • 🚫 Cancelar |
| **Confirmado** | ▶️ Iniciar • ❌ Marcar Falta • 🚫 Cancelar |
| **Em Atendimento** | ✅ Finalizar • ❌ Marcar Falta |
| **Concluído** | 👁️ Visualizar (sem ações) |
| **Faltou** | 👁️ Visualizar (sem ações) |
| **Cancelado** | 👁️ Visualizar (sem ações) |

---

## 🔐 **Permissões:**

- **Admin:** Pode alterar qualquer agendamento
- **Secretária:** Pode alterar qualquer agendamento
- **Médico:** Pode alterar apenas seus próprios agendamentos

---

## 📊 **Fluxo de Status:**

```
agendado
   ↓
confirmado (via WhatsApp/API)
   ↓
em_atendimento (secretária OU médico)
   ↓
concluído (secretária OU médico)

Exceções:
- qualquer → faltou
- qualquer → cancelado
```

---

## 🔒 **Validações Implementadas:**

1. **Transições Permitidas:**
   - `agendado` → confirmado, em_atendimento, faltou, cancelado
   - `confirmado` → em_atendimento, faltou, cancelado
   - `em_atendimento` → concluído, faltou
   - `concluído` → (nenhuma)
   - `faltou` → (nenhuma)
   - `cancelado` → (nenhuma)

2. **Médico só pode alterar seus agendamentos**

3. **Log de todas as mudanças** (quem, quando, de qual para qual)

---

## 📝 **Como Usar:**

### **Na Listagem de Agendamentos:**

1. **Iniciar Atendimento:**
   - Clique no botão verde ▶️
   - Confirme a ação
   - Status muda para "Em Atendimento"

2. **Finalizar Atendimento:**
   - Clique no botão azul ✅
   - Confirme a ação
   - Status muda para "Concluído"

3. **Marcar Falta/Cancelar:**
   - Clique no botão ⋮ (três pontos)
   - Escolha a ação desejada
   - Confirme

---

## 🧪 **Testar:**

1. Crie um agendamento
2. Confirme via WhatsApp (ou manualmente)
3. Clique em "Iniciar Atendimento"
4. Clique em "Finalizar Atendimento"
5. Verifique o log:

```sql
SELECT 
    al.*,
    u.nome as alterado_por_nome,
    a.paciente_id
FROM agendamentos_log al
JOIN users u ON al.alterado_por = u.id
JOIN agendamentos a ON al.agendamento_id = a.id
ORDER BY al.created_at DESC
LIMIT 10;
```

---

## 🎨 **Cores dos Botões:**

- 🟢 **Verde** (Iniciar) - `btn-success`
- 🔵 **Azul** (Finalizar) - `btn-primary`
- ⚪ **Cinza** (Mais ações) - `btn-secondary`
- 🟡 **Amarelo** (Editar) - `btn-warning`
- 🔵 **Azul Claro** (Visualizar) - `btn-info`

---

## 📱 **Feedback Visual:**

- ✅ Mensagem de sucesso no topo da tela
- ❌ Mensagem de erro se falhar
- ⏳ Spinner durante processamento
- 🔄 Reload automático após sucesso

---

## 🐛 **Troubleshooting:**

### **Botões não aparecem:**
- Verifique se o JavaScript foi incluído
- Verifique permissões do usuário logado

### **Erro ao mudar status:**
- Verifique se a tabela `agendamentos_log` foi criada
- Verifique se a API está acessível em `/api/agendamentos/status.php`

### **"Transição não permitida":**
- Verifique o status atual do agendamento
- Consulte a tabela de transições permitidas acima

---

**Implementação completa! 🎉**
