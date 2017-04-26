<?php
	$insert_train = "INSERT INTO train (name, capacity, speed) VALUES (?, ?, ?)";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	
	$name = $_POST['train_name'];
	$capacity = $_POST['train_capacity'];
    $speed = $_POST['train_speed'];
	
	$stmt = $conn->prepare($insert_train);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("sii", $name, $capacity, $speed)) {
		die("Prepare bind failed: " . $stmt->error);		
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php#trains");
?>
