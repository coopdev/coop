DROP TABLE IF EXISTS coop_roles;
CREATE TABLE coop_roles(
   id INT NOT NULL AUTO_INCREMENT,
   role VARCHAR(20) UNIQUE,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_persons;
CREATE TABLE coop_persons(
   id INT NOT NULL AUTO_INCREMENT, 
   fname TEXT,
   lname TEXT,
   roles_id INT,
   uuid CHAR(8) UNIQUE,
   PRIMARY KEY(id),
   FOREIGN KEY(roles_id) REFERENCES roles(id)
);

DROP TABLE IF EXISTS coop_contracts;
CREATE TABLE coop_contracts(
   id INT NOT NULL AUTO_INCREMENT,
   semester VARCHAR(20) UNIQUE,
   PRIMARY KEY(id)
);

DROP TABLE IF EXISTS coop_persons_contracts;
CREATE TABLE coop_persons_contracts(
   id INT NOT NULL AUTO_INCREMENT,
   persons_id INT,
   contracts_id INT,
   date_submited DATETIME,
   date_mod DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(persons_id) REFERENCES coop_persons(id) ON DELETE CASCADE,
   FOREIGN KEY(contracts_id) REFERENCES coop_contracts(id) ON DELETE CASCADE
);
