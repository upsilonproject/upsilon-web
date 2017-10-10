INSERT INTO settings (`key`, `value`) VALUES ('maintenanceMode', 0);
INSERT INTO dashboard (`id`, `title`) VALUES (1, 'First Dashboard');
INSERT INTO widgets (`id`, `class`) VALUES (1, 'BestPractices');
INSERT INTO widget_instances (`dashboard`, `widget`) VALUES (1, 1);

INSERT INTO widgets (`class`) VALUES ('ListMetrics');
INSERT INTO widgets (`class`) VALUES ('GraphMetrics');
INSERT INTO widgets (`class`) VALUES ('ProblemServices');
INSERT INTO widgets (`class`) VALUES ('Nodes');
INSERT INTO widgets (`class`) VALUES ('Tasks');
INSERT INTO widgets (`class`) VALUES ('Events');
INSERT INTO widgets (`class`) VALUES ('ServicesFromGroup');
INSERT INTO widgets (`class`) VALUES ('Clock');
INSERT INTO classes (`title`, `l`, `r`) VALUES ('Root class', 0, 1);
