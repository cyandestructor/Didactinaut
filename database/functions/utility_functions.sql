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

-- From: https://stackoverflow.com/a/754159
DELIMITER $$
DROP FUNCTION IF EXISTS word_count $$

CREATE FUNCTION word_count(str LONGTEXT)
       RETURNS INT
       DETERMINISTIC
       SQL SECURITY INVOKER
       NO SQL
  BEGIN
    DECLARE wordCnt, idx, maxIdx INT DEFAULT 0;
    DECLARE currChar, prevChar BOOL DEFAULT 0;
    SET maxIdx=char_length(str);
    SET idx = 1;
    WHILE idx <= maxIdx DO
        SET currChar=SUBSTRING(str, idx, 1) RLIKE '[[:alnum:]]';
        IF NOT prevChar AND currChar THEN
            SET wordCnt=wordCnt+1;
        END IF;
        SET prevChar=currChar;
        SET idx=idx+1;
    END WHILE;
    RETURN wordCnt;
  END
$$
DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS text_duration $$

CREATE FUNCTION text_duration(str LONGTEXT)
       RETURNS INT
       DETERMINISTIC
  BEGIN
	-- Avg words per page = 800
    -- Avg time to read one page = 3.2 min = 192 s
    -- seconds per word = 192s / 800 words = 0.24
    DECLARE secondsPerWord DECIMAL(5,2) DEFAULT 0.24;
    DECLARE wordCnt DECIMAL(5,2);
    
    SET wordCnt = wordcount(str);
    
    RETURN FLOOR(wordCnt * secondsPerWord);
  END
$$
DELIMITER ;