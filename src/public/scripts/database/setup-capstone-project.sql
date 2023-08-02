--
-- Capstone Project Tables
-- Setup Script Order: 2
--
-- This script assumes that there already exists a `user` table for capstone website users
--
CREATE TABLE IF NOT EXISTS capstone_project_compensation (
    cpcmp_id INT NOT NULL AUTO_INCREMENT,
    cpcmp_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpcmp_id)
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

CREATE TABLE IF NOT EXISTS capstone_project_nda_ip (
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
    cp_u_id CHAR(16) NOT NULL,
    cp_title VARCHAR(256) NOT NULL,
    cp_motivation TEXT,
    cp_description TEXT,
    cp_objectives TEXT ,
    cp_date_start DATETIME,
    cp_date_end DATETIME,
    cp_min_qual TEXT,
    cp_preferred_qual TEXT,
    cp_cpcmp_id INT NOT NULL,
    cp_additional_emails TEXT,
    cp_cpt_id INT NOT NULL,
    cp_cpf_id INT NOT NULL,
    cp_cpcop_id INT NOT NULL,
    cp_cpni_id INT NOT NULL,
    cp_number_groups INT NOT NULL,
    cp_website_link VARCHAR(512),
    cp_video_link VARCHAR(256),
    cp_is_hidden BOOLEAN NOT NULL DEFAULT TRUE,
    cp_is_sponsored BOOLEAN NOT NULL DEFAULT TRUE,
    cp_proposer_comments TEXT,
    cp_admin_comments TEXT,
    cp_cps_id INT NOT NULL,
    cp_archived BOOLEAN NOT NULL DEFAULT FALSE,
    cp_date_created DATETIME NOT NULL,
    cp_date_updated DATETIME,

    PRIMARY KEY (cp_id),
    FOREIGN KEY (cp_u_id) REFERENCES user (u_id),
    FOREIGN KEY (cp_cpcmp_id) REFERENCES capstone_project_compensation (cpcmp_id),
    FOREIGN KEY (cp_cpt_id) REFERENCES capstone_project_type (cpt_id),
    FOREIGN KEY (cp_cpf_id) REFERENCES capstone_project_focus (cpf_id),
    FOREIGN KEY (cp_cpcop_id) REFERENCES capstone_project_cop (cpcop_id),
    FOREIGN KEY (cp_cpni_id) REFERENCES capstone_project_nda_ip (cpni_id),
    FOREIGN KEY (cp_cps_id) REFERENCES capstone_project_status (cps_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_edit_permissions (
    pf_cp_id CHAR(16) NOT NULL,
    pf_u_id CHAR(16) NOT NULL,

    FOREIGN KEY (pf_cp_id) REFERENCES capstone_project (cp_id),
    FOREIGN KEY (pf_u_id) REFERENCES user (u_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_image (
    cpi_id CHAR(16) NOT NULL,
    cpi_cp_id CHAR(16) NOT NULL,
    cpi_name VARCHAR(128) NOT NULL,
    cpi_is_default BOOLEAN NOT NULL,
    cpi_is_provided BOOLEAN NOT NULL,
    cpi_provided_image_name VARCHAR(128),

    PRIMARY KEY (cpi_id),
    FOREIGN KEY (cpi_cp_id) REFERENCES capstone_project (cp_id)
);

CREATE TABLE IF NOT EXISTS capstone_keyword (
    ck_id INT NOT NULL AUTO_INCREMENT,
    ck_name VARCHAR(128) NOT NULL,
    ck_parent_ck_id INT,
    ck_approved BOOLEAN NOT NULL,

    PRIMARY KEY (ck_id),
    FOREIGN KEY (ck_parent_ck_id) REFERENCES capstone_keyword (ck_id)
);

CREATE TABLE IF NOT EXISTS capstone_keyword_for (
    ckf_ck_id INT NOT NULL,
    ckf_entity_id CHAR(16) NOT NULL,

    PRIMARY KEY (ckf_ck_id, ckf_entity_id),
    FOREIGN KEY (ckf_ck_id) REFERENCES capstone_keyword (ck_id)
);

CREATE TABLE IF NOT EXISTS capstone_project_group (
    cpg_id CHAR(16) NOT NULL,
    cpg_cp_id CHAR(16) NOT NULL,
    cpg_cps_id INT NOT NULL,

    PRIMARY KEY (cpg_id),
    FOREIGN KEY (cpg_cp_id) REFERENCES capstone_project (cp_id),
    FOREIGN KEY (cpg_cps_id) REFERENCES capstone_project_status (cps_id)
);

CREATE TABLE IF NOT EXISTS capstone_assigned_to (
    cat_u_id CHAR(16) NOT NULL,
    cat_cpg_id CHAR(16) NOT NULL,

    PRIMARY KEY (cat_u_id, cat_cpg_id),
    FOREIGN KEY (cat_u_id) REFERENCES user (u_id),
    FOREIGN KEY (cat_cpg_id) REFERENCES capstone_project_group (cpg_id)
);

CREATE TABLE IF NOT EXISTS capstone_category (
    cc_id INT NOT NULL AUTO_INCREMENT,
    cc_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cc_id)
);

CREATE TABLE IF NOT EXISTS capstone_category_for (
    ccf_cc_id INT NOT NULL,
    ccf_entity_id CHAR(16) NOT NULL,

    PRIMARY KEY (ccf_cc_id, ccf_entity_id),
    FOREIGN KEY (ccf_cc_id) REFERENCES capstone_category (cc_id)
);

CREATE TABLE IF NOT EXISTS capstone_pref_course (
    cpc_id INT NOT NULL AUTO_INCREMENT,
    cpc_code VARCHAR(128) NOT NULL,
    cpc_name VARCHAR(128) NOT NULL,

    PRIMARY KEY (cpc_id)
);

CREATE TABLE IF NOT EXISTS capstone_pref_course_for (
    cpcf_cpc_id INT NOT NULL,
    cpcf_entity_id CHAR(16) NOT NULL,

    PRIMARY KEY (cpcf_cpc_id, cpcf_entity_id),
    FOREIGN KEY (cpcf_cpc_id) REFERENCES capstone_pref_course (cpc_id)
);
