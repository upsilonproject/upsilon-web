INSERT INTO settings (`key`, `value`) VALUES ('maintenanceMode', 0);
INSERT INTO settings (`key`, `value`) VALUES ('warnNotUsingHttps', 0);
INSERT INTO rooms (title, filename) VALUES ('sample', 'sample.svg') 
INSERT INTO widgets (`class`) VALUES ('ListMetrics');
INSERT INTO widgets (`class`) VALUES ('GraphMetrics');
INSERT INTO widgets (`class`) VALUES ('ProblemServices');
INSERT INTO widgets (`class`) VALUES ('Nodes');
INSERT INTO widgets (`class`) VALUES ('Tasks');
INSERT INTO widgets (`class`) VALUES ('Events');
INSERT INTO widgets (`class`) VALUES ('ServicesFromGroup');
INSERT INTO widgets (`class`) VALUES ('Clock');
INSERT INTO classes (`title`, `l`, `r`) VALUES ('Root class', 0, 1);
INSERT INTO dashboard (`title`) VALUES ('First Dashboard');
INSERT INTO groups (`title`, `description`) VALUES ('Staff', 'The default user group');
