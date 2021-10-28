USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS CreateCategory $$

CREATE PROCEDURE CreateCategory (
	IN _name VARCHAR(50),
    IN _description TEXT
)
BEGIN
	INSERT INTO Categories (
		category_name,
        category_description
    )
    VALUES (
		_name,
        _description
    );
    
	SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCategory $$

CREATE PROCEDURE GetCategory(
	IN _id INT
)
BEGIN
	SELECT
		C.category_id,
		C.category_name,
		C.category_description
	FROM
		Categories AS C
	WHERE
		C.category_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCategories $$

CREATE PROCEDURE GetCategories()
BEGIN
	SELECT
		C.category_id,
		C.category_name,
		C.category_description
	FROM
		Categories AS C;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCourseCategories $$

CREATE PROCEDURE GetCourseCategories (
	IN _course_id INT
)
BEGIN
    SELECT
		C.category_id,
        C.category_name,
        C.category_description
	FROM
		Categories AS C
        INNER JOIN Courses_Categories AS CC ON CC.category_id = C.category_id
	WHERE
		CC.course_id = _course_id;
END $$
DELIMITER ;