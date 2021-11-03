USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS RegisterCertificate $$

CREATE PROCEDURE RegisterCertificate (
	IN _user_id INT,
    IN _course_id INT
)
BEGIN
	DECLARE _course_instructor_id INT;
	DECLARE _certificate_id BINARY(16);

	IF EXISTS ( SELECT user_course_id FROM Users_Courses WHERE user_id = _user_id AND course_id = _course_id) THEN
		SET _course_instructor_id = (SELECT course_instructor FROM Courses WHERE course_id = _course_id);
		SET _certificate_id = UUID_TO_BIN(UUID());
        
        INSERT INTO Certificates (
			certificate_id,
			instructor_id
		) VALUES (
			_certificate_id,
			_course_instructor_id
		);
        
        UPDATE Users_Courses
		SET
			certificate_id = _certificate_id
		WHERE
			user_id = _user_id AND course_id = _course_id;
    END IF;
    
    SELECT BIN_TO_UUID(_certificate_id);
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCertificate $$

CREATE PROCEDURE GetCertificate (
	IN _certificate_id VARCHAR(36)
)
BEGIN
	DECLARE _instructor_name VARCHAR(255);
    
    SET _instructor_name = (
		SELECT
			CONCAT(U.user_name, ' ', U.user_lastname)
		FROM
			Certificates AS CF
            INNER JOIN Users AS U ON U.user_id = CF.instructor_id
		WHERE
			CF.certificate_id = UUID_TO_BIN(_certificate_id)
	);

	SELECT
		BIN_TO_UUID(CF.certificate_id) AS certificate_id,
		CONCAT(U.user_name, ' ', U.user_lastname) AS user_fullname,
		_instructor_name,
		C.course_title,
		CF.expedition_date
	FROM
		Certificates AS CF
        INNER JOIN Users_Courses AS UC ON UC.certificate_id = CF.certificate_id
        INNER JOIN Courses AS C ON C.course_id = UC.course_id
	WHERE
		C.certificate_id = UUID_TO_BIN(_certificate_id);
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetUserCertificates $$

CREATE PROCEDURE GetUserCertificates (
	IN _user_id INT
)
BEGIN
	SELECT
		BIN_TO_UUID(UC.certificate_id) AS certificate_id,
        C.course_title
	FROM
		Users_Certificates AS UC
        INNER JOIN Courses AS C ON C.course_id = UC.course_id
	WHERE
		UC.user_id = _user_id;
END $$
DELIMITER ;
