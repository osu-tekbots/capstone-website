--
-- Capstone Project Application tables
-- Setup Script Order: 3
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
    ca_cp_id CHAR(16) NOT NULL,
    ca_u_id CHAR(16) NOT NULL,
    ca_justification TEXT NOT NULL,
    ca_time_available VARCHAR(256) NOT NULL,
    ca_skill_set TEXT NOT NULL,
    ca_portfolio_link VARCHAR(512),
    ca_cas_id INT NOT NULL,
    ca_date_created DATETIME NOT NULL,
    ca_date_updated DATETIME,
    ca_date_submitted DATETIME,

    PRIMARY KEY (ca_id),
    FOREIGN KEY (ca_cp_id) REFERENCES capstone_project (cp_id),
    FOREIGN KEY (ca_u_id) REFERENCES user (u_id),
    FOREIGN KEY (ca_cas_id) REFERENCES capstone_application_status (cas_id)
);

CREATE TABLE IF NOT EXISTS capstone_interest_level (
    cil_id INT NOT NULL AUTO_INCREMENT,
    cil_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cil_id)
);

CREATE TABLE IF NOT EXISTS capstone_application_review (
    car_id CHAR(16) NOT NULL,
    car_ca_id CHAR(16) NOT NULL,
    car_cil_id INT NOT NULL,
    car_comments TEXT,

    PRIMARY KEY (car_id),
    FOREIGN KEY (car_ca_id) REFERENCES capstone_application (ca_id),
    FOREIGN KEY (car_cil_id) REFERENCES capstone_interest_level (cil_id)
);
