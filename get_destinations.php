<?php
include 'db_connect.php';

if (isset($_GET['origin'])) {
    $origin = $conn->real_escape_string($_GET['origin']);
    $sql = "SELECT destination FROM routes WHERE origin = '$origin'";
    $result = $conn->query($sql);

    $destinations = array();
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row['destination'];
    }

    echo json_encode($destinations);
}
?>
