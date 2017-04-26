<?php
  $station_id = $_GET['id'];
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
  $station_name = $conn->query("select name from station where id=$station_id")->fetch_array()[0];
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
            <p>Edit Station</p>
            <form method="POST" action="update_station.php">
                <input type="hidden" name="station_id" value="<?php echo "$station_id"; ?>" />
                <label>Name: <input type="text" name="station_name" value="<?php echo "$station_name"; ?>" /></label>
                <input type="submit" />
            </form>
        </div>
    </body>
</html>
