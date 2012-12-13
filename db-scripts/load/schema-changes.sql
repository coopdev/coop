DROP VIEW IF EXISTS coop_classinfo_view;
CREATE VIEW coop_classinfo_view AS 
   SELECT c.*, u.fname, u.lname, u.email, u.home_phone 
   FROM coop_classes AS c 
   LEFT JOIN coop_users AS u
      ON c.coordinator = u.username;
