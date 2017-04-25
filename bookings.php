<?php

$select_reservations = <<<EOT
    SELECT 
        r.passenger AS passenger, 
        start.name AS start, 
        s.departure AS departing, 
        end.name AS end, 
        eta(s.departure, train.speed, track.distance) AS eta, 
        train.name AS train
    FROM
        reservation AS r
            LEFT JOIN schedule AS s ON s.id = r.schedule
            LEFT JOIN train AS train ON train.id = s.train
            LEFT JOIN track AS track ON track.id = s.track
                LEFT JOIN station AS start ON start.id = track.start
                LEFT JOIN station AS end ON end.id = track.end
    ORDER BY s.departure, r.passenger ASC
EOT;


    $conn = new mysqli("localhost", "csci601", "csci601", "csci601");
    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }

    $station_options = "";
    $result = $conn->query("SELECT id, name FROM station ORDER BY name ASC");
    while ( $row = $result->fetch_assoc() ) {
        $station_options .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Book A Choooo Chooooo</title>
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
        <div class="insert">
            <p>Book</p>
            <form action="book_travel.php" method="post">
                <label> Passenger: <input type="text" name="book_name" /></label> 
                <label> Date: <input type="text" name="book_date" /></label>
                <label> From: <select name='book_from'><?php echo $station_options; ?></select></label>
                <label> To: <select name='book_to'><?php echo $station_options; ?></select></label>
                <input type="submit" />
            </form>
            
            <table>
                <thead><tr><th>Passenger</th><th>From</th><th>Departing</th><th>To</th><th>Arriving</th><th>Train</th></tr></thead>    
                <tbody>
                    <?php
                        $result = $conn->query($select_reservations);
                        if (!$result) {
                            die("Failed to query reservations: " . $conn->error);
                        }
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr><td>' . $row['passenger'] . '</td><td>' . $row['start'] . '</td><td>'
                                . $row['departing'] . '</td><td>' . $row['end'] . '</td><td>' . $row['eta'] 
                                . '</td><td>' . $row['train'] . '</td></tr>';
                        } 
                    ?>        
                </tbody>
            </table>
        </div>
    </body>
</html>
