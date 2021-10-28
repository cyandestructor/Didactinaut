USE didactinaut_dev;

DELIMITER $$
DROP FUNCTION IF EXISTS get_user_course_completed_lessons $$

CREATE FUNCTION get_user_course_completed_lessons(_user_id INT, _course_id INT)
       RETURNS INT
       DETERMINISTIC
       READS SQL DATA
  BEGIN
	DECLARE _total INT DEFAULT 0;
    
    SET _total = (
		SELECT
			COUNT(*)
		FROM
			User_Lessons AS UL
            INNER JOIN Lessons AS L ON L.lesson_id = UL.lesson_id
            INNER JOIN Sections AS S ON S.section_id = L.section_id
            INNER JOIN Courses AS C ON C.course_id = S.course_id
		WHERE
			UL.user_id = _user_id AND C.course_id = _course_id
	);
                    
	RETURN _total;
  END
$$
DELIMITER ;