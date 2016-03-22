
<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>

<?php  if(!$session->is_logged_in()){redirect_to("login.php"); } ?>
<?php 		

			if($_GET["id"]){
			
			$member = $_SESSION["count" . $_GET["id"]];

			$result = $member->delete();
			
			if($result && $database->affected_rows() == 1){
				$session->message("Deletion Succeed");
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