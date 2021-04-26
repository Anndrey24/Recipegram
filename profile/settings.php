<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true){
    header("location: ../login/login.php");
    exit;
}

// Include connection to database file
require_once "../connectDB.php";

//Save uid and username in global variables
$my_uid = $_SESSION['uid'];
$my_user = $_SESSION['username'];

// Define variables and initialize with empty values
$password = $confirm_password = "";
$password_err = $confirm_password_err = "";

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

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST" & !isset($_GET['up'])){

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "UPDATE users SET passw=? WHERE uid=$my_uid";
         
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_password);
            
            // Set parameters
            $param_password = password_hash($conn->real_escape_string($password), PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: ../logout.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }


}
//if delete account pressed
if(isset($_GET['delete']))
  $deleting = true;
else
  $deleting = false;

//if changing password
if(isset($_GET['change']))
  $changing = true;
else
  $changing = false;

//if changing profile picture
if(isset($_GET['pic']))
  $changePic = true;
else
  $changePic = false;

// if photos name is not empty basically checks if file exists
if(!empty($_FILES["photo"]["name"])){
  // fileName is set to the name of the file to be uploaded
  $fileName = $_FILES['photo']["name"];
  $tmpfileName = $_FILES['photo']["tmp_name"];
  // the filePath is where we want to send the file to its a folder called uploads and uses the basename of the location of fileName
  $filePath = "uploads/" . basename($fileName);
  // The image encoded variable is the contents of the file encoded into base 64
  $imageEncoded = base64_encode(file_get_contents($tmpfileName));
  // we get the file's extension so that we can use it to help decode it from the database
  $imageext = strtolower(pathinfo($filePath,PATHINFO_EXTENSION));
  // This is the variable to be uploaded, it shows the data
  $image = 'data:'.$imageext.';base64,'.$imageEncoded;
}

// if the image variable exists then:
if(isset($image)){
// create SQL statement including the photo
$sql =  "UPDATE users SET profile_pic='$image' WHERE uid='$my_uid'";


$result = mysqli_query($conn, $sql);
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
    <title><?php echo($my_user) ?>'s Settings</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.6/examples/blog/">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <!-- Bootstrap core CSS -->
	  <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function(){

        //Delete Account
        $(document).on("click", "#delete", function(){
          $.get("delete-function.php", {failsafe: 1}).done(function(data){
                    alert("Account deleted successfully!")
                    window.location = "../logout.php"
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

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900" rel="stylesheet">
    
    <!-- Custom styles for this template -->
    <link href="settings_styles.css" rel="stylesheet">

  </head>
  <body>
  	
    
<div class="container">
  <header class="profile-header py-5">
    <div class="row flex-nowrap justify-content-between align-items-center">
      <div class="col-4 pt-1">
        <a class="text-muted" href="#"></a>
        <a class="btn btn-sm btn-outline-secondary" href="feed.php">< Feed</a>
      </div>
      <div class="col-4 text-center">
        <a class="profile-header-logo text-dark">My Settings</a>
          
      </div>

      <div class="col-4 d-flex justify-content-end align-items-center">
      	<div class="dropdown">
  		<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   		My account
  		</button>
 		 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    		<a class="dropdown-item" href="profile.php?username=<?php echo($my_user);?>">My Profile</a>
        <a class="dropdown-item" href="landing.php">Top Recipes</a>
        <a class="dropdown-item" href="<?php if(isset($_SESSION['loggedin'])) echo('../UploadRecipePage.html'); else echo('../login/login.php');?>">Upload</a>
        <a class="dropdown-item" href="../map.html">Map</a>
        <a class="dropdown-item" href="settings.php">Settings</a>
        <a class="dropdown-item" href="../logout.php">Sign Out</a>
  		</div>
	    </div>
	  </div>
  </header>


  <div class="jumbotron p-4 p-md-5 rounded bg-dark">
   
  </div>


<main role="main" class="container">
  <div class="row">
    <div class="col-md-12 profile-friendbar">
      <div class="p-4 mb-3 bg-light rounded">
        <?php

        if ($deleting){
          echo("
          <a>Are you sure you want to delete your account?</a><br>
          <button type=\"button\" class=\"btn btn-danger\" id='delete'>Yes</button>
          <button type=\"button\" class=\"btn btn-secondary\" onclick='location.href=\"settings.php\"'>No</button>
          ");
        }
        elseif($changing){
          echo('
            <form action="' .htmlspecialchars($_SERVER["PHP_SELF"]).'?change=1'. '" method="post">
              
            <div class="form-group ' . ((!empty($password_err)) ? 'has-error' : '') . '">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="' .$password.'">
                <span class="help-block">'.$password_err.'</span>
            </div>
            <div class="form-group ' . ((!empty($confirm_password_err)) ? 'has-error' : '').'">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="'.$confirm_password.'">
                <span class="help-block">'.$confirm_password_err.'</span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>

            ');
        }
        elseif($changePic){
          //get picture
          $picture = getSingle("select profile_pic from users
              where uid='$my_uid'");

          echo("
          <div class='row m-1'>
          <div class='col-md-2'>
            <div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>
              <div class='col p-4 d-flex flex-column position-static'>
                ");
                if($picture)
                  echo("<img class='img-fluid rounded-circle profile-picture mx-auto d-block' src='". $picture ."'>");
                else
                  echo("<img class='img-fluid rounded-circle profile-picture mx-auto d-block' src='../assets/default.png'>");
                echo("
              </div>
            </div>
          </div>

          <div class='col-md-8'>
            <div class='row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative'>

              <div class='col p-4 d-flex flex-column position-static'>
              ");
              if(isset($_GET['up']))
                //uploading photo
                if($_GET['up']==1){
                  echo("
                  <form name = \"myForm\" action=\"#\" method = \"post\" enctype=\"multipart/form-data\" onsubmit = \"return validateform()\" >
              
                    <div class=\"container\">
                    
                      <label for=\"photo\"><b>Upload a photo</b></label>
                      <input type=\"file\" id=\"photo\" name=\"photo\" accept=\"image/*\">
                      <hr>
                      <button type=\"submit\" class=\"uploadbtn\">Upload</button>
                    </div>
                  </form>
                      ");
                }
                //delete current photo
                else{
                  $sql =  "UPDATE users SET profile_pic=NULL WHERE uid='$my_uid'";
                  $result = mysqli_query($conn, $sql);
                  echo("deleted...");
                }
              else{
                //display options to upload or delete
                echo("
                <strong class='d-inline-block mb-2' style='z-index:2;'><a class='text-dark' href=\"settings.php?pic=1&up=1\">Upload</a></strong>
                <strong class='d-inline-block mb-2' style='z-index:2;'><a class='text-dark' href=\"settings.php?pic=1&up=0\">Delete</a></strong>
                ");
              }
              echo("

              </div>
            </div>
          </div> 
          </div>         

            ");
        }
        else{
          //settings
          echo("
            <h4 class=\"font-italic\">Change Account Settings</h4>
            <a href=\"settings.php?change=1\">Change password</a><br>
            <a href=\"settings.php?delete=1\">Delete account</a><br>
            <a href=\"settings.php?pic=1\">Change Profile Picture</a><br>
            ");
        }
        ?>
      </div>
    </div>
  </div><!-- /.row -->
</main><!-- /.container -->
    
  </body>
</html>
