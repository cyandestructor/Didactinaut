USE didactinaut_dev;

DROP VIEW IF EXISTS Sections_Info;
CREATE VIEW Sections_Info
AS
	SELECT
		S.section_id,
		S.section_title,
        S.course_id,
        S.product_id,
		P.product_price AS section_price
	FROM
		Sections AS S
        LEFT JOIN Products AS P ON P.product_id = S.product_id;
