-- Migration v17: Add Financial Fields to mentoria_produtos
-- Description: Adds 'tipo_cobranca' and 'link_checkout' to support hybrid sales (One-time vs Subscription)

-- tipo_cobranca: 'unico' (Life-time/Single payment) or 'recorrente' (Requires subscription/cycle)
ALTER TABLE mentoria_produtos ADD COLUMN tipo_cobranca ENUM('unico', 'recorrente') DEFAULT 'recorrente' AFTER tipo;

-- link_checkout: External URL for payment (Stripe, Kiwify, etc)
ALTER TABLE mentoria_produtos ADD COLUMN link_checkout VARCHAR(500) AFTER ciclo;
