-- Brings an existing EduPortal database up to date. Safe to run more than once;
-- existing accounts are kept (accounts from before roles existed become students).
--   C:\xampp\mysql\bin\mysql.exe -u root < database\upgrade.sql

USE eduportal;

-- Admin / Faculty / Student roles
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS role ENUM('student', 'faculty', 'admin') NOT NULL DEFAULT 'student' AFTER id,
    CHANGE COLUMN IF EXISTS student_id id_number VARCHAR(20) NOT NULL,
    MODIFY program VARCHAR(80) NULL,
    MODIFY year_level TINYINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS department VARCHAR(80) NULL AFTER year_level;

ALTER TABLE users DROP INDEX IF EXISTS uq_users_student_id;
ALTER TABLE users ADD UNIQUE INDEX IF NOT EXISTS uq_users_id_number (id_number);

-- The built-in admin has no Gmail
ALTER TABLE users MODIFY email VARCHAR(190) NULL;

-- Built-in admin account: username "admin", password "admin123"
INSERT IGNORE INTO users (role, id_number, first_name, last_name, department, password_hash)
VALUES ('admin', 'ADMIN', 'Admin', 'User', 'IT Services',
        '$2y$10$oiLacjnw6g7mOn.eiPCyOuC1Fu8VBRA9SgM/4HVmtu.BUHmcJWAm.');
