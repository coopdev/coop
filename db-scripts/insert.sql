INSERT INTO coop_roles (role) 
   VALUES ('super-admin'),('admin'),('coordinator'),('user');

INSERT INTO coop_classes (name, syllabus) 
   VALUES ('HUM 193V', 'Syllabus for HUM 193V'),
          ('SSCI 193V', 'Syllabus for SSCI 193V'),
          ('AMT 93V', 'Syllabus for AMT 93V');

INSERT INTO coop_users (fname, lname, username, roles_id, semesters_id, 
                        classes_id, agreedto_contract, active)
   VALUES ('Joseph', 'Workman', 'kuukekoa', 4, 1, 3, 0, 1),
          ('Jane', 'Doe', 'janedoe', 4, 2, 2, 0, 1),
          ('Becky', 'Ousley', 'ousley', 4, 1, 3, 0, 1),
          ('John', 'Doe', 'johndoe', 4, 2, 3, 0, 0),
          ('Timothy', 'Barclay', 'barclay', 4, 3, 2, 0, 0),
          ('Anne', 'Oliva', 'oliva', 4, 1, 1, 0, 0);

INSERT INTO coop_coordinators (fname, lname, username, roles_id)
   VALUES ('Diane', 'Caulfield', 'dcaulfie', 3);

INSERT INTO coop_contracts (semester)
   VALUES ('Spring 2012');

INSERT INTO coop_users_semesters (users_id, semesters_id, classes_id, credits)
   VALUES (1, 1, 3, 12),
          (2, 2, 2, 11),
          (3, 1, 3, 10);
