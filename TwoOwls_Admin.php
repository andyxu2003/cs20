<!DOCTYPE html>
<html>

<head>
    <title>Order Details</title>
</head>

<body>
    <h1>Past Orders</h1>
    <?php

    $servername = "localhost";
    $username = "uwaerk2zmgkkn";
    $password = "xgzg0am1i0lu";
    $dbname = "dbn0vcrw9e6hfk";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM past_orders";

    $result = $conn->query($sql);

    // Table for past orders
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["first_name"] . "</td><td>" . $row["last_name"] . "</td><td>" . $row["date"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }

    $conn->close();
    ?>

</body>

</html>