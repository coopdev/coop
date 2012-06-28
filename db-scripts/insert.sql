INSERT INTO coop_roles (role) 
   VALUES ('supervisor'),('studentAid'),('coordinator'),('user');

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
          ('Syllabus for HUM 193V', 1, 0),
          ('Syllabus for SSCI 193V', 2, 1),
          ('Syllabus for SSCI 193V', 2, 0),
          ('Syllabus for CENT 293V', 3, 1),
          ('Syllabus for CENT 293V', 3, 0),
          ('Syllabus for AMT 93V', 4, 1),
          ('Syllabus for AMT 93V', 4, 0);

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
          ('Travis', 'Toka', 'toka', 'pass', 3);


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


INSERT INTO coop_users_semesters (student, semesters_id, classes_id, 
                                 credits, coordinator)
   VALUES ('kuukekoa', 14, 3, 12, 'dcaulfie'),
          ('johndoe', 14, 1, 12, 'coord3'),
          ('johndoe', 14, 4, 12, 'coord1'),
          ('johndoe', 14, 2, 12, 'coord2'),
          ('janedoe', 14, 2, 11, 'coord3'),
          ('ousley', 14, 3, 10, 'coord2');

INSERT INTO coop_phonetypes (type)
   VALUES ('home'),
          ('mobile');

INSERT INTO coop_assignments (assignment, due_date, assignment_num, online, questions_editable)
   VALUES ('Student Information Sheet', 20120811, 1, 1, 0),
          ('Midterm Report', 20120809, 2, 1, 1),
          ('Cooperative Education Agreement', 20120701, 3, 0, 0),
          ('Learning Outcome Report', 20120701, 4, 1, 0),
          ('Student Evaluation', 20120801, 5, 1, 1),
          ('Employer Evaluation', 20120801, 6, 0, 1),
          ('assignment2', 20120609, 20, 0, 0),
          ('assignment3', 20120519, 21, 0, 0);

INSERT INTO coop_assignmentquestions (assignments_id, question_number, question_text, 
                                      answer_minlength)
   VALUES (2, '1', 'First Question', 10),
          (2, '2', 'Second Question', 10),
          (2, '3', 'Third Question', 10);

INSERT INTO coop_disclaimer_text (text) 
   VALUES ('');
   

