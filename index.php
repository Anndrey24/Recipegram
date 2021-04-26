<?php
// Initialize the session
session_start();
if(isset($_SESSION["loggedin"])){
	header("location: profile/feed.php");
	exit;
}else{
	//Redirects to login page
	header("location: profile/landing.php");
	exit;
}
?>