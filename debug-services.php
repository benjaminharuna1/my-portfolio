<?php
require 'config.php';

echo "<pre>";
echo "Services in database:\n";
$result = $conn->query("SELECT id, title, icon, tech_icons FROM services");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . $row['title'] . "\n";
    echo "Icon: " . $row['icon'] . "\n";
    echo "Tech Icons: " . $row['tech_icons'] . "\n";
    echo "---\n";
}
echo "</pre>";
?>
