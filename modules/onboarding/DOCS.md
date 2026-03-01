# Documentação do Módulo Onboarding

**Caminho**: `modules/onboarding/`

## 1. Visão Geral
Responsável pelos processos iniciais de boas-vindas e configuração do usuário recém-cadastrado.

## 2. Estrutura de Arquivos
-   `acoes.php`: Processa os formulários do passo-a-passo de onboarding.
-   `debug_request.log`: Arquivo de log (dívida técnica, deve ser ignorado no git).

## 3. Funcionalidade
Geralmente é acionado quando `user.first_login` é verdadeiro ou flag de onboarding está pendente.
