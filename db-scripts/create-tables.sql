DROP TABLE IF EXISTS coop_roles;
CREATE TABLE coop_roles(
   id INT NOT NULL AUTO_INCREMENT,
   role VARCHAR(20) UNIQUE,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_users;
CREATE TABLE coop_users(
   id INT NOT NULL AUTO_INCREMENT, 
   fname TEXT,
   lname TEXT,
   uuid CHAR(8) UNIQUE,
   username VARCHAR(20) UNIQUE,
   password VARCHAR(20),
   email TEXT,
   coord_name TEXT,
   coord_phone TEXT,
   roles_id INT,
   agreedto_contract BOOLEAN DEFAULT 0,
   active BOOLEAN DEFAULT 0,
   PRIMARY KEY(id),
   FOREIGN KEY(roles_id) REFERENCES coop_roles(id)
);

DROP TABLE IF EXISTS coop_phonenumbers;
CREATE TABLE coop_phonenumbers(
   id INT NOT NULL AUTO_INCREMENT,
   phonenumber CHAR(8),
   username VARCHAR(100),
   phonetypes_id INT,
   date_mod DATE,
   PRIMARY KEY(id),
   FOREIGN KEY(phonetypes_id) REFERENCES coop_phonetypes(id),
   FOREIGN KEY(username) REFERENCES coop_users(username)
);

DROP TABLE IF EXISTS coop_phonetypes;
CREATE TABLE coop_phonetypes(
   id INT NOT NULL AUTO_INCREMENT,
   type TEXT,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_addresses;
CREATE TABLE coop_addresses(
   id INT NOT NULL AUTO_INCREMENT,
   address TEXT,
   city TEXT,
   state TEXT,
   zipcode CHAR(5),
   apartment TEXT,
   username VARCHAR(100),
   date_mod DATE,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username)
);
   
-- Student employment info
DROP TABLE IF EXISTS coop_employmentinformation;
CREATE TABLE coop_employmentinformation(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   current_job TEXT,
   wanted_job TEXT,
   start_date DATE,
   end_date DATE,
   rate_of_pay FLOAT,
   job_address TEXT,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username)
);

DROP TABLE IF EXISTS coop_students;
CREATE TABLE coop_students(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   grad_date DATE,
   majors_id INT,
   semester_in_major INT,
   agreedto_contract BOOLEAN DEFAULT 0,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username)
);

DROP TABLE IF EXISTS coop_users_semesters;
CREATE TABLE coop_users_semesters(
   id INT NOT NULL AUTO_INCREMENT,
   student VARCHAR(100),
   semesters_id INT,
   classes_id INT,
   coordinator VARCHAR(100),
   supervisor VARCHAR(100),
   student_coopagreement BOOLEAN,
   superv_coopagreement BOOLEAN,
   credits INT,
   PRIMARY KEY(id),
   FOREIGN KEY(student) REFERENCES coop_users(username) ON DELETE CASCADE,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id),
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id), 
   FOREIGN KEY(coordinator) REFERENCES coop_users(username),
   FOREIGN KEY(supervisor) REFERENCES coop_users(username) 
);

DROP TABLE IF EXISTS coop_coordinators;
CREATE TABLE coop_coordinators(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username)
);

DROP TABLE IF EXISTS coop_contracts;
CREATE TABLE coop_contracts(
   id INT NOT NULL AUTO_INCREMENT,
   semester VARCHAR(20) UNIQUE,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_users_contracts;
CREATE TABLE coop_users_contracts(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   contracts_id INT,
   date_submited DATETIME,
   date_mod DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE,
   FOREIGN KEY(contracts_id) REFERENCES coop_contracts(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS coop_classes;
CREATE TABLE coop_classes(
   id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(20) UNIQUE,
   syllabus TEXT,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_semesters;
CREATE TABLE coop_semesters(
   id INT NOT NULL AUTO_INCREMENT,
   semester VARCHAR(20) UNIQUE,
   current BOOLEAN DEFAULT 0,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_disclaimers;
CREATE TABLE coop_disclaimers(
   id INT NOT NULL AUTO_INCREMENT,
   semesters_id INT,
   username VARCHAR(100),
   date_agreed DATE,
   PRIMARY KEY(id),
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id) ON DELETE CASCADE,
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE
);

DROP TABLE IF EXISTS coop_supervisors;
CREATE TABLE coop_supervisors(
   id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(100),
   PRIMARY KEY(id),
   FOREIGN KEY(username) REFERENCES coop_users(username)
);

-- DROP VIEW IF EXISTS coop_users_semesters_view;
-- CREATE VIEW coop_users_semesters_view AS select u.*, us.semesters_id, us.classes_id, 
--    us.credits, us.coordinators_id, us.student_coopagreement, us.superv_coopagreement, 
--    s.semester, s.current, cl.name AS class, cl.syllabus, co.fname AS coord_fname, co.lname AS 
--    coord_lname, su.fname AS superv_fname, su.lname AS superv_lname from coop_users AS 
--    u join coop_users_semesters AS us ON u.id = us.users_id join coop_semesters AS s 
--    ON us.semesters_id = s.id join coop_classes AS cl ON us.classes_id = cl.id 
--    left join coop_coordinators AS co ON us.coordinators_id = co.id 
--    left join coop_supervisors AS su ON us.supervisors_id = su.id;
