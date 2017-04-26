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
$create_schedule = <<<EOT
	CREATE TABLE IF NOT EXISTS schedule 
	(
        id BIGINT NOT NULL AUTO_INCREMENT,
        departure DATETIME NOT NULL,
        train BIGINT NOT NULL REFERENCES train(id),
        track BIGINT NOT NULL REFERENCES track(id),
        reserved MEDIUMINT NOT NULL DEFAULT 0, 
		PRIMARY KEY (departure, train, track),
        UNIQUE (id)
	)
EOT;
$create_reservation = <<<EOT
    CREATE TABLE IF NOT EXISTS reservation 
    (
        id CHAR(36) NOT NULL,
        passenger VARCHAR(255) NOT NULL,
        schedule BIGINT NOT NULL REFERENCES schedule(id),
        PRIMARY KEY (id, passenger, schedule)
    )
EOT;
$create_pretty = <<<EOT
    CREATE OR REPLACE VIEW pretty AS
        SELECT
            train.name AS train, 
            start.name AS leaving, 
            schedule.departure AS departure,
            end.name AS arriving, 
            eta(schedule.departure, train.speed, track.distance) AS eta,
            train.capacity - schedule.reserved AS open
        FROM
            schedule AS schedule
                LEFT JOIN train AS train ON train.id = schedule.train
                LEFT JOIN track AS track ON track.id = schedule.track
                    LEFT JOIN station AS start ON track.start = start.id
                    LEFT JOIN station AS end ON track.end = end.id
        ORDER BY schedule.departure ASC 
EOT;
$drop_reserved_trigger = "DROP TRIGGER IF EXISTS reserved_trigger";
$create_reserved_trigger = <<<EOT
    CREATE TRIGGER reserved_trigger BEFORE INSERT ON reservation 
        FOR EACH ROW
        UPDATE schedule SET reserved = reserved + 1 WHERE id = NEW.schedule 
EOT;
$drop_unreserved_trigger = "DROP TRIGGER IF EXISTS unreserved_trigger";
$create_unreserved_trigger = <<<EOT
    CREATE TRIGGER unreserved_trigger AFTER DELETE ON reservation
        FOR EACH ROW
        UPDATE schedule SET reserved = reserved - 1 WHERE id = OLD.schedule
EOT;
$drop_mph_to_kmh = "DROP FUNCTION IF EXISTS mph_to_kmh";
$create_mph_to_kmh = <<<EOT
	CREATE FUNCTION mph_to_kmh (mph BIGINT) RETURNS BIGINT RETURN mph * 1.60934
EOT;
$drop_kmh_to_mph = "DROP FUNCTION IF EXISTS kmh_to_mph";
$create_kmh_to_mph = <<<EOT
	CREATE FUNCTION kmh_to_mph (kmh BIGINT) RETURNS BIGINT RETURN kmh * 0.621371
EOT;
$drop_eta = "DROP FUNCTION IF EXISTS eta";
$create_eta = <<<EOT
    CREATE FUNCTION eta (start DATETIME, speed BIGINT, distance BIGINT) RETURNS DATETIME 
        RETURN start + INTERVAL (CAST(distance AS DOUBLE) / CAST(speed AS DOUBLE) * 60 * 60) SECOND
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
        
            td {
                text-align: left;
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
                if (!$conn->query($create_schedule)) {
                    die("Failed to create schedule table: " . $conn->error);
                }
                echo "<li>Create table schedule: ok</li>";
                if (!$conn->query($create_reservation)) {
                    die("Failed to create reservation table: " . $conn->error);
                }
                echo "<li>Create table reservation: ok</li>";
                if (!$conn->query($drop_reserved_trigger) || !$conn->query($create_reserved_trigger)) {
                    die("Failed to create reserved trigger: " . $conn->error);
                }
                echo "<li>Create trigger reserved: ok</li>";
                if (!$conn->query($drop_unreserved_trigger) || !$conn->query($create_unreserved_trigger)) {
                    die("Failed to create unreserved trigger: " . $conn->error);
                }
                echo "<li>Create trigger unreserved: ok</li>";
                if (!$conn->query($create_pretty)) {
                    die("Failed to create pretty view: " . $conn->error);
                }
                echo "<li>Create view pretty: ok</li>";
                if (!$conn->query($drop_mph_to_kmh) || !$conn->query($create_mph_to_kmh)) {
                    die("Failed to create function mph_to_kmh");
                }
                echo "<li>Create function mph_to_kmh: ok</li>";
                if (!$conn->query($drop_kmh_to_mph) || !$conn->query($create_kmh_to_mph)) {
                    die("Failed to create function kmh_to_mph");
                }
                echo "<li>Create function kmh_to_mph: ok</li>";
                if (!$conn->query($drop_eta) || !$conn->query($create_eta)) {
                    die("Failed to create function eta");
                }
                echo "<li>Create function eta: ok</li>";
            ?>
		</ol>
		<h3>Operations</h3>


		
		<div id="stations" class="insert">
			<p>Stations</p>
			<form action="create_station.php" method="post">
				<label>New Station: <input type="text" name="station_name" /></label>
				<input type="submit" />
			</form>
			<table>
				<thead><tr><th>Station</th></thead>
				<tbody>
                    <?php
                        $result = $conn->query("SELECT id, name FROM station ORDER BY name ASC");
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>$row[name]<td><a href='edit_station.php?id=$row[id]'>edit</a></td></tr>";
                            }
                            $result->close();
                        }
                    ?>
				</tbody>
			</table>
		</div>




		<div id="trains" class="insert">
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
                        $result = $conn->query("SELECT id, name, capacity, speed AS kmh, kmh_to_mph(speed) AS mph FROM train ORDER BY name ASC");
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>$row[name]</td><td>$row[capacity]</td><td>$row[mph]</td><td>$row[kmh]</td><td><a href='edit_train.php?id=$row[id]'>edit</a><br /><a href='delete_train.php?id=$row[id]'>delete</a></td></tr>";
                            }
                            $result->close();
                        }
                    ?>
				</tbody>
			</table>
		</div>





		<div id="platforms" class="insert">
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





		<div id="tracks" class="insert">
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
                            $seen = array();
                            while ($row = $result->fetch_assoc()) {
                                if (!array_key_exists($row['b'], $seen) || !$seen[$row['b']] == $row['a']) {
                                    echo '<tr><td>' . $row['a'] . '</td><td>' . $row['b'] . '</td><td>' . $row['mi'] . '</td><td>' . $row['km'] . '</td></tr>';
                                    $seen[$row['a']] = $row['b'];
                                }
                            }
                            $result->close();
                        }
                    ?>
				</tbody>
			</table>
		</div>






        <div id="schedule" class="insert">
            <p>Schedule</p>
            <form action="schedule_train.php" method="post">
                <label>Departure Time: <input type="text" name="schedule_etd" value="YYYY-MM-dd HH:mm::ss" /></label>
                <br/>
                <label>Leaving From: <select name="schedule_source">
                    <?php
                        $station_options = "";
                        $result = $conn->query("SELECT id, name FROM station ORDER BY name ASC");
                        if ($result){
                            while ($row = $result->fetch_assoc()) {
                                $station_options .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            $result->close();
                        }
                        echo $station_options;
                    ?>
                </select></label>
                <label>Arriving At: <select name="schedule_dest"><?php echo $station_options; ?></select></label>
                <label>Train: <select name="schedule_train">
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
                <thead><tr><th>Time</th><th>Departing</th><th>Arriving</th><th>ETA</th><th>Train</th><th>Open</tr></thead>
                <tbody>
                    <?php
                        $result = $conn->query("SELECT * from pretty");
                        if ($result) {
                            while($row = $result->fetch_assoc()) {
                                echo '<tr><td>' . $row['departure'] . '</td><td>' . $row['leaving'] . '</td><td>' 
                                    . $row['arriving'] . '</td><td>' . $row['eta'] . '</td><td>' . $row['train'] 
                                    . '</td><td>' . $row['open'] . '</td></tr>';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>








	</body>
</html>
