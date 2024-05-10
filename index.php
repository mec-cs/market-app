<?php
   session_start();
   require "db.php" ;

   // Process Login Form
   if (!empty($_POST)) {
      extract($_POST) ;
      if (checkUser($email, $pass, $user) ) {
         // user is authenticated
         
         // remember me token
         if (isset($remember)) {
            $token = sha1(uniqid() . "Private Key is Here" . time() ); // generate a random text
            setcookie("remember_token", $token, time() + 60*60*24*365*10); // for 10 years
            setTokenToUser($token, $email);
         }

         // login as $user
         $_SESSION["user"] = $user; 
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
    <title>Market App Login Page</title>
</head>
<body>
    <h1>Market App</h1>
    <form action="?" method="post">
       <table>
         <tr>
            <td>Email :</td>
            <td><input type="text" name="email" ></td>
         </tr>
         <tr>
            <td>Password : </td>
            <td><input type="password" name="pass"></td>
         </tr>
         <tr>
            <td>Remember :</td>
            <td><input type="checkbox" name="remember"></td>
         </tr>
         <tr>
            <td colspan="2"><button><i class="fa fa-right-to-bracket"></i>Login</button></td>
         </tr>
       </table>
    </form>
    <?php
      if ( isset($fail)) {
         echo "<p class='error'>Wrong email or password</p>" ; 
      }
      
      if ( isset($_GET["error"])) {  
        echo "<p class='error'>You tried to access main.php directly</p>" ; 
      }
    ?>
</body>
</html>