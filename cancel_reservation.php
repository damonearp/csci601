<?php
	$delete = "DELETE FROM reservation WHERE id = ?";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	
	$id = $_POST['id'];
	
	$stmt = $conn->prepare($delete);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("s", $id)) {
		die("Prepare bind failed: " . $stmt->error);		
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: bookings.php");
?>
