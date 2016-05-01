
<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/super_user.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>

<?php  if(!$session->is_logged_in('admin')){ redirect_to("../login.php"); } ?>
<?php
if(isset($_GET["super_user_id"])){
    $super_user = SuperUser::find_by_id($_GET["super_user_id"]);
    $result = $super_user->delete();
    if($result && $database->affected_rows() == 1){
        $message = "Deletion Succeed";
        $session->message($message);
        redirect_to("manage_content.php?super_user=1");
    } else {
        $session->message("Deletion Failed");
        redirect_to("manage_content.php?super_user=1");
    }

}
else {
    redirect_to("manage_content.php");
}
?>