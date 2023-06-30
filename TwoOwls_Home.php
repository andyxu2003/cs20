<!DOCTYPE html>
<html>

<head>
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

        #order_form {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        input[type="text"],
        textarea {
            display: block;
            margin-bottom: 10px;
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        #button {
            font-family: serif;
            font-size: 20px;
            font-weight: bold;
            padding: 10px 20px 10px 20px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h1>Two Owls Cafe</h1>
    <div id="hours">Open Daily from 8PM - 3AM</div>
    <div id="order_form">Order Form</div>
    <?php

    $servername = "localhost";
    $username = "uwaerk2zmgkkn";
    $password = "xgzg0am1i0lu";
    $dbname = "dbn0vcrw9e6hfk";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get the food items from the "menu_items" table in the database
    $sql = "SELECT * FROM menu_items";
    $result = mysqli_query($conn, $sql);

    // Form to display food items
    echo '<form method="post" action="">';
    echo '<table>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td><img src="' . $row['image_url'] . '" alt="' . $row['name'] . '" width="100" height="100"> ' . $row['name'] . ' - $' . $row['cost'] . '</td>';
        echo '<td>';
        echo '<select name="' . $row['id'] . '">';
        for ($i = 0; $i <= 10; $i++) {
            $selected = ($i == 0) ? "selected" : "";
            echo "<option value='" . $i . "' " . $selected . ">" . $i . "</option>";
        }
        echo '</select>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';

    // Add fields for user name + special instructions
    echo '<label for="first_name">First Name:</label>';
    echo '<input type="text" id="first_name" name="first_name"><br>';
    echo '<label for="last_name">Last Name:</label>';
    echo '<input type="text" id="last_name" name="last_name"><br>';
    echo '<label for="special_instructions">Special Instructions:</label>';
    echo '<textarea id="special_instructions" name="special_instructions"></textarea><br>';

    // Submit order button
    echo '<input type="submit" name="submit" value="Order" id="button" onsubmit="return validateForm()">';

    echo '</form>';

    // Process the form submission
    if (isset($_POST['submit'])) {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $special_instructions = mysqli_real_escape_string($conn, $_POST['special_instructions']);

        // Check if first name and last name are not empty
        if (!empty($first_name) && !empty($last_name)) {

            // Delete all rows in the "ordered_items" table
            $sql = "DELETE FROM ordered_items";
            mysqli_query($conn, $sql);

            //Delete all rows in "customer_info" table
            $sql = "DELETE FROM customer_info";
            mysqli_query($conn, $sql);

            //Insert customer info into "customer_info" table
            $sql = "INSERT INTO customer_info (first_name, last_name, instructions) VALUES ('$first_name', '$last_name', '$special_instructions')";
            mysqli_query($conn, $sql);

            //Insert name and date into "past_orders" table
            date_default_timezone_set('America/New_York');
            $date = date('Y-m-d H:i:s');
            $sql = "INSERT INTO past_orders (first_name, last_name, date) VALUES ('$first_name', '$last_name', '$date')";
            mysqli_query($conn, $sql);

            // Insert the ordered items into the "ordered_items" table in the database
            foreach ($_POST as $key => $value) {
                if ($value != '' && $key != 'first_name' && $key != 'last_name' && $key != 'special_instructions' && $key != 'submit') {
                    $id = mysqli_real_escape_string($conn, $key);
                    $quantity = mysqli_real_escape_string($conn, $value);

                    //Check if the quantity is greater than 0
                    if ($quantity > 0) {
                        $sql = "INSERT INTO ordered_items (item, quantity, cost) SELECT name, '$quantity', cost * '$quantity' FROM menu_items WHERE id='$id'";
                        mysqli_query($conn, $sql);
                    }
                }
            }
        } else {
            // Display an error message if first name or last name is empty
            echo '<p>Please enter a first name and last name to place your order.</p>';
        }
    }

    // View order details button
    echo "<br>";
    echo "<form action='TwoOwls_orderDetails.php' method='post'>";
    echo "<input type='submit' value='View Order' id='button'>";
    echo "</form>";

    mysqli_close($conn);
    ?>

    <script type="text/javascript">
        // Define the open and close times of the cafe
        const openTime = new Date();
        openTime.setHours(20, 0, 0);
        const closeTime = new Date();
        closeTime.setDate(closeTime.getDate() + 1);
        closeTime.setHours(3, 0, 0);

        function validateOrderForm() {
            // Check if at least one item is selected
            const selectElements = document.getElementsByTagName("select");
            let itemSelected = false;
            for (let i = 0; i < selectElements.length; i++) {
                if (selectElements[i].value > 0) {
                    itemSelected = true;
                    break;
                }
            }
            if (!itemSelected) {
                alert("Please select at least one item to order.");
                return false;

            }

            // Check if the customer's name is filled out
            const firstName = document.getElementsByName("first_name")[0].value.trim();
            const lastName = document.getElementsByName("last_name")[0].value.trim();
            if (firstName === "" || lastName === "") {
                alert("Please provide your first and last name.");
                return false;

            }

            // Check if the order is placed during open hours
            const now = new Date();
            if (now < openTime || now > closeTime) {
                alert("The cafe is currently closed. Please place your order during open hours.");
                return false;
            }

            // Check if the order is placed at least 30 minutes prior to closing
            const minutesToClosing = (closeTime - now) / 1000 / 60;
            if (minutesToClosing < 30) {
                alert("Please place your order at least 30 minutes prior to closing.");
                return false;
            }

            // Messagage to user if form filled out correctly
            alert("Thanks for ordering! Click on the View Order button to get your order details.")
            return true;
        }

        // Attach the validateOrderForm function to the submit event of the form
        const orderForm = document.getElementsByTagName("form")[0];
        orderForm.addEventListener("submit", function(event) {
            if (!validateOrderForm()) {
                event.preventDefault();
            }
        });
    </script>

    <!-- Past order/admin page link -->
    <a href='TwoOwls_Admin.php'>Admin</a>

</body>

</html>