USE didactinaut_dev;

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
