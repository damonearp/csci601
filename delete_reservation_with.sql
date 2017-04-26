DELIMITER //

CREATE PROCEDURE delete_reservations_with(IN scheduleId BIGINT)
BEGIN
    DECLARE done INT DEFAULT FALSE;

    DECLARE rid BIGINT;
    DECLARE found INT DEFAULT FALSE;

    DECLARE reserved CURSOR FOR SELECT id FROM reservation WHERE schedule = scheduleId GROUP BY id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN reserved;

    read_loop: LOOP
        IF done THEN
            LEAVE read_loop;
        END IF;

        FETCH reserved INTO rid;

        DELETE FROM reservation WHERE id=rid;
    END LOOP;

    CLOSE reserved;
END;

//
DELIMITER ;
