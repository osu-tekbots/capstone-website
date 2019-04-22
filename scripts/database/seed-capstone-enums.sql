--
-- Capstone Enum Table Seeder
-- Setup Script Order: 4
--
-- This script assumes that all database table setup scripts for the capstone website have been
-- executed and that the corresponding tables exist in the database
--
INSERT INTO user_type (ut_id, ut_name) VALUES 
    (1, 'User'), (2, 'Proposer'), (3, 'Admin');

INSERT INTO user_salutation (us_id, us_name) VALUES 
    (1, 'None'), (2, 'Mr.'), (3, 'Mrs.'), (4, 'Miss'), (5, 'Ms.'), (6, 'Dr.'), (7, 'Prof.');

INSERT INTO user_auth_provider (uap_id, uap_name) VALUES 
    (1, 'None'), (2, 'ONID'), (3, 'Google'), (4, 'Microsoft');

INSERT INTO capstone_interest_level (cil_id, cil_name) VALUES 
    (1, 'Desireable'), (2, 'Undesireable'), (3, 'Impartial');

INSERT INTO capstone_application_status (cas_id, cas_name) VALUES 
    (1, 'Started'), (2, 'Submitted'), (3, 'Accepted'), (4, 'Closed');

INSERT INTO capstone_project_compensation (cpcmp_id, cpcmp_name) VALUES 
    (1, 'None'), (2, 'Hourly'), (3, 'Stipend'), (4, 'Completion-dependent'), (5, 'Other');

INSERT INTO capstone_project_category (cpc_id, cpc_name) VALUES 
    (1, 'Electrical Engineering'), (2, 'Computer Science'), (3, 'EECS');

INSERT INTO capstone_project_type (cpt_id, cpt_name) VALUES 
    (1, 'Capstone'), (2, 'Long-term'), (3, 'Student Club Project');

INSERT INTO capstone_project_focus (cpf_id, cpf_name) VALUES 
    (1, 'Research'), (2, 'Development');

-- TODO: get additional seeds for the communities of practice
INSERT INTO capstone_project_cop (cpcop_id, cpcop_name) VALUES  
    (1, 'Internet-of-Things Alliance'), (2, 'Linux Users Group');

INSERT INTO capstone_project_nda_ip (cpni_id, cpni_name) VALUES 
    (1, 'No Agreement Required'), (2, 'NDA Required'), (3, 'NDA/IP Required');

INSERT INTO capstone_project_status (cps_id, cps_name) VALUES 
    (1, 'Pending Approval'), (2, 'Rejected'), (3, 'Accepting Applicants'), (4, 'In-progress'), (5, 'Complete'), 
    (6, 'Incomplete');

