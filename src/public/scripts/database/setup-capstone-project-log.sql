CREATE TABLE IF NOT EXISTS capstone_project_log (
    lg_cp_id CHAR(16) NOT NULL,
    lg_date_created DATETIME,
    lg_message VARCHAR(512),

    PRIMARY KEY (cp_date_updated),
    FOREIGN KEY (cp_id) REFERENCES capstone_project (cp_id)
);
