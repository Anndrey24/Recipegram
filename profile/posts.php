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
if (isset($_POST['pageno']))
  $pageno = htmlspecialchars($_POST['pageno']);
else
  $pageno = 1;
$no_of_posts_per_page = 10;
$offset = ($pageno-1) * $no_of_posts_per_page;
$curr_page = query("select p.rid, p.type ,u.username, u.profile_pic,r.name, p.points, p.date, r.price, r.difficulty, r.preparation, r.cooking, r.resting, r.author, r.photo
                from posts p
                join users u on p.uid = u.uid
                join recipes r on p.rid = r.rid
                join follows f on p.uid = f.followee
                where f.uid=$my_uid and (p.type='added' or p.type='shared')
                order by p.date desc
                limit $offset, $no_of_posts_per_page");
while($row = $curr_page->fetch_assoc()){
  	$rid = $row['rid'];
  	$type = $row['type'];
    $username = $row['username'];
    $profile_pic = $row['profile_pic'];
    $name = $row['name'];
    $points = ($type=='Added') ? $row['points'] : '-';
    $date = $row['date'];
    $image = $row['photo'];
    $price = $row['price'];
    $diff = $row['difficulty'];
    $prep = $row['preparation'];
    $cook = $row['resting'];
    $rest = $row['preparation'];
    $total = $prep + $cook + $rest;
    $descText = "Difficulty: " . $diff . "<br>Price: £" . $price . "<br>Time: " . $total . " minutes";
    $authorid = $row['author'];
    $authorQuery = query("select username from users
              where uid='$authorid'");
    $authorData = $authorQuery -> fetch_assoc();
    $author = $authorData['username'];
    $liked = getSingle("select * from posts p
    					where p.rid=$rid and p.uid=$my_uid and p.type='liked'");
    echo("
    
  <div class='row m-1'>

    <div class='col-md-2'>
      <div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>
        <div class='col p-4 d-flex flex-column position-static'>
          <a href='profile.php?username=".$username."'>");
    if($profile_pic)
      echo("<img class='rounded-circle profile-picture mx-auto d-block' src='". $profile_pic ."'>");
    else
      echo("<img class='rounded-circle profile-picture mx-auto d-block' src='../assets/default.png'>");
    echo("
            <strong class='mx-auto d-block text-center'>$username</strong>
          </a>
        </div>
      </div>
    </div>

    <div class='col-md-8'>
      <div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>
        <div class='col p-4 d-flex flex-column position-static'>
          <strong class='d-inline-block mb-2' style='z-index:2;'><a class='text-dark' href='profile.php?username=".$author."'>".$author."'s</a></strong>
          <h3 class='mb-0'>$name</h3>
          <div class='mb-1 text-muted'>$date</div>
          <p class='card-text mb-auto'>".$descText."</p>
          <a href='recipe.php?rid=".$rid."' class='stretched-link'>More...</a>
        </div>
        <div class='col-auto d-none d-lg-block'>");
    if($image != null)
        echo('<img src="'. $image . '" alt="Thumbnail" class="recipe-thumbnail">');
    else
    	echo("<svg class='bd-placeholder-img' width='200' height='250' xmlns='http://www.w3.org/2000/svg' role='img' aria-label='Placeholder: Thumbnail' preserveAspectRatio='xMidYMid slice' focusable='false'><title>Placeholder</title><rect width='100%' height='100%' fill='#55595c'/><text x='50%' y='50%' fill='#eceeef' dy='.3em'>Thumbnail</text></svg>");
    echo("   
        </div>
      </div>
    </div>

    <div class='col-md-2'>
      <div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>
        <div class='col p-4 d-flex flex-column position-static'>
          <p style='text-align:center; margin:0px;'>Likes:</p>
          <strong style='font-size:40px; text-align:center;' id='points'>$points</strong>
          ");
    if($type=="Shared")
    	echo("<p class='text-center mt-3 mb-4'>Shared post</p>");
    elseif($liked)
        echo('<svg class="unlike" alt="Unlike" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24"><path d="M12 4.248c-3.148-5.402-12-3.825-12 2.944 0 4.661 5.571 9.427 12 15.808 6.43-6.381 12-11.147 12-15.808 0-6.792-8.875-8.306-12-2.944z"/><title>Unlike</title></svg>');
    else
    	echo('<svg class="like" alt="Like" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24"><path d="M6.28 3c3.236.001 4.973 3.491 5.72 5.031.75-1.547 2.469-5.021 5.726-5.021 2.058 0 4.274 1.309 4.274 4.182 0 3.442-4.744 7.851-10 13-5.258-5.151-10-9.559-10-13 0-2.676 1.965-4.193 4.28-4.192zm.001-2c-3.183 0-6.281 2.187-6.281 6.192 0 4.661 5.57 9.427 12 15.808 6.43-6.381 12-11.147 12-15.808 0-4.011-3.097-6.182-6.274-6.182-2.204 0-4.446 1.042-5.726 3.238-1.285-2.206-3.522-3.248-5.719-3.248z"/><title>Like</title></svg>');

    echo("
    	<svg class='share' xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 15.889v-2.223s-3.78-.114-7 3.333c1.513-6.587 7-7.778 7-7.778v-2.221l5 4.425-5 4.464z'/><title>Share recipe</title></svg>
    	<input type='hidden' class='rid' id='rid' value='$rid'>
        </div>
      </div>
    </div>


  
  </div>


    	");
}
?>