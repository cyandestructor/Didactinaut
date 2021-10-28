USE didactinaut_dev;

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