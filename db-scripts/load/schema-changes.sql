 DROP VIEW IF EXISTS coop_userrole_view;
 CREATE VIEW coop_userrole_view AS
     SELECT u.*, r.role
     FROM coop_users AS u
     LEFT JOIN coop_roles AS r
        ON u.roles_id = r.id;
