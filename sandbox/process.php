<?php


if (isset($_POST['username'], $_POST['pass'])) {
	
	session_start();
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['password'] = $_POST['pass'];
	
	
	header('location: login.php');
}