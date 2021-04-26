<?php      
    include('connection.php');  

	//Return query object
	function query($query){
	    global $con;
	    $result = $con->query($query);
	    return $result;
	}

	//Return single element from query or null
	function getSingle($query){
	    $result = query($query);
	    $row = $result->fetch_row();
	    return isset($row[0]) ? $row[0] : null;
	}

    $username = $_POST['user'];  
    $password1 = $_POST['pass']; 
	$email = $_POST['email']; 
    
    //to prevent from mysqli injection  
    $username = stripcslashes($username);  
    $password1 = stripcslashes($password1);  
	$email = stripcslashes($email); 
    $username = mysqli_real_escape_string($con, $username);  
    $password1 = mysqli_real_escape_string($con, $password1);
	$email = mysqli_real_escape_string($con, $email);  
	$u1=trim($username); 
	$p1=trim($password1); 
	$non_unique = getSingle("select * from users where username='$username'");
	if($non_unique){
		echo('
			<!DOCTYPE html>
			<html lang="en">
			    <head>
			    </head>
			    <body>
			        <h1>Registration failed - username taken</h1>
			        <h2>Click <a href="Registration.html">here</a> to try again</h2>
			    </body>
			</html>
			');
	}
	else{
	$sql = "insert into users(username,passw,email) values( '$u1','" . password_hash($p1, PASSWORD_DEFAULT) . "','$email' )";
	$result = mysqli_query($con, $sql);
	header("location:register_to_login.html");
	exit;
}
/*$row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
$count = mysqli_num_rows($result); */

/*       if(isset($username)){
			if(isset($password1)
				&& isset($password2)
				&& $password1 == $password2){//The password is the same and is not empty
				//Put the information in the database
					$sql = "insert into login(username,password) value( '".$username."','".md5($password1)."' )";
					//echo $sql;
					if(mysqli_query($link,$sql)){
						echo "Registered successfully,<a href = 'login.html' target = '_blank'>Return to Personal Center</a>";
						setcookie("name",$username);			
					}else{				
						echo "sql error";
					}					
				}
				else{
					echo "Incorrect password input <br />";
					echo  "<a href='register.php'>Please re-register</a>";				
				}	
		}else{
			echo "The user name cannot be empty<br />";
			echo  "<a href='register.php'>Please re-register</a>";	
		}
	else{
		echo "Please submit correctly<br />";
		header("Location:register.php");	
	}
	*/
    

?>
