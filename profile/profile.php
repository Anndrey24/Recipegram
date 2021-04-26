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
 
 $username = $my_user;

}else if(!isset($_GET['username'])){
  header("location: ../login/login.php");
  exit;
}

//Whose profile

if(isset($_GET['username']))
    $username=htmlspecialchars($_GET['username']);


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
    global $recipeID, $name, $points, $date, $image, $description;
    $row = $result->fetch_assoc();
    if ($row != null){
            $recipeID = $row['rid'];
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
            $description = "Difficulty: " . $diff . "<br>Price: £" . $price . "<br>Time: " . $total . " minutes";
          }
    else{
      $recipeID = null;
      $name = "Upload more Recipes";
      $points = 0;
      $date = "";
      $image = null;
      $description = "";
    }
}

function getActivity($query){
    global $recipeID, $name, $type, $date;
    $row = $query->fetch_assoc();
    $recipeID = $row['rid'];
    $name = $row['name'];
    $type = $row['type'];
    $date = $row['date'];
  }


$uid = getSingle("select uid from users where username = '$username'");

//Get recipes from this account
$bestRecipes = query("select r.rid, r.name, p.points, p.date, r.price, r.difficulty, r.preparation, r.cooking, r.resting, r.photo
                from posts p
                join users u on p.uid = u.uid
                join recipes r on p.rid = r.rid
                where p.type='added' and u.username ='$username'
                order by p.points desc
                limit 2");

$allRecipesText = "select r.rid, r.name, p.points, p.date, r.price, r.difficulty, r.preparation, r.cooking, r.resting, r.photo
                from posts p
                join users u on p.uid = u.uid
                join recipes r on p.rid = r.rid
                where p.type='added' and u.username ='$username'
                order by p.date desc";

$allRecipes = query($allRecipesText);

$follows = query("select u.username
                 from users u
                 join follows f on u.uid = f.followee
                 where f.uid='$uid'
                 limit 10");

$activity = query("select r.rid, r.name, p.type, p.date
                  from posts p
                  join recipes r on p.rid = r.rid
                  where (p.type='liked' or p.type='shared') and p.uid='$uid'
                  order by p.date desc
                  limit 4");

if ($activity){
  $noOfActivity = $activity->num_rows;
}
else{
  $noOfActivity = 0;
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.80.0">
    <title><?php echo($username) ?>'s Profile</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.6/examples/blog/">

    

    <!-- Bootstrap core CSS -->
	<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">



    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
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
    <link href="profile_styles.css" rel="stylesheet">

  </head>
  <body>
  	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    
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
      <div class="col-3 pt-1">
        <a class="text-muted" href="#"></a>
        <?php
        if($loggedin)
          echo('<a class="btn btn-sm btn-outline-secondary" href="feed.php">< Feed</a>');
        else
          echo('<a class="btn btn-sm btn-outline-secondary" href="landing.php">< Top Recipes</a>')
        ?>
      </div>
      <div class="'col p-1 position-static'">
        <?php
          $picture = getSingle("select profile_pic from users
              where uid='$uid'");
          if($picture)
              echo("<img class='img-fluid rounded-circle profile-picture mx-auto d-block' src='". $picture ."'>");
          else
              echo("<img class='img-fluid rounded-circle profile-picture mx-auto d-block' src='../assets/default.png'>");
        ?>
      </div>
      <div class="col-4 text-center">
        <a class="profile-header-logo text-dark" href="#">
          <?php 
          echo("Artisan Chef " . $username . "</a>"); 
          ?>
          
      </div>

      <div class="'col p-1 position-static'">
        <?php
          if($loggedin && $my_uid != $uid && getSingle("select  * from users where uid=$uid")){
            //Check if already following
            $check = getSingle("select followee from follows where uid=$my_uid and followee=$uid");
            //Set follow/unfollow request link
            $follow = ($check) ? "<a class='btn btn-outline-dark' href=?unfollow=$uid&username=$username>Unfollow</a>" : "<a class='btn btn-dark' href=?follow=$uid&username=$username>Follow</a>";
            echo("<div class='row flex-nowrap justify-content-between align-items-center'><div class='col-12 pt-3 text-center'>$follow</div></div>");
          }
          else{
            echo("
              <div class='row flex-nowrap justify-content-between align-items-center'><div class='col-12 pt-3 text-center'><a class='btn btn-outline-secondary disabled'>Follow</a></div></div>");
          }
        ?>

      </div>

      <div class="col-3 d-flex justify-content-end align-items-center">
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

    <div class="col-md-6">
      <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <?php
          //get first best recipe
          getRecipe($bestRecipes);
          ?>
          <strong class="d-inline-block mb-2"><?php echo($username); ?>'s</strong>
          <h3 class="mb-0"><?php echo($name); ?></h3>
          <div class="mb-1 text-muted"><?php echo($date); ?></div>
          <p class="card-text mb-auto"><?php echo($description); ?></p>
          <a href='<?php if($recipeID) echo("recipe.php?rid=".$recipeID); else echo("#"); ?>' class="stretched-link">More...</a>
        </div>
        <div class="col-auto d-none d-lg-block">
          <?php
          if($image != null)
            echo('<img src="'. $image . '" alt="Thumbnail" class="recipe-thumbnail">');
          else
          echo('<svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>');
          ?>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
          <?php
          //get second second recipe
          getRecipe($bestRecipes);
          ?>
          <strong class="d-inline-block mb-2"><?php echo($username); ?>'s</strong>
          <h3 class="mb-0"><?php echo($name); ?></h3>
          <div class="mb-1 text-muted"><?php echo($date); ?></div>
          <p class="card-text mb-auto"><?php echo($description); ?></p>
          <a href='<?php if($recipeID) echo("recipe.php?rid=".$recipeID); else echo("#"); ?>' class="stretched-link">More...</a>
        </div>
        <div class="col-auto d-none d-lg-block">
          <?php
          if($image != null)
            echo('<img src="' . $image . '" class="recipe-thumbnail">');
          else
          echo('<svg class="bd-placeholder-img" width="200" height="250" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"/><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>');
          ?>
        </div>
      </div>
    </div>

</div>


<main role="main" class="container">
  <div class="row">
    <div class="col-md-8 blog-main">
      <h3 class="pb-4 mb-4 font-italic border-bottom">
        All Recipes
      </h3>

      <?php 
        if (isset($_GET['pageno']))
          $pageno = htmlspecialchars($_GET['pageno']);
        else
          $pageno = 1;

        $no_of_recipes_per_page = 3;
        $offset = ($pageno-1) * $no_of_recipes_per_page; 
        $no_of_recipes = $allRecipes->num_rows;
        $total_pages = ceil($no_of_recipes / $no_of_recipes_per_page);

        $curr_page = query($allRecipesText . " limit $offset, $no_of_recipes_per_page");
        $no_of_recipes_curr_page = $curr_page->num_rows;
        for($i = 1; $i <= $no_of_recipes_curr_page; $i++){
          getRecipe($curr_page);
          print "<div class=\"profile-recipe\">\n";
          print "        <a href='recipe.php?rid=". $recipeID. "' class=\"profile-recipe-title\"> " . $name . "</a>\n";
          print "        <p class=\"profile-recipe-meta\">". $date ."</p>\n";
          print "        <p>". $description ."</p>\n";
          print "      </div>";
        } ?>

      <nav class="profile-pagination">
        <a class="btn btn-outline-primary 
        <?php 
          if($pageno >= $total_pages){
            echo('disabled" href="#'); 
          }else{
            echo('" href="?pageno=' . ($pageno + 1));
          } echo('&username='.$username)?>" href="#">Older</a>
        <a class="btn btn-outline-secondary 
        <?php 
          if($pageno <= 1){
            echo('disabled" href="#'); 
          }else{
            echo('" href="?pageno=' . ($pageno - 1));
          } echo('&username='.$username)?>" tabindex="-1" aria-disabled="true">Newer</a>
      </nav>

    </div><!-- /.blog-main -->

    <aside class="col-md-4 profile-friendbar">
      <div class="p-4 mb-3 bg-light rounded">
        <h4 class="font-italic">Activity</h4>
        <?php
        $displayed = 0;
        while($noOfActivity > 0 & $displayed < 4){
          $noOfActivity -= 1;
          $displayed += 1;
          getActivity($activity);
          if ($type == "Liked"){
            print "<p class=\"activity\">". $username ." liked a recipe</p>\n";
          }
          // if ($type == "Disliked"){
          //   print "<p class=\"activity\">". $username ." disliked a recipe</p>\n";
          // }
          if ($type == "Shared"){
            print "<p class=\"activity\">". $username ." shared a recipe</p>\n";
          }
          print "<a class=\"activity\" href='recipe.php?rid=".$recipeID."'>". $name ."</a>\n";
          print "<p class=\"activity-time\">". $date ."</p>";
        } 
        ?>
      </div>

      <div class="p-4">
        <h4 class="font-italic">Follows</h4>
        <ul class="friends-list mb-0">
          <?php 
          while($row = $follows->fetch_assoc()){ 
                  $followee = $row['username'];
                  print "<li><a href=\"../profile/profile.php?username=".$followee." \">". $followee ."</a></li>";
                  }
            ?>
        </ul>
      </div>
    </aside>

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
