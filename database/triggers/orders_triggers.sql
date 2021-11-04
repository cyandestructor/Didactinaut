USE didactinaut_dev;

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