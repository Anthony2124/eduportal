-- EduPortal database (fresh install)
-- Import in phpMyAdmin (http://localhost/phpmyadmin > Import) or run:
--   C:\xampp\mysql\bin\mysql.exe -u root < database\eduportal.sql
-- Already have an older eduportal database? Run database\upgrade.sql instead.

CREATE DATABASE IF NOT EXISTS eduportal
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE eduportal;

CREATE TABLE IF NOT EXISTS users (
    id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    role           ENUM('student', 'faculty', 'admin') NOT NULL DEFAULT 'student',
    id_number      VARCHAR(20)      NOT NULL,             -- Student ID, Employee ID (faculty) or username (admin)
    first_name     VARCHAR(60)      NOT NULL,
    last_name      VARCHAR(60)      NOT NULL,
    program        VARCHAR(80)      NULL,                 -- students only
    year_level     TINYINT UNSIGNED NULL,                 -- students only
    department     VARCHAR(80)      NULL,                 -- faculty: department, admin: office
    email          VARCHAR(190)     NULL,                 -- linked Gmail (receives the OTP); none for the built-in admin
    google_sub     VARCHAR(64)      NULL,                 -- Google account ID, set when linked through Google sign-in
    picture_url    VARCHAR(500)     NULL,
    password_hash  VARCHAR(255)     NOT NULL,             -- PHP password_hash(), never the plain password
    created_at     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at  TIMESTAMP        NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_id_number (id_number),
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_google_sub (google_sub)
) ENGINE=InnoDB;

-- Built-in admin account: username "admin", password "admin123"
-- (usernames are stored in capitals; the sign-in form accepts any case)
INSERT IGNORE INTO users (role, id_number, first_name, last_name, department, password_hash)
VALUES ('admin', 'ADMIN', 'Admin', 'User', 'IT Services',
        '$2y$10$oiLacjnw6g7mOn.eiPCyOuC1Fu8VBRA9SgM/4HVmtu.BUHmcJWAm.');

-- Content managed from the portal (Add / Edit / Remove buttons)
CREATE TABLE IF NOT EXISTS announcements (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title       VARCHAR(150)  NOT NULL,
    body        TEXT          NOT NULL,
    author_id   INT UNSIGNED  NULL,                       -- admin or faculty who posted it
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_announcements_author FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title        VARCHAR(150)  NOT NULL,
    event_date   DATE          NOT NULL,
    start_time   TIME          NULL,
    end_time     TIME          NULL,
    location     VARCHAR(150)  NULL,
    description  TEXT          NULL,
    created_by   INT UNSIGNED  NULL,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP     NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_events_date (event_date),
    CONSTRAINT fk_events_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS scholarships (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name          VARCHAR(150)  NOT NULL,
    sponsor       VARCHAR(150)  NULL,
    amount        VARCHAR(80)   NULL,                     -- free text, e.g. "PHP 15,000 per semester"
    deadline      DATE          NULL,
    requirements  TEXT          NULL,
    created_by    INT UNSIGNED  NULL,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP     NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_scholarships_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS courses (
    id          INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    code        VARCHAR(20)       NOT NULL,               -- e.g. CS 201
    title       VARCHAR(150)      NOT NULL,
    units       TINYINT UNSIGNED  NOT NULL,
    schedule    VARCHAR(100)      NULL,                   -- e.g. MWF 8:00 - 9:00 AM
    room        VARCHAR(60)       NULL,
    faculty_id  INT UNSIGNED      NULL,                   -- assigned faculty member
    created_by  INT UNSIGNED      NULL,
    created_at  TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP         NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courses_code (code),
    CONSTRAINT fk_courses_faculty FOREIGN KEY (faculty_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_courses_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;
