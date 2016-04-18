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
if(isset($_POST['add'])){
   if(isset($_POST['checklist'])){
       $checklist_ids = $_POST['checklist'];
       $list_pubs = []; // save checked pubs in here
        foreach($checklist_ids as $id){
            $publication = ScholarObject::find_by_id($id);
            $list_pubs[] = $publication;
        }
       $save_list = ScholarObject::already_exists_in_publications($list_pubs, $member->id);
        if($save_list) {
            $result_count = Publication::save($save_list, $member->id);
            $session->message($result_count . " publication/s added to your publications");
            redirect_to("public.php");
        }else{
            $message = count($save_list) . " was added";
        }
   } else {
       $message = "No publication was selected";
   }
}

if(isset($_POST['delete'])) {
    $result_count = 0 ;
    if (isset($_POST['checklist'])) {
        foreach ($_POST['checklist'] as $id) {
            $publication = ScholarObject::find_by_id($id);
            $result = $publication->delete();
            if($result){ $result_count++; }
        }

        $session->message($result_count . " publication/s deleted from your wait list");
        redirect_to("wait_list.php");
    }else {
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
        <?php if($schlar_pubs){
            ?>
        <?php foreach($schlar_pubs as $pub):?>
            <?php if($pub->url == 'None'){ ?>
         <p><?php echo $pub->title; ?>
                  <?php } else {?>
         <p><a href="<?php echo $pub->url; ?>"><?php echo htmlentities($pub->title); ?></a>
                  <?php }?>
            <input type="checkbox" name="checklist[]" value="<?php echo $pub->id; ?>">
        </p>
        <?php endforeach; ?>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="delete" value="Delete">
        <?php } else{ echo "<p>Your wait list is empty.</p>"; } ?>
    </form>

</div>
<?php include("../layouts/member_footer.php"); ?>
