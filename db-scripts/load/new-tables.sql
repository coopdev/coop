drop table if exists coop_supervisoreval_formdata;
create table coop_supervisoreval_formdata(
    id INT NOT NULL AUTO_INCREMENT,
    position TEXT,
    company TEXT,
    hrs_per_week TEXT,
    semester_dates TEXT,
    supervisor TEXT,
    telephone TEXT,
    username VARCHAR(100),
    classes_id INT,
    semesters_id INT,
    PRIMARY KEY(id),
    FOREIGN KEY(username) REFERENCES coop_users(username),
    FOREIGN KEY(classes_id) REFERENCES coop_classes(id),
    FOREIGN KEY(semesters_id) REFERENCES coop_semesters(id)
) ENGINE InnoDB;

