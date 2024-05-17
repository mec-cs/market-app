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
      if (checkUser($email, $password, $user) ) {
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
    <link rel="stylesheet" href="./style/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Market App Login</title>
</head>
<body>

<div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>
<div class="content">
    <div class="container mt-15">
        <div class="row justify-content-center">
            <div class="col-md-20">
                <div class="card">
                    <div class="card-header">
                        <h1>Login</h1>
                    </div>
                    <div class="card-body">
                        <form action="?" method="post">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <button class="btnSpecial" role="button">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Not a member ? <a href="./register.php">Sign up</a> now.</p>
                        <p class="text-danger"></p>
                        <?php
                            if(isset($fail)) {
                                echo '<p class="text-danger">Wrong email or password</p>';
                            } elseif (isset($_GET["error"])) {
                                echo '<p class="text-danger">Please <b>register</b> to the system.</p>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- bootstrap js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>