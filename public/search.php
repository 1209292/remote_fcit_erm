<?php
require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");

$message="";
$search_done = false;

if(isset($_POST['submit'])){
    $words = $_POST['search'];
    if($words === ""){
        $message = "Enter a word first";
    }else{
        $words = explode(" ", $words);
        $members_query = "SELECT * FROM members WHERE ";
        $upload_query = "SELECT * FROM uploads WHERE ";
        $i = 0;
        foreach($words as $word){
            $i++;
            if($i == 1){
                $members_query .= "first_name LIKE '%{$word}%' OR last_name LIKE '%{$word}%' ";
                $upload_query .= "filename='%{$word}%' ";
            }else{
                $members_query .= "OR first_name LIKE '{$word}' OR last_name LIKE '%{$word}%' ";
                $upload_query .= "filename='%{$word}%' ";
            }
        }
        $members_set = Member::find_by_sql($members_query);
        // var_dump($members_set);
        if(count($members_set) > 0){
            foreach($members_set as $member){
                $search_done = true;
            }
        }
    }


}
?>

<?php if(!$search_done){
include ("layouts/header.php");
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
include ("layouts/footer.php");
    }
?>
<?php include("layouts/header.php"); ?>
    <div id="navigation">
        <?php include("../includes/public_navigation.php"); ?>
    </div>
    <div id="page">
        <?php
        if($search_done){
            foreach($members_set as $member):
                $member_uploads = Upload::find_uploads_by_member_id($member->id);
                echo "<p>";
                echo "<p><img src='images/$member->image_file' alt='No image' width='100'></p>";
                echo "<p>Member Name: ".$member->first_name ." ". $member->last_name . "</p>";
                echo "<ul>";
                foreach($member_uploads as $upload):
                    echo "<li><a href='member/uploads/$upload->filename '> $upload->filename </a></li>";
                    endforeach;
            echo "</p><br />";
            endforeach;
        }
        ?>
    </div>
<?php include("layouts/footer.php"); ?>
