
-- Seed file for initial admin user
-- Password: ChangeThisPassword123! (should be changed after first login)
-- Password hash generated with: password_hash('ChangeThisPassword123!', PASSWORD_ARGON2ID)
-- Note: Replace this hash with your own generated hash before running

INSERT INTO users (email, password_hash, display_name, role) VALUES
('jesseboudoin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$VS5mZW90bzJFU3QuVGZ1Vw$9hNOIH3RWQDe/W6mEFgDxn3xLdZZNIQRleQL9fA1N7w', 'Site Pastor', 'site_pastor')
ON DUPLICATE KEY UPDATE email=email;
