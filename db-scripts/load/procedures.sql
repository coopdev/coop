-- Create procedure to increment
DROP PROCEDURE IF EXISTS next_sem;

-- Change the delimiter (default is ';') so that semicolons can be used in the statements 
-- within the procedure.
DELIMITER // 
CREATE PROCEDURE next_sem()
BEGIN
   SELECT id into @sem_id FROM coop_semesters WHERE current = 1;
   UPDATE coop_semesters SET current = 0 WHERE id = @sem_id;
   UPDATE coop_semesters SET current = 1 WHERE id = @sem_id + 1;
   -- SELECT * FROM coop_semesters WHERE id = @sem_id + 1;
END//
DELIMITER ;


-- Create procedure to decrement the current semester
DROP PROCEDURE IF EXISTS prev_sem;

-- Change the delimiter (default is ';') so that semicolons can be used in the statements 
-- within the procedure.
DELIMITER // 
CREATE PROCEDURE prev_sem()
BEGIN
   SELECT id into @sem_id FROM coop_semesters WHERE current = 1;
   UPDATE coop_semesters SET current = 0 WHERE id = @sem_id;
   UPDATE coop_semesters SET current = 1 WHERE id = @sem_id - 1;
   -- SELECT * FROM coop_semesters WHERE id = @sem_id - 1;
END//
DELIMITER ;


DROP PROCEDURE IF EXISTS get_majors;
DELIMITER // 
CREATE PROCEDURE get_majors()
BEGIN
   select distinct substring_index(name, ' ', 1) AS major from coop_classes order by name;
END//
DELIMITER ;
