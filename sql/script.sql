ALTER TABLE typepret ADD COLUMN deleted_at DATE;

ALTER TABLE typepret ADD COLUMN taux_assurance FLOAT DEFAULT 0;

ALTER TABLE remboursement ADD COLUMN assurance FLOAT DEFAULT 0;