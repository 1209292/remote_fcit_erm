<?php //require_once("../../includes/initialize.php"); ?>
<?php
 require_once ("../../includes/database.php");
 require_once ("../../includes/member.php");
 require_once ("../../includes/session.php");
 require_once ("../../includes/functions.php");
?>

<?php  if(!$session->is_logged_in()){redirect_to("login.php"); } ?>
<?php include("../layouts/admin_header.php"); ?>
<div id="main">
<div id="navigation">
    <br />
	<a href="manage_content.php">- Home</a>
	<br />
	<a href="manage_content.php?members=1">- Members</a>
</div>
<div id="page">

<?php
if(isset($_GET["members"])){
    echo "<h2>Members of FCIT</h2>";
    echo output_message($message);
    ?>

        <?php

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
    }else{
        echo "Error: No Records in members' table.";
    }
    echo "<p><a href=\"add_member.php\"> + Add member</a></p>";

    ?>

 <?php
 } else {
	 echo "<h2>Welcome</h2>";
	 echo "<h6> FCIT ERM</h6>";
 }
 ?>

</div>
</div>


<?php include("../layouts/admin_footer.php"); ?>