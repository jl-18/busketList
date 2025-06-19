<?php
// Include your database connection
include 'config.php';

// Query the 'bus' table
$result = $conn->query("SELECT * FROM bus");

// Check if rows were returned
if ($result->num_rows > 0) {
    // Display each row
    while ($row = $result->fetch_assoc()) {
        echo "Bus ID: " . $row['busid'] . "<br>";
    }
} else {
    echo "No buses found.";
}

// Close the database connection
$conn->close();
?>
