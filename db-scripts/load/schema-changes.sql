-- MAKE SURE TO EMPTY ASSIGNMENTANSWERS TABLE AFTER DOING THIS BECAUSE THE RECORDS ALREADY IN THERE ARE NOT USING submittedassignments_id YET
alter table coop_assignmentanswers add column submittedassignments_id INT after assignments_id;
alter table coop_assignmentanswers add foreign key (submittedassignments_id) references coop_submittedassignments(id) ON DELETE CASCADE;



drop view if exists submittedassignment_answers_view;
create view submittedassignment_answers_view AS
   SELECT sa.id as submitted_id, sa.classes_id, sa.username, sa.assignments_id, 
      sa.semesters_id, sa.is_final, aa.answer_text, aa.static_question, aa.assignmentquestions_id 
      FROM coop_submittedassignments sa JOIN coop_assignmentanswers aa
      ON sa.id = aa.submittedassignments_id;
