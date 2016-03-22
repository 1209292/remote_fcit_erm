<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 15/03/2016
 * Time: 01:53 am
.rar    application/x-rar-compressed, application/octet-stream
.zip    application/zip, application/octet-stream*/

$file = $_GET['file_name'];

header("Content-type: application/octet-stream");
header("Content-Disposition: inline; filename='" . $_GET['file_name'] . "'");
header("Content-Transfer-Encoding: binary");
header("Accept-Ranges: bytes");
@readfile($file);

