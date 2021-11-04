USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS RegisterUser $$

CREATE PROCEDURE RegisterUser (
    IN _username VARCHAR(50),
    IN _name VARCHAR(50),
    IN _lastname VARCHAR(50),
    IN _email VARCHAR(60),
    IN _password VARCHAR(255),
    IN _gender VARCHAR(20),
    IN _birthdate DATE
)
BEGIN
	INSERT INTO Users (
		user_username,
		user_name,
		user_lastname,
		user_email,
		user_password,
        user_gender,
        user_birthdate
	)
	VALUES (
		_username,
		_name,
		_lastname,
		_email,
		_password,
        _gender,
        _birthdate
	);
    
    SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS EditUser $$

CREATE PROCEDURE EditUser (
	IN _id INT,
	IN _username VARCHAR(50),
	IN _name VARCHAR(50),
    IN _lastname VARCHAR(50),
    IN _email VARCHAR(60),
    IN _description TEXT,
    IN _role ENUM('Instructor', 'User'),
    IN _gender VARCHAR(20),
    IN _birthdate DATE,
    IN _password VARCHAR(255)
)
BEGIN
	UPDATE Users AS U
    SET
        U.user_username = _username,
        U.user_name = _name,
        U.user_lastname = _lastname,
        U.user_email = _email,
        U.user_description = _description,
        U.user_role = _role,
        U.user_gender = _gender,
        U.user_birthdate = _birthdate,
        U.user_password = COALESCE(_password, U.user_password)
	WHERE
		U.user_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS SetUserImage $$

CREATE PROCEDURE SetUserImage (
	IN _id INT,
    IN _image MEDIUMBLOB,
    IN _content_type VARCHAR(50)
)
BEGIN
	DECLARE _image_id INT;

	IF NOT EXISTS(
		SELECT I.image_id
        FROM Images AS I
		INNER JOIN Users AS U ON U.user_image = I.image_id
        WHERE U.user_id = _id AND I.image_id = U.user_image
    ) THEN
		INSERT INTO Images (
			image_content,
            image_content_type
        ) VALUES (
			_image,
            _content_type
        );
        
        SET _image_id = LAST_INSERT_ID();
        
        UPDATE Users AS U
        SET
			U.user_image = _image_id
		WHERE
			U.user_id = _id;
    ELSE
		UPDATE Users AS U, Images AS I
		SET
			I.image_content = _image,
			I.image_content_type = _content_type
		WHERE
			U.user_image = I.image_id AND U.user_id = _id;
	END IF;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS UserExists $$

CREATE PROCEDURE UserExists (
	IN _username VARCHAR(50),
    IN _email VARCHAR(60)
)
BEGIN
	SELECT
		COUNT(*)
	FROM
		Users AS U
	WHERE
		U.user_username = _username OR U.user_email = _email;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetUserInfo $$

CREATE PROCEDURE GetUserInfo (
	IN _id INT
)
BEGIN
	SELECT
		U.user_id,
		U.user_username,
		U.user_name,
		U.user_lastname,
        U.user_email,
        U.user_image,
		U.user_description,
		U.user_role,
        U.user_gender,
        U.user_birthdate,
		U.account_creation,
		U.account_last_change,
        U.user_is_public
    FROM
		Users AS U
    WHERE
		U.user_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS UserLogin $$

CREATE PROCEDURE UserLogin (
	IN _input VARCHAR(60)
)
BEGIN
	SELECT
		U.user_id,
		U.user_username,
        U.user_name,
        U.user_lastname,
        U.user_image,
		U.user_role,
        U.user_password
    FROM
		Users AS U
    WHERE
		_input IN (U.user_username, U.user_email);
END $$
DELIMITER ;
