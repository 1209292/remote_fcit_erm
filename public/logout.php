<?php
require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");
?>

<?php
if($session->is_logged_in()){
    $session->logout();
}
redirect_to("index.php");
?>
