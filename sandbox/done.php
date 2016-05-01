<?php

require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");

?>


<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="POST">

    <p><input type="text" name="f_name"/></p>
    <p><input type="text" name="l_name"/></p>
    <p><input type="text" name="id"/></p>
    <p id="tst"></p>

    <input id="myForm" type="submit" name="submit" value="upload"/>
    <script type="text/javascript">
        var request = new XMLHttpRequest();
        request.open("GET", "form.php");
        request.send();
        if(request.status == 200) {
            console.log(request);
            document.writeln(request.responseText);
        }
        //        request.onreadystatechange = function(){
//            if(request.readyState === 4 && request.status === 400){
//                console.log(request);
//                document.getElementById("tst");
//                document.innerHTML = request.responseText;
//            }
//            request.send();
//        }
    </script>

</form>
