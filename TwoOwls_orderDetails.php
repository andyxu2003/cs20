<!DOCTYPE html>
<html>

<head>
    <title>Order Details</title>
    <style>
        body {
            font-family: serif;
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        #hours {
            font-size: 20px;
            margin-bottom: 40px;
        }

        #order_details {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            margin: 0 auto;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
            border: 1px solid black;
        }

        .subtotal {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Two Owls Cafe</h1>
    <div id="hours">Open Daily from 8PM - 3AM</div>
    <div id="order_details">Order Details</div>
    <?php

    $servername = "localhost";
    $username = "uwaerk2zmgkkn";
    $password = "xgzg0am1i0lu";
    $dbname = "dbn0vcrw9e6hfk";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get ordered items from the database
    $sql = "SELECT item, quantity, cost FROM ordered_items";
    $result = $conn->query($sql);

    // Table for ordered items
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Item</th><th>Quantity</th><th>Cost</th></tr>";
        $subtotal = 0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["item"] . "</td><td>" . $row["quantity"] . "</td><td>$" . number_format($row["cost"], 2) . "</td></tr>";
            $subtotal += $row['cost'];
        }
        echo "<tr class='subtotal'><td colspan='2'>Subtotal:</td><td>$" . number_format($subtotal, 2) . "</td></tr>";
        echo "</table>";
    } else {
        echo "0 results";
    }

    // Calculate tax and total cost
    $tax_rate = 0.0625;
    $tax = $subtotal * $tax_rate;
    $total = $subtotal + $tax;
    echo "<p style='text-align: center;'>Tax: " . number_format(($tax_rate * 100), 2) . "%</p>";
    echo "<p style='text-align: center; font-weight: bold;'>Total: $" . number_format($total, 2) . "</p>";

    // Calculate pickup time
    $user_timezone = new DateTimeZone('America/New_York');
    $pickup_time = new DateTime('now', $user_timezone);
    $pickup_time->add(new DateInterval('PT15M')); // Add 15 minutes
    echo "<p style='text-align: center;'>Pickup time: " . $pickup_time->format('h:i A') . "</p>";

    mysqli_close($conn);
    ?>

</body>

</html>