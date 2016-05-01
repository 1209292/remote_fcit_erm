
<?php
require_once "../includes/database.php";
$sql = "INSERT INTO admin (id, first_name, last_name, password) VALUES (1, 'a', 'b', 'a1')";
$database->query($sql);
?>
