USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS AddResource $$

CREATE PROCEDURE AddResource (
    IN _content LONGBLOB,
    IN _name VARCHAR(60),
    IN _content_type VARCHAR(255),
    IN _lesson_id INT
)
BEGIN
	INSERT INTO Resources (
		resource_content,
        resource_name,
        resource_content_type,
		lesson_id
    )
    VALUES (
		_content,
        _name,
        _content_type,
		_lesson_id
    );
    
    SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS DeleteResource $$

CREATE PROCEDURE DeleteResource (
	IN _id INT
)
BEGIN
	DELETE FROM Resources
    WHERE
		resource_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetLessonResources $$

CREATE PROCEDURE GetLessonResources (
	IN _lesson_id INT
)
BEGIN
	SELECT
		R.resource_id,
        R.resource_name,
        R.resource_content_type
	FROM
		Resources AS R
	WHERE
		R.lesson_id = id_lesson;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetResource $$

CREATE PROCEDURE GetResource (
	IN _id INT
)
BEGIN
	SELECT
		R.resource_id,
        R.resource_content,
        R.resource_name,
        R.resource_content_type,
        R.lesson_id
	FROM
		Resources AS R
	WHERE
		R.resource_id = _id;
END $$
DELIMITER ;