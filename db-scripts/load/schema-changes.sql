drop view if exists extended_duedates_view;
create view extended_duedates_view as 
   select ext.id, u.fname, u.lname, u.username, ext.due_date, a.assignment, 
          s.current AS cur_sem, c.name AS class
   from coop_extended_duedates ext 
   JOIN coop_users       u ON ext.username = u.username 
   JOIN coop_assignments a ON ext.assignments_id = a.id 
   JOIN coop_semesters   s ON ext.semesters_id = s.id
   JOIN coop_classes     c ON ext.classes_id = c.id;



DROP PROCEDURE IF EXISTS get_majors;
DELIMITER // 
CREATE PROCEDURE get_majors()
BEGIN
   select distinct substring_index(name, ' ', 1) AS major from coop_classes order by name;
END//
DELIMITER ;
