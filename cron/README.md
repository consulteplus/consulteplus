# 🕐 Guia de Configuração do Cron - Windows

## 📋 Visão Geral

O sistema utiliza um script PHP (`cron/processar_notificacoes.php`) que deve ser executado periodicamente para processar e enviar notificações automáticas.

No Windows, usamos o **Agendador de Tarefas** (Task Scheduler) em vez do cron do Linux.

---

## ⚙️ Configuração Passo a Passo

### Método 1: Interface Gráfica (Recomendado)

#### 1. Abrir o Agendador de Tarefas

**Opção A:** Pesquisar
- Pressione `Win + S`
- Digite: `Agendador de Tarefas`
- Clique no aplicativo

**Opção B:** Executar
- Pressione `Win + R`
- Digite: `taskschd.msc`
- Pressione Enter

#### 2. Criar Nova Tarefa

1. No painel direito, clique em **"Criar Tarefa Básica..."**
2. Ou clique com botão direito em **"Biblioteca do Agendador de Tarefas"** → **"Criar Tarefa..."**

#### 3. Configurar a Tarefa

**Aba Geral:**
- **Nome:** `Cinco - Processar Notificações`
- **Descrição:** `Processa e envia notificações automáticas do sistema de clínica`
- **Configurar para:** Windows 10/11
- ☑ **Executar estando o usuário conectado ou não**
- ☑ **Executar com privilégios mais altos** (se necessário)

**Aba Gatilhos:**
1. Clique em **"Novo..."**
2. **Iniciar a tarefa:** Em uma agenda
3. **Configurações:**
   - **Diariamente**
   - Iniciar em: (data de hoje)
   - **Repetir a tarefa a cada:** `1 hora`
   - **Por um período de:** `Indefinidamente`
4. ☑ **Habilitado**
5. Clique em **OK**

**Aba Ações:**
1. Clique em **"Novo..."**
2. **Ação:** Iniciar um programa
3. **Programa/script:** 
   ```
   C:\xampp\php\php.exe
   ```
4. **Adicionar argumentos:**
   ```
   C:\xampp\htdocs\cinco\cron\processar_notificacoes.php
   ```
5. **Iniciar em (opcional):**
   ```
   C:\xampp\htdocs\cinco\cron
   ```
6. Clique em **OK**

**Aba Condições:**
- ☐ Desmarque "Iniciar a tarefa somente se o computador estiver conectado à energia CA"
- ☑ Marque "Ativar se o computador estiver ocioso por"

**Aba Configurações:**
- ☑ Permitir que a tarefa seja executada sob demanda
- ☑ Executar a tarefa assim que possível após uma inicialização agendada ser perdida
- ☑ Se a tarefa falhar, reiniciar a cada: `1 minuto`
- Tentar reiniciar até: `3 vezes`

#### 4. Salvar e Testar

1. Clique em **OK**
2. Digite sua senha do Windows se solicitado
3. Localize a tarefa na lista
4. Clique com botão direito → **"Executar"**
5. Verifique se executou sem erros

---

### Método 2: Linha de Comando (PowerShell)

Abra o PowerShell como Administrador e execute:

```powershell
# Criar a tarefa
$action = New-ScheduledTaskAction -Execute "C:\xampp\php\php.exe" -Argument "C:\xampp\htdocs\cinco\cron\processar_notificacoes.php"

$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Hours 1) -RepetitionDuration ([TimeSpan]::MaxValue)

$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable

Register-ScheduledTask -TaskName "Cinco - Processar Notificações" -Action $action -Trigger $trigger -Settings $settings -Description "Processa notificações do sistema de clínica"
```

---

### Método 3: Script Batch (.bat)

Crie um arquivo `executar_cron.bat` na pasta `cron/`:

```batch
@echo off
REM Script para executar o processador de notificações
REM Caminho do PHP
set PHP_PATH=C:\xampp\php\php.exe

REM Caminho do script
set SCRIPT_PATH=C:\xampp\htdocs\cinco\cron\processar_notificacoes.php

REM Executar
"%PHP_PATH%" "%SCRIPT_PATH%"

REM Opcional: Registrar em log
echo [%date% %time%] Cron executado >> C:\xampp\htdocs\cinco\cron\log.txt
```

Depois, agende este `.bat` no Agendador de Tarefas (mais simples).

---

## 🔍 Verificação

### 1. Testar Manualmente

Abra o CMD ou PowerShell e execute:

```cmd
cd C:\xampp\htdocs\cinco\cron
C:\xampp\php\php.exe processar_notificacoes.php
```

**Saída esperada:**
```
✓ Enviado para João Silva
✓ Enviado para Maria Santos

=== Resumo ===
Enviados: 2
Erros: 0
```

### 2. Verificar Logs

Verifique no banco de dados:

```sql
SELECT * FROM log_notificacoes 
ORDER BY created_at DESC 
LIMIT 10;
```

### 3. Verificar Tarefa Agendada

No Agendador de Tarefas:
1. Localize a tarefa
2. Veja a aba **"Histórico"**
3. Verifique **"Última Execução"** e **"Resultado da Última Execução"**

---

## ⏱️ Frequências Recomendadas

### Opção 1: A cada 1 hora (Padrão)
- **Uso:** Clínicas pequenas/médias
- **Configuração:** Repetir a cada 1 hora

### Opção 2: A cada 15 minutos
- **Uso:** Clínicas grandes com alto volume
- **Configuração:** Repetir a cada 15 minutos

### Opção 3: A cada 30 minutos
- **Uso:** Balanceado
- **Configuração:** Repetir a cada 30 minutos

---

## 🐛 Troubleshooting

### Problema: "PHP não encontrado"

**Solução:** Verifique o caminho do PHP

```cmd
where php
```

Se não encontrar, use o caminho completo:
```
C:\xampp\php\php.exe
```

### Problema: "Arquivo não encontrado"

**Solução:** Verifique se o caminho está correto

```cmd
dir C:\xampp\htdocs\cinco\cron\processar_notificacoes.php
```

### Problema: "Permissão negada"

**Solução:** Execute o Agendador de Tarefas como Administrador

### Problema: "Tarefa não executa"

**Verificações:**
1. ☑ Tarefa está habilitada?
2. ☑ Gatilho está correto?
3. ☑ Caminho do PHP está correto?
4. ☑ Caminho do script está correto?
5. ☑ Usuário tem permissões?

### Problema: "Erros ao enviar"

**Verificações:**
1. ☑ Configuração n8n está ativa?
2. ☑ URL do webhook está correta?
3. ☑ n8n está online?
4. ☑ Há regras de notificação ativas?

---

## 📊 Monitoramento

### Script de Monitoramento

Crie `cron/verificar_status.php`:

```php
<?php
require_once __DIR__ . '/../config/database.php';

// Últimas 24 horas
$stmt = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) as enviados,
        SUM(CASE WHEN status = 'erro' THEN 1 ELSE 0 END) as erros
    FROM log_notificacoes
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
");

$stats = $stmt->fetch_assoc();

echo "=== Últimas 24 horas ===\n";
echo "Total: {$stats['total']}\n";
echo "Enviados: {$stats['enviados']}\n";
echo "Erros: {$stats['erros']}\n";

if ($stats['erros'] > 0) {
    echo "\n⚠️ Há erros! Verifique o log.\n";
} else {
    echo "\n✅ Tudo funcionando!\n";
}
?>
```

Execute:
```cmd
php C:\xampp\htdocs\cinco\cron\verificar_status.php
```

---

## 📝 Log Personalizado

Para criar um log de execução, modifique o script:

```php
// No início do processar_notificacoes.php
$logFile = __DIR__ . '/execucoes.log';
$logMsg = "[" . date('Y-m-d H:i:s') . "] Iniciando processamento...\n";
file_put_contents($logFile, $logMsg, FILE_APPEND);

// No final
$logMsg = "[" . date('Y-m-d H:i:s') . "] Finalizado. Enviados: $enviados, Erros: $erros\n";
file_put_contents($logFile, $logMsg, FILE_APPEND);
```

---

## 🔄 Alternativa: Executar Manualmente

Se não quiser usar o Agendador de Tarefas, pode executar manualmente quando necessário:

1. Crie um atalho no Desktop
2. **Destino:**
   ```
   C:\xampp\php\php.exe C:\xampp\htdocs\cinco\cron\processar_notificacoes.php
   ```
3. **Iniciar em:**
   ```
   C:\xampp\htdocs\cinco\cron
   ```
4. Clique duplo para executar

---

## ✅ Checklist de Configuração

- [ ] PHP instalado e funcionando
- [ ] Script `processar_notificacoes.php` existe
- [ ] Configuração n8n ativa
- [ ] Pelo menos 1 regra de notificação ativa
- [ ] Tarefa criada no Agendador de Tarefas
- [ ] Tarefa testada manualmente
- [ ] Histórico da tarefa verificado
- [ ] Log de notificações verificado

---

## 📞 Suporte

Se tiver problemas:
1. Verifique o log de notificações no sistema
2. Execute o script manualmente para ver erros
3. Verifique o histórico do Agendador de Tarefas
4. Consulte a documentação completa em `/documentacao`

---

**Última atualização:** 12/12/2025
