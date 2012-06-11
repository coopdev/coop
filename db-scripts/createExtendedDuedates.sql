create table coop_extended_duedates(
   id INT NOT NULL AUTO_INCREMENT,
   assignments_id INT,
   classes_id INT,
   username VARCHAR(100),
   due_date DATE,
   PRIMARY KEY(id),
   FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id) ON DELETE CASCADE,
   FOREIGN KEY(classes_id) REFERENCES coop_classes(id) ON DELETE SET NULL,
   FOREIGN KEY(username) REFERENCES coop_users(username) ON DELETE CASCADE
);
