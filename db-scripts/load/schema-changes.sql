alter table coop_majors modify column major VARCHAR(20) UNIQUE;

alter table coop_classes add column major VARCHAR(20);

alter table coop_classes add FOREIGN KEY(major) REFERENCES coop_majors(major) ON UPDATE CASCADE;
