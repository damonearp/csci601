<?php
	$insert_track = "INSERT INTO track (start, end, distance) VALUES (?, ?, ?)";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	
	$to = $_POST['track_to'];
	$from = $_POST['track_from'];
	$dist = $_POST['track_distance'];

	if ($to === $from) {
		die("Condition Failed: track cannot connect the same station");
	}
	
	if ($to > $from) {
		$tmp = $to;
		$to = $from;
		$from = $tmp;
	}
	
	$stmt = $conn->prepare($insert_track);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("iii", $to, $from, $dist)) {
		die("Prepare bind failed: " . $stmt->error);		
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php#tracks");
?>
