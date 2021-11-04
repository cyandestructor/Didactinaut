USE didactinaut_dev;

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
