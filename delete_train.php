<?php
	 $conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }

    $id = $_POST['train_id'];
    $conn->query("DELETE FROM train WHERE id=$id");
	  header("Location: index.php#trains");
?>
