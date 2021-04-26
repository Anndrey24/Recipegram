<?php


// Start session
session_start();


// If user is not logged in then take the user to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_REQUEST['recipeName'])){
  header("location: login/login.php");
  exit;
} else{
  // else set the variable my_uid to the uid session variable
  $my_uid = $_SESSION['uid'];
}


//include the connectDB page which contains everything to connect to the DB and the $conn variable
include 'connectDB.php';


// Request different variables from the UploadRecipePage.html e.g. name, difficulty, directions, tags etc..
$name = $_REQUEST['recipeName'];
$diff = $_REQUEST['diff'];
$prep = (int)$_REQUEST['prep'];
$cook = (int)$_REQUEST['cook'];
$rest = (int)$_REQUEST['rest'];
$serv = (int)$_REQUEST['servings'];
$directions = $_REQUEST['directions'];
$tags = $_REQUEST['tags'];
$ingredients = $_POST['ingHidden'];
$calories = $_POST['KCalHidden'];
$price = $_POST['Hiddenprice'];


/// Create an array ingarray which is an exploded ingredients array
$ingarray = explode(',', $ingredients);
// json encode the ingarray for it to be passed to the database
$ingarray = json_encode($ingarray);

// Create an array called dirarray which is the directions array exploded
$dirarray = explode('|', $directions);
// Encode this array to be sent to the database
$directions = json_encode($dirarray);

$tags = str_replace("'", "", $tags);
$tags = str_replace('"', "", $tags);
// Create an array splitTags which is the exploded version of tags
$splitTags = explode(",", $tags);
// for the length of split tags
for ($i=0; $i < count($splitTags); $i++) { 
  // remove the whitespace in the tags
  $splitTags[$i] = trim($splitTags[$i]);
}
// Set tags to the json encoded split tags so it can be sent to DB
$tags = json_encode($splitTags);

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
$sql =  "INSERT into recipes (name, price, difficulty, preparation, cooking, resting, servings, ingredients, directions, tags, calories, author, photo) VALUES ('$name', '$price','$diff', '$prep', '$cook', '$rest', '$serv', '$ingarray', '$directions', '$tags', '$calories', '$my_uid', '$image')";

} else {
// else dont include it
  $sql =  "INSERT into recipes (name, price, difficulty, preparation, cooking, resting, servings, ingredients, directions, tags, calories, author) VALUES ('$name', '$price','$diff', '$prep', '$cook', '$rest', '$serv', '$ingarray', '$directions', '$tags', '$calories', '$my_uid')";
}
$result = mysqli_query($conn, $sql);

// Get rid of recipe
$rid = mysqli_insert_id($conn);

// Add "Recipe Added" post
$sql = "INSERT into posts (type, rid, uid) values ('Added', $rid, $my_uid)";
$result = mysqli_query($conn, $sql);

//use mysqli query with $conn and the sql statement to send data to DB
header("location: profile/recipe.php?rid=".$rid);
?>
