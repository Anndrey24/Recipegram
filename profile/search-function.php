<?php

require_once "../connectDB.php";
 
if(isset($_REQUEST["term"])){
    // Prepare a select statement
    $sql1 = "SELECT u.username FROM users u WHERE u.username LIKE ? LIMIT 5";
    $sql2 = "SELECT r.name, r.rid FROM recipes r WHERE r.name LIKE ? LIMIT 5";
    
    if($stmt1 = $conn->prepare($sql1)){
        if($stmt2 = $conn->prepare($sql2)){
            // Bind variables to the prepared statement as parameters
            $stmt1->bind_param("s", $param_term);
            $stmt2->bind_param("s", $param_term);
            
            // Set parameters
            $param_term = $_REQUEST["term"] . '%';
            
            // Attempt to execute the first prepared statement
            if($stmt1->execute()){
                $result1 = $stmt1->get_result();
                
                // Check number of rows in the result set
                if($result1->num_rows > 0){
                    // Fetch result rows as an associative array
                    while($row = $result1->fetch_array(MYSQLI_ASSOC)){
                        $link_user = $row["username"];
                        echo "<a class='dropdown-item' href= 'profile.php?username=$link_user'>" . $link_user . "</a>";
                    }
                }
                
            } else{
                echo "ERROR: Could not able to execute $sql1. " . mysqli_error($conn);
            }
            $stmt1->close();

            //Add divider
            echo('<div class="dropdown-divider"></div>');
            
            // Attempt to execute the second prepared statement
            if($stmt2->execute()){
                $result2 = $stmt2->get_result();

                // Check number of rows in the result set
                if($result2->num_rows > 0){
                    // Fetch result rows as an associative array
                    while($row = $result2->fetch_array(MYSQLI_ASSOC)){
                        $link_recipe = $row["rid"];
                        $recipe_name = $row["name"];
                        echo "<a class='dropdown-item' href= 'recipe.php?rid=$link_recipe'>" . $recipe_name . "</a>";
                    }
                }
                
            } else{
                echo "ERROR: Could not able to execute $sql2. " . mysqli_error($conn);
            }
            $stmt2->close();
        }
    }
}
 
// Close connection
$conn->close();
?>