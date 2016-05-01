<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in('member')){redirect_to("../login.php");} ?>
<?php  $member = Member::find_by_id($session->find_id());  ?>
<?php
if(isset($_POST["submit"])){

    $current_id = $member->id; // needed for the updata SQL query (where id = ...)
    $member->first_name = $database->escape_value(trim($_POST["first_name"]));
    $member->last_name = $database->escape_value(trim($_POST["last_name"]));
    $member->full_name = $database->escape_value(trim($_POST["full_name"]));
    $member->id = (int) trim($_POST["id"]);
    $member->password = $database->escape_value(trim($_POST["password"]));
    $member->discription = $database->escape_value(trim($_POST["discription"]));

    // validate
    $required_fields = array('password', 'id', 'first_name', 'last_name', 'full_name');
    $max_length = array("first_name" => 10, "last_name" => 10, "id" => 9, "password" => 30, "full_name" => 100);
    $min_length = array("first_name" => 3, "last_name" => 3, "id" => 5, "password" => 6, "full_name" => 10);
    $member->validate($required_fields, $max_length, $min_length);

    if (empty($member->errors)) {
        $result = $member->update($current_id);
        if ($result) {
            $session->message("update Succeed");
            redirect_to("index.php");
        } else {
            $session->message("update Failed");
            redirect_to("index.php");
        }
    } else {
        $message = join("<br />", $member->errors);
        unset($member->errors);
    }
}

if(isset($_POST['upload_image'])){
    $member->attach_file($_FILES['file_upload']);
    if($member->save()){
        // Success
        $session->message("Photograph uploaded successfully");
        redirect_to("index.php");
    }else{
        // Failure
        $message = join("<br />", $member->errors);
    }
}
?>
<?php include("../layouts/member_header.php"); ?>
    <div id="navigation">
        <?php include("../../includes/member_navigation.php");?>
    </div>
    <div id="page">
        <?php 	 echo output_message($message);	?>

        <h2>Edit member: <?php echo $member->first_name . " " . $member->last_name; ?></h2>
    <span>
        <form action = "manage_account.php" enctype="multipart/form-data" method="POST">

            <input type="hidden" name="MAX_FILE_SIZE" value="<?echo $max_file_size; ?>"

            <p><input type="file" name="file_upload"/></p>

            <input type="submit" name="upload_image" value="upload"/>

        </form>

        <img src="../images/<?php echo $member->id ."/". $member->image_file?>" alt="NOTHING"
             width="150" />
    </span>
        <hr />

        <form action="manage_account.php" method="post">
            <p>First name:
                <input type="text" name="first_name" value="<?php echo $member->first_name; ?>"/>
            </p>

            <p>Last name:
                <input type="text" name="last_name" value="<?php echo $member->last_name; ?>"/>
            </p>

            <p>Your Scholar complete name (must be same as scholar
                <input type="text" name="full_name" size="50" value="<?php echo $member->full_name; ?>"/>
            </p>

            <p>ID:
                <input type="text" name="id" value="<?php echo $member->id; ?>"/>
            </p>


            <p>Password:
                <input type="password" name="password" value="<?php echo $member->password;?>"/>
            </p>

            <p>Description
                <textarea name="discription" rows="10" cols="50">Write something here</textarea>

            </p>
            <input type="submit" name="submit" value="Edit member" />
        </form>
        <br />
        <a href="index.php">Cancle</a>




    </div>
<?php include("../layouts/member_footer.php"); ?>