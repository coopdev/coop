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
   address TEXT,
   wanted_job TEXT,
   grad_date DATE,
   majors_id INT,
   semester_in_major INT,
   phone TEXT,
   mobile TEXT,
   email TEXT,
   cur_job TEXT,
   start_date DATE,
   end_date DATE,
   rate_of_pay FLOAT,
   employer TEXT,
   department TEXT,
   job_address TEXT,
   superv_name TEXT,
   superv_title TEXT,
   superv_phone TEXT,
   superv_email TEXT,
   coord_name TEXT,
   coord_phone TEXT,
   roles_id INT,
   agreedto_contract BOOLEAN DEFAULT 0,
   active BOOLEAN DEFAULT 0,
   PRIMARY KEY(id),
   FOREIGN KEY(majors_id) REFERENCES coop_majors(id),
   FOREIGN KEY(roles_id) REFERENCES coop_roles(id)
);

DROP TABLE IF EXISTS coop_users_semesters;
CREATE TABLE coop_users_semesters(
   id INT NOT NULL AUTO_INCREMENT,
   users_id INT,
   semesters_id INT,
   classes_id INT,
   coordinators_id INT,
   supervisors_id INT,
   student_coopagreement BOOLEAN,
   superv_coopagreement BOOLEAN,
   credits INT,
   PRIMARY KEY(id),
   FOREIGN KEY(users_id) REFERENCES coop_users(id) ON DELETE CASCADE,
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id),
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id), 
   FOREIGN KEY(coordinators_id) REFERENCES coop_coordinators(id),
   FOREIGN KEY(supervisors_id) REFERENCES coop_supervisors(id) 
);

DROP TABLE IF EXISTS coop_coordinators;
CREATE TABLE coop_coordinators(
   id INT NOT NULL AUTO_INCREMENT,
   fname TEXT,
   lname TEXT,
   uuid CHAR(8) UNIQUE,
   username TEXT,
   password VARCHAR(20),
   roles_id INT,
   PRIMARY KEY(id),
   FOREIGN KEY(roles_id) REFERENCES coop_roles(id)
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
   users_id INT,
   contracts_id INT,
   date_submited DATETIME,
   date_mod DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(users_id) REFERENCES coop_users(id) ON DELETE CASCADE,
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
   users_id INT,
   date_agreed DATE,
   PRIMARY KEY(id),
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id) ON DELETE CASCADE,
   FOREIGN KEY(users_id) REFERENCES coop_users(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS coop_supervisors;
CREATE TABLE coop_supervisors(
   id INT NOT NULL AUTO_INCREMENT,
   fname TEXT,
   lname TEXT,
   username VARCHAR(20) UNIQUE,
   password VARCHAR(20),
   PRIMARY KEY(id)
);

DROP VIEW IF EXISTS coop_users_semesters_view;
CREATE VIEW coop_users_semesters_view AS select u.*, us.semesters_id, us.classes_id, 
   us.credits, us.coordinators_id, us.student_coopagreement, us.superv_coopagreement, 
   s.semester, s.current, cl.name AS class, cl.syllabus, co.fname AS coord_fname, co.lname AS 
   coord_lname, su.fname AS superv_fname, su.lname AS superv_lname from coop_users AS 
   u join coop_users_semesters AS us ON u.id = us.users_id join coop_semesters AS s 
   ON us.semesters_id = s.id join coop_classes AS cl ON us.classes_id = cl.id 
   left join coop_coordinators AS co ON us.coordinators_id = co.id 
   left join coop_supervisors AS su ON us.supervisors_id = su.id;
