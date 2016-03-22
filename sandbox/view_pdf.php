<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 12/03/2016
 * Time: 03:01 pm
 */
$file = $_SESSION['pdf_file'];
//$file = 'ABC.pdf';
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename='" . $file . "'");
header("Content-Transfer-Encoding: binary");
header("Accept-Ranges: bytes");
@readfile($file);

?>