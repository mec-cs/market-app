<?php 
      session_start();

      // Set CSP headers
      // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
      header("X-Content-Type-Options: nosniff");
      header("X-Frame-Options: DENY");
      header("X-XSS-Protection: 1; mode=block");

      require "db.php";
     
      // if user auth, direct to the main.php
      if(isUserAuthenticated()) {
         header("Location: main.php");
         exit; 
      }

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
         extract($_POST);
         $token = null;
         $errors = [];

         if (checkExists($email) != false) {
            $errors["exist"] = "Mail is already registered to the system!";
         } else if ($name == "" || $email == "" || $password == "" || $city == "" || $address == "") {
            $errors["blank"] = "Form values can not be blank!";
         } else if (false) {
            // in this part form validation and verification must be implemented
            
            
            
            $errors["hack"] = "Dont even try bro!";
         } else {

            // validated input will be used as parameter, boolean return if registered or not
            $register = registerUser($usertype, $name, $email, $password, $city, $district, $address);
            
            // user will be registered
            if (isset($remember)) {
               $token = sha1(uniqid() . "Private Key is Here" . time()); // generate a random text
               setcookie("remember_token", $token, time() + 60*60*24*365*1); // for 10 years
               setTokenToUser($token, $email);
            }

            // login as $user, and to store user data in the tmp/session file as crypted
            $user_data = ["type" => $usertype, "name" => $name, "email" => $email, "password" => $password, "city" => $city, "district" => $district, "address" => $address, "usrtoken" => $token];

            $_SESSION["user"] = $user_data;
            
            header("Location: main.php");
            exit;
         }
      }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
   <link rel="stylesheet" href="style/styleRegister.css">
    <title>Market App Register Page</title>
</head>
<body>
   <div class="realDealBody">
      <form action="?" method="post">
         <div class="containers">
            <h1 style="margin:15px;">Registiration</h1>
            <hr style="color:#a9a9a9;opacity:0.3;">
            <div class="radio-buttons-container">
               <div class="radio-image-label">
                  <label for="customer">
                     <img class="type_logo" src="./assets/system/customer_32px.png" alt="">
                     <div class="radioButtonDiv">
                        <input type="radio" id="customer" class="userType" name="usertype" value="C">
                     </div>
                  </label>
               </div>
               <div class="radio-image-label">
                  <label for="market">
                     <img class="type_logo" src="./assets/system/market_32px.png" alt="">
                     <div class="radioButtonDiv">
                        <input type="radio" id="market" class="userType" name="usertype" value="M">
                     </div>
                  </label>
               </div>
            </div>
            
            <div class="container">
     <div class="label">
         <p>Name :</p>
      </div>
      <input type="text" name="name" id="name-input" placeholder="Customer or market">
   </div>
     <div class="container">
        <div class="label">
           <p>Email :</p>
         </div>
         <input type="text" name="email" id="email" placeholder="eg. john@yahoo.com">
      </div>
      <div class="container">
         <div class="label">
            <p>Password :</p>
         </div>
         <input type="password" name="password" id="password">
      </div>
      <div class="container">
         <div class="label">
            <p>City :</p>
         </div>
         <input type="text" name="city" id="city">
      </div>
      <div class="container">
         <div class="label">
            <p>District :</p>
         </div>
         <input type="text" name="district" id="district">
      </div>
      <div class="container">
         <div class="label">
            <p>Address :</p>
         </div>
         <input type="text" name="address" id="address" placeholder="eg. Fye Ave Oxford 6850">
      </div>
      <div style="display:flex; margin-top: 10px; margin-left: 10px;">
        <p>Remember Me:</p>
        <input type="checkbox" name="remember" id="remember">
      </div>
      <hr style="color:#a9a9a9;opacity:0.3;">
      <div style="margin-bottom: 10px;">
         <button><i class="fa fa-right-to-bracket"></i>&nbsp;&nbsp;Register</button>
      </div>
   </div>
</form>


 <div class="error">
   <p style="color:red;">Given Error Text</p>
   </div>
 </div>
</body>
</html>