<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/scholar_object.php");
?>
<?php  if(!$session->is_logged_in('member')){redirect_to("../login.php");} ?>
<?php  $member = Member::find_by_id($session->find_id());  ?>
<?php
if(isset($_POST['manual_search'])) {
    $title = $database->escape_value(trim($_POST['title']));
    $year = $_POST['year']; // not used yet
    if(mb_strlen($title) != 0){
        $result_set = ScholarObject::search_by_publication_name($title, $member->full_name, $member->id);
        if($result_set) {
            $result_set = ScholarObject::already_exists_in_publications($result_set, $member->id);
            if($result_set) {
                $result_set = ScholarObject::already_exists_in_scholar($result_set, $member->id);
                if($result_set) {
                    $count = count($result_set);
                    $count_x = 0;
                    for($i=0; $i<$count; $i++) { // save publications
                        $result = ScholarObject::save(array_shift($result_set), $member->id);
                        if($result === true){$count_x++;} // add counter_x if save returns true
                    }
                    if($count_x > 0) { // if we have saved more than ZERO publications
                        $message = count($count_x) . " publications were added to your wait list.";
                    }else{
                        $message = "System was not able to save your publications, please try again later.";
                    }
                }else{
                    $message = "publication with the title '{$title}' already exists in your wait list";
                }
            }else{
                $message = "publication with the title '{$title}' already exists in your publications.";
            }
        }else{
            $message = "No match for the publication title '{$title}'.";
        }
    }else{
        $message = "You can't leave the title empty.";
    }
}

if(isset($_POST['auto_search'])) {
//    $result_set = ScholarObject::search($member->full_name);
    $result_set = ScholarObject::my_search($member->full_name);
    if($result_set){
        $result_set = ScholarObject::already_exists_in_publications($result_set, $member->id);
        /**** check already_exists_in_scholar() ****/
        if($result_set){ // publications found not existed before
            $count = count($result_set);
            $count_x = 0;
            for($i=0; $i<$count; $i++) { // save publications
                $result = ScholarObject::save(array_shift($result_set), $member->id);
                if($result === true){$count_x++;} // add counter_x if save returns true
            }
            if($count_x > 0) { // if we have saved more than ZERO publications
                $message = count($count_x) . " publications were added to your wait list.";
            }else{
                $message = "System was not able to save your publications, please try again later.";
            }
        }else{
            $message = "Nothing was added to your wait list, you are updated.";
        }
    }else{
        $message = "No publications were found in google scholar by the name {$member->full_name}.";
    }
}

?>

<?php include("../layouts/member_header.php"); ?>
    <div id="navigation">
        <?php include("../../includes/member_navigation.php");?>
    </div>
    <div id="page">
        <?php echo output_message($message); ?>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <table>
                <tbody>
                <tr>
                    <td>
            <p>Publication Title:
                </td>
                    <td>
                <input type="text" name="title" size="80"/>
            </p>
                    </td>
                </tr>
                <tr>
                    <td>
            <p>Date (Enter year only)
                    </td>
                    <td>
                    <input type="number" name="year" value="2000"/>
            </p>
                    </td>
                </tr>
                <tr>
                    <td>
            <input type="submit" name="manual_search" value="Add Publication" />
                    </td>
                </tr>
                </tbody>
            </table>
        </form>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <table>
            <tr>
                <td>
            <input type="submit" name="auto_search" value="Find all my publications" />
                </td>
            </tr>
            </table>
        </form>


    </div>
<?php include("../layouts/member_footer.php"); ?>