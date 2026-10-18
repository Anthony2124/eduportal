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

-- Academics: which students are in which course, plus assignments, exams and grades per course.
-- Removing a course or a student account also removes their rows here.
CREATE TABLE IF NOT EXISTS enrollments (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    course_id    INT UNSIGNED  NOT NULL,
    student_id   INT UNSIGNED  NOT NULL,
    enrolled_by  INT UNSIGNED  NULL,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_enrollments_course_student (course_id, student_id),
    CONSTRAINT fk_enrollments_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_enrollments_student FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_enrollments_by FOREIGN KEY (enrolled_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assignments (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    course_id     INT UNSIGNED  NOT NULL,
    title         VARCHAR(150)  NOT NULL,
    due_date      DATE          NOT NULL,
    due_time      TIME          NULL,
    instructions  TEXT          NULL,
    created_by    INT UNSIGNED  NULL,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP     NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_assignments_due (due_date),
    CONSTRAINT fk_assignments_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_assignments_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS exams (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    course_id   INT UNSIGNED  NOT NULL,
    title       VARCHAR(150)  NOT NULL,
    exam_type   VARCHAR(30)   NOT NULL,                 -- Quiz, Prelim, Midterm, Pre-final, Final, Other
    exam_date   DATE          NOT NULL,
    start_time  TIME          NULL,
    end_time    TIME          NULL,
    room        VARCHAR(60)   NULL,
    created_by  INT UNSIGNED  NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_exams_date (exam_date),
    CONSTRAINT fk_exams_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_exams_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS grades (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    course_id   INT UNSIGNED  NOT NULL,
    student_id  INT UNSIGNED  NOT NULL,
    prelim      DECIMAL(3,2)  NULL,                     -- 1.00 (highest) to 5.00 (failed)
    midterm     DECIMAL(3,2)  NULL,
    final       DECIMAL(3,2)  NULL,
    graded_by   INT UNSIGNED  NULL,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_grades_course_student (course_id, student_id),
    CONSTRAINT fk_grades_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_grades_student FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_grades_by FOREIGN KEY (graded_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Prelim grade, next to midterm and final
ALTER TABLE grades ADD COLUMN IF NOT EXISTS prelim DECIMAL(3,2) NULL AFTER student_id;

-- Classes get a block (section) and meeting days and times, so faculty can pick open classes without clashes.
-- The same course can be offered to several blocks. The old free-text "schedule" column is kept and shown
-- for classes created before this update, until they're given days and times.
ALTER TABLE courses
    ADD COLUMN IF NOT EXISTS block VARCHAR(30) NULL AFTER units,
    ADD COLUMN IF NOT EXISTS days VARCHAR(30) NULL AFTER block,
    ADD COLUMN IF NOT EXISTS start_time TIME NULL AFTER days,
    ADD COLUMN IF NOT EXISTS end_time TIME NULL AFTER start_time,
    DROP INDEX IF EXISTS uq_courses_code,
    ADD UNIQUE INDEX IF NOT EXISTS uq_courses_code_block (code, block);

-- Every class belongs to a degree program (BS Information Technology, ...): the admin picks the program first,
-- then adds the course. Classes from before this update have none until they're edited.
-- ALGORITHM=COPY rebuilds the table: XAMPP's MariaDB 10.4 crashes adding it "instantly" after the columns above.
ALTER TABLE courses ADD COLUMN IF NOT EXISTS program VARCHAR(80) NULL AFTER id, ALGORITHM=COPY;

-- Class group chats: every course has one. Its members are the course's faculty and enrolled students
-- (worked out from enrollments, so enrolling a student adds them automatically).
-- kind 'added' / 'removed' are the "... was added to the group" notices; user_id is that student.
CREATE TABLE IF NOT EXISTS chat_messages (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    course_id   INT UNSIGNED  NOT NULL,
    user_id     INT UNSIGNED  NULL,                     -- NULL once the account is deleted
    kind        ENUM('message', 'added', 'removed') NOT NULL DEFAULT 'message',
    body        TEXT          NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_chat_messages_course (course_id, id),
    CONSTRAINT fk_chat_messages_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_chat_messages_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- The last message each person has seen in each chat (for unread counts)
CREATE TABLE IF NOT EXISTS chat_reads (
    course_id     INT UNSIGNED  NOT NULL,
    user_id       INT UNSIGNED  NOT NULL,
    last_read_id  INT UNSIGNED  NOT NULL DEFAULT 0,
    PRIMARY KEY (course_id, user_id),
    CONSTRAINT fk_chat_reads_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_chat_reads_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Program group chats: one per degree program, for the admin and the faculty who teach a class of that program
-- (worked out from courses, so picking a class of the program adds a faculty member). The admin isn't in class chats.
-- Same shape as chat_messages / chat_reads so the same code reads both; program chats only have kind 'message'.
CREATE TABLE IF NOT EXISTS program_chat_messages (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    program     VARCHAR(80)   NOT NULL,                 -- one of PROGRAMS (api/bootstrap.php)
    user_id     INT UNSIGNED  NULL,                     -- NULL once the account is deleted
    kind        ENUM('message', 'added', 'removed') NOT NULL DEFAULT 'message',
    body        TEXT          NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_program_chat_messages_program (program, id),
    CONSTRAINT fk_program_chat_messages_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS program_chat_reads (
    program       VARCHAR(80)   NOT NULL,
    user_id       INT UNSIGNED  NOT NULL,
    last_read_id  INT UNSIGNED  NOT NULL DEFAULT 0,
    PRIMARY KEY (program, user_id),
    CONSTRAINT fk_program_chat_reads_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Students' gender, asked at sign-up (unspecified = prefer not to say); older accounts set it in Settings.
-- ALGORITHM=COPY rebuilds the table, as XAMPP's MariaDB 10.4 can crash adding columns "instantly".
ALTER TABLE users ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'unspecified') NULL AFTER last_name, ALGORITHM=COPY;

-- Activity log (Activity Logs page, admin only): sign-ins, account changes, posts, courses, enrollment and grades.
-- The person's name and role are copied in, so a line still says who it was after their account is deleted.
CREATE TABLE IF NOT EXISTS activity_logs (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NULL,                     -- NULL once the account is deleted, or for an unknown sign-in
    user_name   VARCHAR(130)  NULL,
    role        VARCHAR(10)   NULL,
    category    VARCHAR(20)   NOT NULL,                 -- auth, account, content or academics (LOG_CATEGORIES)
    action      VARCHAR(60)   NOT NULL,                 -- e.g. "Signed in", "Added course"
    details     VARCHAR(255)  NOT NULL DEFAULT '',
    ip_address  VARCHAR(45)   NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_activity_logs_created (created_at),
    CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Assignments can have points (the highest score); empty = not graded
ALTER TABLE assignments ADD COLUMN IF NOT EXISTS points SMALLINT UNSIGNED NULL AFTER instructions, ALGORITHM=COPY;

-- Assignment submissions (like Google Classroom): a student's work for an assignment - an answer or links and
-- uploaded files - turned in, then returned by the faculty member with a score and private feedback.
-- turned_in_at NULL = not turned in yet (or unsubmitted); returned_at set = returned (graded).
CREATE TABLE IF NOT EXISTS submissions (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    assignment_id  INT UNSIGNED  NOT NULL,
    student_id     INT UNSIGNED  NOT NULL,
    body           TEXT          NULL,                  -- the student's answer or links
    turned_in_at   TIMESTAMP     NULL,
    score          DECIMAL(6,2)  NULL,
    feedback       TEXT          NULL,                  -- private comment from the faculty member
    returned_at    TIMESTAMP     NULL,
    graded_by      INT UNSIGNED  NULL,
    created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP     NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_submissions_assignment_student (assignment_id, student_id),
    CONSTRAINT fk_submissions_assignment FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_student FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_grader FOREIGN KEY (graded_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Files attached to a submission. They're saved under a random name in uploads/submissions/ (not reachable from
-- the web) and downloaded through api/submissions.php, which checks who is asking.
CREATE TABLE IF NOT EXISTS submission_files (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    submission_id  INT UNSIGNED  NOT NULL,
    original_name  VARCHAR(255)  NOT NULL,
    stored_name    VARCHAR(80)   NOT NULL,
    size           INT UNSIGNED  NOT NULL,
    created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_submission_files_submission FOREIGN KEY (submission_id) REFERENCES submissions (id) ON DELETE CASCADE
) ENGINE=InnoDB;
