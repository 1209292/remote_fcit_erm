<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in()){redirect_to("../member_profile.php"); } ?>
<?php  $member = Member::find_by_id($session->find_id());  ?>
<?php include("../layouts/member_header.php"); ?>
    <div id="navigation">
        <?php include("../../includes/member_navigation.php");?>
    </div>
    <div id="page">
        <?php echo output_message($message); ?>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <p>Publication Title:
                <input type="text" name="title"/>
            </p>

            <p>Publication Website:
                <input type="text" name="website" />
            </p>

            <p>URL:
                <input type="text" name="url" />
            </p>


            <p>Date
                <input type="date" name="date" />
            </p>

            <input type="submit" name="submit" value="Add Publication" />
        </form>


    </div>
<?php include("../layouts/member_footer.php"); ?>