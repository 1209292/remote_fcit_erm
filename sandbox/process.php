<?php
require_once "../includes/upload.php";
require_once "../includes/member.php";
require_once "../includes/database_object.php";
$number1 = 123456789; $number2 = "email-symbol-red-envelope-17249668.jpg";
$target_path = $_SERVER['DOCUMENT_ROOT'] . "fcit_erm/public/images/" .
    $number1 . "/" . $number2;
echo $target_path;
echo "<br/>";
echo unlink($target_path) ? true : false;
echo "<br/>";
echo $_SERVER['DOCUMENT_ROOT'];
