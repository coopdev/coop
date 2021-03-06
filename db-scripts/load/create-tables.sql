DROP TABLE IF EXISTS coop_logins;
DROP TABLE IF EXISTS coop_extended_duedates;
DROP TABLE IF EXISTS coop_disclaimer_text;
DROP TABLE IF EXISTS coop_disclaimers;
DROP TABLE IF EXISTS coop_submittedassignments;
DROP TABLE IF EXISTS coop_assignmentanswers;
DROP TABLE IF EXISTS coop_assignmentquestions;
DROP TABLE IF EXISTS coop_survey_specifics;
DROP TABLE IF EXISTS coop_assignments;
DROP TABLE IF EXISTS coop_syllabuses;
DROP TABLE IF EXISTS coop_users_semesters;
DROP TABLE IF EXISTS coop_employmentinfo;
DROP TABLE IF EXISTS coop_classes;
DROP TABLE IF EXISTS coop_coordinators;
DROP TABLE IF EXISTS coop_semesters;
DROP TABLE IF EXISTS coop_supervisors;
DROP TABLE IF EXISTS coop_phonenumbers;
DROP TABLE IF EXISTS coop_addresses;
DROP TABLE IF EXISTS coop_students;
DROP TABLE IF EXISTS coop_users;
DROP TABLE IF EXISTS coop_roles;
DROP TABLE IF EXISTS coop_phonetypes;
DROP TABLE IF EXISTS coop_majors;
DROP TABLE IF EXISTS coop_backups;





DROP TABLE IF EXISTS coop_phonetypes;
CREATE TABLE coop_phonetypes(
   id INT NOT NULL AUTO_INCREMENT,
   type TEXT,
   PRIMARY KEY(id)
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_majors;
CREATE TABLE coop_majors(
   id INT NOT NULL AUTO_INCREMENT,
   major TEXT,
   PRIMARY KEY(id)
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_assignments;
CREATE TABLE coop_assignments(
   id INT NOT NULL AUTO_INCREMENT,
   assignment VARCHAR(100),
   due_date DATE NULL,
   assignment_num INT,
   option_amount INT,
   online BOOLEAN,
   questions_editable BOOLEAN DEFAULT 0,
   PRIMARY KEY(id)
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_semesters;
CREATE TABLE coop_semesters(
   id INT NOT NULL AUTO_INCREMENT,
   semester VARCHAR(20) UNIQUE,
   current BOOLEAN DEFAULT 0,
   PRIMARY KEY(id)
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_roles;
CREATE TABLE coop_roles(
   id INT NOT NULL AUTO_INCREMENT,
   role VARCHAR(20) UNIQUE,
   PRIMARY KEY(id)
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_users;
CREATE TABLE coop_users(
   id INT NOT NULL AUTO_INCREMENT, 
   fname TEXT,
   lname TEXT,
   uuid CHAR(8) UNIQUE,
   username VARCHAR(100),
   password VARCHAR(20),
   email TEXT,
   roles_id INT,
   active BOOLEAN DEFAULT 0,
   PRIMARY KEY(id),
   KEY user (username),
   FOREIGN KEY(roles_id) REFERENCES coop_roles(id) ON DELETE SET NULL
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_coordinators;
CREATE TABLE coop_coordinators(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_classes;
CREATE TABLE coop_classes(
   id INT NOT NULL AUTO_INCREMENT,
   coordinator VARCHAR(100) COMMENT "username of coordinator",
   name TEXT,
   PRIMARY KEY(id),
   FOREIGN KEY(coordinator) REFERENCES coop_users(username) ON DELETE SET NULL 
      ON UPDATE CASCADE
) ENGINE InnoDB;

-- Stuff related specifically to the survey type assignments, like option amount.
CREATE TABLE coop_survey_specifics(
    id INT NOT NULL AUTO_INCREMENT,
    option_amount INT,
    use_global BOOLEAN,
    assignments_id INT,
    classes_id INT,
    PRIMARY KEY(id),
    FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id),
    FOREIGN KEY(classes_id) REFERENCES coop_classes(id)
) ENGINE InnoDB;

CREATE TABLE coop_assignmentquestions(
   id INT NOT NULL AUTO_INCREMENT,
   assignments_id INT,
   classes_id INT,
   question_number TEXT,
   question_type TEXT,
   parent INT,
   question_text TEXT,
   answer_minlength INT,
   PRIMARY KEY(id),
   FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id) ON DELETE CASCADE,
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE CASCADE
) ENGINE InnoDB;

CREATE TABLE coop_assignmentanswers(
   id INT NOT NULL AUTO_INCREMENT,
   assignments_id INT,
   assignmentquestions_id INT,
   classes_id INT,
   semesters_id INT,
   username VARCHAR(100),
   answer_text TEXT,
   PRIMARY KEY(id),
   FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id) ON DELETE CASCADE,
   FOREIGN KEY(assignmentquestions_id) REFERENCES coop_assignmentquestions(id)
      ON DELETE CASCADE,
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE CASCADE,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id) ON DELETE CASCADE,
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_syllabuses;
CREATE TABLE coop_syllabuses(
   id INT NOT NULL AUTO_INCREMENT,
   classes_id INT,
   syllabus TEXT,
   final BOOLEAN,
   PRIMARY KEY(id),
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE CASCADE
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_disclaimers;
CREATE TABLE coop_disclaimers(
   id INT NOT NULL AUTO_INCREMENT,
   semesters_id INT,
   username VARCHAR(100),
   date_agreed DATE DEFAULT NULL,
   PRIMARY KEY(id),
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id) ON DELETE CASCADE,
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_supervisors;
CREATE TABLE coop_supervisors(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON UPDATE CASCADE
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_submittedassignments;
CREATE TABLE coop_submittedassignments(
   id INT NOT NULL AUTO_INCREMENT,
   assignments_id INT,
   username VARCHAR(100),
   semesters_id INT,
   classes_id INT,
   date_submitted DATE NULL,
   is_final BOOLEAN DEFAULT 0,
   PRIMARY KEY(id),
   FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id) ON DELETE CASCADE,
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id),
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE SET NULL
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_users_semesters;
CREATE TABLE coop_users_semesters(
   id INT NOT NULL AUTO_INCREMENT,
   student VARCHAR(100),
   semesters_id INT,
   classes_id INT,
   coordinator VARCHAR(100),
   supervisor VARCHAR(100),
   credits INT,
   PRIMARY KEY(id),
   FOREIGN KEY(student) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id) ON DELETE SET NULL,
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE CASCADE,
   FOREIGN KEY(coordinator) REFERENCES coop_users(username) ON DELETE SET NULL 
      ON UPDATE CASCADE,
   FOREIGN KEY(supervisor) REFERENCES coop_users(username) ON UPDATE CASCADE
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_phonenumbers;
CREATE TABLE coop_phonenumbers(
   id INT NOT NULL AUTO_INCREMENT,
   phonenumber CHAR(8),
   username VARCHAR(100),
   phonetypes_id INT,
   date_mod DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(phonetypes_id) REFERENCES coop_phonetypes(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;

-- Addresses for users in database
DROP TABLE IF EXISTS coop_addresses;
CREATE TABLE coop_addresses(
   id INT NOT NULL AUTO_INCREMENT,
   address TEXT,
   city TEXT,
   state TEXT,
   zipcode CHAR(5),
   username VARCHAR(100),
   date_mod DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;
   
-- Student employment info
DROP TABLE IF EXISTS coop_employmentinfo;
CREATE TABLE coop_employmentinfo(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   classes_id INT,
   semesters_id INT,
   current_job TEXT,
   wanted_job TEXT,
   wanted_class TEXT,
   start_date DATE NULL,
   end_date DATE NULL,
   rate_of_pay FLOAT,
   department TEXT,
   job_address TEXT,
   employer TEXT,
   superv_name TEXT,
   superv_title TEXT,
   superv_phone TEXT,
   superv_email TEXT,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE SET NULL,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id)
) ENGINE InnoDB;

DROP TABLE IF EXISTS coop_students;
CREATE TABLE coop_students(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   grad_date DATE NULL,
   majors_id INT,
   semester_in_major INT,
   coord_name TEXT,
   coord_phone TEXT,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY(majors_id) REFERENCES coop_majors(id) ON DELETE SET NULL
) ENGINE InnoDB;

create table coop_extended_duedates(
   id INT NOT NULL AUTO_INCREMENT,
   assignments_id INT,
   semesters_id INT,
   classes_id INT,
   username VARCHAR(100),
   due_date DATE,
   PRIMARY KEY(id),
   FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id) ON DELETE CASCADE,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id) ON DELETE SET NULL,
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE SET NULL,
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE
) ENGINE InnoDB;

create table coop_logins(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   login_date DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE
) ENGINE InnoDB;

create table coop_disclaimer_text(
   id INT NOT NULL AUTO_INCREMENT,
   text text,
   PRIMARY KEY(id)
) ENGINE InnoDB;

create table coop_backups(
   id INT NOT NULL AUTO_INCREMENT,
   name text,
   date DATETIME,
   PRIMARY KEY(id)
) ENGINE InnoDB;
   

-- View for a specific semester for a specific student
DROP VIEW IF EXISTS coop_users_semesters_view;
CREATE VIEW coop_users_semesters_view AS SELECT u.*, us.semesters_id, us.classes_id, 
   us.credits, us.student, s.semester, s.current, cl.name AS class, cl.coordinator,
   u2.fname AS coordfname, u2.lname AS coordlname
   FROM coop_users AS u JOIN coop_users_semesters AS us 
   ON u.username = us.student JOIN coop_semesters AS s
   ON us.semesters_id = s.id LEFT JOIN coop_classes AS cl ON us.classes_id = cl.id
   LEFT JOIN coop_users AS u2 ON cl.coordinator = u2.username; 
   -- LEFT JOIN coop_users AS u2 ON us.coordinator = u2.username; 

DROP VIEW IF EXISTS coop_survey_option_amount_view;
CREATE VIEW coop_survey_option_amount_view AS 
   SELECT a.option_amount AS global_amount, ss.option_amount AS specific_amount, 
   ss.use_global, ss.assignments_id, ss.classes_id
   FROM coop_assignments AS a
   LEFT JOIN coop_survey_specifics AS ss
     ON a.id = ss.assignments_id;



DROP VIEW IF EXISTS coop_classinfo_view;
CREATE VIEW coop_classinfo_view AS 
   SELECT c.*, u.fname, u.lname, u.email 
   FROM coop_classes AS c 
   LEFT JOIN coop_users AS u
      ON c.coordinator = u.username;

DROP VIEW IF EXISTS coop_userrole_view;
CREATE VIEW coop_userrole_view AS
   SELECT u.*, r.role
   FROM coop_users AS u 
   LEFT JOIN coop_roles AS r
      ON u.roles_id = r.id;

-- Create procedure to fetch the next semester after the current one
DROP PROCEDURE IF EXISTS next_sem;
DELIMITER //
CREATE PROCEDURE next_sem()
BEGIN
   SELECT id into @sem_id FROM coop_semesters WHERE current = 1;
   SELECT * FROM coop_semesters WHERE id = @sem_id + 1;
END//
DELIMITER ;
