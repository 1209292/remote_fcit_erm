
<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>

<?php  if(!$session->is_logged_in()){redirect_to("../member_profile.php"); } ?>
<?php
			if($_GET["member_id"]){
			$member = Member::find_by_id($_GET["member_id"]);
			$result = $member->delete();
			if($result && $database->affected_rows() == 1){
				$message = "Deletion Succeed";
                if(!$member->destroy_assets($_GET["member_id"])){
                    $message .= ", BUT physical files could not be deleted";
                }
                $session->message($message);
				redirect_to("manage_content.php?members=1");
			} else {
                $session->message("Deletion Failed");
				redirect_to("manage_content.php?members=2");
			}
			
	}
	  else {
		  redirect_to("manage_content.php");
	  }
  ?>