<?php
require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");
?>

<?php
if(!isset($_GET['member_id'])) { redirect_to("all_profiles.php"); }
/**** check if $_GET is int, and between ZERO and PHP_INT_MEX****/
if ( filter_var($_GET['member_id'], FILTER_VALIDATE_INT , array("min_range"=>1,"max_range"=>PHP_INT_MAX)) === false ) {
    $session->message("Select a member.");
    redirect_to('all_profiles.php');
}
$member = Member::find_by_id($_GET['member_id']);
$pub_found = false;
if($member){ //check if member exists
    $all_publications = Publication::count_all_by_author($member->id);
    $all_uploads = Upload::count_all_by_author($member->id);
}else{
    $session->message("Not able to find member with the id: ". $_GET['member_id'] . ", please select a member.");
    redirect_to("all_profiles.php");
}

// ****************** pagination section ********************* prepare everything
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 15;
$total_count = Publication::count_all_by_author($member->id);
$pagination = new Pagination($page, $per_page, $total_count);
$publications = Publication::find_publication_by_author($member->id, $per_page, $pagination->offset());
$per_page = 3;
$total_count = Upload::count_all_by_author($member->id);
$uploads_pagination = new Pagination($page, $per_page, $total_count);
$uploads = Upload::find_uploads_by_member_id($member->id, $per_page, $uploads_pagination->offset());
// ************************ pagination section  *****************
?>

    <?php include("layouts/header.php"); ?>

    <div id="main">

        <div id="navigation">
            <?php  include("../includes/public_navigation.php"); ?>
        </div>

        <div id="page">

            <h2><?php echo $member->first_name ." ".  $member->last_name ?></h2>

            <p> <img src="images/<?php echo $member->id ."/". $member->image_file; ?>"
                     alt="NO IMAGE" width="150"/> </p>
            <p> First Name: <?php echo $member->first_name ?></p>
            <p> Last Name: <?php echo $member->last_name ?></p>
            <p> ID: <?php echo $member->id ?></p>
            <p><a href="member_profile.php?member_id=<?php echo $member->id?>&publications=true">Publications</a>
                <?php echo str_repeat('&nbsp', 15);?>
                <a href="member_profile.php?member_id=<?php echo $member->id?>&uploads=true">uploads</a>
            </p>

            <?php
            if(isset($_GET['publications']) && $_GET['publications'] == true) {
                if ($all_publications == 0) {
                    echo "<h2>No Publications</h2>";
                } else {
                    echo "<table>";
                    echo "<tbody>";
                    foreach ($publications as $publication):
                        ?>
                        <tr>
                            <td>
                                <p>
                                    <a href="pub_info.php?member_id=<?php echo $member->id; ?>&pub_id=<?php echo $publication->id; ?>"><?php echo $publication->title; ?></a>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    echo "</tbody>";
                    echo "</table>";
                    ?>


                    <!-- ********** Pagination Part -->
                    <p>
                    <div id="pagination" style="clear: both;">
                        <?php if($pagination->total_pages() > 1){
                            if($pagination->has_previous_page()){
                                echo "<a href=\"member_profile.php?member_id={$member->id}&page=";
                                echo $pagination->previous_page();
                                echo "&publications=true";
                                echo "\">&laquo Previous</a>";
                            }

                            for($i = 1; $i <= $pagination->total_pages(); $i++){
                                if($i == $page){
                                    echo "<span class='selected'>{$i}</span>";
                                }else {
                                    echo " <a href='member_profile.php?member_id={$member->id}&page={$i}&publications=true'>{$i}</a> ";
                                }
                            }

                            if($pagination->has_next_page()){
                                echo "<a href=\"member_profile.php?member_id={$member->id}&page=";
                                echo $pagination->next_page();
                                echo "&publications=true";
                                echo "\">Next &raquo</a>";
                            }

                        }
                        ?>
                    </div></p>
                    <?php
                }
            } // curly brace of ---> if(isset($_GET['publications']) && $_GET['publications'] == true)
            elseif(isset($_GET['uploads']) && $_GET['uploads'] == true) {
                if ($all_uploads == 0) {
                    echo "<h2>No Uploads</h2>";
                } else {
                    echo "<table>";
                    echo "<tbody>";
                    foreach ($uploads as $upload):
                        ?>
                        <tr>
                            <td>
                                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/<?php
                                    echo htmlentities($upload->member_id)
                                        . "/" . $upload->filename; ?>"> <?php echo $upload->filename; ?></a></p>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    echo "</tbody>";
                    echo "</table>";
                    ?>


                    <!-- ********** Pagination Part -->
                    <p>
                    <div id="pagination" style="clear: both;">
                        <?php if($uploads_pagination->total_pages() > 1){
                            if($uploads_pagination->has_previous_page()){
                                echo "<a href=\"member_profile.php?member_id={$member->id}&page=";
                                echo $pagination->previous_page();
                                echo "&uploads=true";
                                echo "\">&laquo Previous</a>";
                            }

                            for($i = 1; $i <= $uploads_pagination->total_pages(); $i++){
                                if($i == $page){
                                    echo "<span class='selected'>{$i}</span>";
                                }else {
                                    echo " <a href='member_profile.php?member_id={$member->id}&page={$i}&uploads=true'>{$i}</a> ";
                                }
                            }

                            if($uploads_pagination->has_next_page()){
                                echo "<a href=\"member_profile.php?member_id={$member->id}&page=";
                                echo $uploads_pagination->next_page();
                                echo "&uploads=true";
                                echo "\">Next &raquo</a>";
                            }

                        }
                        ?>
                    </div></p>

<?php           }
            }  // curly brace of ---> if(isset($_GET['uploads']) && $_GET['uploads'] == true)
            ?>

        </div>
    </div>
    </div>


<?php include("layouts/footer.php"); ?>