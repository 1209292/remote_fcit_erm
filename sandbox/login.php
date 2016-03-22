<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
	
		<form action="process.php" method="POST">
			<label>username:
				<input type="text" name="username" />
			</label>
			<br>
			<label>Password:
				<input type="password" name="pass" />
			</label>
			
			<br>
			<input type="submit" value="submit">
		
		</form>
		
		<?php
		session_start();
		echo $_SESSION['username'].'<br>';
		echo $_SESSION['password'].'<br>';
		
		unset($_SESSION['username']);
		?>
		
	
	</body>
</html>