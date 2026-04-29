<?php
echo "✅ Testing includes...<br>";

// Test if includes exist
if (file_exists('../includes/header.php')) {
    echo "✅ header.php exists<br>";
} else {
    echo "❌ header.php NOT found<br>";
}

if (file_exists('../includes/auth_check.php')) {
    echo "✅ auth_check.php exists<br>";
} else {
    echo "❌ auth_check.php NOT found<br>";
}

// Try to include them
echo "<br>Attempting to include...<br>";
include('../includes/header.php');
include('../includes/auth_check.php');
echo "✅ Includes loaded successfully!<br>";
?>
