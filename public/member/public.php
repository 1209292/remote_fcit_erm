<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>
<?php  if(!$session->is_logged_in()){redirect_to("../member_profile.php"); } ?>
<?php  $member = Member::find_by_id($session->find_id());
       $publications = Publication::find_publication_by_member_id($member->id);
        $public_exists = "";
        if($publications){
            $public_exists = true;
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
            foreach($publications as $publication):
?>
            <p><a href="<?php echo $publication->url ?>"><?php echo $publication->title ?></a></p>
<?php
                endforeach;
        }
?>



        <a href="add_public.php">+Add Publication Now</a>

    </div>
<?php include("../layouts/member_footer.php"); ?>