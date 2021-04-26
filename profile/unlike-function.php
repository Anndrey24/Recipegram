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

//Return single element from query or null
function getSingle($query){
    $result = query($query);
    $row = $result->fetch_row();
    return isset($row[0]) ? $row[0] : null;
}

$my_uid = $_SESSION['uid'];
if(isset($_REQUEST["rid"])){
  $rid = intval(htmlspecialchars($_REQUEST['rid']));
  $liked = getSingle("select * from posts p
                where p.rid=$rid and p.uid=$my_uid and p.type='liked'");
  $addPost = query("select p.pid, p.points from posts p
                where p.rid=$rid and p.type='added'");
  $row = $addPost->fetch_assoc();
  $pid = $row['pid'];
  $points = $row['points'];
  if($liked){
      $points -= 1;
      query("delete from posts where pid=$liked");
      query("update posts set points=$points where pid=$pid");
  }
  echo("<p style='text-align:center; margin:0px;'>Likes:</p>
        <strong style='font-size:40px; text-align:center;' id='points'>$points</strong>
        <svg class='like' xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 24 24'><path d='M6.28 3c3.236.001 4.973 3.491 5.72 5.031.75-1.547 2.469-5.021 5.726-5.021 2.058 0 4.274 1.309 4.274 4.182 0 3.442-4.744 7.851-10 13-5.258-5.151-10-9.559-10-13 0-2.676 1.965-4.193 4.28-4.192zm.001-2c-3.183 0-6.281 2.187-6.281 6.192 0 4.661 5.57 9.427 12 15.808 6.43-6.381 12-11.147 12-15.808 0-4.011-3.097-6.182-6.274-6.182-2.204 0-4.446 1.042-5.726 3.238-1.285-2.206-3.522-3.248-5.719-3.248z'/><title>Like</title></svg>
        <svg class='share' xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 15.889v-2.223s-3.78-.114-7 3.333c1.513-6.587 7-7.778 7-7.778v-2.221l5 4.425-5 4.464z'/><title>Share recipe</title></svg>
        <input type='hidden' class='rid' id='rid' value='$rid'>");
}
?>