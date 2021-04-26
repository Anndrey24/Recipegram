<?php      
    include('connection.php'); 

    // Initialize the session
    session_start();

    // Check if the user is already logged in, if yes then redirect him to feed
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
        header("location: ../profile/feed.php");
        exit;
    }

    $username = $_POST['user'];  
    $password = $_POST['pass'];  
      
        //to prevent from mysqli injection  
        $username = stripcslashes($username);  
        $password = stripcslashes($password);  
        $username = mysqli_real_escape_string($con, $username);  
        $password = mysqli_real_escape_string($con, $password);  

        $sql = "select uid, username, passw from users where username = '$username'"; 
        
        $result = mysqli_query($con, $sql);  
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
        $count = mysqli_num_rows($result);  
          
        if($count == 1){  
            $uid = $row['uid'];
            $hashed_password = $row['passw'];
            if(password_verify($password, $hashed_password)){
                // Password is correct, so start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["uid"] = $row['uid'];
                $_SESSION["username"] = $username;                            
                
                // Redirect user to feed
                header("location: ../profile/feed.php");
            }
        }  
        echo "<h1> Login failed. Invalid username or password.</h1>";  
            
?>  