<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php include("../layouts/member_header.php"); ?>
<?php  if(!$session->is_logged_in()){redirect_to("../login.php");} ?>
<?php  $member = Member::find_by_id($session->find_id());  ?>
    <div id="navigation">
        <?php include("../../includes/member_navigation.php");?>
    </div>
    <div id="page">
        <?php output_message($message); ?>
    <h2>Welcome <?php echo $member->first_name ." ".  $member->last_name ?></h2>

    <p> <img src="../images/<?php echo $member->image_file; ?>" alt="NO IMAGE"
        width="150"/> </p>
    <p> First Name: <?php echo $member->first_name ?></p>
    <p> Last Name: <?php echo $member->last_name ?></p>
    <p> ID: <?php echo $member->id ?></p>
    <p> Password: <?php echo $member->password ?></p>
    <p> Description: <?php echo $member->description ?></p>
    <p> Email: <?php echo $member->email ?></p>
    </div>
<?php include("../layouts/member_footer.php"); ?>