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
  if($liked == null){
      $points += 1;
      query("insert into posts (type, rid, uid) values ('Liked', $rid, $my_uid)");
      query("update posts set points=$points where pid=$pid");
  }
  echo("<p style='text-align:center; margin:0px;'>Likes:</p>
        <strong style='font-size:40px; text-align:center;' id='points'>$points</strong>
        <svg class='unlike' xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 24 24'><path d='M12 4.248c-3.148-5.402-12-3.825-12 2.944 0 4.661 5.571 9.427 12 15.808 6.43-6.381 12-11.147 12-15.808 0-6.792-8.875-8.306-12-2.944z'/><title>Unlike</title></svg>
        <svg class='share' xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 15.889v-2.223s-3.78-.114-7 3.333c1.513-6.587 7-7.778 7-7.778v-2.221l5 4.425-5 4.464z'/><title>Share recipe</title></svg>
        <input type='hidden' class='rid' id='rid' value='$rid'>");
}
?>