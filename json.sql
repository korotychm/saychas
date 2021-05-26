CREATE TABLE jemp (
	c JSON,
	g INT GENERATED ALWAYS AS (C->"$.ID"),
	INDEX i(g)
);

INSERT INTO jemp (c) VALUES
('{"id": "1", "name": "Fred"}'),('{"id": "2", "name": "Wilma"}'),
('{"id": "3", "name": "Barney"}'),('{"id": "4", "name": "Betty"}');
