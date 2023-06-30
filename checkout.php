<!DOCTYPE html>
<html>
  <head>
    <title>Checkout Page</title>
  </head>
  <body>
    <h1>Checkout Page</h1>
    <?php
      // Connect to the database
      $servername = "localhost";
      $username = "uwaerk2zmgkkn";
      $password = "xgzg0am1i0lu";
      $dbname = "dbb3qgv0axgmcw";

      $conn = new mysqli($servername, $username, $password, $dbname);
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      // Retrieve data from the "ingredients" table
      $sql = "SELECT name, image, price FROM ingredients";
      $result = $conn->query($sql);

      // Display the retrieved data in an HTML table
      if ($result->num_rows > 0) {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>Image</th>";
        echo "<th>Price</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".$row["name"]."</td>";
          echo "<td><img src='".$row["image"]."' alt='".$row["name"]."' width='100'></td>";
          echo "<td>".$row["price"]."</td>";
          echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
      } else {
        echo "0 results";
      }

      $conn->close();
    ?>
  </body>
</html>
