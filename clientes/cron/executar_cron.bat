@echo off
REM ============================================
REM Script para executar processador de notificações
REM Sistema de Gestão de Clínica
REM ============================================

echo.
echo ========================================
echo  Processador de Notificacoes - Cinco
echo ========================================
echo.

REM Caminho do PHP (ajuste se necessário)
set PHP_PATH=C:\xampp\php\php.exe

REM Caminho do script
set SCRIPT_PATH=%~dp0processar_notificacoes.php

REM Verificar se PHP existe
if not exist "%PHP_PATH%" (
    echo [ERRO] PHP nao encontrado em: %PHP_PATH%
    echo.
    echo Por favor, ajuste o caminho do PHP no script.
    pause
    exit /b 1
)

REM Verificar se script existe
if not exist "%SCRIPT_PATH%" (
    echo [ERRO] Script nao encontrado em: %SCRIPT_PATH%
    pause
    exit /b 1
)

REM Executar o script
echo Executando processador...
echo.
"%PHP_PATH%" "%SCRIPT_PATH%"

REM Capturar código de saída
set EXIT_CODE=%ERRORLEVEL%

echo.
echo ========================================
if %EXIT_CODE% EQU 0 (
    echo  Execucao concluida com sucesso!
) else (
    echo  Execucao finalizada com erros!
)
echo ========================================
echo.

REM Registrar em log
echo [%date% %time%] Executado - Codigo: %EXIT_CODE% >> execucoes.log

pause
