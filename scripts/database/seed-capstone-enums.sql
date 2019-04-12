--
-- Capstone Enum Table Seeder
--
-- This script assumes that all database table setup scripts for the capstone website have been
-- executed and that the corresponding tables exist in the database
--
INSERT INTO user_type (ut_name) VALUES ('User'), ('Proposer'), ('Admin');

INSERT INTO user_salutation (us_name) VALUES ('Mr.'), ('Mrs.'), ('Miss'), ('Ms.'), ('Dr.'), ('Prof.');

INSERT INTO user_auth_provider (uap_name) VALUES ('ONID'), ('Google'), ('Microsoft');

INSERT INTO capstone_interest_level (cil_name) VALUES ('Desireable'), ('Undesireable'), ('Impartial');

INSERT INTO capstone_application_status (cas_name) VALUES ('Started'), ('Submitted'), ('Accepted'), ('Closed');

INSERT INTO capstone_project_compensation (cpcmp_name) VALUES 
    ('Hourly'), ('Stipend'), ('Completion-dependent'), ('Other');

INSERT INTO capstone_project_category (cpc_name) VALUES ('Electrical Engineering'), ('Computer Science'), ('EECS');

INSERT INTO capstone_project_type (cpt_name) VALUES ('Capstone'), ('Long-term'), ('Student Club Project');

INSERT INTO capstone_project_focus (cpf_name) VALUES ('Research'), ('Development');

-- TODO: get additional seeds for the communities of practice
INSERT INTO capstone_project_cop (cpcop_name) VALUES  ('Internet-of-Things Alliance'), ('Linux Users Group');

INSERT INTO capstone_nda_ip (cpni_name) VALUES ('No Agreement Required'), ('NDA Required'), ('NDA/IP Required');

INSERT INTO capstone_project_status (cps_name) VALUES 
    ('Pending Approval'), ('Rejected'), ('Accepting Applicants'), ('In-progress'), ('Complete'), ('Incomplete');

