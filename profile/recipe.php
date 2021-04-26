<?php
// Initialize the session
session_start();

// Include connection to database file
require_once "../connectDB.php";

// Check if the user is logged in
$loggedin = false;
if(isset($_SESSION["loggedin"])){
  $loggedin = $_SESSION["loggedin"];

  //Save uid and username in global variables
  $my_uid = $_SESSION['uid'];
  $my_user = $_SESSION['username'];

  //Check for a follow request
  if(isset($_REQUEST['follow']) && $_REQUEST['follow']){
      $followee = $conn->real_escape_string($_REQUEST['follow']);
      query("insert ignore into follows(uid, followee) values ($my_uid,$followee)");
  }

  //Check for an unfollow request
  if(isset($_REQUEST['unfollow']) && $_REQUEST['unfollow']){
      $unfollow = $conn->real_escape_string($_REQUEST['unfollow']);
      query("delete from follows where uid=$my_uid and followee=$unfollow");
  }

}

//Whose profile
if(isset($_GET['rid']))
  $recipeID=htmlspecialchars($_GET['rid']);
else{
  header("location: ../login/login.php");
  exit;
}

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

function getRecipe($result){
    global $name, $points, $date, $image, $description, $username, $prep, $cook, $rest, $ingr, $dir, $tags, $loggedin, $authorid;
    $row = $result->fetch_assoc();
    $authorid = $row['author'];
    $username = $row['username'];
    $name = $row['name'];
    $points = $row['points'];
    $likeText = ($points == 1) ? " like" : " likes";
    $date = ($loggedin) ? $row['date'] : ($row['date'] . " - ".$points.$likeText);
    $image = $row['photo'];
    $price = $row['price'];
    $diff = $row['difficulty'];
    $prep = $row['preparation'];
    $cook = $row['resting'];
    $rest = $row['preparation'];
    $kcals = $row['calories'];
    $servings = $row['servings'];
    $cal_serving = ($servings != 0 ) ? floor($kcals/$servings) : $kcals;
    $description = "Difficulty: " . $diff . "<br>Price: £" . $price . "<br>Calories: " . $cal_serving . " kcals/serving". "<br>Servings: " . $servings . (($servings != 1) ? " people": " person");
    $ingr = json_decode($row['ingredients']);
    $dir = json_decode($row['directions']);
    $tags = json_decode($row['tags']);
          
}


//Get recipe data
$result = query("select r.*, p.points, p.date, u.username
                from posts p
                join users u on p.uid = u.uid
                join recipes r on p.rid = r.rid
                where r.rid = $recipeID and p.type = 'Added'");

getRecipe($result);

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.80.0">
    <title><?php echo($name) ?></title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.6/examples/blog/"> 

    <!-- Bootstrap core CSS -->
  	<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){

        //Like posts
        $(document).on("click", ".like", function(){
          var rid = parseInt($(this).siblings(".rid").val());
          var parentDiv = $(this).parent();
          $.get("like-function.php", {rid: rid}).done(function(data){
                    // Display the returned data in browser
                    parentDiv.html(data);
                });
        });

        //Unlike posts
        $(document).on("click", ".unlike", function(){
          var rid = parseInt($(this).siblings(".rid").val());
          var parentDiv = $(this).parent();
          $.get("unlike-function.php", {rid: rid}).done(function(data){
                    // Display the returned data in browser
                    parentDiv.html(data);
                });
        });

        //Share posts
        $(document).on("click", ".share", function(){
          var rid = parseInt($(this).siblings(".rid").val());
          $.get("share-function.php", {rid: rid}).done(function(data){
                alert("Recipe has been shared successfully!");
                });
        });

        //Delete Recipe
        $(document).on("click", "#delete", function(){
          var rid = document.getElementById('rid').value;
          $.get("delete-recipe.php", {recipeid: rid}).done(function(data){
                    alert("Recipe deleted successfully!")
                    window.location = "profile.php"
                });
        });
    });
    </script>

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
      .like{
        margin:10px auto;
        fill:#000000;
        transition: 0.3s;
      }
      .like:hover{
        fill:#ff6666;
      }
      .unlike{
        transition: 0.3s;
        margin:10px auto;
        fill:#ff0000;
      }
      .unlike:hover{
        opacity:0.6;
      }
      .share{
        margin:10px auto;
        transition: 0.3s;
      }
      .share:hover{
        opacity:0.6;
      }
      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="recipe_styles.css" rel="stylesheet">

  </head>
  <body>
  	
    
<div class="container">
  <header class="profile-header py-5">
    <?php
    if(!$loggedin)
      echo(
      "<div class='row'>
        <div class='col align-self-end'>
          <p style='text-align:right;'>You are viewing this page as a guest. <a href='../login/login.php'>Click here to log in!</a></p>
        </div>
      </div>"
      );
    ?>
    <div class="row flex-nowrap justify-content-between align-items-center">
      <div class="col-4 pt-1">
        <a class="text-muted" href="#"></a>
        <?php
        if($loggedin)
          echo('<a class="btn btn-sm btn-outline-secondary" href="feed.php">< Feed</a>');
        else
          echo('<a class="btn btn-sm btn-outline-secondary" href="landing.php">< Top Recipes</a>')
        ?>
      </div>
      <div class="col-4 text-center">
        <a class="profile-header-logo text-dark" href="#">
          <?php 
          echo($name . "</a>"); 
          ?>
          
      </div>

      <div class="col-4 d-flex justify-content-end align-items-center">
      	<div class="dropdown">
  		<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   		My account
  		</button>
 		<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="<?php if(isset($_SESSION['loggedin'])) echo('profile.php?username='.$my_user); else echo('../login/login.php');?>">My Profile</a>
    		<a class="dropdown-item" href="landing.php">Top Recipes</a>
        <a class="dropdown-item" href="<?php if(isset($_SESSION['loggedin'])) echo('../UploadRecipePage.html'); else echo('../login/login.php');?>">Upload</a>
        <a class="dropdown-item" href="../map.html">Map</a>
    		<a class="dropdown-item" href="settings.php">Settings</a>
    		<a class="dropdown-item" href="../logout.php">Sign Out</a>
  		</div>
	    </div>
	  </div>
    </div>
  </header>


  <div class="jumbotron p-4 p-md-5 rounded bg-dark">
   
  </div>

  <div class="row mb-2">

    <div class="col-md-<?php if($loggedin) echo('10'); else echo('12');?>">
      <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <?php
          echo('
          <strong class="d-inline-block mb-2"><a class="text-dark" href="profile.php?username='.$username.'">'.$username.'\'s</a></strong>
          ');
          ?>
          <h3 class="mb-0"><?php echo($name); ?></h3>
          <div class="mb-1 text-muted"><?php echo($date); ?></div>
          <p class="card-text mb-auto"><?php echo($description); ?></p>
        </div>
        <div class="col-auto d-none d-lg-block">
          <?php
          if($image != null)
            echo('<img src="' . $image . '" alt="Thumbnail" class="recipe-thumbnail">');
          else
          echo('<svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>');
          ?>
        </div>
      </div>
    </div>
    <?php
  if($loggedin){
    $liked = getSingle("select * from posts p
              where p.rid=$recipeID and p.uid=$my_uid and p.type='liked'");
    echo("
    <div class='col-md-2'>
      <div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>
        <div class='col p-4 d-flex flex-column position-static'>
          <p style='text-align:center; margin:0px;'>Likes:</p>
          <strong style='font-size:40px; text-align:center;' id='points'>$points</strong>
          ");
    if($liked)
        echo('<svg class="unlike" alt="Unlike" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24"><path d="M12 4.248c-3.148-5.402-12-3.825-12 2.944 0 4.661 5.571 9.427 12 15.808 6.43-6.381 12-11.147 12-15.808 0-6.792-8.875-8.306-12-2.944z"/><title>Unlike</title></svg>');
    else
      echo('<svg class="like" alt="Like" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24"><path d="M6.28 3c3.236.001 4.973 3.491 5.72 5.031.75-1.547 2.469-5.021 5.726-5.021 2.058 0 4.274 1.309 4.274 4.182 0 3.442-4.744 7.851-10 13-5.258-5.151-10-9.559-10-13 0-2.676 1.965-4.193 4.28-4.192zm.001-2c-3.183 0-6.281 2.187-6.281 6.192 0 4.661 5.57 9.427 12 15.808 6.43-6.381 12-11.147 12-15.808 0-4.011-3.097-6.182-6.274-6.182-2.204 0-4.446 1.042-5.726 3.238-1.285-2.206-3.522-3.248-5.719-3.248z"/><title>Like</title></svg>');

    echo("
      <svg class='share' xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm1 15.889v-2.223s-3.78-.114-7 3.333c1.513-6.587 7-7.778 7-7.778v-2.221l5 4.425-5 4.464z'/><title>Share recipe</title></svg>
      <input type='hidden' class='rid' id='rid' value='$recipeID'>
        </div>
      </div>
    </div>
    ");
  }
    ?>
</div>

<div class="row mb-2">

    <div class="col-md-4">
      <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <h4 class="mb-1 text-center">Preparation time:</h4>
          <h5 class="mb-0 text-center"><?php echo($prep . " minutes" )?></h5>
        </div>
      </div>
    </div>    
    <div class="col-md-4">
      <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <h4 class="mb-1 text-center">Cooking time:</h4>
          <h5 class="mb-0 text-center"><?php echo($cook . " minutes" )?></h5>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <h4 class="mb-1 text-center">Resting time:</h4>
          <h5 class="mb-0 text-center"><?php echo($rest . " minutes" )?></h5>
        </div>
      </div>
    </div>

</div>

<main role="main" class="container">
  <div class="row">
    <div class="col-md-8 blog-main">
      <h3 class="pb-4 mb-4 font-italic border-bottom">Ingredients</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 blog-main">
      <ul class="list-group mb-4">
        <?php foreach($ingr as $key=>$value) echo(
          '<li class="list-group-item">'. htmlspecialchars(ucfirst($value)) . '</li>');?>
      </ul>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8 blog-main">
      <h3 class="pb-4 mb-4 font-italic border-bottom">Instructions</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 blog-main">
      <p class="pb-4 mb-4 font-italic"><?php foreach($dir as $key=>$value) echo(htmlspecialchars($value) . "<br>");?></p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8 blog-main">
      <h3 class="pb-4 mb-4 font-italic border-bottom">Tags</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 blog-main">
      <ul class="mb-4 pl-0 ml-3">
        <?php foreach($tags as $key=>$value) echo(
          '<li>'. htmlspecialchars($value) . '</li>');?>
      </ul>
    </div>
  </div>
  <?php
  if($loggedin && $my_uid == $authorid)
    echo('
  <div class="row mb-2 border-top">
    <div class="col-md-2 pt-4 blog-main">
    <button type="button" class="btn btn-danger" id="delete">Delete Recipe</button>
    </div>
  </div>
  ');
  ?>
    </div><!-- /.blog-main -->

  </div><!-- /.row -->
</main><!-- /.container -->

<footer class="profile-footer">
  <p>Blog template built for <a href="https://getbootstrap.com/">Bootstrap</a> by <a href="https://twitter.com/mdo">@mdo</a>.</p>
  <p>
    <a href="#">Back to top</a>
  </p>
</footer>


    
  </body>
</html>
