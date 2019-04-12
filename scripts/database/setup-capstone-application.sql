--
-- Capstone Project Application tables
--
-- This script assumes that there already exists a `user` table and a `capstone_project` table
--
CREATE TABLE IF NOT EXISTS capstone_application_status (
    cas_id INT NOT NULL AUTO_INCREMENT,
    cas_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cas_id)
);

CREATE TABLE IF NOT EXISTS capstone_application (
    ca_id CHAR(16) NOT NULL,
    ca_project CHAR(16) NOT NULL,
    ca_student CHAR(16) NOT NULL,
    ca_justification TEXT NOT NULL,
    ca_time_available VARCHAR(256) NOT NULL,
    ca_skill_set TEXT NOT NULL,
    ca_ext_portfolio_link VARCHAR(512),
    ca_status INT NOT NULL,
    ca_date_created DATETIME NOT NULL,
    ca_date_updated DATETIME,
    ca_date_applied DATETIME,

    PRIMARY KEY (ca_id),
    FOREIGN KEY (ca_project) REFERENCES capstone_project (cp_id),
    FOREIGN KEY (ca_student) REFERENCES user (u_id),
    FOREIGN KEY (ca_status) REFERENCES capstone_application_status (cas_id)
);

CREATE TABLE IF NOT EXISTS capstone_interest_level (
    cil_id INT NOT NULL AUTO_INCREMENT,
    cil_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cil_id)
);

CREATE TABLE IF NOT EXISTS capstone_application_review (
    car_id CHAR(16) NOT NULL,
    car_application CHAR(16) NOT NULL,
    car_interest_level INT NOT NULL,
    car_comments TEXT,

    PRIMARY KEY (car_id),
    FOREIGN KEY (car_application) REFERENCES capstone_application (ca_id),
    FOREIGN KEY (car_interest_level) REFERENCES capstone_interest_level (cil_id)
);
