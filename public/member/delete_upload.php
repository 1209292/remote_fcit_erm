<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 26/03/2016
 * Time: 09:33 am
 */

require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/upload.php");
?>

<?php  if(!$session->is_logged_in()){redirect_to("../member_profile.php");} ?>
<?php  $upload = Upload::find_by_id($_GET["file_id"]); ?>
<?php  $id = $session->find_id(); ?>
<?php include("../layouts/member_header.php"); ?>
<div id="navigation">
    <?php include("../../includes/member_navigation.php");?>
</div>
<div id="page">

<?php
if($upload) {
    if($upload->delete()){
        $message = "Upload was deleted successfully";
        if(!$upload->destroy()){
            $message .= ", But the phisical file could not be deleted";
        }
        $session->message($message);
        redirect_to("uploads.php");
    }

}else{
    $session->message("No upload match!");
    redirect_to("uploads.php");
}?>

</div>
<?php include("../layouts/member_footer.php"); ?>
