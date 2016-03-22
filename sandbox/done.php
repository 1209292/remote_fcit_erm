<?php
$message = "";

    if(isset($_FILES['file'])){
//    $accepted_extentions = array('doc','docx','ppt','pptx');
//    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
//    $message = "here we go";
//    if(!in_array($ext, $accepted_extentions)){
//        $message = "Not Found";
//    }else{
//        $message = "Found";
//    }
        echo "<p>YES </p>";
        print_r($_FILES['file']);
}
?>

<?php echo $message; ?>
<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="POST">

    <p><input type="file" name="file" id="file"/></p>

<!--    <p>Caption: <input type="text" name="caption" value=""/></p>-->

    <input type="submit" name="submit" value="upload"/>

</form>
