	
<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in()){redirect_to("login.php"); } ?>


	<?php if(empty($_GET['id'])){
        $session->message("Please select a member first");
        redirect_to("manage_content.php?members=1");
    }
        $count = $_GET["id"];
        $member = $_SESSION["count" . $count];
        $current_id = $member->id;
    ?>
	<?php 
	
	if(isset($_POST["submit"])) {

        $member->first_name = $_POST["first_name"];
        $member->last_name = $_POST["last_name"];
        $member->id = $_POST["id"];
        $member->password = $_POST["password"];
        $member->discription = $database->escape_value($_POST["discription"]);

        // validate
        $required_fields = array('password', 'id', 'first_name', 'last_name');
        $max_length = array("first_name" => 10, "last_name" => 10, "id" => 9, "password" => 30);
        $min_length = array("first_name" => 3, "last_name" => 3, "id" => 5, "password" => 6);
        $member->validate($required_fields, $max_length, $min_length);

        if (empty($member->errors)) {
            $result = $member->update($current_id);
            if ($result) {
                $session->message("update Succeed");
                redirect_to("manage_content.php?members=1");
            } else {
                $session->message("update Failed");
                redirect_to("manage_content.php?members=1");
            }
        } else {
            $message = join("<br />", $member->errors);
            unset($member->errors);
        }
    }/*else {
		// get request
		$_SESSION["message"] = "select a member first to edit";
		redirect_to("manage_content.php?members=1");
		
	} */
	
?>
<?php include("../layouts/admin_header.php"); ?>
<div id="main">
		<div id="navigation">
		
		</div>
		<div id="page">

            <?php 	 echo output_message($message);	?>
			
		<h2>Edit member: <?php echo $member->first_name . " " . $member->last_name; ?></h2>
		 <form action="edit_member.php?id=<?php echo $count; ?>" method="post">
		  <p>First name:
			<input type="text" name="first_name" value="<?php echo $member->first_name; ?>"/>
		  </p>
		  
		  <p>Last name:
			<input type="text" name="last_name" value="<?php echo $member->last_name; ?>"/>
		  </p>
		  
		  <p>ID:
			<input type="text" name="id" value="<?php echo $member->id; ?>"/>
		  </p>
		  
		  
		  <p>Password:
			<input type="password" name="password" value="<?php echo $member->password;?>"/>
		  </p>
		  
		  <p>Discription
			<textarea name="discription" rows="10" cols="50">Write something here</textarea>
			
		  </p>
			<input type="submit" name="submit" value="Edit member" />
		 </form>
		 <br />
		 <a href="manage_content.php">Cancle</a>
		 &nbsp
		 &nbsp
		 <!-- simple error: we send $_GET["id"], to get session for corresponding member -->
		 <a href="delete_member.php?id=<?php echo
		  $_GET["id"]; ?>" onclick="return confirm('Are you sure ?');">Delete member</a>
		</div>
		</div>


<?php include("../layouts/admin_footer.php"); ?>