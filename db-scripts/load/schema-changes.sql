drop table if exists coop_comments;
create table coop_comments(
   id INT NOT NULL AUTO_INCREMENT,
   coordinator VARCHAR(100),
   student VARCHAR(100),
   semesters_id INT,
   comment TEXT,
   date    DATETIME,
   PRIMARY KEY(id),
   FOREIGN KEY(coordinator) REFERENCES coop_users(username),
   FOREIGN KEY(student) REFERENCES coop_users(username),
   FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id)
) Engine InnoDB;
