<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 21/04/2016
 * Time: 02:03 am
 */
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/super_user.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/publication.php");

if(!$session->is_logged_in('super_user')){ redirect_to("../login.php"); }

$members = Member::find_all();
?>
<?php
include("../layouts/member_header.php");
?>
<div id="navigation">
    <?php include("../../includes/member_navigation.php");?>
</div>
<div id="page">
    <br />
    <table>
        <tbody>
        <tr>
            <td><p><strong>Number of FCIT members publications: </strong><?php echo Publication::get_count_publc();?></p></td>
        </tr>
        <tr>
            <td><p><strong>Number of FCIT members citations: </strong><?php echo Publication::get_count_citations();?></p></td>
        </tr>
        </tbody>
    </table>
    <?php foreach($members as $member): ?>
    <p><a href="member_profile.php?member_id=<?php echo  $member->id  ?>">
            <img src="../images/<?php echo $member->id ."/".$member->image_file ?>" alt="Member image"
                 width='100'/><p>Member name:  <?php echo $member->first_name ." ".
            $member->last_name ?> </a> </p>

    <?php endforeach; ?>
    <table>
        <tbody>
            <tr>

            </tr>
        </tbody>
    </table>

</div>
<?php include("../layouts/member_footer.php"); ?>

