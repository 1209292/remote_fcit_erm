<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in()){redirect_to("../member_profile.php"); } ?>
<?php
if(!isset($_GET['member_id'])) { redirect_to("all_profiles.php"); }
$member = Member::find_by_id($_GET['member_id']);
if(!$member){
    $session->message("Not able to find member with the id: ". $_GET['member_id']);
    redirect_to("all_profiles.php");
}
?>

<?php include("../layouts/admin_header.php"); ?>

    <div id="main">

        <div id="navigation">
            <?php include("../../includes/admin_navigation.php"); ?>
        </div>

        <div id="page">

            <h2><?php echo $member->first_name ." ".  $member->last_name ?></h2>

            <p> <img src="../images/<?php echo $member->id ."/". $member->image_file; ?>"
                     alt="NO IMAGE" width="150"/> </p>
            <p> First Name: <?php echo $member->first_name ?></p>
            <p> Last Name: <?php echo $member->last_name ?></p>
            <p> Full Name: <?php echo $member->full_name ?></p>
            <p> ID: <?php echo $member->id ?></p>
            <p> Password: <?php echo $member->password ?></p>
            <p> Description: <?php echo $member->description ?></p>
            <p> Messing: Publications & uploads & info about them</p>

        </div>
    </div>
    </div>



<?php include("../layouts/admin_footer.php"); ?>