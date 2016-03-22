	
<?php
 
 require_once("../includes/functions.php"); 
 require_once("../includes/validation_function.php"); 
 require_once("../includes/db_connect.php"); 
 require_once("../includes/session.php"); 
 include("../includes/layout/header.php");
 ?> 

<html>

<head>
  

</head>

<body>
 <div id="header">
 <ul>
        <li><a class="active" href="members_page.php">Home</a></li>
        <li><a href="aboutme.php">About Me</a></li>
        <li><a href="accountmanagement.php">Account management</a></li>
       
      </ul>
	
</div>	




</body>







</html> 
	 
	 
		
<div id="main">





 
 
 
<?php
        if(isset($_POST['submit'])){
                move_uploaded_file($_FILES['file']['tmp_name'],"images/".$_FILES['file']['name']);
                $members = mysqli_query($connection,"UPDATE members SET images = ".$_FILES['file']['name']." WHERE id = ".$_SESSION['member_id']);
				
        }
?>
 
<!DOCTYPE html>
<html>
        <head>
       
        </head>
        <body>
                <form action="members_page.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="file" value="choose file">
                        <input type="submit" name="submit" value="submit">
                </form>
               
               
                <?php
									$member = find_member_by_id($_SESSION["member_id"]);
									// var_dump($member);
									//echo $member['id'];
									if($member['images'] == ""){
													echo "<img width='100' height='100' src='images/default.jpg' alt='Default Profile Pic'>";
									} else {
													echo "<img width='100' height='100' src='images/".$member['images']."' alt='Profile Pic'>";
									}
									echo "<br>";
                        
                ?>
				
				
				
				 
				
				
				
        </body>
</html>
</div>



<?php include("../includes/layout/footer.php"); ?>