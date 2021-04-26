<?php
// Initialize the session
session_start();
// Include connection to database file
require_once "../connectDB.php";

//Return query object
function query($query){
    global $conn;
    $result = $conn->query($query);
    return $result;
}

$my_uid = $_SESSION['uid'];
if(isset($_REQUEST["failsafe"])){
  query("delete from users where uid=$my_uid");
	// Unset all of the session variables
	$_SESSION = array();
	// Destroy the session.
	session_destroy();
}
?>