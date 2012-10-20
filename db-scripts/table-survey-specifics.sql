DROP TABLE IF EXISTS coop_survey_specifics;

-- Stuff related specifically to the survey type assignments, like option amount.
CREATE TABLE coop_survey_specifics(
    id INT NOT NULL AUTO_INCREMENT,
    option_amount INT,
    assignments_id INT,
    classes_id INT,
    PRIMARY KEY(id),
    FOREIGN KEY(assignments_id) REFERENCES coop_assignments(id),
    FOREIGN KEY(classes_id) REFERENCES coop_classes(id)
) ENGINE InnoDB;
