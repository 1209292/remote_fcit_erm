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
    $publications = Publication::find_publication_by_author($member->id);
    $uploads = Upload::find_uploads_by_member_id($member->id);
}else{
    $session->message("Not able to find member with the id: ". $_GET['member_id'] . ", please select a member.");
    redirect_to("all_profiles.php");
}
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

            <?php
            if($publications){
                echo "<table>";
                echo "<tbody>";
                foreach($publications as $publication):
                    ?>
                    <tr>
                        <td>
                            <p><a href="pub_info.php?member_id=<?php echo $member->id;?>&pub_id=<?php echo $publication->id;?>"><?php echo $publication->title; ?></a>
                        </td>
                    </tr>
                    <?php
                endforeach;
                echo "</tbody>";
                echo "</table>";
            }else{ echo "<h2>No Publications</h2>"; }
            ?>
            <?php
            if($uploads){
                echo "<table>";
                echo "<tbody>";
                foreach($uploads as $upload):
                    ?>
                    <tr>
                        <td>
                            <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/<?php
                                echo htmlentities($upload->member_id)
                                    ."/".$upload->filename; ?>"> <?php  echo $upload->filename; ?></a></p>
                        </td>
                    </tr>
                    <?php
                endforeach;
                echo "</tbody>";
                echo "</table>";
            } else { echo "<h2>No Uploads</h2>"; }
            ?>

        </div>
    </div>
    </div>


<?php include("layouts/footer.php"); ?>