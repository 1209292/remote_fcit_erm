<?php


if (isset($_POST['fname'])) {
	echo "your first name is " . $_POST['fname']. '<br>';
	echo "your last name is " . $_POST['lname']. '<br>';
	echo "your gender is " . $_POST['gender']. '<br>';
	echo "your title is " . $_POST['title']. '<br>';
	echo "your major is " . $_POST['major']. '<br>';
	
	if (isset($_POST['course'])) {
		$v = count($_POST['course']);
		for ($i = 0; $i < $v ; ++$i) {
			echo "course number: " . $_POST['course'][$i].'<br>';
			setcookie("course[$i]", $_POST['course'][$i], time()+9000, '/lab7/', 'localhost');
		}
	}
	
	echo "your hidden data is ".$_POST['somedata'];
	
	setcookie('firstname', $_POST['fname'], time()+60*60*24*360, '/lab7/', 'localhost');
	setcookie('lastname', $_POST['lname'], time()+30, '/lab7/', 'localhost');
	setcookie('gender', $_POST['gender'], time()+30, '/lab7/', 'localhost');
	
	header("Location: form.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
	
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<input type="hidden" name="somedata" value="nothing" />
			<label>Frist Name:
				<input type="text" name="fname" />
			</label>
			<br>
			<label>Last Name:
				<input type="text" name="lname" />
			</label>
			
			<br>
			
			Gender:
			<label>Male
				<input type="radio" name="gender" value="male" />
			</label>
			<label>Female
				<input type="radio" name="gender" value="female" />
			</label>
			<br>
			<label>Yor title:
			<select name="title">
				<option>Mr</option>
				<option>Ms</option>
				<option>Mss</option>
			</select>
			</label>
			
			<br>
			<label>Yor Major:
			<select name="major">
				<option value="cs">Computer Science</option>
				<option value="it">Information Technology</option>
				<option value="is">Information System</option>
			</select>
			</label>
			
			<br>
			
			Course:
			<label>
			cpit405<input type="checkbox" name="course[]" value="405" />
			</label>
			<label>
			cpit305<input type="checkbox" name="course[]" value="305" />
			</label>
			<label>
			cpit250<input type="checkbox" name="course[]" value="250" />
			</label>
			
			<br>
			<input type="submit" value="submit">
		
		</form>
		
<?php

if (isset($_COOKIE['firstname'])) {
	echo "your are saved on cookie as ".$_COOKIE['firstname'].'<br>';
	echo "your are saved on cookie as ".$_COOKIE['lastname'].'<br>';
	echo "your are saved on cookie as ".$_COOKIE['gender'].'<br>';
	
	var_dump($_COOKIE);
	
	setcookie('firstname', null, time() - 10);
}


?>
		
	
	</body>
</html>