<?php require_once "../includes/session.php"; ?>
<?php require_once "../includes/functions.php"; ?>
<?php
?>

<?php include("layouts/header.php"); ?>
        <div id="navigation">
            <?php include("../includes/public_navigation.php"); ?>
        </div>
        <?php echo output_message($message)?>
        <div id="page">
        </div>
<?php include("layouts/footer.php"); ?>