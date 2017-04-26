<?php
	$update_train = "UPDATE train SET name = ?, capacity = ?, speed = ? WHERE id = ?";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }

    $id = $_POST['train_id'];
	$name = $_POST['train_name'];
	$capacity = $_POST['train_capacity'];
	$speed = $_POST['train_speed'];

	$stmt = $conn->prepare($update_train);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("siii", $name, $capacity, $speed, $id)) {
		die("Prepare bind failed: " . $stmt->error);
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php#trains");
?>
