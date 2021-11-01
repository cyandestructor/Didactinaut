USE didactinaut_dev;

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
        O.order_date,
        O.orderer_user_id AS orderer_id,
        O.seller_user_id AS seller_id,
        O.payment_method
	FROM
		Products_Info AS PI
        INNER JOIN Orders_Products AS OP ON OP.product_id = PI.product_id
        INNER JOIN Orders AS O ON O.order_id = OP.order_id;
