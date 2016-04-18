<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");

 ?>

<?php  if(!$session->is_logged_in()){ redirect_to("../member_profile.php"); } ?>
		
<?php
if(isset($_POST["submit"])){

        $first_name = $database->escape_value(trim($_POST["first_name"]));
        $last_name = $database->escape_value(trim($_POST["last_name"]));
        $full_name = $database->escape_value(trim($_POST["full_name"]));
        $id = (int) trim($_POST["id"]);
        $password =  $database->escape_value(trim($_POST["password"]));
        $discription = $database->escape_value(trim($_POST["discription"]));

        $new_member = Member::construct_with_args($id, $password, $first_name, $last_name, $full_name);
        $required_fields = array('password', 'id', 'first_name', 'last_name', 'full_name');
        $max_length = array("first_name" => 10, "last_name" => 10, "id" => 9, 'full_name' => 100);
        $min_length = array("first_name" => 3, "last_name" => 3, "id" => 5, "full_name" => 10);
        $new_member->validate($required_fields, $max_length, $min_length);
        //$new_member->validate_password($password);
        if(!empty($new_member->errors)){
            $message = join("<br />", $new_member->errors);
        }else {
            $result = $new_member->create();
            $assets_folders = $new_member->create_assets($id);
            if ($result) {
                if(!$assets_folders['images'] && $assets_folders['uploads']) {
                    $session->message("Insertion Succeed BUT there was an error on creating image folder");
                }elseif(!$assets_folders['uploads'] && $assets_folders['images']) {
                    $session->message("Insertion Succeed BUT there was an error on creating upload folder");
                }elseif(!$assets_folders['uploads'] && !$assets_folders['images']){
                    $session->message("Insertion Succeed BUT there was an error on creating image and upload folders");
                }else{
                    $session->message("Insertion Succeed");
                }
                redirect_to("manage_content.php?members=1");
            } else {
                $session->message("Insertion Failed");
                redirect_to("manage_content.php?members=1");
            }
        }
    }
	?>


    <?php include("../layouts/admin_header.php"); ?>
    <div id="main">
<div id="navigation">
 <!-- we could embedd the code here, but we choose to make navigation function (find it in ) better to have a look on it -->
 
	
</div>
		<div id="page">
            <?php 	 echo output_message($message);	?>

		<h2>Add Member</h2>
		 <form action="add_member.php" method="post">
		  <p>First name:
			<input type="text" name="first_name" value=""/>
		  </p>
		  
		  <p>Last name:
			<input type="text" name="last_name" value=""/>
		  </p>

             <p>Your Scholar complete name (must be same as scholar
                 <input type="text" name="full_name" size="50" value=""/>
             </p>
		  
		  <p>ID:
			<input type="text" name="id" value=""/>
		  </p>
		  
		  <p>Password:
			<input type="password" name="password" value=""/>
		  </p>
		  
		  <p>Discription
			<textarea name="discription" rows="10" cols="50">Write something here</textarea>
			
		  </p>
			<input type="submit" name="submit" value="Add member" />
		 </form>
		 <br />
		 <a href="manage_content.php">Cancle</a>
		</div>
</div>


<?php include("../layouts/admin_footer.php"); ?>