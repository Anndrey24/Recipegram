<?php
    // Initialize the session
    session_start();

    // Check if the user is already logged in, if yes then redirect him to feed
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
        header("location: ../profile/feed.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta>
    <title>Recipegram - Login page</title>
    <style>
        #bottom {
            color: black;
            height: auto;
            width: auto;
            text-align: center;
            margin-top: 310px;
        }
        *{
            margin: 0;
            padding: 0;
        }
        html,body{
            background:url(bg.jpeg) no-repeat; 
            background-size: 100%;
            overflow: hidden;
            height:100%;
            width:100%;
        }
<!--we have not decided the the background pic -->
        .main{
            width: 100%;
            height: 100%;
            background:url(bg.jpeg);
            }
        .loginFrame{
            width: 700px;
            height: 400px;
            background: rgba(0,0,0,0.2);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            border-radius: 10px;
            padding: 50px 0;
            box-sizing: border-box;
            box-shadow: 0 0 5px 5px rgba(0,0,0,0.2);
        }
        .loginFrame > div{
            width: 350px;
            height: 50px;
            margin: 40px auto;
            color: rgb(255, 255, 255);
            font-size: 20px;

        }
        .loginFrame > p{
            text-align: center;
            color: rgb(255, 255, 255);
            font-family: "Times New Roman", Times, serif;
            font-size: 40px;
        }
        .loginFrame > div > span{
            display: inline-block;
            cursor: pointer;
            font-size: 20px;

        }
        .loginFrame > div input{
            width: 100%;
            height: 30px;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgb(253, 253, 253);
            outline: none;
            color: rgb(255, 255, 255);
            font-size: 20px;
        }
        .loginFrame .enterbtn{
            width: 200px;
            height: 50px;
            border-radius: 50px;
            background-image: linear-gradient(to right, #f1b308,#df1010);
            text-align: center;
            padding-top: 10px;
            box-sizing: border-box;
            font-size: 20px;
            cursor: pointer;
        }
        .loginFrame .enterbtn:hover{
            box-shadow: 0 0 20px rgba(0,0,0,0.4) inset;
        }
        input::-webkit-input-placeholder{
            color: #BDCADA;

        }
        svg{
            vertical-align: bottom;
        }
        #jump_regist {
            width:95%;
            padding-right: 50px;
            margin-bottom: 5px;
            display: inline-block;
            text-align: right;
            font-size: 20px;
            text-decoration: none;
            color: #f7f6f1;
        }
        .loginTitle{
            margin-left: 0px;
        }
        .errorMsg{
            float: right;
            margin-right: 100px;
            margin-top: -35px;
            color: #fa719d;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <!--background-->
    <div class="main">
    
<!--Login box-->
    <form name="f1" action = "authentication.php" onsubmit = "return validation()" method = "POST"> 
<!--back the the main page with user account-->
            <input type="hidden" name="action" value="login">
            <div class="loginFrame">
    <!--frame-->
                <p class="loginTitle">Recipegram</p>

                <a id="jump_regist" href="../profile/landing.php">Continue as guest</a>
                <a id="jump_regist" href="Registration.html">Register now</a><!-- link to the register page-->
                
    <!--user name-->
                <div class="user">
                    <label>
                        <span>

                        </span>
                        <span>Username</span>
                        <span class="errorMsg">  </span>
                        <input name="user" type="text" id="user">
                    </label>
                </div>
    <!--password-->
                <div class="password">
                    <label>
                        <span>

                        </span>
                        <span>Password</span>
                        <input name="pass" type="password" id="pass">
                    </label>
                </div>
    <!--Login button-->
                <div class="enterbtn">
                    <input type="submit" id="btn" value="Login" style="border: none;cursor: pointer">
                </div>
                
            </div>
    </form>  
    </div>



    <script>  
        function validation()  
        {  
            var id=document.f1.user.value;  
            var ps=document.f1.pass.value;  
            if(id.length=="" && ps.length=="") {  
                alert("User Name and Password fields are empty");  
                return false;  
            }  
            else  
            {  
                if(id.length=="") {  
                    alert("User Name is empty");  
                    return false;  
                }   
                if (ps.length=="") {  
                alert("Password field is empty");  
                return false;  
                }  
            }                             
        }  
    </script>  
</body>
</html>
