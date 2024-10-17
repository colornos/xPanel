<?php
$dir = "/var/www/html/";

if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
}

$files = scandir($dir);

echo "<h2>File Manager - $dir</h2>";
echo "<ul>";
foreach ($files as $file) {
    if ($file == "." || $file == "..") continue;
    echo "<li><a href='?dir=$dir/$file'>$file</a></li>";
}
echo "</ul>";
?>
