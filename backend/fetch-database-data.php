<?php
require 'database_config.php';

header('Content-Type: application/json');

$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

$sql = "SHOW DATABASES";
$databases = $conn->query($sql);
$tree = [];

if ($databases->num_rows > 0) {
    while ($dbRow = $databases->fetch_row()) {
        $dbName = $dbRow[0];
        $dbNode = ['text' => "📁 $dbName", 'nodes' => []];

        $conn->select_db($dbName);
        $tablesResult = $conn->query("SHOW TABLES");

        if ($tablesResult->num_rows > 0) {
            while ($tableRow = $tablesResult->fetch_row()) {
                $dbNode['nodes'][] = ['text' => "📄 " . $tableRow[0]];
            }
        }
        $tree[] = $dbNode;
    }
}

$conn->close();
echo json_encode($tree);
?>
