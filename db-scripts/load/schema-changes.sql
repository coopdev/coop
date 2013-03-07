-- alter table coop_assignments add column allowed_submit_count INT;
update coop_assignments set allowed_submit_count = 3 where id = 4;
