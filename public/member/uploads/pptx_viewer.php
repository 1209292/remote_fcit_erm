<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 14/03/2016
 * Time: 02:18 pm
 */
$file = $_GET['file_name'];

header("Content-type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
header("Content-Disposition: inline; filename='" . $_GET['file_name'] . "'");
header("Content-Transfer-Encoding: binary");
header("Accept-Ranges: bytes");
@readfile($file);

?>