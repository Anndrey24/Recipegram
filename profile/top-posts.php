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

if (isset($_POST['pageno']))
  $pageno = htmlspecialchars($_POST['pageno']);
else
  $pageno = 1;
$no_of_posts_per_page = 10;
$offset = ($pageno-1) * $no_of_posts_per_page;
$curr_page = query("select p.rid, p.type ,u.username, u.profile_pic, r.name, p.points, p.date, r.price, r.difficulty, r.preparation, r.cooking, r.resting, r.author, r.photo
                from posts p
                join users u on p.uid = u.uid
                join recipes r on p.rid = r.rid
                where p.type='added'
                order by p.points desc
                limit $offset, $no_of_posts_per_page");
while($row = $curr_page->fetch_assoc()){
  	$rid = $row['rid'];
  	$type = $row['type'];
    $username = $row['username'];
    $profile_pic = $row['profile_pic'];
    $name = $row['name'];
    $points = $row['points'];
    $likeText = ($points == 1) ? " like" : " likes";
    $date = $row['date'] . " - ".$points.$likeText;
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

    <div class='col-md-10'>
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

  
  </div>


    	");
}
?>