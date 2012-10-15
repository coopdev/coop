-- View for a specific semester for a specific student
DROP VIEW IF EXISTS coop_users_semesters_view;
CREATE VIEW coop_users_semesters_view AS SELECT u.*, us.semesters_id, us.classes_id, 
   us.credits, us.student, s.semester, s.current, cl.name AS class, cl.coordinator,
   u2.fname AS coordfname, u2.lname AS coordlname
   FROM coop_users AS u JOIN coop_users_semesters AS us 
   ON u.username = us.student JOIN coop_semesters AS s
   ON us.semesters_id = s.id LEFT JOIN coop_classes AS cl ON us.classes_id = cl.id
   LEFT JOIN coop_users AS u2 ON cl.coordinator = u2.username; 
   -- LEFT JOIN coop_users AS u2 ON us.coordinator = u2.username; 
