	
<?php
 require_once("../includes/session.php");
 require_once("../includes/functions.php"); 
 require_once("../includes/validation_function.php"); 
 require_once("../includes/db_connect.php"); 
 include("../includes/layout/header.php"); 
 ?> 

 
 <?php
			if(isset($_POST["submit"])){ 
				$member_login = validate_member_login(); 
				if($member_login){
					$_SESSION['member_id'] = $member_login["id"];
					redirect_to("members_page.php");
				}else{
				echo "user or pass is invalid";
				}
			
		}
			
	 ?>
	 
	 
	 
		
<div id="main">
<div id="navigation">
 <h6>Add Member</h6>
		 <form action="main_page.php" method="post">
		  <p>ID:
			<input type="text" name="id" value=""/>
		  </p>
		  
		  <p>Password:
			<input type="text" name="password" value=""/>
		  </p>
		  <input type="submit" name="submit" value="Log in" />
	
</div>
<div id="page">

<?php 	echo message();	?>
 
</div>
</div>


<?php include("../includes/layout/footer.php"); ?>