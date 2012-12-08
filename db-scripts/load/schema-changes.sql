alter table coop_students add foreign key(grad_date) references coop_semesters(id);
alter table coop_students add column coop_credits int;
alter table coop_students add column total_credits int;
alter table coop_students add column other_courses text;
alter table coop_students add foreign key(semesters_id) references coop_semesters(id);
alter table coop_students add column coop_sem_yr text after coop_credits;
alter table coop_students drop column coord_phone;
alter table coop_students drop column coord_name;


alter table coop_employmentinfo change current_job job_title text
alter table coop_employmentinfo add column coop_jobtitle text after job_title;
alter table coop_employmentinfo add column fax text
alter table coop_employmentinfo change job_address street_address text;
alter table coop_employmentinfo add column city_state_zip text after street_address;
alter table coop_employmentinfo add column fax text;
alter table coop_employmentinfo drop column wanted_class;
alter table coop_employmentinfo drop column wanted_job;
alter table coop_employmentinfo add column is_final boolean;


drop table coop_phonenumbers;
drop table coop_phonetypes;

alter table coop_users add column home_phone text after email;
alter table coop_users add column mobile_phone text after email;

