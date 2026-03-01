# 🔄 Loading States em Formulários

## Versão: 2.1.0
**Data:** 14/12/2024

---

## 📋 **O que foi implementado:**

Sistema automático de loading states para todos os formulários do sistema, melhorando a experiência do usuário (UX) com feedback visual durante o processamento.

---

## ✨ **Funcionalidades:**

### **1. Loading Automático**
- Todos os formulários agora mostram automaticamente um spinner durante o submit
- Botão é desabilitado para prevenir cliques duplicados
- Texto customizável via atributo `data-loading-text`

### **2. Estados Visuais**
- **Normal:** Botão com texto original
- **Loading:** Spinner + texto "Processando..."
- **Sucesso:** ✅ + texto "Sucesso!" (verde)
- **Erro:** ❌ + texto "Erro!" (vermelho)

### **3. Gerenciamento Inteligente**
- Salva estado original do botão
- Restaura automaticamente após sucesso/erro
- Previne múltiplos submits

---

## 🎯 **Onde está ativo:**

✅ **Login** - "Autenticando..."
✅ **Novo Agendamento** - "Criando agendamento..."
✅ **Editar Agendamento** - "Salvando alterações..."
✅ **Todos os outros formulários** - "Processando..." (padrão)

---

## 💻 **Como Usar:**

### **Uso Automático (Padrão):**

Qualquer formulário automaticamente terá loading. Nada precisa ser feito!

```html
<form method="POST">
    <!-- campos do formulário -->
    <button type="submit" class="btn btn-primary">
        Salvar
    </button>
</form>
```

### **Customizar Texto de Loading:**

```html
<button type="submit" class="btn btn-primary" 
        data-loading-text="Salvando dados...">
    Salvar
</button>
```

### **Desabilitar Loading (se necessário):**

```html
<button type="submit" class="btn btn-primary" 
        data-no-loading>
    Salvar
</button>
```

---

## 🔧 **Uso Avançado (AJAX):**

### **Exemplo com Fetch API:**

```javascript
const form = document.querySelector('#meuForm');
const submitBtn = form.querySelector('button[type="submit"]');

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Iniciar loading
    formLoading.start(submitBtn, 'Enviando...');
    
    try {
        const response = await fetch('/api/endpoint', {
            method: 'POST',
            body: new FormData(form)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Mostrar sucesso
            formLoading.success(submitBtn, 'Enviado!');
            
            // Redirecionar após 1.5s
            setTimeout(() => {
                window.location.href = '/sucesso';
            }, 1500);
        } else {
            // Mostrar erro
            formLoading.error(submitBtn, 'Falha no envio');
        }
    } catch (error) {
        formLoading.error(submitBtn, 'Erro de conexão');
    }
});
```

### **Função Auxiliar submitFormAjax:**

```javascript
const form = document.querySelector('#meuForm');

submitFormAjax(
    form,
    '/api/endpoint',
    // Sucesso
    (data) => {
        alert('Salvo com sucesso!');
        window.location.reload();
    },
    // Erro
    (error) => {
        alert('Erro: ' + error.message);
    }
);
```

---

## 🎨 **API do FormLoadingManager:**

### **Métodos Disponíveis:**

```javascript
// Iniciar loading
formLoading.start(button, 'Processando...');

// Mostrar sucesso
formLoading.success(button, 'Sucesso!', 1500);

// Mostrar erro
formLoading.error(button, 'Erro!', 2000);

// Restaurar estado original
formLoading.reset(button);
```

### **Parâmetros:**

| Método | Parâmetros | Descrição |
|--------|-----------|-----------|
| `start(button, text)` | button: HTMLElement<br>text: string | Inicia loading |
| `success(button, text, duration)` | button: HTMLElement<br>text: string<br>duration: number (ms) | Mostra sucesso |
| `error(button, text, duration)` | button: HTMLElement<br>text: string<br>duration: number (ms) | Mostra erro |
| `reset(button)` | button: HTMLElement | Restaura original |

---

## 📦 **Arquivos do Sistema:**

```
assets/js/
└── form-loading.js          # Gerenciador de loading states

includes/
└── footer.php               # Inclui o script globalmente

Formulários atualizados:
├── login.php                # Login com loading
├── modules/agendamentos/
│   ├── novo.php            # Novo agendamento
│   └── editar.php          # Editar agendamento
```

---

## 🎯 **Próximos Passos:**

Para adicionar em novos formulários:

1. **Automático:** Nada! Já funciona.
2. **Customizado:** Adicione `data-loading-text="Seu texto..."`
3. **AJAX:** Use `formLoading.start/success/error`

---

## 🐛 **Troubleshooting:**

### **Loading não aparece:**
- Verifique se o `form-loading.js` está sendo carregado
- Abra o console e veja se há erros
- Confirme que o botão tem `type="submit"`

### **Botão não volta ao normal:**
- Verifique se há erros no submit
- Use `formLoading.reset(button)` manualmente se necessário

### **Múltiplos submits ainda acontecem:**
- O loading desabilita o botão automaticamente
- Se usar AJAX, adicione `e.preventDefault()`

---

## ✅ **Benefícios:**

- ✨ **Melhor UX** - Usuário sabe que algo está acontecendo
- 🚫 **Previne duplicação** - Botão desabilitado durante processamento
- 🎨 **Visual profissional** - Spinners e feedback colorido
- 🔧 **Fácil de usar** - Funciona automaticamente
- 📱 **Responsivo** - Funciona em todos os dispositivos

---

**Implementação completa!** 🎉
