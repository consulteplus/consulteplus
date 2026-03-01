# 📅 Cron Jobs - Notificações para Profissionais

Este diretório contém os scripts de cron jobs para envio automático de notificações aos profissionais.

## 📋 Scripts Disponíveis

### 1. `enviar-resumo-diario.php`
**Função:** Envia um resumo dos agendamentos do dia para cada profissional.

**Conteúdo:**
- Lista de todos os agendamentos do dia
- Horário, paciente, tipo de procedimento e sala
- Status de cada agendamento

**Horário padrão:** 07:00 (configurável por profissional)

---

### 2. `enviar-agenda-amanha.php`
**Função:** Envia a agenda do dia seguinte para cada profissional.

**Conteúdo:**
- Lista completa dos agendamentos de amanhã
- Quantidade de confirmados vs pendentes
- Detalhes de cada agendamento com status

**Horário padrão:** 18:00 (configurável por profissional)

---

## ⚙️ Configuração

### Linux/Unix (crontab)

Edite o crontab:
```bash
crontab -e
```

Adicione as linhas:
```bash
# Resumo diário às 07:00
0 7 * * * /usr/bin/php /var/www/html/cinco/cron/enviar-resumo-diario.php >> /var/www/html/cinco/cron/logs/resumo-diario.log 2>&1

# Agenda de amanhã às 18:00
0 18 * * * /usr/bin/php /var/www/html/cinco/cron/enviar-agenda-amanha.php >> /var/www/html/cinco/cron/logs/agenda-amanha.log 2>&1
```

**Importante:** Ajuste o caminho `/var/www/html/cinco` para o caminho real do seu projeto.

---

### Windows (Task Scheduler)

#### Resumo Diário:
1. Abra o **Agendador de Tarefas**
2. Criar Tarefa Básica
3. Nome: "Cinco - Resumo Diário"
4. Gatilho: Diariamente às 07:00
5. Ação: Iniciar programa
   - Programa: `C:\xampp\php\php.exe`
   - Argumentos: `C:\xampp\htdocs\cinco\cron\enviar-resumo-diario.php`

#### Agenda de Amanhã:
1. Criar Tarefa Básica
2. Nome: "Cinco - Agenda Amanhã"
3. Gatilho: Diariamente às 18:00
4. Ação: Iniciar programa
   - Programa: `C:\xampp\php\php.exe`
   - Argumentos: `C:\xampp\htdocs\cinco\cron\enviar-agenda-amanha.php`

---

### Hostinger (cPanel)

1. Acesse o **cPanel**
2. Vá em **Cron Jobs**
3. Adicione:

**Resumo Diário:**
```
0 7 * * * /usr/bin/php /home/usuario/public_html/cron/enviar-resumo-diario.php
```

**Agenda Amanhã:**
```
0 18 * * * /usr/bin/php /home/usuario/public_html/cron/enviar-agenda-amanha.php
```

---

## 🧪 Teste Manual

Para testar os scripts manualmente:

```bash
# Linux/Mac
php /caminho/completo/cron/enviar-resumo-diario.php
php /caminho/completo/cron/enviar-agenda-amanha.php

# Windows
C:\xampp\php\php.exe C:\xampp\htdocs\cinco\cron\enviar-resumo-diario.php
C:\xampp\php\php.exe C:\xampp\htdocs\cinco\cron\enviar-agenda-amanha.php
```

---

## 📊 Logs

Os scripts exibem informações no console:
- Quantidade de profissionais processados
- Agendamentos encontrados
- Status de envio

**Recomendação:** Redirecione a saída para arquivos de log:
```bash
php script.php >> logs/script.log 2>&1
```

---

## 🔧 Personalização

Cada profissional pode configurar:
- ✅ Quais notificações receber
- ⏰ Horários dos resumos
- 📱 Canais (WhatsApp e/ou Sistema)

Acesse: **Minhas Notificações → Configurações**

---

## 📝 Observações

1. **Fuso Horário:** Certifique-se de que o servidor está no fuso correto
2. **Permissões:** Os scripts precisam de permissão de execução
3. **Banco de Dados:** Verifique se a conexão está configurada corretamente
4. **WhatsApp:** Integração com n8n será implementada em breve

---

## 🆘 Troubleshooting

### Cron não está executando
- Verifique se o caminho do PHP está correto: `which php`
- Teste o script manualmente primeiro
- Verifique os logs do cron: `/var/log/syslog` (Linux)

### Notificações não aparecem
- Verifique se o profissional tem as configurações ativas
- Confirme que existem agendamentos para o período
- Verifique os logs de erro do PHP

### Erro de conexão com banco
- Confirme que `config/database.php` está correto
- Verifique se o usuário do cron tem acesso ao banco

---

**Desenvolvido para o Sistema de Gestão de Clínica v2.2.0** 🏥
