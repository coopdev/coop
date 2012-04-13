INSERT INTO coop_roles (role) 
   VALUES ('super-admin'),('admin'),('coordinator'),('user');

INSERT INTO coop_users (fname, lname, roles_id, username, agreedto_contract)
   VALUES ('Joseph', 'Workman', 4, 'kuukekoa', 1);

INSERT INTO coop_contracts (semester)
   VALUES ('Spring 2012');

INSERT INTO coop_classes (name, syllabus) 
   VALUES ('HUM 193V', 'Syllabus for HUM 193V'),
          ('SSCI 193V', 'Syllabus for SSCI 193V'),
          ('AMT 93V', 'Syllabus for AMT 93V');
