alter table coop_assignments change column due_date fall_due_date DATE;
alter table coop_assignments add column spring_due_date DATE after fall_due_date;
alter table coop_assignments add column summer_due_date DATE after spring_due_date;
