<?php
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
	//$conn->query("drop view schedule");
	//$conn->query("drop table itinerary");
	//$conn->query("drop table train");
	//$conn->query("drop table platform");
	//$conn->query("drop table station");

	header("Location: index.php");
?>
