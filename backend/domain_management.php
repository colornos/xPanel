<?php
echo "<h2>Domain Management</h2>";

$domains = file('/etc/hosts');

echo "<ul>";
foreach ($domains as $domain) {
    echo "<li>" . htmlspecialchars($domain) . "</li>";
}
echo "</ul>";
?>
