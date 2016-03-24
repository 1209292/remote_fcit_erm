
<?php
 require_once ("../../includes/database.php");
 require_once ("../../includes/member.php");
 require_once ("../../includes/session.php");
 require_once ("../../includes/functions.php");
 require_once ("../../includes/admin.php");
?>

<?php  if(!$session->is_logged_in()){ redirect_to("../login.php"); } ?>
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
        if($members && Member::count_all() > 0){
            $count = 1;
            echo "<ul>";
            foreach($members as $member){
                $_SESSION["count".$count] = $member;
                echo "Member name: " . $member->first_name
                    . " " . $member->last_name . " "
                    . "<a href=\"edit_member.php?id={$count}\">Edit member</a>" . "<br />" ;
                $count++;
            }
        echo "</ul>";
        echo "<p><a href=\"add_member.php\"> + Add member</a></p>";
        }else{
            echo "Error: No Records in members' table.";
        }
     }elseif(isset($_GET["admins"])){
        echo "<h2>Admins of FCIT</h2>";
        echo output_message($message);
        $admins = Admin::find_all();
        if($admins && Admin::count_all() > 0){
            $count = 1;
            echo "<ul>";
            foreach($admins as $admin){
                $_SESSION["count".$count] = $admin;
                echo "Admin name: " . $admin->first_name
                    . " " . $admin->last_name . "<br />" ;
                $count++;
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