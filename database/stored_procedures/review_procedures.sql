USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS CreateReview $$

CREATE PROCEDURE CreateReview (
	IN _body TEXT,
    IN _course_id INT,
    IN _user_id INT,
    IN _score TINYINT
)
BEGIN
	INSERT INTO Reviews (
		review_body,
        review_score,
        user_id,
        course_id
    )
    VALUES (
		_body,
        _score,
        _user_id,
        _course_id
    );
    
    SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS EditReview $$

CREATE PROCEDURE EditReview (
	IN _id INT,
	IN _body TEXT,
    IN _score TINYINT,
    IN _published BIT
)
BEGIN
	UPDATE Reviews AS R
    SET
		R.review_body = _body,
        R.review_score = _score,
        R.review_published = _published
	WHERE
		R.review_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCourseReviews $$

CREATE PROCEDURE GetCourseReviews (
	IN _course_id INT,
    IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	SELECT
		R.review_id,
		R.review_body,
		R.review_date,
        R.review_score,
        R.review_published,
        R.user_id AS reviewer_id,
        COALESCE(CONCAT(U.user_name, ' ', U.user_lastname), 'User') AS reviewer_name,
        R.course_id
	FROM
		Reviews AS R
        LEFT JOIN Users AS U ON U.user_id = R.user_id
	WHERE
		R.review_published = 1 AND R.course_id = _course_id
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetReview $$

CREATE PROCEDURE GetReview (
	IN _id INT
)
BEGIN
	SELECT
		R.review_id,
		R.review_body,
		R.review_date,
        R.review_score,
        R.review_published,
        R.user_id AS reviewer_id,
        COALESCE(CONCAT(U.user_name, ' ', U.user_lastname), 'User') AS reviewer_name,
        R.course_id
	FROM
		Reviews AS R
        LEFT JOIN Users AS U ON U.user_id = R.user_id
	WHERE
		R.review_id = _id
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;
