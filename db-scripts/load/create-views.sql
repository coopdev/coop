DROP VIEW IF EXISTS coop_survey_option_amount_view;
CREATE VIEW coop_survey_option_amount_view AS 
   SELECT a.option_amount AS global_amount, ss.option_amount AS specific_amount, 
   ss.use_global, ss.assignments_id, ss.classes_id
   FROM coop_assignments AS a
   LEFT JOIN coop_survey_specifics AS ss
     ON a.id = ss.assignments_id;

-- View for a specific semester for a specific student
DROP VIEW IF EXISTS coop_users_semesters_view;
CREATE VIEW coop_users_semesters_view AS SELECT u.*, us.semesters_id, us.classes_id, 
   us.credits, us.student, us.status AS sem_status, s.semester, s.current, 
   cl.name AS class, cl.coordinator, u2.fname AS coordfname, u2.lname AS coordlname
   FROM coop_users AS u JOIN coop_users_semesters AS us 
   ON u.username = us.student JOIN coop_semesters AS s
   ON us.semesters_id = s.id LEFT JOIN coop_classes AS cl ON us.classes_id = cl.id
   LEFT JOIN coop_users AS u2 ON cl.coordinator = u2.username; 
   -- LEFT JOIN coop_users AS u2 ON us.coordinator = u2.username; 

DROP VIEW IF EXISTS coop_survey_option_amount_view;
CREATE VIEW coop_survey_option_amount_view AS 
   SELECT a.option_amount AS global_amount, ss.option_amount AS specific_amount, 
   ss.use_global, ss.assignments_id, ss.classes_id
   FROM coop_assignments AS a
   LEFT JOIN coop_survey_specifics AS ss
     ON a.id = ss.assignments_id;



DROP VIEW IF EXISTS coop_classinfo_view;
CREATE VIEW coop_classinfo_view AS 
   SELECT c.*, u.fname, u.lname, u.email, u.home_phone 
   FROM coop_classes AS c 
   LEFT JOIN coop_users AS u
      ON c.coordinator = u.username;

DROP VIEW IF EXISTS coop_userrole_view;
CREATE VIEW coop_userrole_view AS
   SELECT u.*, r.role
   FROM coop_users AS u 
   LEFT JOIN coop_roles AS r
      ON u.roles_id = r.id;

DROP VIEW IF EXISTS coop_incompletes_view;
CREATE VIEW coop_incompletes_view AS
   SELECT u.username, u.fname, u.lname, us.classes_id FROM coop_users u
   JOIN coop_users_semesters us ON u.username = us.student
   WHERE us.status = 'Incomplete';

drop view if exists submittedassignment_answers_view;
create view submittedassignment_answers_view AS
   SELECT sa.id as submitted_id, sa.classes_id, sa.username, sa.assignments_id, 
      sa.semesters_id, sa.is_final, aa.answer_text, aa.static_question, aa.assignmentquestions_id 
      FROM coop_submittedassignments sa JOIN coop_assignmentanswers aa
      ON sa.id = aa.submittedassignments_id;

drop view if exists extended_duedates_view;
create view extended_duedates_view as 
   select u.fname, u.lname, u.username, ext.id, ext.due_date, a.assignment, 
          s.current AS cur_sem 
   from coop_extended_duedates ext 
   JOIN coop_users u ON ext.username = u.username 
   JOIN coop_assignments a ON ext.assignments_id = a.id 
   JOIN coop_semesters s ON ext.semesters_id = s.id;
