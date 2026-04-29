<?php
echo "✅ PHP is working!<br>";
echo "Current directory: " . getcwd() . "<br>";
echo "Files in admin folder: <br>";
$files = scandir("admin");
print_r($files);
?>
