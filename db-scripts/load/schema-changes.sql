drop view if exists extended_duedates_view;
create view extended_duedates_view as 
   select u.fname, u.lname, u.username, ext.id, ext.due_date, a.assignment, 
          s.current AS cur_sem 
   from coop_extended_duedates ext 
   JOIN coop_users u ON ext.username = u.username 
   JOIN coop_assignments a ON ext.assignments_id = a.id 
   JOIN coop_semesters s ON ext.semesters_id = s.id;
