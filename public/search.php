<?php
require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");
require_once ("../includes/publication.php");

$search_done = false;
$upload_search_done = false;
$member_search_done = false;
$public_search_done = false;

if(isset($_POST['submit'])){

    $search_words = $_POST['search'];
    if(mb_strlen($search_words === 0 )) { $message = "Enter a message first"; }
    else{
        $upload_set = Upload::search($search_words);
        $member_set = Member::search($search_words);
        $public_set = Publication::search($search_words);
        if($member_set || $upload_set || $public_set) {
            $search_done = true ;
            if($member_set) {$member_search_done = true;}
            if($upload_set) {$upload_search_done = true;}
            if($public_set) {$public_search_done = true;}
        }
    }
}
?>


<?php include ("layouts/header.php"); ?>
<!-- search is not done yet, or search is done and some result was found,
  in the second case we dont want the search box to appear -->
<?php if(!$search_done){
echo $message
?>
    <div id="navigation">
        <?php include ("../includes/public_navigation.php"); ?>
    </div>
    <div id="page">
        <h2>Search</h2>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="text" name="search" size="20" placeholder="Enter words to search" />
            <input type="submit" name="submit" value="Search" />
        </form>
    </div>
<?php
    }
?>

        <?php
        if($search_done){
            echo "<div id=\"navigation\">";
            include("../includes/public_navigation.php");
            echo "</div>";
            echo "<div id=\"page\">";
            echo "<br >";
            if($member_search_done){
                echo "<span>";
                foreach($member_set as $member):
                    echo "<p><img src='images/$member->image_file' alt='No image' width='150'/>";
                    echo "<a href=''> $member->first_name  $member->last_name</a> ";
                endforeach;
                echo "<span>";
            }
            if($upload_search_done) {
                foreach ($upload_set as $upload):
                    $member = Member::find_by_id($upload->member_id);
                    echo "<p><a href='member/uploads/$upload->filename '> $upload->filename </a> ";
                    echo "Author Name: " . $member->first_name . " " . $member->last_name . "</p>";
                endforeach;
            }
            if($public_search_done){
                foreach($public_set as $public):
                    echo "<a href='$public->url' onclick='$public->increment_hits()'> $public->title <a/>";
                    endforeach;
            }
        }

    echo "</div>";
        include("layouts/footer.php"); ?>
