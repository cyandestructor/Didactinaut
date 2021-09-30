USE didactinaut_dev;

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