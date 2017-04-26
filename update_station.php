<?php
	$update_station = "UPDATE station SET name = ? WHERE id = ?";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }

  $id = $_POST['station_id'];
	$name = $_POST['station_name'];

	$stmt = $conn->prepare($update_station);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("si", $name, $id)) {
		die("Prepare bind failed: " . $stmt->error);
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php");
?>
