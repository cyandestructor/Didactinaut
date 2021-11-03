USE didactinaut_dev;

INSERT INTO PaymentMethods (payment_method_name)
VALUES
	('Credit Card'), ('Paypal');

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