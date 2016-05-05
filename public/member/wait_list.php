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

<?php  if(!$session->is_logged_in('member')){redirect_to("../login.php");} ?>

<?php  $member = Member::find_by_id($session->find_id());  ?>

<?php
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

if(isset($_POST['delete_all'])){
    $delete_all = ScholarObject::find_by_author_id($member->id, 0, 0);
    if($delete_all){
        $count = 0;
        foreach($delete_all as $item){
            $res = $item->delete();
            if($res){
                $count++;
            }
        }
        $message = $count . " publications was deleted from your wait list";
    }else{
        $message = "You have no publications in your wait list to be deleted.";
    }
}
?>

<?php
if(isset($_POST['add'])){
if(isset($_POST['checklist'])) {
        $checklist_ids = $_POST['checklist'];
        $list_pubs = []; // save checked pubs in here
        foreach ($checklist_ids as $id) {
            $publication = ScholarObject::find_by_id($id);
            $list_pubs[] = $publication;
        }
        $save_list = ScholarObject::object_already_exists_in_publications($list_pubs, $member->id);
        if ($save_list) { // publication selected not already exists, so go ahead
            $saved_pub = Publication::save($save_list, $member->id); // save to DB
            if ($saved_pub) {
                $message = count($saved_pub) . " publication/s added to your publications";
                foreach ($saved_pub as $pub) { // delete saved item from wait list (scholar table)
                    $pub->delete();
                }
                redirect_to("public.php");
            } else { // maybe the system is down or DB problem (something out of hands)
                $message = "Publications selected could not be saved to your publications, please try again later";
            }
        }else{
            $message = "Publication selected already exists.";
        }
   } else {
       $message = "No publication was selected";
   }
}

if(isset($_POST['add_all'])){ // add all items of wait list
    $add_all = ScholarObject::find_by_author_id($member->id, 0, 0);
    if($add_all){ // member has publications in wait list (scholar table)
        $result = Publication::save($add_all, $member->id);
        $session->message(count($result). " publication/s added to your publications");
        foreach ($result as $pub) { // delete saved item from wait list
            $pub->delete();
        }
        redirect_to("public.php");
    }else{
        $message = "You have no publications in your wait list to add.";
    }
}
?>
<?php
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 20;
$total_count = ScholarObject::count_all_by_author($member->id);
$pagination = new Pagination($page, $per_page, $total_count);
$schlar_pubs = ScholarObject::find_by_author_id($member->id, $per_page, $pagination->offset());
?>

<?php include("../layouts/member_header.php"); ?>
<div id="navigation">
    <?php include("../../includes/member_navigation.php");?>
</div>
<div id="page">
    <?php echo output_message($message); ?>
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
        <?php if($schlar_pubs){ ?>
             <table>
                <tbody>
        <?php foreach($schlar_pubs as $pub):?>
                    <tr>
                        <td>
            <?php if($pub->url == 'None'){ ?>
         <p><?php echo $pub->title; ?></p>
                  <?php } else {?>
         <p><a href="<?php echo $pub->url; ?>"><?php echo htmlentities($pub->title); ?></a>
                  <?php }?>
                        </td>
                        <td>
            <input type="checkbox" name="checklist[]" value="<?php echo $pub->id; ?>">
                        </td>
        </p>
                    </tr>
        <?php endforeach; ?>
                </tbody>
            </table>

        <input type="submit" name="add" value="Add">
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="add_all" value="Add All">
        <input type="submit" name="delete_all" value="Delete All">
        <?php } else{ echo "<p>Your wait list is empty.</p>"; } ?>
    </form>

    <!-- ********** Pagination Part -->
    <p>
    <div id="pagination" style="clear: both;">
        <?php if($pagination->total_pages() > 1){
            if($pagination->has_previous_page()){
                echo "<a href=\"wait_list.php?page=";
                echo $pagination->previous_page();
                echo "\">&laquo Previous</a>";
            }

            for($i = 1; $i <= $pagination->total_pages(); $i++){
                if($i == $page){
                    echo "<span class='selected'>{$i}</span>";
                }else {
                    echo " <a href='wait_list.php?page={$i}'>{$i}</a> ";
                }
            }

            if($pagination->has_next_page()){
                echo "<a href=\"wait_list.php?page=";
                echo $pagination->next_page();
                echo "\">Next &raquo</a>";
            }

        }
        ?>
    </div></p>

</div>
<?php include("../layouts/member_footer.php"); ?>
