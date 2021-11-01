USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS AddLesson $$

CREATE PROCEDURE AddLesson (
	IN _title VARCHAR(50),
    IN _text MEDIUMTEXT,
    IN _section_id INT
)
BEGIN
	INSERT INTO Lessons (
		lesson_title,
		lesson_text,
		section_id
    )
    VALUES (
		_title,
		_text,
		_section_id
    );
        
	SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS EditLesson $$

CREATE PROCEDURE EditLesson (
	IN _id INT,
	IN _title VARCHAR(50),
    IN _text MEDIUMTEXT
)
BEGIN
	UPDATE Lessons AS L
    SET
		L.lesson_title = _title,
		L.lesson_text = _text
	WHERE
		L.lesson_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetSectionLessons $$

CREATE PROCEDURE GetSectionLessons (
	IN _section_id INT
)
BEGIN
	SELECT
		LI.lesson_id,
		LI.lesson_title,
        LI.lesson_duration,
        LI.section_id,
        LI.course_id
	FROM
		Lessons_Info AS LI
	WHERE
		LI.section_id = _section_id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetLessonInfo $$

CREATE PROCEDURE GetLessonInfo (
	IN _id INT
)
BEGIN
	SELECT
		LI.lesson_id,
		LI.lesson_title,
		LI.lesson_text,
        LI.video_address,
		LI.lesson_duration,
        LI.section_id,
        LI.course_id
	FROM
		Lessons_Info AS LI
	WHERE
		LI.lesson_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS SetLessonCompleted $$

CREATE PROCEDURE SetLessonCompleted (
	IN _user_id INT,
    IN _lesson_id INT,
    IN _completed BIT
)
BEGIN
	DELETE FROM Users_Lessons
    WHERE user_id = _user_id AND lesson_id = _lesson_id;
    
    IF _completed = 1 THEN
		INSERT INTO Users_Lessons (
			user_id,
            lesson_id
        ) VALUES (
			_user_id,
            _lesson_id
        );
    END IF;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetSectionUserLessons $$

CREATE PROCEDURE GetSectionUserLessons (
	IN _user_id INT,
    IN _section_id INT
)
BEGIN
	SELECT
		LI.lesson_id,
		LI.lesson_title,
        LI.lesson_duration,
        LI.section_id,
        LI.course_id,
        (SELECT
			COUNT(*)
		FROM
			Users_Lessons AS UL
		WHERE
			UL.user_id = _user_id AND UL.lesson_id = LI.lesson_id) AS lesson_completed
	FROM
		Lessons_Info AS LI
	WHERE
		LI.section_id = _section_id;
END $$
DELIMITER ;