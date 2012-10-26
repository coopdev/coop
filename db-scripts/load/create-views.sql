DROP VIEW IF EXISTS coop_survey_option_amount_view;
CREATE VIEW coop_survey_option_amount_view AS 
   SELECT a.option_amount AS global_amount, ss.option_amount AS specific_amount, 
   ss.use_global, ss.assignments_id, ss.classes_id
   FROM coop_assignments AS a
   LEFT JOIN coop_survey_specifics AS ss
     ON a.id = ss.assignments_id;
