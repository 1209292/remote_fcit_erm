<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/publication.php");
require_once ("../../includes/pagination.php");
?>
<?php  if(!$session->is_logged_in('member')){redirect_to("../login.php"); } ?>
<?php  $member = Member::find_by_id($session->find_id());
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 20;
$total_count = Publication::count_all_by_author($member->id);
       $pagination = new Pagination($page, $per_page, $total_count);
       $publications = Publication::find_publication_by_author($member->id, $per_page, $pagination->offset());
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
<!--        <p>You have --><?php //echo Publication::count_all_by_author($member->id); ?><!-- publications</p>-->
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
               <td><?php echo str_repeat('&nbsp;', 5); ?><a href="delete_public.php?p_id=<?php echo $publication->id;?>"
                                                            onclick="return confirm('Are you sure you want to delete?');">Delete</a> </p></td>
            </tr>
<?php
                endforeach;
            echo "</tbody>";
            echo "</table>";?>
            <!-- ********** Pagination Part -->
            <p>
            <div id="pagination" style="clear: both;">
                <?php if($pagination->total_pages() > 1){
                    if($pagination->has_previous_page()){
                        echo "<a href=\"public.php?page=";
                        echo $pagination->previous_page();
                        echo "\">&laquo Previous</a>";
                    }

                    for($i = 1; $i <= $pagination->total_pages(); $i++){
                        if($i == $page){
                            echo "<span class='selected'>{$i}</span>";
                        }else {
                            echo " <a href='public.php?page={$i}'>{$i}</a> ";
                        }
                    }

                    if($pagination->has_next_page()){
                        echo "<a href=\"public.php?page=";
                        echo $pagination->next_page();
                        echo "\">Next &raquo</a>";
                    }

                }
                ?>
            </div></p>
<?php
        } // if curly brace
?>



        <p><strong><a href="add_public.php">+Add Publication Now</a></strong></p>

    </div>
<?php include("../layouts/member_footer.php"); ?>