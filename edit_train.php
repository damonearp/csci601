<?php
  $id = $_GET['id'];
	$conn = new mysqli("localhost", "csci601", "csci601", "csci601");
        if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
        }
  $attrs = $conn->query("select name,capacity,speed from train where id=$id")->fetch_array();
  $name = $attrs[0];
  $capacity = $attrs[1];
  $speed = $attrs[2];
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
            <p>Edit Train</p>
            <form method="POST" action="update_train.php">
                <input type="hidden" name="train_id" value="<?php echo "$id"; ?>" />
                <label>Name: <input type="text" name="train_name" value="<?php echo "$name"; ?>" /></label>
                <label>Capacity: <input type="text" name="train_capacity" value="<?php echo "$capacity"; ?>" /></label>
                <label>Speed: <input type="text" name="train_speed" value="<?php echo "$speed"; ?>" /></label>
                <input type="submit" />
            </form>
        </div>
    </body>
</html>
