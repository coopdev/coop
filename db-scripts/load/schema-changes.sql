alter table coop_students add foreign key(grad_date) references coop_semesters(id);

alter table coop_students add column coop_credits int;

alter table coop_students add column total_credits int;

alter table coop_students add column coop_jobtitle text;

alter table coop_students add column other_courses text;
