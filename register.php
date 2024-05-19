<?php 
      session_start();

      // Set CSP headers
      // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
      header("X-Content-Type-Options: nosniff");
      header("X-Frame-Options: DENY");
      header("X-XSS-Protection: 1; mode=block");

      require "db.php";
      require_once './vendor/autoload.php' ;
      require_once './mail.php' ;
     
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
         } else if(strlen($password) < 8){
            $errors["shortPassword"] = "Password must be at least 8 characters!";
         } else if(checkSpecialChar($password) != true){
            $errors["noSpecialCharPassword"] = "Password must include at least 1 special character!";
         } else if (!isValidName($name) || !isValidEmail($email)) {
            $errors["invalid"] = "Please give valid inputs to name or email!";
         } else {
   
            // user will be registered
            if (isset($remember)) {
               $token = sha1(uniqid() . "Private Key is Here" . time()); // generate a random text
               setcookie("remember_token", $token, time() + 60*60*24*365*1); // for 10 years
               setTokenToUser($token, $email);
            }

            // login as $user, and to store user data in the tmp/session file as crypted
            $user_data = ["usertype" => $usertype, "name" => $name, "email" => $email, "password" => $password, "city" => $city, "district" => $district, "address" => $address, "usrtoken" => $token];

            $_SESSION["user"] = $user_data;
            $_SESSION["auth_code"] = mt_rand(100000, 999999);
            
            // sending authentication mail to the user
            $send_flag = Mail::send($email, "Welcome from Market App Team!", $_SESSION["auth_code"]) ;
            
            if (!$send_flag) {
               $errors["mail_error"] = '<p>Authentication mail has been sent to your mail account. Please check the code and provide it to login the system.</p>';
               header("Location: register.php?mail_error");
               exit;
            }
            
            // var_dump($_POST);
            // var_dump($register);

            header("Location: auth.php");
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
    <link rel="stylesheet" href="./style/app.css">
    <title>Market App Register Page</title>
</head>
<body>
<div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>
<div class="content">
   
   <div class="realDealBody">
      <form action="?" method="post">
         <div class="containers">
            <h1 style="margin:15px;">Registration</h1>
            <hr style="color:#a9a9a9;opacity:0.3;">
            <div class="radio-buttons-container">
               <div class="radio-image-label">
                  <label for="customer">
                     <img class="type_logo" src="./assets/system/customer_32px.png" alt="">
                     <div class="radioButtonDiv">
                        <input type="radio" id="customer" class="userType" name="usertype" value="C" checked>
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
      <input type="text" name="name" id="name-input" placeholder="Customer or market" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
   </div>
     <div class="container">
        <div class="label">
           <p>Email :</p>
         </div>
         <input type="text" name="email" id="email" placeholder="eg. john@yahoo.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </div>
      <div class="container">
         <div class="label">
            <p>Password :</p>
         </div>
         <input type="password" name="password" id="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
      </div>
      <div class="container">
         <div class="label">
            <p>City :</p>
         </div>
         <input type="text" name="city" id="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
      </div>
      <div class="container">
         <div class="label">
            <p>District :</p>
         </div>
         <input type="text" name="district" id="district" value="<?php echo isset($_POST['district']) ? htmlspecialchars($_POST['district']) : ''; ?>">
      </div>
      <div class="container">
         <div class="label">
            <p>Address :</p>
         </div>
         <input type="text" name="address" id="address" placeholder="eg. Fye Ave Oxford 6850" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
      </div>
      <!-- <div style="display:flex; margin-top: 10px; margin-left: 10px;">
        <p>Remember Me:</p>
        <input type="checkbox" name="remember" id="remember">
      </div> -->
      <div class="checkbox-wrapper-2">
         <p>Remember Me:</p>
         <input type="checkbox" class="sc-gJwTLC ikxBAC" name="remember" id="remember">
      </div>
      <hr style="color:#a9a9a9;opacity:0.3;">
      <div style="margin-bottom: 10px;">
         <a href="./index.php" class="button">Get Back</a>
         <button class="btnSpecial" role="button">Register</button>
      </div>
   </div>
</form>


 <div class="error">
   <?php
      if(!empty($errors)){
         echo "<p style='color:red;'>" . implode('<br>', $errors) . "</p>";
      }
   ?>
   </div>
 </div>
</div>
</body>
</html>