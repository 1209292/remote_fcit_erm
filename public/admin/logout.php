<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php
if(!$session->is_logged_in()){
    // message to tell him that he is not logged in from the first place
    redirect_to("../member_profile.php");
}
$session->logout();
redirect_to("member_profile.php");

?>