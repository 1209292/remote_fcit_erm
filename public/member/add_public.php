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
    $title = $database->escape_value($_POST['title']);
    $year = $_POST['year']; // not used yet

    $result_set = ScholarObject::search_by_publication_name($title, $member->id);
    if($result_set === false) {
        $message = "Enter a title.";
    }elseif($result_set == 0){
        $message = "No match for the publication title '{$title}'.";
    }elseif($result_set == 1){
        $message = "publication with the name '{$title}' already exists.";
    }elseif(count($result_set) > 0){
        $message = "publication with the title '{$title}' added to your wait list.";
        var_dump($result_set);
    }else{
        $message = "Error: propably during form submission.";
    }
}

if(isset($_POST['auto_search'])) {
    $full_name = "Rizwan Jameel Qureshi";
    $result = ScholarObject::search($full_name, $member->id);
    if($result == 0){
        $message = "no results found in scholar, propably name mistype";
    }elseif($result == 1){
        $message = "no new pub found";
    }elseif($result == 2){ // pub found
        redirect_to("wait_list.php");
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
                <input type="text" name="title"/>
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