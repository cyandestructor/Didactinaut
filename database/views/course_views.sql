USE didactinaut_dev;

DROP VIEW IF EXISTS Courses_Info;
CREATE VIEW Courses_Info
AS
	SELECT
		C.course_id,
        C.course_title,
		C.course_description,
		C.publication_date,
		C.last_update,
		C.course_published,
		C.course_image,
        C.product_id,
        P.product_price AS course_price,
        U.user_id AS instructor_id,
        U.user_username AS instructor_username,
        U.user_name AS instructor_name,
        U.user_lastname AS instructor_lastname,
        get_course_score(C.course_id) AS course_score,
        get_course_total_students(C.course_id) AS total_students,
        get_course_total_lessons(C.course_id) AS total_lessons
	FROM
		Courses AS C
        INNER JOIN Products AS P ON P.product_id = C.product_id
        INNER JOIN Users AS U ON U.user_id = C.course_instructor;