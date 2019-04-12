--
-- Capstone Project Tables
-- This script assumes that there already exists a `user` table for capstone website users
--
CREATE TABLE IF NOT EXISTS capstone_project_compensation (
    cpcmp_id INT NOT NULL AUTO_INCREMENT,
    cpcmp_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpcmp_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_category (
    cpc_id INT NOT NULL AUTO_INCREMENT,
    cpc_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpc_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_type (
    cpt_id INT NOT NULL AUTO_INCREMENT,
    cpt_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpt_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_focus (
    cpf_id INT NOT NULL AUTO_INCREMENT,
    cpf_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpf_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_cop (
    cpcop_id INT NOT NULL AUTO_INCREMENT,
    cpcop_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpcop_id)
);

CREATE TABLE IF NOT EXISTS capstone_nda_ip (
    cpni_id INT NOT NULL AUTO_INCREMENT,
    cpni_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpni_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_status (
    cps_id INT NOT NULL AUTO_INCREMENT,
    cps_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cps_id)
);

CREATE TABLE IF NOT EXISTS capstone_project (
    cp_id CHAR(16) NOT NULL,
    cp_proposer CHAR(16) NOT NULL,
    cp_title VARCHAR(256) NOT NULL,
    cp_motivation TEXT NOT NULL,
    cp_description TEXT NOT NULL,
    cp_objectives TEXT NOT NULL,
    cp_date_start DATETIME NOT NULL,
    cp_date_end DATETIME NOT NULL,
    cp_min_qualifications TEXT NOT NULL,
    cp_preferred_qualifications TEXT,
    cp_compensation INT NOT NULL,
    cp_additional_emails TEXT,
    cp_category INT NOT NULL,
    cp_type INT NOT NULL,
    cp_focus INT NOT NULL,
    cp_cop INT NOT NULL,
    cp_nda_ip INT NOT NULL,
    cp_website VARCHAR(512),
    cp_image VARCHAR(256),
    cp_video VARCHAR(256),
    cp_is_hidden BOOLEAN NOT NULL DEFAULT TRUE,
    cp_proposer_comments TEXT,
    cp_status INT NOT NULL,
    cp_archived BOOLEAN NOT NULL DEFAULT FALSE,
    cp_date_created DATETIME NOT NULL,
    cp_date_updated DATETIME NOT NULL,

    PRIMARY KEY (cp_id),
    FOREIGN KEY (cp_proposer) REFERENCES user (u_id),
    FOREIGN KEY (cp_compensation) REFERENCES capstone_project_compensation (cpcmp_id),
    FOREIGN KEY (cp_category) REFERENCES capstone_project_category (cpc_id),
    FOREIGN KEY (cp_type) REFERENCES capstone_project_type (cpt_id),
    FOREIGN KEY (cp_focus) REFERENCES capstone_project_focus (cpf_id),
    FOREIGN KEY (cp_cop) REFERENCES capstone_project_cop (cpcop_id),
    FOREIGN KEY (cp_nda_ip) REFERENCES capstone_nda_ip (cpni_id),
    FOREIGN KEY (cp_status) REFERENCES capstone_project_status (cps_id)
);

CREATE TABLE IF NOT EXISTS capstone_tag (
    ct_id INT NOT NULL AUTO_INCREMENT,
    ct_name VARCHAR(128) NOT NULL,
    ct_parent INT,
    ct_approved BOOLEAN NOT NULL DEFAULT TRUE,

    PRIMARY KEY (ct_id),
    FOREIGN KEY (ct_parent) REFERENCES capstone_tag (ct_id)
);

CREATE TABLE IF NOT EXISTS capstone_tag_for (
    ct_id INT NOT NULL,
    cp_id CHAR(16) NOT NULL,

    PRIMARY KEY (ct_id, cp_id),
    FOREIGN KEY (ct_id) REFERENCES capstone_tag (ct_id),
    FOREIGN KEY (cp_id) REFERENCES capstone_project (cp_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_group (
    cpg_id CHAR(16) NOT NULL,
    cpg_project CHAR(16) NOT NULL,
    cpg_status INT NOT NULL,

    PRIMARY KEY (cpg_id),
    FOREIGN KEY (cpg_project) REFERENCES capstone_project (cp_id),
    FOREIGN KEY (cpg_status) REFERENCES capstone_project_status (cps_id)
);

CREATE TABLE IF NOT EXISTS capstone_assigned_to (
    u_id CHAR(16) NOT NULL,
    cpg_id CHAR(16) NOT NULL,

    PRIMARY KEY (u_id, cpg_id),
    FOREIGN KEY (u_id) REFERENCES user (u_id),
    FOREIGN KEY (cpg_id) REFERENCES capstone_project_group (cpg_id)
);