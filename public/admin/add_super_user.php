<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/super_user.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");

?>

<?php  if(!$session->is_logged_in('admin')){ redirect_to("../login.php"); } ?>

<?php
if(isset($_POST["submit"])){

    $first_name = $database->escape_value(trim($_POST["first_name"]));
    $last_name = $database->escape_value(trim($_POST["last_name"]));
    $id = (int) trim($_POST["id"]);
    $password =  $database->escape_value(trim($_POST["password"]));

    $new_super_user = SuperUser::construct_with_args($id, $password, $first_name, $last_name);
    $required_fields = array('password', 'id', 'first_name', 'last_name');
    $max_length = array("first_name" => 10, "last_name" => 10, "id" => 9);
    $min_length = array("first_name" => 3, "last_name" => 3, "id" => 5);
    $new_super_user->validate($required_fields, $max_length, $min_length);
    $new_member->validate_password($password);
    if(!empty($new_super_user->errors)){
        $message = join("<br />", $new_super_user->errors);
    }else {
        $result = $new_super_user->create();
        if ($result) {
            $session->message("Insertion Succeed");
            redirect_to("manage_content.php?super_user=1");
        } else {
            $session->message("Insertion Failed");
            redirect_to("manage_content.php?super_user=1");
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

            <h2>Add Super User</h2>
            <form action="add_super_user.php" method="post">
                <p>First name:
                    <input type="text" name="first_name" value=""/>
                </p>

                <p>Last name:
                    <input type="text" name="last_name" value=""/>
                </p>

                <p>ID:
                    <input type="text" name="id" value=""/>
                </p>

                <p>Password:
                    <input type="password" name="password" value=""/>
                </p>

                <input type="submit" name="submit" value="Add member" />
            </form>
            <br />
            <a href="manage_content.php">Cancle</a>
        </div>
    </div>


<?php include("../layouts/admin_footer.php"); ?>