<?php

    $select_tracks = "SELECT id, start, end FROM track WHERE ? IN (start, end)";
    $select_track = "SELECT distance FROM track WHERE id=?";
    $call_hop = "CALL next_hop(?, ?, @found, @nextId)";
    $select_hop = "SELECT @found, @nextId";
    $select_train = "SELECT speed FROM train WHERE id=?";
    $calculate_time = "SELECT ? + INTERVAL ? SECOND AS t";
    $insert = "INSERT INTO schedule (departure, train, track) values (?, ?, ?)";

	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }
	
    $departure = $_POST['schedule_etd'];
    $source = $_POST['schedule_source'];
    $dest = $_POST['schedule_dest'];
    $train = $_POST['schedule_train'];

    if (!$stmt = $conn->prepare($select_tracks)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$dstmt = $conn->prepare($select_track)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$hstmt = $conn->prepare($call_hop)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$tstmt = $conn->prepare($select_train)) {
        die("Prepare failed: " . $conn->error);
    }
    if (!$cstmt = $conn->prepare($calculate_time)){
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
    
    function dateAdd($base, $secs) {
        global $cstmt;
        $cstmt->reset();
        $cstmt->bind_param("si", $base, $secs);
        $cstmt->execute();
        $cstmt->bind_result($t);
        $cstmt->fetch();
        $cstmt->store_result();
        return $t;
    }
    
    function trackDistance($tid) {
        global $dstmt;
        $dstmt->reset();
        $dstmt->bind_param("i", $tid);
        $dstmt->execute();
        $dstmt->bind_result($d);
        $dstmt->fetch();
        $dstmt->store_result();
        return $d;
    }
    
    /* step 1 - find our first hop */
    if (!$stmt->bind_param("i", $source)) {
        die("Prepare bind failed: " . $stmt->error);        
    }
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->store_result();
    if (!$stmt->bind_result($i, $s, $e)){die("bind_result: " . $stmt->error);}
    $first = 0;
    while ($stmt->fetch()) {
        if ($s == $dest) {
            die('Source and Destination are the same');
        }
        if ($e == $dest) { 
            $first = $i;
            break;
        }
        if (($n = nextHop($i, $dest)) > 0) {
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
        $next = nextHop($next, $dest);
    } 
    $hstmt->close();

    /* step 3 - lookup our train's speed */
    $tstmt->bind_param("i", $train);
    $tstmt->execute();
    $tstmt->bind_result($speed);
    $tstmt->fetch();
    $tstmt->close();

    /* step 4 - calculate when it will arrive and insert a schedule */
    $offset = 0;
    foreach ($hops as $h) {
        $date = dateAdd($departure, $offset);    
        $istmt->reset();
        if(!$istmt->bind_param("sii", $date, $train, $h)){die("bind_param: " . $istmt->error);}
        if(!$istmt->execute()){die("execute: " . $istmt->error);}
        
        $dist = trackDistance($h);
        /* # of seconds to get to the next dest + a 15 min window */
        $offset += (($dist / $speed) * 60 * 60) + (15 * 60);
    }

	header("Location: index.php#schedule");
?>
