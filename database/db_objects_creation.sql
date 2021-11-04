USE didactinaut_dev;

-- utility_functions.sql

DELIMITER $$
DROP FUNCTION IF EXISTS datetime_min_value $$

CREATE FUNCTION datetime_min_value()
   RETURNS DATETIME
   DETERMINISTIC
BEGIN
	RETURN (SELECT CAST('17530101' AS DATETIME));
END
$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS datetime_max_value $$

CREATE FUNCTION datetime_max_value()
   RETURNS DATETIME
   DETERMINISTIC
BEGIN
	RETURN (SELECT CAST('99991231' AS DATETIME));
END
$$
DELIMITER ;

-- From: https://stackoverflow.com/a/754159
DELIMITER $$
DROP FUNCTION IF EXISTS word_count $$

CREATE FUNCTION word_count(str LONGTEXT)
       RETURNS INT
       DETERMINISTIC
       SQL SECURITY INVOKER
       NO SQL
  BEGIN
    DECLARE wordCnt, idx, maxIdx INT DEFAULT 0;
    DECLARE currChar, prevChar BOOL DEFAULT 0;
    SET maxIdx=char_length(str);
    SET idx = 1;
    WHILE idx <= maxIdx DO
        SET currChar=SUBSTRING(str, idx, 1) RLIKE '[[:alnum:]]';
        IF NOT prevChar AND currChar THEN
            SET wordCnt=wordCnt+1;
        END IF;
        SET prevChar=currChar;
        SET idx=idx+1;
    END WHILE;
    RETURN wordCnt;
  END
$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS text_duration $$

CREATE FUNCTION text_duration(str LONGTEXT)
       RETURNS INT
       DETERMINISTIC
  BEGIN
	-- Avg words per page = 800
    -- Avg time to read one page = 3.2 min = 192 s
    -- seconds per word = 192s / 800 words = 0.24
    DECLARE secondsPerWord DECIMAL(5,2) DEFAULT 0.24;
    DECLARE wordCnt DECIMAL(5,2);
    
    SET wordCnt = word_count(str);
    
    RETURN FLOOR(wordCnt * secondsPerWord);
  END
$$
DELIMITER ;

-- course_functions.sql

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

-- user_functions.sql

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
			Users_Lessons AS UL
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

-- course_views.sql

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
        
-- lesson_views.sql

DROP VIEW IF EXISTS Lessons_Info;
CREATE VIEW Lessons_Info
AS
	SELECT
		L.lesson_id,
		L.lesson_title,
		L.lesson_text,
        L.section_id,
        C.course_id,
		V.video_address,
		COALESCE(V.video_duration, text_duration(L.lesson_text)) AS lesson_duration
	FROM
		Lessons AS L
        INNER JOIN Sections AS S ON S.section_id = L.section_id
        INNER JOIN Courses AS C ON C.course_id = S.course_id
		LEFT JOIN Videos AS V ON V.lesson_id = L.lesson_id;
        
-- product_views.sql

DROP VIEW IF EXISTS Products_Info;
CREATE VIEW Products_Info
AS
	SELECT
		P.product_id,
		P.product_price,
		C.course_title AS product_name,
        C.course_image AS product_image
	FROM
		Products AS P
		INNER JOIN Courses AS C ON C.product_id = P.product_id
	UNION
	SELECT
		P.product_id,
		P.product_price,
		S.section_title AS product_name,
        NULL AS product_image
	FROM
		Products AS P
		INNER JOIN Sections AS S ON S.product_id = P.product_id;
        
DROP VIEW IF EXISTS Sales;
CREATE VIEW Sales
AS
	SELECT
		PI.product_id,
        PI.product_name,
        OP.final_product_price,
        O.order_id,
        O.order_date,
        O.orderer_user_id AS orderer_id,
        O.payment_method
	FROM
		Products_Info AS PI
        INNER JOIN Orders_Products AS OP ON OP.product_id = PI.product_id
        INNER JOIN Orders AS O ON O.order_id = OP.order_id;
        
DROP VIEW IF EXISTS Courses_Sales;
CREATE VIEW Courses_Sales
AS
	SELECT
		C.course_id,
        SL.payment_method,
        SUM(SL.final_product_price) AS total_sales
	FROM
		Sales AS SL
        RIGHT JOIN Courses AS C ON C.product_id = SL.product_id
        LEFT JOIN Sections AS SC ON SC.course_id = C.course_id AND SC.product_id = SL.product_id
	GROUP BY
		C.course_id, SL.payment_method;
        
-- section_views.sql

DROP VIEW IF EXISTS Sections_Info;
CREATE VIEW Sections_Info
AS
	SELECT
		S.section_id,
		S.section_title,
        S.course_id,
        S.product_id,
		P.product_price AS section_price
	FROM
		Sections AS S
        LEFT JOIN Products AS P ON P.product_id = S.product_id;
        
-- category_procedures.sql

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

-- certificate_procedures.sql

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
		_instructor_name AS instructor_fullname,
		C.course_title,
		CF.expedition_date
	FROM
		Certificates AS CF
        INNER JOIN Users_Courses AS UC ON UC.certificate_id = CF.certificate_id
        INNER JOIN Courses AS C ON C.course_id = UC.course_id
        INNER JOIN Users AS U ON U.user_id = UC.user_id
	WHERE
		CF.certificate_id = UUID_TO_BIN(_certificate_id);
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
		Users_Courses AS UC
        INNER JOIN Courses AS C ON C.course_id = UC.course_id
	WHERE
		UC.user_id = _user_id;
END $$
DELIMITER ;

-- chat_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS CreateChat $$

CREATE PROCEDURE CreateChat (
	IN _first_member_id INT,
    IN _second_member_id INT,
    IN _subject VARCHAR(80)
)
BEGIN
	DECLARE _chat_id INT;
	
    INSERT INTO Chats (
		chat_subject
	)
	VALUES (
		_subject
	);
	
	SET _chat_id = LAST_INSERT_ID();
	
	INSERT INTO Users_Chats (
		user_id,
		chat_id
	)
	VALUES (
		_first_member_id,
		_chat_id
	),
	(
		_second_member_id,
		_chat_id
	);
    
    SELECT _chat_id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetUserChats $$

CREATE PROCEDURE GetUserChats (
	IN _user_id INT
)
BEGIN
	SELECT
		C.chat_id,
        C.chat_subject
	FROM
		Chats AS C
        INNER JOIN Users_Chats AS UC ON UC.chat_id = C.chat_id
	WHERE
		UC.user_id = _user_id;
END $$
DELIMITER ;

-- course_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS CreateCourse $$

CREATE PROCEDURE CreateCourse (
    IN _title VARCHAR(70),
    IN _description TEXT,
    IN _price DECIMAL(15, 2),
    IN _instructor_id INT
)
BEGIN
	DECLARE _product_id INT;

	INSERT INTO Products (product_price) VALUES (_price);

	SET _product_id = LAST_INSERT_ID();

	INSERT INTO Courses (
		course_title,
		course_description,
        product_id,
		course_instructor,
		last_update
    )
    VALUES (
		_title,
		_description,
        _product_id,
		_instructor_id,
		CURRENT_TIMESTAMP()
    );
    
SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS EditCourse $$

CREATE PROCEDURE EditCourse (
	IN _id INT,
	IN _title VARCHAR(70),
    IN _description TEXT,
    IN _price DECIMAL(15, 2),
    IN _published BIT
)
BEGIN
	UPDATE Courses AS C
    SET
		C.course_title = _title,
		C.course_description = _description,
        C.course_published = _published,
        C.publication_date = IF (C.publication_date IS NULL AND _published = 1, CURRENT_TIMESTAMP(), C.publication_date)
	WHERE
		C.course_id = _id;
        
	UPDATE
		Products AS P
		INNER JOIN Courses AS C ON C.product_id = P.product_id 
	SET 
		P.product_price = _price
	WHERE
		C.course_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS SetCourseImage $$

CREATE PROCEDURE SetCourseImage (
	IN _course_id INT,
	IN _image MEDIUMBLOB,
    IN _content_type VARCHAR(50)
)
BEGIN
	DECLARE _image_id INT;
    
    IF NOT EXISTS(
		SELECT I.image_id
        FROM Images AS I
        INNER JOIN Courses AS C ON C.course_image = I.image_id
        WHERE C.course_id = _course_id AND I.image_id = C.course_image
	) THEN
		INSERT INTO Images (
			image_content,
            image_content_type
        ) VALUES (
			_image,
            _content_type
        );
        
        SET _image_id = LAST_INSERT_ID();
        
		UPDATE Courses AS C 
		SET 
			C.course_image = _image_id
		WHERE
			C.course_id = _course_id;
	ELSE
		UPDATE Courses AS C, Images AS I
        SET
			I.image_content = _image,
            I.image_content_type = _content_type
		WHERE
			C.course_image = I.image_id AND C.course_id = _course_id;
	END IF;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCourses $$

CREATE PROCEDURE GetCourses (
	IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	SELECT
		CI.course_id,
        CI.course_image,
        CI.course_title,
        CI.course_description,
        CI.product_id,
        CI.course_price,
        CI.instructor_id,
        CI.instructor_name,
        CI.instructor_lastname,
        CI.course_score,
        CI.course_published
    FROM
		Courses_Info AS CI
	WHERE
		CI.course_published = 1
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetTopSalesCourses $$

CREATE PROCEDURE GetTopSalesCourses (
	IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	SELECT
		CI.course_id,
        CI.course_image,
        CI.course_title,
        CI.course_description,
        CI.product_id,
        CI.course_price,
        CI.instructor_id,
        CI.instructor_name,
        CI.instructor_lastname,
        CI.course_score,
        CI.course_published
    FROM
		Courses_Info AS CI
	WHERE
		CI.course_published = 1
	ORDER BY
		CI.total_students DESC
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetTopScoreCourses $$

CREATE PROCEDURE GetTopScoreCourses (
	IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	SELECT
		CI.course_id,
        CI.course_image,
        CI.course_title,
        CI.course_description,
        CI.product_id,
        CI.course_price,
        CI.instructor_id,
        CI.instructor_name,
        CI.instructor_lastname,
        CI.course_score,
        CI.course_published
    FROM
		Courses_Info AS CI
	WHERE
		CI.course_published = 1
	ORDER BY
		CI.course_score DESC
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetMostRecentCourses $$

CREATE PROCEDURE GetMostRecentCourses (
	IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	SELECT
		CI.course_id,
        CI.course_image,
        CI.course_title,
        CI.course_description,
        CI.product_id,
        CI.course_price,
        CI.instructor_id,
        CI.instructor_name,
        CI.instructor_lastname,
        CI.course_score,
        CI.course_published
    FROM
		Courses_Info AS CI
	WHERE
		CI.course_published = 1
	ORDER BY
		CI.publication_date DESC
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetInstructorCourses $$

CREATE PROCEDURE GetInstructorCourses (
	IN _id INT,
    IN _only_published BIT,
	IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	IF _only_published = 1 THEN
		SELECT
			CI.course_id,
            CI.course_image,
			CI.course_title,
			CI.course_description,
			CI.product_id,
			CI.course_price,
			CI.instructor_id,
			CI.instructor_name,
			CI.instructor_lastname,
			CI.course_score,
			CI.course_published
		FROM
			Courses_Info AS CI
		WHERE
			CI.instructor_id = _id AND CI.course_published = 1
		LIMIT
			_total_rows
		OFFSET
			_row_offset;
	ELSE
		SELECT
			CI.course_id,
            CI.course_image,
			CI.course_title,
			CI.course_description,
			CI.product_id,
			CI.course_price,
			CI.instructor_id,
			CI.instructor_name,
			CI.instructor_lastname,
			CI.course_score,
			CI.course_published
		FROM
			Courses_Info AS CI
		WHERE
			CI.instructor_id = _id
		LIMIT
			_total_rows
		OFFSET
			_row_offset;
    END IF;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetUserCourses $$

CREATE PROCEDURE GetUserCourses (
	IN _id INT,
	IN _total_rows INT,
    IN _row_offset INT
)
BEGIN
	SELECT
		CI.course_id,
        CI.course_image,
		CI.course_title,
		CI.course_description,
		CI.product_id,
		CI.course_price,
		CI.instructor_id,
		CI.instructor_name,
		CI.instructor_lastname,
		CI.course_score,
        CI.total_lessons AS course_total_lessons,
		CI.course_published,
        UC.enroll_date,
        UC.last_time_checked,
        BIN_TO_UUID(UC.certificate_id) AS certificate_id,
        get_user_course_completed_lessons(UC.user_id, UC.course_id) AS total_completed_lessons
	FROM
		Courses_Info AS CI
        INNER JOIN Users_Courses AS UC ON UC.course_id = CI.course_id
	WHERE
		UC.user_id = _id
	ORDER BY
		UC.last_time_checked DESC
	LIMIT
		_total_rows
	OFFSET
		_row_offset;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS SearchCourses $$

CREATE PROCEDURE SearchCourses (
	IN _input TINYTEXT,
	IN _total_rows INT,
    IN _row_offset INT,
    IN _from DATETIME,
    IN _to DATETIME,
    IN _category_id INT
)
BEGIN
	DECLARE _from_date DATETIME DEFAULT COALESCE(_from, datetime_min_value());
	DECLARE _to_date DATETIME DEFAULT COALESCE(_to, datetime_max_value());

	IF _category_id IS NOT NULL THEN
		SELECT
			CI.course_id,
			CI.course_image,
			CI.course_title,
			CI.course_description,
			CI.product_id,
			CI.course_price,
			CI.instructor_id,
			CI.instructor_name,
			CI.instructor_lastname,
			CI.course_score,
			CI.course_published
		FROM
			Courses_Info AS CI
            INNER JOIN Courses_Categories AS CC ON CC.course_id = CI.course_id
		WHERE
			(MATCH(CI.course_description) AGAINST(_input IN NATURAL LANGUAGE MODE)
            OR CI.course_title LIKE (CONCAT('%', _input, '%'))
			OR _input IN (CI.instructor_name, CI.instructor_lastname, CI.instructor_username))
			AND CI.course_published = 1
            AND CI.publication_date BETWEEN _from_date AND _to_date
            AND CC.category_id = _category_id
		LIMIT
			_total_rows
		OFFSET
			_row_offset;
    ELSE
		SELECT
			CI.course_id,
			CI.course_image,
			CI.course_title,
			CI.course_description,
			CI.product_id,
			CI.course_price,
			CI.instructor_id,
			CI.instructor_name,
			CI.instructor_lastname,
			CI.course_score,
			CI.course_published
		FROM
			Courses_Info AS CI
		WHERE
			(MATCH(CI.course_description) AGAINST(_input IN NATURAL LANGUAGE MODE)
            OR CI.course_title LIKE (CONCAT('%', _input, '%'))
			OR _input IN (CI.instructor_name, CI.instructor_lastname, CI.instructor_username))
			AND CI.course_published = 1
            AND CI.publication_date BETWEEN _from_date AND _to_date
		LIMIT
			_total_rows
		OFFSET
			_row_offset;
    END IF;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetCourseInfo $$

CREATE PROCEDURE GetCourseInfo (
	IN _id INT
)
BEGIN
	SELECT
		CI.course_id,
        CI.course_image,
        CI.course_title,
        CI.course_description,
        CI.product_id,
        CI.course_price,
        CI.instructor_id,
        CI.instructor_name,
        CI.instructor_lastname,
        CI.publication_date,
        CI.last_update,
        CI.total_students,
        CI.total_lessons,
        CI.course_score,
        CI.course_published
    FROM
		Courses_Info AS CI
	WHERE
		CI.course_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS AddCourseCategory $$

CREATE PROCEDURE AddCourseCategory (
	IN _course_id INT,
    IN _category_id INT
)
BEGIN
	IF NOT EXISTS(
		SELECT
			course_category_id
		FROM
			Courses_Categories
		WHERE
			course_id = _course_id AND category_id = _category_id
	) THEN
		INSERT INTO Courses_Categories (
			course_id,
			category_id
		)
		VALUES (
			_course_id,
			_category_id
		);
	END IF;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS DeleteCourseCategory $$

CREATE PROCEDURE DeleteCourseCategory (
	IN _course_id INT,
    IN _category_id INT
)
BEGIN
	DELETE FROM Courses_Categories
    WHERE
		course_id = _course_id AND category_id = _category_id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS InstructorReportA $$

CREATE PROCEDURE InstructorReportA (
	IN _instructor_id INT
)
BEGIN
	CREATE TEMPORARY TABLE Instructor_Courses_Sales
    SELECT
		CS.course_id,
        SUM(CS.total_sales) AS total_sales
	FROM
		Courses_Sales AS CS
	GROUP BY
		CS.course_id;

	CREATE TEMPORARY TABLE Courses_Avg_Users_Completion
    SELECT
		ICS.course_id,
        ICS.total_sales,
        AVG(
			get_user_course_completed_lessons(UC.user_id, UC.course_id) /
            GREATEST(get_course_total_lessons(UC.course_id), 1)
        ) AS average_completion
	FROM
		Instructor_Courses_Sales AS ICS
        LEFT JOIN Users_Courses AS UC ON UC.course_id = ICS.course_id
	GROUP BY
		ICS.course_id, ICS.total_sales;

	SELECT
		C.course_id,
        C.course_title,
        get_course_total_students(C.course_id) AS total_students,
        CAUC.average_completion,
        CAUC.total_sales
    FROM
		Courses AS C
        INNER JOIN Courses_Avg_Users_Completion AS CAUC ON CAUC.course_id = C.course_id
	WHERE
		C.course_instructor = _instructor_id;
        
	DROP TABLE Instructor_Courses_Sales;
    DROP TABLE Courses_Avg_Users_Completion;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS InstructorReportB $$

CREATE PROCEDURE InstructorReportB (
	IN _instructor_id INT
)
BEGIN
	SELECT
		PM.payment_method_name AS payment_method,
		CS.total_sales
    FROM
		Courses AS C
        INNER JOIN Courses_Sales AS CS ON CS.course_id = C.course_id
        INNER JOIN PaymentMethods AS PM ON PM.payment_method_id = CS.payment_method
	WHERE
		C.course_instructor = _instructor_id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS CourseReportA $$

CREATE PROCEDURE CourseReportA (
	IN _course_id INT
)
BEGIN
	SELECT
		CONCAT(U.user_name, ' ', U.user_lastname) AS user_fullname,
        UC.enroll_date,
        (
			get_user_course_completed_lessons(UC.user_id, UC.course_id) /
            GREATEST(get_course_total_lessons(UC.course_id), 1)
        ) AS completion_ratio,
        S.final_product_price AS total_paid,
        PM.payment_method_name AS payment_method
    FROM
		Courses AS C
		INNER JOIN Users_Courses AS UC ON UC.course_id = C.course_id
        INNER JOIN Users AS U ON U.user_id = UC.user_id
        INNER JOIN Sales AS S ON S.product_id = C.product_id AND S.orderer_id = U.user_id
        INNER JOIN PaymentMethods AS PM ON PM.payment_method_id = S.payment_method
	WHERE
		C.course_id = _course_id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS SetUserCourseLastTimeChecked $$

CREATE PROCEDURE SetUserCourseLastTimeChecked (
	IN _user_id INT,
    IN _course_id INT
)
BEGIN
	UPDATE Users_Courses
	SET
		last_time_checked = CURRENT_TIMESTAMP()
	WHERE
		user_id = _user_id AND course_id = _course_id;
END $$
DELIMITER ;

-- image_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS GetImage $$

CREATE PROCEDURE GetImage (
    IN _id INT
)
BEGIN
	SELECT
		I.image_id,
		I.image_content,
		I.image_content_type
	FROM
		Images AS I
	WHERE
		I.image_id = _id;
END $$
DELIMITER ;

-- lesson_procedures.sql

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

-- message_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS CreateMessage $$

CREATE PROCEDURE CreateMessage (
	IN _sender_id INT,
    IN _chat_id INT,
    IN _body MEDIUMTEXT
)
BEGIN
    INSERT INTO Messages (
		message_body,
        sender_user_id,
        chat_id
    )
    VALUES (
		_body,
        _sender_id,
        _chat_id
    );
    
    SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetChatMessages $$

CREATE PROCEDURE GetChatMessages (
	IN _chat_id INT
)
BEGIN
	SELECT
		M.message_id,
		M.message_body,
		M.message_date,
        COALESCE(U.user_id, 0) AS sender_id,
        COALESCE(CONCAT(U.user_name, ' ', U.user_lastname), 'User') AS sender_name,
        M.chat_id
	FROM
		Messages AS M
        LEFT JOIN Users AS U ON U.user_id = M.sender_user_id
	WHERE
		M.chat_id = _chat_id
	ORDER BY
		M.message_date ASC;
END $$
DELIMITER ;

-- orders_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS PlaceOrder $$

CREATE PROCEDURE PlaceOrder (
	IN _orderer_id INT,
    IN _payment_method INT
)
BEGIN
	INSERT INTO Orders (
		orderer_user_id,
        payment_method
    ) VALUES (
		_orderer_id,
        _payment_method
    );
    
    SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS AddProductToOrder $$

CREATE PROCEDURE AddProductToOrder (
	IN _order_id INT,
    IN _product_id INT
)
BEGIN
	DECLARE _final_price DECIMAL(15 , 2 );
    
    SET _final_price = (
		SELECT
			product_price
		FROM
			Products
		WHERE
			product_id = _product_id
    );

	INSERT INTO Orders_Products (
		order_id,
        product_id,
        final_product_price
    ) VALUES (
		_order_id,
        _product_id,
        _final_price
    );
END $$
DELIMITER ;

-- payment_methods_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS GetPaymentMethods $$

CREATE PROCEDURE GetPaymentMethods ()
BEGIN
	SELECT
		payment_method_id,
        payment_method_name
	FROM
		PaymentMethods;
END $$
DELIMITER ;

-- resource_procedures.sql

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
		R.lesson_id = _lesson_id;
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

-- review_procedures

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
		R.review_id = _id;
END $$
DELIMITER ;

-- section_procedures.sql

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

-- user_procedures.sql

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

-- video_procedures.sql

DELIMITER $$
DROP PROCEDURE IF EXISTS AddVideo $$

CREATE PROCEDURE AddVideo (
	IN _address VARCHAR(255),
    IN _duration INT,
    IN _lesson_id INT
)
BEGIN
	DELETE FROM Videos
    WHERE lesson_id = _lesson_id;

	INSERT INTO Videos (
		video_address,
        video_duration,
        lesson_id
    )
    VALUES (
		_address,
        _duration,
        _lesson_id
    );
    
    SELECT LAST_INSERT_ID();
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS EditVideo $$

CREATE PROCEDURE EditVideo (
	IN _id INT,
	IN _address VARCHAR(255),
    IN _duration INT
)
BEGIN
	UPDATE Videos AS V
    SET
		V.video_address = _address,
        V.video_duration = _duration
	WHERE
		V.video_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS DeleteVideo $$

CREATE PROCEDURE DeleteVideo (
	IN _id INT
)
BEGIN
	DELETE FROM Videos
    WHERE
		video_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetVideo $$

CREATE PROCEDURE GetVideo (
	IN _id INT
)
BEGIN
	SELECT
		V.video_id,
        V.video_address,
        V.video_duration,
        V.lesson_id
	FROM
		Videos as V
	WHERE
		V.video_id = _id;
END $$
DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetLessonVideo $$

CREATE PROCEDURE GetLessonVideo (
	IN _lesson_id INT
)
BEGIN
	SELECT
		V.video_id,
        V.video_address,
        V.video_duration,
        V.lesson_id
	FROM
		Videos as V
	WHERE
		V.lesson_id = _lesson_id;
END $$
DELIMITER ;

-- lesson_triggers.sql

DELIMITER $$
DROP TRIGGER IF EXISTS TR_AFTER_INSERT_ON_LESSONS $$

CREATE TRIGGER TR_AFTER_INSERT_ON_LESSONS
AFTER INSERT
ON Lessons FOR EACH ROW
BEGIN
	UPDATE
		Courses AS C
        INNER JOIN Sections AS S ON S.course_id = C.course_id
	SET
		C.last_update = CURRENT_TIMESTAMP()
	WHERE
		S.section_id = New.section_id;
END $$
DELIMITER ;

-- order_triggers.sql

DELIMITER $$
DROP TRIGGER IF EXISTS TR_AFTER_INSERT_ON_ORDERS_PRODUCTS $$

CREATE TRIGGER TR_AFTER_INSERT_ON_ORDERS_PRODUCTS
AFTER INSERT
ON Orders_Products FOR EACH ROW
BEGIN
	DECLARE _course_id INT DEFAULT NULL;
    DECLARE _user_id INT DEFAULT NULL;
    
    SET _course_id = (SELECT course_id FROM Courses WHERE product_id = New.product_id);
    
    IF _course_id IS NOT NULL THEN
		SET _user_id = (
			SELECT
				O.orderer_user_id
			FROM
				Orders AS O
			WHERE
				O.order_id = New.order_id
		);
        
        IF _user_id IS NOT NULL THEN
			INSERT INTO Users_Courses (
				user_id,
                course_id
            ) VALUES (
				_user_id,
                _course_id
            );
        END IF;
    END IF;
END $$
DELIMITER ;

-- section_triggers.sql

DELIMITER $$
DROP TRIGGER IF EXISTS TR_AFTER_INSERT_ON_SECTIONS $$

CREATE TRIGGER TR_AFTER_INSERT_ON_SECTIONS
AFTER INSERT
ON Sections FOR EACH ROW
BEGIN
	UPDATE Courses AS C
    SET
		C.last_update = CURRENT_TIME()
	WHERE
		C.course_id = New.course_id;
END $$
DELIMITER ;
