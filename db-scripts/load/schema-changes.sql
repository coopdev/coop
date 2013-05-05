DROP VIEW IF EXISTS submitted_assignments_view;
CREATE VIEW submitted_assignments_view AS
   SELECT u.fname AS student_fname, u.lname AS student_lname, u.username, 
      sa.classes_id, sa.semesters_id, a.assignment
   FROM coop_users AS u
   INNER JOIN coop_submittedassignments AS sa ON u.username = sa.username 
      AND sa.is_final = 1
   INNER JOIN coop_assignments AS a ON sa.assignments_id = a.id;
