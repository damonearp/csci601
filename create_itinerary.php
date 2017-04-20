<?php
	$insert_itinerary = "INSERT INTO itinerary (train, depart, etd, arrive, eta) VALUES (?, ?, ?, ?, ?)";
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	
	$train = $_POST['itinerary_train'];
	$depart  = $_POST['itinerary_depart'];
	$etd = $_POST['itinerary_etd'];
	$arrive = $_POST['itinerary_arrival'];
	$eta = $_POST['itinerary_eta'];
	
	$stmt = $conn->prepare($insert_itinerary);
	if (!$stmt) {
		die("Prepare failed: " . $conn->error);
	}
	if (!$stmt->bind_param("iisis", $train, $depart, $etd, $arrive, $eta)) {
		die("Prepare bind failed: " . $stmt->error);		
	}
	if (!$stmt->execute()) {
		die("Execute failed: " . $stmt->error);
	}
	header("Location: index.php");
?>
