INSERT INTO coop_roles (role) 
   VALUES ('supervisor'),('admin'),('coordinator'),('user');

INSERT INTO coop_majors (major)
   VALUES ('HUM'),
          ('SSCI'),
          ('CENT'),
          ('AMT');

INSERT INTO coop_classes (name) 
   VALUES ('HUM 193V'),
          ('SSCI 193V'),
          ('CENT 293V'),
          ('AMT 93V');

INSERT INTO coop_syllabuses (syllabus, classes_id, final)
   VALUES ('Syllabus for HUM 193V', 1, 1),
          ('Syllabus for SSCI 193V', 2, 1),
          ('Syllabus for CENT 293V', 3, 1),
          ('Syllabus for AMT 93V', 4, 1);

INSERT INTO coop_users (fname, lname, username, password, roles_id)
   VALUES ('Joseph', 'Workman', 'kuukekoa','pass', 4),
          ('Jane', 'Doe', 'janedoe','pass', 4),
          ('Becky', 'Ousley', 'ousley','pass', 4),
          ('John', 'Doe', 'johndoe','pass', 4),
          ('Timothy', 'Barclay', 'barclay','pass', 4),
          ('Anne', 'Oliva', 'oliva','pass', 4),
          ('Diane', 'Caulfield', 'dcaulfie', 'pass', 3),
          ('coord1', 'coord1', 'coord1', 'pass', 3),
          ('coord2', 'coord2', 'coord2', 'pass', 3),
          ('coord3', 'coord3', 'coord3', 'pass', 3),
          ('Travis', 'Toka', 'toka', 'pass', 3),
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
          ('coord1'),
          ('coord2'),
          ('coord3');

INSERT INTO coop_supervisors (username)
   VALUES ('superv1'),
          ('superv2'),
          ('superv3');


INSERT INTO coop_users_semesters (student, semesters_id, classes_id, 
                                 credits, coordinator)
   VALUES ('kuukekoa', 1, 3, 12, 'dcaulfie'),
          ('johndoe', 3, 1, 12, 'coord3'),
          ('johndoe', 2, 1, 12, 'coord1'),
          ('johndoe', 1, 2, 12, 'coord2'),
          ('janedoe', 2, 2, 11, 'coord3'),
          ('ousley', 1, 3, 10, 'coord2');

INSERT INTO coop_phonetypes (type)
   VALUES ('home'),
          ('mobile');

INSERT INTO coop_assignments (assignment, due_date, online)
   VALUES ('Student Info Sheet', 20120411, 1),
          ('assignment2', 20120609, 0),
          ('assignment3', 20120519, 0);
