
<?php
 require_once ("../../includes/database.php");
 require_once ("../../includes/member.php");
 require_once ("../../includes/session.php");
 require_once ("../../includes/functions.php");
 require_once ("../../includes/admin.php");
?>

<?php  if(!$session->is_logged_in()){ redirect_to("../member_profile.php"); } ?>
<?php include("../layouts/admin_header.php"); ?>

<div id="main">

    <div id="navigation">
        <?php  include("../../includes/admin_navigation.php"); ?>
    </div>

    <div id="page">

    <?php

    if(isset($_GET["members"])){
        echo "<h2>Members of FCIT</h2>";
        echo output_message($message);

        $members = Member::find_all();
        if($members && count($members) > 0){
            echo "<div>";
            foreach($members as $member):?>

                 <p><a href="member_profile.php?member_id=<?php echo $member->id ?>">
                     <img src="../images/<?php echo $member->id ."/".$member->image_file ?>" alt="Member image"
                     width='100'/><p>Member name:  <?php echo $member->first_name ." ".
                     $member->last_name ?> </a>  <?php echo str_repeat('&nbsp', 10) ?>
                     <a href="edit_member.php?member_id=<?php echo $member->id; ?>">
                     Edit member</a></p>
            <?php
            endforeach;
        echo "</div>";
        echo "<p><a href=\"add_member.php\"> + Add member</a></p>";
        }else{
            echo "Error: No Records in members' table.";
        }
     }elseif(isset($_GET["admins"])){
        echo "<h2>Admins of FCIT</h2>";
        echo output_message($message);
        $admins = Admin::find_all();
        if($admins && count($admin) > 0){
            echo "<ul>";
            foreach($admins as $admin){
                echo "Admin name: " . $admin->first_name
                    . " " . $admin->last_name . "<br />" ;
            }
            echo "</ul>";
            echo "<p><a href=\"add_admin.php\"> + Add Admin</a></p>";
        }else{
            echo "Error: No Records in members' table.";
        }
        }else {
         echo "<h2>Welcome</h2>";
         echo "<h6> FCIT ERM</h6>";
        }
     ?>

    </div>
</div>


<?php include("../layouts/admin_footer.php"); ?>