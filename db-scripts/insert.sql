INSERT INTO coop_roles (role) 
   VALUES ('super-admin'),('admin'),('manager'),('normal');

INSERT INTO coop_users (fname, lname, roles_id, uuid, agreedto_contract)
   VALUES ('Joseph', 'Workman', 4, '17861806', 1);

INSERT INTO coop_contracts (semester)
   VALUES ('Spring 2012');

INSERT INTO coop_courses (name, syllabus) 
   VALUES ('HUM 193V', 'Syllabus for HUM 193V'),
          ('SSCI 193V', 'Syllabus for SSCI 193V');
