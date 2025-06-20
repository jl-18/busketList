<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "busket-list";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getUniqueLocations($connection) {
    $locations = array();
    $sql = "SELECT origin as location FROM routes UNION SELECT destination as location FROM routes";
    $result = $connection->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $locations[] = $row['location'];
        }
    }
    return $locations;
}
?>