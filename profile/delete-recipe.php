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
if(isset($_REQUEST["recipeid"])){
	$rid = $_REQUEST["recipeid"];
	query("delete from recipes where author=$my_uid and rid=$rid");
}
?>