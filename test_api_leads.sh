#!/bin/bash

# Teste 1: Criar novo lead
echo "=== TESTE 1: Criar novo lead ==="
curl -X POST "https://app.consulteplus.com.br/api/v1/leads.php" \
  -H "X-API-KEY: sk_crm_83b2ca0d57eb82212ff47d449d6b126483b" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Teste API",
    "telefone": "5586988887777",
    "origem": "whatsapp",
    "status": "novo"
  }'

echo -e "\n\n"

# Teste 2: Tentar criar novamente (deve atualizar)
echo "=== TESTE 2: Atualizar lead existente (mesmo telefone) ==="
curl -X POST "https://app.consulteplus.com.br/api/v1/leads.php" \
  -H "X-API-KEY: sk_crm_83b2ca0d57eb82212ff47d449d6b126483b" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Teste API Atualizado",
    "telefone": "5586988887777",
    "origem": "whatsapp",
    "status": "novo"
  }'

echo -e "\n\n"

# Teste 3: Buscar o lead
echo "=== TESTE 3: Buscar lead por telefone ==="
curl -X GET "https://app.consulteplus.com.br/api/v1/leads.php?telefone=5586988887777&limit=1" \
  -H "X-API-KEY: sk_crm_83b2ca0d57eb82212ff47d449d6b126483b" \
  -H "Content-Type: application/json"

echo -e "\n"
