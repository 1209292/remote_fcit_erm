<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 12/03/2016
 * Time: 02:42 pm
 */
$message = "";
echo "Hello";
if(isset($_POST['submit'])){
    echo "Bello";
    if(empty($_FILES['file']) || !$_FILES['file'] || !is_array($_FILES['file'])){
        echo "SALAM";
        echo "NO FILE  UPLOADED";
    }elseif($_FILES['file']['error'] != 0){
        echo $_FILES['file']['error'];
    }else{
        if($_FILES["file"]['type'] == "application/pdf"){
            if(move_uploaded_file($_FILES['file']['tmp_name'], "C:/wamp/www/fcit_erm/sandbox/" . $_FILES['file']['name'])) {
                session_start();
                $_SESSION['pdf_file'] = $_FILES;
                header("Location: view_pdf.php");
                exit();
            }
        }
    }
}
?>
<?php //echo $message; ?>
<form action="<?php $_SERVER['PHP_SELF']?>" enctype="multipart/form-data" method="POST">
    <input type="file" name="file" />
    <input type="submit" value="submit" name="submit">
</form>
