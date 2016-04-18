<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/scholar_object.php");
?>
<?php  if(!$session->is_logged_in()){redirect_to("../login.php"); } ?>
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
                $result_set = ScholarObject::save($result_set, $member->id);
                $message = "publication with the title '{$title}' added to your wait list.";
            }else{
                $message = "publication with the title '{$title}' already exists.";
            }
        }else{
            $message = "No match for the publication title '{$title}'.";
        }
    }else{
        $message = "You can't leave the title empty.";
    }
}

if(isset($_POST['auto_search'])) {
    $full_name = $member->full_name;
    $result_set = ScholarObject::search($full_name, $member->id);
    if($result_set){
        $result_set = ScholarObject::already_exists_in_publications($result_set, $member->id);
        if($result_set){
            ScholarObject::save($result_set, $member->id);
            $num_of_pub = count($result_set);
            $message = "{$num_of_pub} publications were added to your wait list.";
        }else{
            $message = "Nothing was added.";
        }
    }else{
        $message = "No publications were found.";
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
            <p>Publication Title:
                <input type="text" name="title" size="150"/>
            </p>

            <p>Date (Enter year only)
                <input type="number" name="year" value="2000"/>
            </p>

            <input type="submit" name="manual_search" value="Add Publication" />
        </form>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="submit" name="auto_search" value="Find all my publications" />
        </form>


    </div>
<?php include("../layouts/member_footer.php"); ?>