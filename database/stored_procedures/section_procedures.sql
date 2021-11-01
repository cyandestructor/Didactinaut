USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS AddSection $$

CREATE PROCEDURE AddSection (
	IN _title VARCHAR(50),
    IN _course_id INT,
    IN _price DECIMAL(15, 2)
)
BEGIN
	DECLARE _product_id INT DEFAULT NULL;
    
    IF _price IS NOT NULL AND _price > 0 THEN
		INSERT INTO Products (product_price) VALUES (_price);
        SET _product_id = LAST_INSERT_ID();
	END IF;
    
    INSERT INTO Sections (
		section_title,
		course_id,
		product_id
    )
    VALUES (
		_title,
		_course_id,
		_product_id
    );
        
	SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS EditSection $$

CREATE PROCEDURE EditSection (
	IN _id INT,
	IN _title VARCHAR(50),
    IN _price DECIMAL(15, 2)
)
BEGIN
	DECLARE _product_id INT;

	SET _product_id = (SELECT product_id FROM Sections WHERE section_id = _id);
    
    IF _price IS NOT NULL THEN
		IF _product_id IS NOT NULL THEN
			UPDATE Products AS P
			SET
				P.product_price = _price
			WHERE
				P.product_id = _product_id;
		ELSE
			INSERT INTO Products (product_price) VALUES (_price);
            SET _product_id = LAST_INSERT_ID();
		END IF;
	END IF;

	UPDATE Sections AS S
    SET
		S.section_title = _title,
        S.product_id = _product_id
	WHERE
		S.section_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetSection $$

CREATE PROCEDURE GetSection (
	IN _id INT
)
BEGIN
	SELECT
		SI.section_id,
		SI.section_title,
        SI.product_id,
		SI.section_price,
        SI.course_id
	FROM
		Sections_Info AS SI
	WHERE
		SI.section_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCourseSections $$

CREATE PROCEDURE GetCourseSections (
	IN _course_id INT
)
BEGIN
	SELECT
		SI.section_id,
		SI.section_title,
        SI.product_id,
		SI.section_price,
        SI.course_id
	FROM
		Sections_Info AS SI
	WHERE
		SI.course_id = _course_id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetUserCourseSections $$

CREATE PROCEDURE GetUserCourseSections (
	IN _course_id INT,
    IN _user_id INT
)
BEGIN
	SELECT
		SI.section_id,
		SI.section_title,
        SI.product_id,
		SI.section_price,
        SI.course_id,
        EXISTS(
			SELECT
				S.product_id
			FROM
				Sales AS S
			WHERE
				S.orderer_id = _user_id AND
                S.product_id IN (SI.product_id, C.product_id)
        ) AS user_access
	FROM
		SectionsInfo AS SI
        INNER JOIN Courses AS C ON C.course_id = SI.course_id
	WHERE
		SI.course_id = _course_id;
END $$
DELIMITER ;
