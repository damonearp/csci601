<?php
	$insert_station = "INSERT INTO station (name) VALUES (?)";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	
	$name = $_POST['station_name'];
	
	$stmt = $conn->prepare($insert_station);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("s", $name)) {
		die("Prepare bind failed: " . $stmt->error);		
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php");
?>
