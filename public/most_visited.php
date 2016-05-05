<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 05/05/2016
 * Time: 09:13 am
 */
require_once "../includes/session.php"; ?>
<?php require_once "../includes/functions.php"; ?>
<?php
$most_visited = Publication::most_visited_publc_list();
?>

<?php include("layouts/header.php"); ?>
    <div id="navigation">
        <?php include("../includes/public_navigation.php"); ?>
    </div>
<?php echo output_message($message)?>
    <div id="page">
<?php if(!$most_visited){
    output_message("No publications");
}else{
    foreach($most_visited as $publc):?>
        <p><a id='publc_url' href="pub_info.php?member_id=<?php echo $publc->member_id;?>&pub_id=<?php echo $publc->id; ?>">
                <?php echo $publc->title; ?></a> </p>
        <?php
        endforeach;
        }?>
    </div>
    <script type="text/javascript">
        document.getElementById("publc_url").onclick = function(){
            var nav = document.getElementById('navigation');
            var request = new XMLHttpRequest();
            request.open("GET", "increment_hits.php?publc_id=<?php echo $publc->id;?>", false);
            request.send();
            if(request.response === 200){
                console.log(request.response);
            }
        };

    </script>
<?php include("layouts/footer.php"); ?>