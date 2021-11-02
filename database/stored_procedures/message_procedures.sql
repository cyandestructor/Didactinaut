USE didactinaut_dev;

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
		M.chat_id = _chat_id;
END $$
DELIMITER ;
