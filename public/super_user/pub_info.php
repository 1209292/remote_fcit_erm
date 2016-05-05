<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 21/04/2016
 * Time: 12:26 am
 */

require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
?>

<?php
if(!$session->is_logged_in('super_user')){ redirect_to("../login.php"); }
if(!isset($_GET['member_id'])) { redirect_to("all_profiles.php"); }
/**** check if $_GET is int, and between ZERO and PHP_INT_MEX****/
if ( filter_var($_GET['member_id'], FILTER_VALIDATE_INT , array("min_range"=>1,"max_range"=>PHP_INT_MAX)) === false ) {
    $session->message("Select a member.");
    redirect_to('dashboard.php');
}
$member = Member::find_by_id($_GET['member_id']);
if(!$member){ //check if member exists
    $session->message("Select a member.");
    redirect_to('dashboard.php');
}
if ( filter_var($_GET['pub_id'], FILTER_VALIDATE_INT , array("min_range"=>1,"max_range"=>PHP_INT_MAX)) === false ) {
    $session->message("Select a member.");
    redirect_to('dashboard.php');
}
$publication = Publication::find_by_id($_GET['pub_id']);
if(!$publication){ //check if member exists
    $session->message("Select a member.");
    redirect_to('dashboard.php');
}
?>

<?php include("../layouts/member_header.php"); ?>

    <div id="main">

        <div id="navigation">
            <?php  include("../../includes/super_user_navigation.php"); ?>
        </div>

        <div id="page">

            <h2><?php echo $member->first_name ." ".  $member->last_name ?></h2>

            <p> <img id='img' src="../images/<?php echo $member->id ."/". $member->image_file; ?>"
                     alt="NO IMAGE" width="150"/> </p>
            <?php
            if($publication){
                echo "<table>";
                echo "<tbody>";
                ?>
                <tr>
                    <td>
                        <p><strong>Title: </strong><?php echo $publication->title;?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($publication->url != 'None'){?>
                            <p><strong>URL: </strong><a id="publc_url" href="<?php echo $publication->url;?>"><?php echo $publication->url;?></a></p>
                        <?php }else{?>
                            <p><strong>URL: </strong><?php echo $publication->url;?></p>
                        <?php }?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Citations: </strong><?php echo $publication->num_citations;?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($publication->url_pdf != 'None'){?>
                            <p><strong>PDF: </strong><a href="<?php echo $publication->url_pdf;?>"><?php echo $publication->url_pdf;?></a></p>
                        <?php }else{?>
                            <p><strong>PDF: </strong><?php echo $publication->url_pdf;?></p>
                        <?php }?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($publication->url_citations != 'None'){?>
                            <p><strong>Citations URL: </strong><a href="<?php echo $publication->url_citations;?>"><?php echo $publication->url_citations;?></a></p>
                        <?php }else{?>
                            <p><strong>Citations URL: </strong><?php echo $publication->url_citations;?></p>
                        <?php }?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Excerpt: </strong><?php echo $publication->excerpt;?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Year: </strong><?php echo $publication->year;?></p>
                    </td>
                </tr>
                <?php
                echo "</tbody>";
                echo "</table>";
            }else{ echo "<h2>No Publications</h2>"; }
            ?>
        </div>
    </div>
    </div>



<?php include("../layouts/footer.php"); ?>