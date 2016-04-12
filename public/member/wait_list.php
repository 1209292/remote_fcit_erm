<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 10/04/2016
 * Time: 05:22 pm
 */
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/scholar_object.php");
?>

<?php  if(!$session->is_logged_in()){redirect_to("../login.php"); } ?>
<?php
if(isset($_GET['p_id'])){ // (((temporary for testing))) delete from wait_list
    $result = ScholarObject::find_by_id($_GET['p_id']);
    $result->delete();
    if($result){
        $message = "Deleted successfully.";
    }else{
        $message = "Coudn't be deleted.";
    }
}
?>
<?php  $member = Member::find_by_id($session->find_id());  ?>
<?php  $schlar_pubs = ScholarObject::find_by_author_id($member->id);?>
<?php
if(isset($_POST['submit'])){
   if(isset($_POST['checklist'])){
       $checklist_ids = $_POST['checklist'];
       $save_list = []; // save checked pubs in here
        foreach($checklist_ids as $id){
            $publication = ScholarObject::find_by_id($id);
            $save_list[] = $publication;
        }
       Publication::save($save_list, $member->id);
   } else {
       $message = "No publication was selected";
   }

}


?>

<?php include("../layouts/member_header.php"); ?>
<div id="navigation">
    <?php include("../../includes/member_navigation.php");?>
</div>
<div id="page">
    <?php echo output_message($message); ?>
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

        <?php foreach($schlar_pubs as $pub):?>
            <?php if($pub->url == 'None'){ ?>
         <p><?php echo $pub->title; ?>
                  <?php } else {?>
         <p><a href="<?php echo $pub->url; ?>"><?php echo $pub->title; ?></a>
                  <?php }?>
            <input type="checkbox" name="checklist[]" value="<?php echo $pub->id; ?>">
            <a href="wait_list.php?p_id=<?php echo $pub->id;?>">Delete</a>

        </p>
        <?php endforeach; ?>
        <input type="submit" name="submit" value="submit">
    </form>

</div>
<?php include("../layouts/member_footer.php"); ?>
