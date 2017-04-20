<?php 
$create_station = <<<EOT
	CREATE TABLE IF NOT EXISTS station 
	(
		id BIGINT NOT NULL AUTO_INCREMENT, 
		name VARCHAR(255) NOT NULL UNIQUE,
		PRIMARY KEY (id)
	)
EOT;
$create_platform = <<<EOT
	CREATE TABLE IF NOT EXISTS platform
	(
		id BIGINT AUTO_INCREMENT NOT NULL,
	  	station BIGINT NOT NULL REFERENCES station(id),
		designation VARCHAR(16) NOT NULL,
		PRIMARY KEY (id),
		UNIQUE(station, designation)
	)
EOT;
$create_train = <<<EOT
	CREATE TABLE IF NOT EXISTS train
	(
		id BIGINT NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL UNIQUE,
		capacity MEDIUMINT NOT NULL CHECK(VALUE >= 0),
		speed BIGINT NOT NULL, 
		PRIMARY KEY (id)
	)
EOT;
$create_track = <<<EOT
	CREATE TABLE IF NOT EXISTS track
	(
		id BIGINT NOT NULL AUTO_INCREMENT,
		start BIGINT NOT NULL REFERENCES station(id),
		end BIGINT NOT NULL REFERENCES station(id),
		distance BIGINT NOT NULL,
		PRIMARY KEY (id),
		UNIQUE (start, end)
	)
EOT;
$create_itinerary = <<<EOT
	CREATE TABLE IF NOT EXISTS itinerary
	(
		id BIGINT NOT NULL AUTO_INCREMENT,
		train BIGINT NOT NULL REFERENCES train(id),
		depart BIGINT NOT NULL REFERENCES platform(id),
		etd DATETIME NOT NULL,
		arrive BIGINT NOT NULL REFERENCES platform(id),
		eta DATETIME NOT NULL,
		status VARCHAR(255) NOT NULL DEFAULT "On Time",
		PRIMARY KEY (id)
	)	
EOT;
$create_passenger = <<<EOT
	CREATE TABLE IF NOT EXISTS passenger
	(
		id BIGINT NOT NULL AUTO_INCREMENT,
		name VARCHAR(255) NOT NULL,
		itinerary BIGINT NOT NULL REFERENCES itinerary(id),
		PRIMARY KEY (id),
		UNIQUE(name, itinerary)
	)
EOT;
$create_schedule = <<<EOT
	CREATE OR REPLACE VIEW schedule AS
		SELECT 
			i.id AS id, 
			t.name AS train, 
			i.etd AS depart_time, ds.name AS depart_station, dp.designation AS depart_platform, 
			i.eta AS arrive_time, ars.name AS arrive_station, arp.designation AS arrive_platform,
			i.status AS status
		FROM itinerary AS i 
			LEFT JOIN platform AS dp ON i.depart = dp.id
				LEFT JOIN station AS ds ON dp.station = ds.id
			LEFT JOIN platform AS arp ON i.arrive = arp.id
				LEFT JOIN station AS ars ON arp.station = ars.id
			LEFT JOIN train AS t ON i.train = t.id 
EOT;
$drop_mph_to_kmh = "DROP FUNCTION IF EXISTS mph_to_kmh";
$create_mph_to_kmh = <<<EOT
	CREATE FUNCTION mph_to_kmh (mph BIGINT) RETURNS BIGINT RETURN mph * 1.60934
EOT;
$drop_kmh_to_mph = "DROP FUNCTION IF EXISTS kmh_to_mph";
$create_kmh_to_mph = <<<EOT
	CREATE FUNCTION kmh_to_mph (kmh BIGINT) RETURNS BIGINT RETURN kmh * 0.621371
EOT;
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Choooo Chooooo</title>
		<style>
			body {
				font-family: "Courier New", monospace;
				font-size: small;
			}
		
			.insert {
				padding: 5px;
				border-style: solid;
				border-width: 1px;
				margin-bottom: 5px;	
			}
			
			.insert p {
				font-weight: bold;
				color: red;
			}

			.insert form {
				margin-bottom: 5px;
			}
	
			.insert th {
				color: red;
			}
			
			th, td {
				text-align: center;
    				border-bottom: 1px solid #bbb;
			}
		</style>
	</head>
	<body>
		<h3>Initialization Status</h3>
		<ol>
<?php
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
	if ($conn->connect_error) {
		die("Connection failed: ". $conn->connect_error);
	}
	echo "<li>DB Connection Success</li>";
	if(!$conn->query($create_station)) {
		die("Failed to create station table: " . $conn->error);
	}
	echo "<li>Create table station: ok</li>";
	if (!$conn->query($create_platform)) {
		die("Failed to create platform table: " . $conn->error);
	}
	echo "<li>Create table platform: ok</li>";
    if (!$conn->query($create_train)) {
		die("Failed to create train table: " . $conn->error);
	}	
	echo "<li>Create table train: ok</li>";
	if (!$conn->query($create_track)) {
		die("Failed to create track table: " . $conn->error);
	}
	echo "<li>Create table track: ok</li>";
	if (!$conn->query($create_itinerary)) {
		die("Failed to create itinerary table: " . $conn->error);
	}
	echo "<li>Create table itinerary: ok</li>";
	if (!$conn->query($create_passenger)) {
		die("Failed to create passenger table: " . $conn->error);
	}
	echo "<li>Create table passenger: ok</li>";
	if (!$conn->query($create_schedule)) {
		die("Failed to create view schedule: " . $conn->error);
	}
	echo "<li>Create view schedule: ok</li>";
	echo "<li>DB Tables created</li>";
	if (!$conn->query($drop_mph_to_kmh) || !$conn->query($create_mph_to_kmh)) {
		die("Failed to create function mph_to_kmh");
	}
	echo "<li>Create function mph_to_kmh: ok</li>";
	if (!$conn->query($drop_kmh_to_mph) || !$conn->query($create_kmh_to_mph)) {
        die("Failed to create function kmh_to_mph");
    }
    echo "<li>Create function kmh_to_mph: ok</li>";
?>
		</ol>
		<h3>Operations</h3>
		
		<div class="insert">
			<p>Stations</p>
			<form action="create_station.php" method="post">
				<label>New Station: <input type="text" name="station_name" /></label>
				<input type="submit" />
			</form>
			<table>
				<thead><tr><th>Station</th></thead>
				<tbody>
<?php
	$result = $conn->query("SELECT name FROM station ORDER BY name ASC");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo "<tr><td>" . $row["name"]  . "</td></tr>";
		}
		$result->close();
	}
?>
				</tbody>
			</table>
		</div>
		<div class="insert">
			<p>Trains</p>
			<form action="create_train.php" method="post">
				<label>Train Name: <input type="text" name="train_name" /></label>
				<label>Capacity: <input type="text" name="train_capacity" /></label>
				<input type="submit" />
			</form>
			<table>
				<thead><tr><th>Train</th><th>Capacity</th><th>Speed (mph)</th><th>Speed (kmh)</th></tr></thead>
				<tbody>
<?php
	$result = $conn->query("SELECT name, capacity, speed AS kmh, kmh_to_mph(speed) AS mph FROM train ORDER BY name ASC");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo "<tr><td>" . $row["name"]  . "</td><td>" . $row["capacity"] . "</td><td>" . $row["mph"] . "</td><td>" . $row["kmh"] . "</td></tr>";
		}
		$result->close();
	}
?>
				</tbody>
			</table>
		</div>
		<div class="insert">
			<p>Platforms</p>	
			<form action="create_platform.php" method="post">
				<label>Platform: <input type="text" name="platform_label" /></label>
				<label>Station: <select name="platform_station">
<?php
	$result = $conn->query("SELECT id, name FROM station ORDER BY name ASC");	
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
		}
		$result->close();
	}
?>
				</select></label>
				<input type="submit" />
			</form>
			<table>
                <thead><tr><th>Station</th><th>Platforms</th></thead>
                <tbody>
<?php
	$result = $conn->query("SELECT s.name AS station, p.designation AS label FROM platform AS p LEFT JOIN station AS s ON s.id = p.station ORDER BY s.name, p.designation ASC");
    if ($result) {
		$flat = array();
        while ($row = $result->fetch_assoc()) {
			if (!array_key_exists($row['station'], $flat)) {
				$flat[$row['station']] = array();
			}
			array_push($flat[$row['station']], $row['label']);
        }
		$result->close();
		foreach ($flat as $station => $platforms) {
			echo '<tr><td>' . $station . '</td><td>' . join(', ', $platforms) . '</td></tr>';
		}
    } else { die($conn->error); }
?>		
				</tbody>
			</table>
		</div>
		<div class="insert">
			<p>Tracks</p>
			<form action="create_track.php" method="post">
				<label>From: <select name="track_to">

<?php
	$result = $conn->query("SELECT id, name FROM station ORDER BY name asc");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>'; 
		}
		$result->close();
	}
?>
				</select></label>
				<label>To: <select name="track_from">
<?php
    $result = $conn->query("SELECT id, name FROM station ORDER BY name asc");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
        }
		$result->close();
    }
?>				
				</select></label>
				<label>Distance: <input type="text" name="track_distance" value="kilometers"></input></label>
				<input type="submit" />
			</form>
			<table>
				<thead><tr><th>A</th><th>B</th><th>Distance (mi)</th><th>km</th></tr></thead>
				<tbody>
<?php
	$result = $conn->query("SELECT a.name AS a, b.name AS b, distance AS km, kmh_to_mph(distance) AS mi FROM track AS t LEFT JOIN station AS a ON t.start = a.id LEFT JOIN station AS b ON t.end = b.id ORDER BY a.name, b.name ASC");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo '<tr><td>' . $row['a'] . '</td><td>' . $row['b'] . '</td><td>' . $row['mi'] . '</td><td>' . $row['km'] . '</td></tr>';
		}
		$result->close();
	}
?>
				</tbody>
			</table>
		</div>
		<div class="insert">
			<p>Itineraries</p>
			<form action="create_itinerary.php" method="post">
				<label>Departure Time: <input type="text" name="itinerary_etd" value="YYYY-MM-dd HH:mm::ss" /></label>
				<label>Platform: <select name="itinerary_depart">
<?php
	$platform_options = "";
	$result = $conn->query("SELECT p.id AS id, s.name AS station, p.designation AS label FROM platform AS p LEFT JOIN station AS s ON s.id = p.station ORDER BY s.name, p.designation ASC");
	if ($result){
		echo "RESULTS";
		while ($row = $result->fetch_assoc()) {
			$platform_options .= '<option value="' . $row['id'] . '">' . $row['station'] . ':' . $row['label'] . '</option>';
		}
		$result->close();
	}
	echo $platform_options;
?>

				</select></label>
				<br />
				<label>Arrival&nbsp;&nbsp; Time: <input type="text" name="itinerary_eta" value="/YYYY-MM-dd HH:mm::ss" /></label>
				<label>Platform: <select name="itinerary_arrival">
				<?php echo $platform_options; ?>
				</select></label>
				<br />
				<label>Train: <select name="itinerary_train">
<?php
	$result = $conn->query("SELECT id, name FROM train ORDER BY name ASC");
	if ($result) {
		while($row = $result->fetch_assoc()) {
			echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
		}
		$result->close();
	}
?>
				</select></label>
				<br/>
				<input type="submit" />
			</form>	
			<table>
				<thead><tr>
					<th>Depart Time</th><th>Depart Station</th><th>Depart Platform</th>
					<th>Arrival Time</th><th>Arrival Station </th><th>Arrival Platform</th>
					<th>Train</th><th>Status</th>
				</tr></thead>
				<tbody>
<?php
	$result = $conn->query("SELECT * FROM schedule ORDER BY depart_time, depart_station, arrive_time, arrive_station ASC");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			echo '<tr>', 
				'<td>' . $row['depart_time'] . '</td>',
				'<td>' . $row['depart_station'] . '</td>', 
				'<td>' . $row['depart_platform'] . '</td>',
				'<td>' . $row['arrive_time'] . '</td>',
				'<td>' . $row['arrive_station'] . '</td>',
				'<td>' . $row['arrive_platform'] . '</td>',
				'<td>' . $row['train'] . '</td>',
				'<td>' . $row['status'] . '</td>',
				'</tr>';
		}
		$result->close();
	}
?>
				</tbody>
			</table>
		</div>
	</body>
</html>
