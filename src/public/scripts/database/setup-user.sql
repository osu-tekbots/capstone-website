--
-- User Tables
-- Setup Script Order: 1
--
CREATE TABLE IF NOT EXISTS user_type (
    ut_id INT NOT NULL AUTO_INCREMENT,
    ut_name VARCHAR(128),

    PRIMARY KEY (ut_id)
);

CREATE TABLE IF NOT EXISTS user_salutation (
    us_id INT NOT NULL AUTO_INCREMENT,
    us_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (us_id)
);

CREATE TABLE IF NOT EXISTS user_auth_provider (
    uap_id INT NOT NULL AUTO_INCREMENT,
    uap_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (uap_id)
);


CREATE TABLE IF NOT EXISTS user (
    u_id CHAR(16) NOT NULL,
    u_ut_id INT NOT NULL,
    u_fname VARCHAR(128),
    u_lname VARCHAR(128),
    u_us_id INT,
    u_email VARCHAR(256),
    u_phone VARCHAR(16),
    u_major VARCHAR(128),
    u_affiliation VARCHAR(128),
    u_onid VARCHAR(32),
    u_uap_id INT NOT NULL,
    u_uap_provided_id VARCHAR(256),
    u_date_created DATETIME NOT NULL,
    u_date_updated DATETIME,
    u_date_last_login DATETIME,

    PRIMARY KEY (u_id),
    FOREIGN KEY (u_ut_id) REFERENCES user_type(ut_id),
    FOREIGN KEY (u_us_id) REFERENCES user_salutation (us_id),
    FOREIGN KEY (u_uap_id) REFERENCES user_auth_provider (uap_id)
);

CREATE TABLE IF NOT EXISTS user_local_auth (
    ula_id CHAR(16) NOT NULL,
    ula_pw CHAR(128) NOT NULL,
    
    FOREIGN KEY (ula_id) REFERENCES user(u_id)
);


CREATE TABLE IF NOT EXISTS user_local_auth_salt (
    ulas_salt CHAR(128) NOT NULL
);

CREATE TABLE IF NOT EXISTS user_local_auth_reset (
    ular_user CHAR(16) NOT NULL,
    ular_key VARCHAR(256),
    ular_date_expires DATETIME NOT NULL,

    FOREIGN KEY (ular_user) REFERENCES user(u_id)
);