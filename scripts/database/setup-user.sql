--
-- User Tables
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
    u_type INT NOT NULL,
    u_fname VARCHAR(128),
    u_lname VARCHAR(128),
    u_salutation INT,
    u_email VARCHAR(200),
    u_phone INT,
    u_major VARCHAR(128),
    u_affiliation VARCHAR(256),
    u_onid VARCHAR(32),
    u_auth_provider INT NOT NULL,
    u_auth_provider_id VARCHAR(128),
    u_date_created DATETIME NOT NULL,
    u_date_updated DATETIME,
    u_date_last_login DATETIME,

    PRIMARY KEY (u_id),
    FOREIGN KEY (u_type) REFERENCES user_type(ut_id),
    FOREIGN KEY (u_salutation) REFERENCES user_salutation (us_id),
    FOREIGN KEY (u_auth_provider) REFERENCES user_auth_provider (uap_id)
);