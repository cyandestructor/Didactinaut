USE didactinaut_dev;

DELIMITER $$
DROP PROCEDURE IF EXISTS GetProductInfo $$

CREATE PROCEDURE GetProductInfo (
	IN _id INT
)
BEGIN
	SELECT
		product_id,
        product_name,
        product_price,
        product_image
	FROM
		Products_Info
	WHERE
		product_id = _id;
END $$
DELIMITER ;

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