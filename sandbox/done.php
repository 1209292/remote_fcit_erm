<?php



?>


<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="POST">

    <p><input type="text" name="f_name"/></p>
    <p><input type="text" name="l_name"/></p>
    <p><input type="text" name="id"/></p>

    <input type="submit" name="submit" value="upload"/>

</form>
