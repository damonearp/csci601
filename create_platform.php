<?php
	$insert_platform = "INSERT INTO platform (station, designation) VALUES (?, ?)";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	
	$lbl = $_POST['platform_label'];
	$station  = $_POST['platform_station'];
	
	$stmt = $conn->prepare($insert_platform);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("is", $station, $lbl)) {
		die("Prepare bind failed: " . $stmt->error);		
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php#platforms");
?>
