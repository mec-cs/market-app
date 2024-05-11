<?php
   session_start();
   
   // Set CSP headers
   // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
   header("X-Content-Type-Options: nosniff");
   header("X-Frame-Options: DENY");
   header("X-XSS-Protection: 1; mode=block");

   require "db.php" ;

   // Process Login Form
   if (!empty($_POST)) {
      extract($_POST) ;
      if (checkUser($email, $passwd, $user) ) {
         // user is authenticated
         
         // remember me token
         if (isset($remember)) {
            $token = sha1(uniqid() . "Private Key is Here" . time() ); // generate a random text
            setcookie("remember_token", $token, time() + 60*60*24*365*10); // for 10 years
            setTokenToUser($token, $email);
         }

         // login as $user
         $_SESSION["user"] = $user; 
         var_dump($_POST);
         header("Location: main.php");
         exit;

      } else { 
         $fail = true ; 
      }
  }

  // Remember-me part
   if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_COOKIE["remember_token"])) {
      $user = getUserByToken($_COOKIE["remember_token"]);
      if ($user) {
         // auto login
        $_SESSION["user"] = $user;
        header("Location: main.php");
        exit; 
   }
}
 
 // if the user has already logged in, don't show login form
   if ($_SERVER["REQUEST_METHOD"] == "GET" && isUserAuthenticated()) {
      // auto login8o98
      header("Location: main.php") ;
      exit;
   } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet' type='text/css'>
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
    <title>Market App Login Page</title>
</head>
<body>

<div class="loginTemplate">
   <h1>Log In</h1>
   <form action="?" method="post">
      <hr>
      <label id="icon" for="name"><i class="icon-envelope "></i></label>
      <input type="text" name="email" id="name" placeholder="Email" required/>
      <label id="icon" for="name"><i class="icon-shield"></i></label>
      <input type="password" name="passwd" id="name" placeholder="Password" required/>
      <button class="loginButton">Log In</button>
      <p>If you do not have an account, you may <a href="./register.php">sign up</a>.</p>
   </form>
   <?php
      if ( isset($fail)) {
         echo "<p class='error'>Wrong email or password.</p>" ; 
      }
      
      if ( isset($_GET["error"])) {  
        echo "<p class='error'>Please <b>register</b> to the system.</p>" ; 
      }
   ?>
</div>
</body>
</html>