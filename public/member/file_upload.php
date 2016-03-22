<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 14/03/2016
 * Time: 03:39 am
 */
?>
<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/upload.php");
?>
<?php  if(!$session->is_logged_in()){redirect_to("../login.php");} ?>
<?php  $member = Member::find_by_id($session->find_id());  ?>

<?php // Form proccessing
$message="";
if(isset($_POST['submit'])){
    $file = new Upload();
    $file->attach_file($_FILES['file_upload'], $member->id);
    if($file->save()){
        $session->message("Uploaded Successfully");
        redirect_to("uploads.php");
    }else{
        $message = join("<br />", $file->errors);
    }
}
?>
<?php include("../layouts/member_header.php"); ?>

<div id="navigation">
    <br />
    <a href="index.php"><strong><p>Home</p></strong></a>
    <a href="manage_account.php"><strong><p>Manage Account</p></strong></a>
    <a href="index.php"><strong><p>Publications</p></strong></a>
    <a href="uploads.php"><strong><p>Uploads</p></strong></a>
    <a href="index.php"><strong><p>STAT</p></strong></a>
    <a href="../logout.php"><strong><p>Logout</p></strong></a>
</div>

<div id="page">
    <?php echo $message; ?>
    <?php echo $session->message();?>
    <p> Hint: Accepted Files allowed is ('pdf' 'doc' 'docx' 'ppt' 'pptx'), else
    will not be uploaded. <br /> Max File size is 1M. </p>
    <form action = "<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="POST">

        <input type="hidden" name="MAX_FILE_SIZE" value="<?echo $max_file_size; ?>"

        <p><input type="file" name="file_upload"/></p>

        <p>Caption: <input type="text" name="caption" value=""/></p>

        <input type="submit" name="submit" value="upload"/>

    </form>

    <a href="index.php">Cancle</a>
</div>
<?php include("../layouts/member_footer.php"); ?>
