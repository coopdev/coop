INSERT INTO coop_roles (role) 
   VALUES ('super-admin'),('admin'),('coordinator'),('user');

INSERT INTO coop_classes (name, syllabus) 
   VALUES ('HUM 193V', 'Syllabus for HUM 193V'),
          ('SSCI 193V', 'Syllabus for SSCI 193V'),
          ('AMT 93V', 'Syllabus for AMT 93V');

INSERT INTO coop_users (fname, lname, username, password, roles_id,
                        agreedto_contract, active)
   VALUES ('Joseph', 'Workman', 'kuukekoa','pass', 4, 0, 0),
          ('Jane', 'Doe', 'janedoe','pass', 4, 0, 0),
          ('Becky', 'Ousley', 'ousley','pass', 4, 0, 0),
          ('John', 'Doe', 'johndoe','pass', 4, 0, 0),
          ('Timothy', 'Barclay', 'barclay','pass', 4, 0, 0),
          ('Anne', 'Oliva', 'oliva','pass', 4, 0, 0);

INSERT INTO coop_coordinators (fname, lname, username, password, roles_id)
   VALUES ('Diane', 'Caulfield', 'dcaulfie', 'pass', 3),
          ('coordinator', 'coordinator', 'coordinator', 'pass', 3);

INSERT INTO coop_supervisors (fname, lname, username, password)
   VALUES ('superv1', 'superv1', 'superv1', 'pass'),
          ('superv2', 'superv2', 'superv2', 'pass'),
          ('superv3', 'superv3', 'superv3', 'pass');

INSERT INTO coop_contracts (semester)
   VALUES ('Spring 2012');

INSERT INTO coop_users_semesters (users_id, semesters_id, classes_id, supervisors_id, credits)
   VALUES (1, 1, 3, 1, 12),
          (4, 3, 1, 2, 12),
          (4, 2, 1, 1, 12),
          (4, 1, 2, 1, 12),
          (2, 2, 2, 3, 11),
          (3, 1, 3, 2, 10);

