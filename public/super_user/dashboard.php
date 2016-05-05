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
    <?php include("../../includes/super_user_navigation.php");?>
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
        <tr>
            <?php $publ = Publication::ballondor_publc(); ?>
            <td><p><strong>Top cited publication: </strong>
                    <?php if($publ->url != "None"){?>
                        <a href="<?php echo $publ->url; ?>"><?php echo $publ->title; ?></a></p></td>
            <?php }else{?>
            <p><?php echo $publ->title; ?></p>
            <?php } ?>
        </tr>
        <tr>
            <?php $member = Publication::ballondor_author_in_publc();?>
            <td><p><strong>Most publishing member: </strong>
                    <a href="member_profile.php?member_id=<?php echo $member->id; ?>"><?php echo $member->full_name; ?></a></p></td>
        </tr>

        <tr>
            <?php $member = Publication::ballondor_author_in_citation();?>
            <td><p><strong>Most cited member: </strong>
                    <a href="member_profile.php?member_id=<?php echo $member->id; ?>"><?php echo $member->full_name; ?></a></p></td>
        </tr>
        </tbody>
    </table>


    <h3>Most Cited: </h3>
        <?php $most_cited = Publication::most_cited_publc_list($member->id);
            if($most_cited){
                foreach($most_cited as $publc):?>
                    <p><a href="pub_info.php?member_id=<?php echo $member->id; ?>&pub_id=<?php echo $publc->id?>">
                        <?php echo $publc->title; ?></a></p>
                <?php
                    endforeach;
            }
        ?>
    <h3>Most Visited: </h3>
        <?php $most_visited = Publication::most_cited_publc_list($member->id);
        if($most_visited){
            foreach($most_visited as $publc):?>
                <p><a href="pub_info.php?member_id=<?php echo $member->id; ?>&pub_id=<?php echo $publc->id?>">
                        <?php echo $publc->title; ?></a></p>
    <?php
            endforeach;
        }
        ?>
</div>

<?php include("../layouts/member_footer.php"); ?>

