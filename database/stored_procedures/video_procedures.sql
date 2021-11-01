USE didactinaut_dev;

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
        _lesson
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