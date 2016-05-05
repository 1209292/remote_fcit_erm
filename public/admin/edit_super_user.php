<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/super_user.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in('admin')){ redirect_to("../login.php"); } ?>
<?php
if(!isset($_GET['super_user_id'])) { redirect_to("all_profiles.php"); }
$super_user = SuperUser::find_by_id($_GET['super_user_id']);
if(!$super_user){
    $session->message("Not able to find member with the id: ". $_GET['$super_user']);
    redirect_to("all_profiles.php");
}
?>

<?php include("../layouts/admin_header.php"); ?>

    <div id="main">

        <div id="navigation">
            <?php include("../../includes/admin_navigation.php"); ?>
        </div>

        <div id="page">

            <h2><?php echo $super_user->first_name ." ".  $super_user->last_name ?></h2>
            <p> First Name: <?php echo $super_user->first_name ?></p>
            <p> Last Name: <?php echo $super_user->last_name ?></p>
            <p> ID: <?php echo $super_user->id ?></p>

            <a href="delete_super_user.php?super_user_id=<?php echo $super_user->id; ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </div>
    </div>
    </div>



<?php include("../layouts/admin_footer.php"); ?>