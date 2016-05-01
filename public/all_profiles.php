
<?php
require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");
require_once ("../includes/admin.php");
?>

<?php include("layouts/header.php"); ?>

<div id="main">

    <div id="navigation">
        <?php  include("../includes/public_navigation.php"); ?>
    </div>

    <div id="page">

        <?php $members = Member::find_all();
                foreach($members as $member): ?>
        <p><a href="member_profile.php?member_id=<?php echo  $member->id  ?>">
                <img src="images/<?php echo $member->id ."/".$member->image_file ?>" alt="Member image"
                     width='100'/><p>Member name:  <?php echo $member->first_name ." ".
                $member->last_name ?> </a>  <?php echo str_repeat('&nbsp', 10) ?> </p>
        <?php endforeach; ?>
    </div>

    </div>
</div>


<?php include("layouts/footer.php"); ?>