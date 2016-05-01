<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in('member')){redirect_to("../login.php"); } ?>
<?php  $member = Member::find_by_id($session->find_id());
       $publications = Publication::find_publication_by_author($member->id);
        $public_exists = "";
        if($publications && count($publications) > 0){
            $public_exists = true;
        }else{
            $message = "You have no publications yet.";
        }
      include("../layouts/member_header.php");
?>

    <div id="navigation">
        <?php include("../../includes/member_navigation.php");?>
    </div>
    <div id="page">
        <?php echo output_message($message); ?>
        <h2>Welcome <?php echo $member->first_name ." ".  $member->last_name ?></h2>
<?php
        if($public_exists){
            echo "<table>";
            echo "<tbody>";
            foreach($publications as $publication):
?>
            <tr>
            <td>
                <?php if($publication->url == 'None'){ ?>
                <p><?php echo $publication->title; ?></p>
                <?php } else {?>
                <p><a href="../pub_info?pub<?php echo $publication->url; ?>"><?php echo $publication->title; ?></a>
                    <?php } // else curly brace?>
                </td>
               <td><?php echo str_repeat('&nbsp;', 5); ?><a href="delete_public.php?p_id=<?php echo $publication->id;?>">Delete</a> </p></td>
            </tr>
<?php
                endforeach;
            echo "</tbody>";
            echo "</table>";
        }
?>



        <p><strong><a href="add_public.php">+Add Publication Now</a></strong></p>

    </div>
<?php include("../layouts/member_footer.php"); ?>