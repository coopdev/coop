DROP TABLE IF EXISTS coop_survey_globaloptions;
CREATE TABLE coop_survey_globaloptions(
    id INT NOT NULL AUTO_INCREMENT,
    option_text TEXT,
    assignments_id INT,
    PRIMARY KEY(id),
    FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id)
) ENGINE InnoDB;
