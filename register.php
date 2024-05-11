<?php 
      session_start();
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
    <title>Market App Register Page</title>
</head>
<body>
   <h1>Market App</h1>
   <h3>Register Please</h3>
   <form action="?" method="post">
      <table>
         </tr>
            <label for="customer" class="radio-image-label">
               <img src="./assets/system/customer_32px.png" alt="">
               <input type="radio" id="customer" name="usertype" value="C">
            </label>
            <label for="market" class="radio-image-label">
               <img src="./assets/system/market_32px.png" alt="">
               <input type="radio" id="market" name="usertype" value="M">
            </label>
         </tr>
         <tr>
            <td>Name :</td>
            <td><input type="text" name="name" id="name-input"></td>
         </tr>
         <tr>
            <td>Email :</td>
            <td><input type="text" name="email" ></td>
         </tr>
         <tr>
            <td>Password :</td>
            <td><input type="password" name="password"></td>
         </tr>
         <tr>
            <td>User Type :</td>
            <td>
               <select name="usertype" id="user-checklist">
                  <option value="M" name="market">Market</option>
                  <option value="C" name="customer">Customer</option>
               </select>
            </td>
         </tr>
         <tr>
            <td>City :</td>
            <td><input type="text" name="city" id="city"></td>
         </tr>
         <tr>
            <td>District :</td>
            <td><input type="text" name="district" id="district"></td>
         </tr>
         <tr>
            <td>Address :</td>
            <td><input type="text" id="address" name="address" placeholder="Enter your address with comma"></td>
         </tr>
         <tr>
            <td>Remember Me:</td>
            <td><input type="checkbox" name="remember"></td>
         </tr>
         <tr>
            <td colspan="2"><button><i class="fa fa-right-to-bracket"></i>Register</button></td>
         </tr>
      </table>
   </form>

   <div class="error">
      <p style="color:red;">Given Error Text</p>
   </div>
</body>
</html>