USE didactinaut_dev;

DROP VIEW IF EXISTS Lessons_Info;
CREATE VIEW Lessons_Info
AS
	SELECT
		L.lesson_id,
		L.lesson_title,
		L.lesson_text,
        L.section_id,
        C.course_id,
		V.video_address,
		COALESCE(V.video_duration, text_duration(L.lesson_text)) AS lesson_duration
	FROM
		Lessons AS L
        INNER JOIN Sections AS S ON S.section_id = L.section_id
        INNER JOIN Courses AS C ON C.course_id = S.course_id
		LEFT JOIN Videos AS V ON V.lesson_id = L.lesson_id;