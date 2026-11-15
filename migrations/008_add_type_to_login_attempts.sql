ALTER TABLE login_attempts
    ADD COLUMN type VARCHAR(20) NOT NULL DEFAULT 'login' AFTER email,
    ADD INDEX idx_type_ip_time (type, ip, attempted_at);