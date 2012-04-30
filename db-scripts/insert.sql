INSERT INTO coop_roles (role) 
   VALUES ('supervisor'),('admin'),('coordinator'),('user');

INSERT INTO coop_classes (name, syllabus) 
   VALUES ('HUM 193V', 'Syllabus for HUM 193V'),
          ('SSCI 193V', 'Syllabus for SSCI 193V'),
          ('CENT 293V', 'Syllabus for CENT 293V'),
          ('AMT 93V', 'Syllabus for AMT 93V');

INSERT INTO coop_users (fname, lname, username, password, roles_id)
   VALUES ('Joseph', 'Workman', 'kuukekoa','pass', 4),
          ('Jane', 'Doe', 'janedoe','pass', 4),
          ('Becky', 'Ousley', 'ousley','pass', 4),
          ('John', 'Doe', 'johndoe','pass', 4),
          ('Timothy', 'Barclay', 'barclay','pass', 4),
          ('Anne', 'Oliva', 'oliva','pass', 4),
          ('Diane', 'Caulfield', 'dcaulfie', 'pass', 3),
          ('coordinator', 'coordinator', 'coordinator', 'pass', 3),
          ('superv1', 'superv1', 'superv1', 'pass', 1),
          ('superv2', 'superv2', 'superv2', 'pass', 1),
          ('superv3', 'superv3', 'superv3', 'pass', 1);


INSERT INTO coop_students (username)
   VALUES ('kuukekoa'),
          ('janedoe'),
          ('ousley'),
          ('johndoe'),
          ('barclay'),
          ('oliva');

INSERT INTO coop_coordinators (username)
   VALUES ('dcaulfie'),
          ('coordinator');

INSERT INTO coop_supervisors (username)
   VALUES ('superv1'),
          ('superv2'),
          ('superv3');

INSERT INTO coop_contracts (semester)
   VALUES ('Spring 2012');

INSERT INTO coop_users_semesters (student, semesters_id, classes_id, supervisor, 
                                 credits)
   VALUES ('kuukekoa', 1, 3, 'superv1', 12),
          ('johndoe', 3, 1, 'superv2', 12),
          ('johndoe', 2, 1, 'superv3', 12),
          ('johndoe', 1, 2, 'superv1', 12),
          ('janedoe', 2, 2, 'superv2', 11),
          ('ousley', 1, 3, 'superv3', 10);

INSERT INTO coop_phonetypes (type)
   VALUES ('home'),
          ('mobile');
