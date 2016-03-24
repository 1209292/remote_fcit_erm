<?php
require_once "../includes/upload.php";
require_once "../includes/member.php";
require_once "../includes/database_object.php";
$up = new Upload();
$member = Upload::find_uploads_by_member_id(191919);
if($member){
    echo "True <br />";
    var_dump($member);
}else{
    echo "false <br />";
}
echo "<hr >";
echo "<hr >";
