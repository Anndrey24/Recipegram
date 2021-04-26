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
if(isset($_REQUEST["rid"])){
  $rid = intval(htmlspecialchars($_REQUEST['rid']));
  query("insert into posts (type, rid, uid) values ('Shared', $rid, $my_uid)");
}
?>