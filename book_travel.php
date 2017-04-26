<?php

$select_scheduled_hop = <<<EOT
    SELECT 
        s.id AS id, eta(s.departure, train.speed, track.distance) AS eta
    FROM 
        schedule AS s
            LEFT JOIN train AS train ON train.id = s.train
            LEFT JOIN track AS track ON track.id = s.track
    WHERE 
        track=? AND departure>=? 
    ORDER BY s.departure ASC LIMIT 1
EOT;


    $select_tracks = "SELECT id, start, end FROM track WHERE ? IN (start, end)";
    $call_hop = "CALL next_hop(?, ?, @found, @nextId)";
    $select_hop = "SELECT @found, @nextId";
    $insert = "INSERT INTO reservation (id, passenger, schedule) values (?, ?, ?)";

	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }
	
    $passenger = $_POST['book_name'];
    $date = $_POST['book_date'];
    $from = $_POST['book_from'];
    $to = $_POST['book_to'];
    
    if (!$stmt = $conn->prepare($select_tracks)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$hstmt = $conn->prepare($call_hop)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$sstmt = $conn->prepare($select_scheduled_hop)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$istmt = $conn->prepare($insert)) {
        die("Prepare failed: " . $conn->error);
    }   

    function nextHop($trackId, $dest) {
        global $hstmt, $conn, $select_hop;
        $hstmt->reset();
        if(!$hstmt->bind_param("ii", $trackId, $dest)) { die("Bind failed: " . $hstmt->error); }
        if(!$hstmt->execute()) { die("Execute failed: " . $hstmt->error); }
        $rs = $conn->query($select_hop);
        if(!$rs) { die("Query failed: " . $conn->error); }
        $n = $rs->fetch_array();
        if ($n[0]){
            return $n[1];
        }
        return 0;
    }

    function genUUID() {
        global $conn;
        if ($result = $conn->query("SELECT uuid()")) {
            return $result->fetch_array()[0];
        }
        die("Cannot generate uuid: " . $conn->error);
    }
    
    /* step 1 - find our first hop */
    if (!$stmt->bind_param("i", $from)) {
        die("Prepare bind failed: " . $stmt->error);        
    }
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->store_result();
    if (!$stmt->bind_result($i, $s, $e)){die("bind_result: " . $stmt->error);}
    $first = 0;
    while ($stmt->fetch()) {
        if ($s == $to) {
            die('Source and Destination are the same');
        }
        if ($e == $to) { 
            $first = $i;
            break;
        }
        if (($n = nextHop($i, $to)) > 0) {
            $first = $i;
            break;
        }
    }
    $stmt->close();


    /* step 2 - build an array of each hop */
    $hops = array();
    $next = $first;
    while($next){
        array_push($hops, $next);
        $next = nextHop($next, $to);
    } 
    $hstmt->close();


    /* step 3 - for each hop find a scheduled train */
    $reservationId = genUUID();
    $ref = $date;

    $conn->query("BEGIN");
    foreach ($hops as $h) {
        $sstmt->bind_param("is", $h, $ref); 
        $sstmt->execute();
        $sstmt->bind_result($id, $ref);
        if(!$sstmt->fetch()){
            $sstmt->reset();
            $conn->query("ROLLBACK");
            die("Cannot book trip: no viable path scheduled");
        }
        $sstmt->reset();

        $istmt->bind_param("ssi", $reservationId, $passenger, $id);
        $istmt->execute();
        $istmt->reset(); 
    }
    $conn->query("COMMIT"); 
	header("Location: bookings.php");
?>
