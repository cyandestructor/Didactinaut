USE didactinaut_dev;

DELIMITER $$
DROP FUNCTION IF EXISTS get_course_score $$

CREATE FUNCTION get_course_score(_course_id INT)
       RETURNS INT
       DETERMINISTIC
       READS SQL DATA
  BEGIN
	DECLARE _score INT DEFAULT 0;
    
    SET _score = (
		SELECT
			AVG(R.review_score)
		FROM
			Reviews AS R
		WHERE
			R.course_id = _course_id
	);
                    
	RETURN _score;
  END
$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS get_course_total_students $$

CREATE FUNCTION get_course_total_students(_course_id INT)
       RETURNS INT
       DETERMINISTIC
       READS SQL DATA
  BEGIN
	DECLARE _total INT DEFAULT 0;
    
    SET _total = (
		SELECT
			COUNT(*)
		FROM
			Users_Courses AS UC
		WHERE
			UC.course_id = _course_id
	);
                    
	RETURN _total;
  END
$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS get_course_total_lessons $$

CREATE FUNCTION get_course_total_lessons(_course_id INT)
       RETURNS INT
       DETERMINISTIC
       READS SQL DATA
  BEGIN
	DECLARE _total INT DEFAULT 0;
    
    SET _total = (
		SELECT
			COUNT(*)
		FROM
			Lessons AS L
            INNER JOIN Sections AS S ON S.section_id = L.section_id
            INNER JOIN Courses AS C ON C.course_id = S.course_id
		WHERE
			C.course_id = _course_id
	);
                    
	RETURN _total;
  END
$$
DELIMITER ;