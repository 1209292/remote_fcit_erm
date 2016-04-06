<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 14/03/2016
 * Time: 02:57 pm
 */
?>

<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/upload.php");
?>

<?php include("../layouts/member_header.php"); ?>
<?php  if(!$session->is_logged_in()){redirect_to("../member_profile.php");} ?>
<?php  $member = Member::find_by_id($session->find_id());  ?>
<?php  $uploads = Upload::find_uploads_by_member_id($member->id); ?>
<div id="navigation">
    <?php include("../../includes/member_navigation.php");?>
</div>
<div id="page">
    <?php echo output_message($message); ?>
    <h2>Welcome <?php echo $member->first_name ." ".  $member->last_name ?></h2>
    <p> <img src="../images/<?php echo $member->id ."/".$member->image_file; ?>"
             alt="NO IMAGE" width="150"/> </p>
            <?php if($uploads != false){ ?>
<?php   foreach($uploads as $upload): ?>
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="../uploads/<?php
                    echo htmlentities($upload->member_id)
                        ."/".$upload->filename; ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><a href="delete_upload.php?file_id=<?php echo $upload->id;?>"
                    onclick="return confirm('Are you sure you want to delete this item?');"> Delete </a></p>
<?php endforeach; ?>
<?php       }else{
                echo output_message("You have no uploads available!");
            } // if curly brace ?>

    <p><a href="file_upload.php">+ upload file</a></p>

</div>
<?php include("../layouts/member_footer.php"); ?>
