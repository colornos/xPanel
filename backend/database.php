<?php
require 'database_config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SHOW DATABASES";
$result = $conn->query($sql);

echo "<h2>Database Management</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>" . $row['Database'] . "</li>";
}
echo "</ul>";

$conn->close();
?>
