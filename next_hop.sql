DELIMITER //

CREATE PROCEDURE next_hop(IN tid BIGINT, IN dest BIGINT, OUT success INT, OUT next BIGINT)
BEGIN
    DECLARE done INT DEFAULT FALSE;

    DECLARE previous, station, trackId, endId BIGINT;
    DECLARE found INT DEFAULT FALSE;
    DECLARE nextHop BIGINT;
 
    DECLARE tracks CURSOR FOR SELECT id, end FROM track WHERE start = station AND end <> previous;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SELECT start, end INTO previous, station FROM track where id = tid;
   
    OPEN tracks;

    read_loop: LOOP
        IF done THEN
            SET success = FALSE;
            SET next = 0;
            LEAVE read_loop;
        END IF;

        FETCH tracks INTO trackId, endId;

        IF endId = dest THEN
            SET success = TRUE; 
            SET next = trackId;
            LEAVE read_loop;
        ELSE
            CALL next_hop(trackId, dest, found, nextHop);
            IF found THEN
                SET success = found;
                SET next = trackId;
                LEAVE read_loop;
            END IF;
        END IF;
    END LOOP;

    CLOSE tracks;
END; 

//

