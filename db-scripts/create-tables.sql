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
   address TEXT,
   semesters_id INT,
   wanted_job TEXT,
   courses_id INT,
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
   PRIMARY KEY(id),
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id),
   FOREIGN KEY(courses_id) REFERENCES coop_courses(id),
   FOREIGN KEY(majors_id) REFERENCES coop_majors(id),
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

DROP TABLE IF EXISTS coop_courses;
CREATE TABLE coop_courses(
   id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(20) UNIQUE,
   syllabus TEXT,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_semesters;
CREATE TABLE coop_semesters(
   id INT NOT NULL AUTO_INCREMENT,
   semester VARCHAR(20) UNIQUE,
   PRIMARY KEY(id)
);
