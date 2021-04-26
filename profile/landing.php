<?php
// Initialize the session
session_start();
 
// Include connection to database file
require_once "../connectDB.php";

$loggedin = false;
if(isset($_SESSION["loggedin"]))
  $loggedin = $_SESSION["loggedin"];

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
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.80.0">
    <title>Recipegram</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.6/examples/blog/">

    <!-- Add icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Bootstrap core CSS -->
	  <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    
    <!-- Search Bar Functionality -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <!-- Inview Js (jquery.inview.js) -->
    <script src="../assets/jquery.inview.js"></script>

    <script type="text/javascript">
    $(document).ready(function(){
        $('.search-box input[type="text"]').on("keyup input", function(){
            /* Get input value on change */
            var inputVal = $(this).val();
            var resultDropdown = $(this).siblings(".result");
            if(inputVal.length){
                $.get("search-function.php", {term: inputVal}).done(function(data){
                    // Display the returned data in browser
                    resultDropdown.html(data);
                    if(resultDropdown.children().length > 1)
                      resultDropdown.show();
                    else
                      resultDropdown.hide();
                });
            } else{
                resultDropdown.empty();
                resultDropdown.hide();
            }
        });

        //Show results on focus of search bar
        $( ".search-box input[type='text']" ).focus(function() {
          var inputVal = $(this).val();
          var resultDropdown = $(this).siblings(".result");
          if(resultDropdown.children().length > 1)
            resultDropdown.show();
        });

        //Hide results when search bar is not in focus
        $(document).click(function(e){
          if($(e.target).is('.search-box *'))return;
          $('.search-box').children(".result").hide();
        });

        $('#loader').on('inview', function(event, isInView) {
                 if (isInView) {
                     var nextPage = parseInt($('#pageno').val())+1;
                     $.ajax({
                         type: 'POST',
                         url: 'top-posts.php',
                         data: { pageno: nextPage },
                         success: function(data){
                             if(data != ''){               
                                 $('.post-feed').append(data);
                                 $('#pageno').val(nextPage);
                             } else {                
                                 $("#loader").hide();
                             }
                         }
                     });
                 }
             });
    });
    </script>

    <style>
      .button {
        background-color: #4CAF50; /* Green */
        border: none;
        color: white;
        padding: 10px 2px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 12px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 4px;
      }
      .button-up {
        background-color: green;
        margin: 10px;
      }
      .button-down {
        background-color: red;
        margin: 10px;
      }
    	.bg-cover {
    		background-image: url('logo.jpeg');
    	}
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
      #loader{
            display: block;
            margin: 50px auto;
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
    <link href="feed_styles.css" rel="stylesheet">

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
        <div class="col-10 input-group rounded">
          <span class="input-group-text bg-white border-0" id="search-addon">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
          </svg>
          </span>
          <div class="search-box dropdown">
            <input type="text" class="form-control rounded" autocomplete="off" placeholder="Search ..." aria-label="Search" aria-describedby="search-addon" />
            <div class="result dropdown-menu col-12"></div>
          </div>
        </div>
      </div>
      <div class="col-4 text-center">
        <a class="profile-header-logo text-dark" href="#">Top Recipes</a>
      </div>

      <div class="col-4 d-flex justify-content-end align-items-center">
      	<div class="dropdown">
  		<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   		My account
  		</button>
 		<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    		<a class="dropdown-item" href="profile.php">My Profile</a>
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


  <div class="jumbotron p-4 p-md-5 rounded bg-cover">
    <!--<div class="col-md-6 px-0">
      <h1 class="display-4 font-italic"></h1>
      <p class="lead my-3"></p>
      <p class="lead mb-0"><a href="#" class="text-white font-weight-bold"></a></p>
    </div>-->
  </div>

  <div class="post-feed">

  </div>
  <input type="hidden" id="pageno" value="0">
  <img id="loader" src="../assets/loading.svg">
  <footer class="profile-footer">
    <p>Blog template built for <a href="https://getbootstrap.com/">Bootstrap</a> by <a href="https://twitter.com/mdo">@mdo</a>.</p>
    <p>
      <a href="#">Back to top</a>
    </p>
  </footer>
  </body>
</html>
