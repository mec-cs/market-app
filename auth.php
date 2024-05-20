<?php
session_start();

require "db.php";

// Check if authentication code is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['code'])) {

    $enteredCode = $_POST['code'];
    $storedCode = isset($_SESSION['auth_code']) ? $_SESSION['auth_code'] : null;

    // Check if entered code matches the stored code
    if ($storedCode && intVal($enteredCode) === $storedCode) {
        // Authentication successful, redirect to main.php
        $user = $_SESSION["user"];
        $register = registerUser($user["usertype"], $user["name"], $user["email"], $user["password"], $user["city"], $user["district"], $user["address"]);

        if ($register) {
            // var_dump($register);
            header("Location: ./main.php");
            exit;
        } else {
            header("Location: ./index.php?error");
            exit;
        }
    } else {
        // Authentication failed, show error message
        header("Location: index.php?error");
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
    <title>Market App Authentication</title>
</head>
<body>
<div class="bg"></div>
<div class="bg bg2"></div>
<div class="bg bg3"></div>
<div class="content">
   
   <div class="realDealBody">
      <form action="" method="post">
         <div class="containers">
            <h1 style="margin:15px;">Authentication</h1>
            <hr style="color:#a9a9a9;opacity:0.3;">
            <div class="container">
                <div class="label">
                    <p>Enter Authentication Code :</p>
                </div>
                <input type="text" name="code" id="code" placeholder="Enter code here">
            </div>
            <?php if(isset($error)): ?>
                <div class="error">
                    <p style='color:red;'><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            <hr style="color:#a9a9a9;opacity:0.3;">
            <div style="margin-bottom: 10px;">
                <button class="btnSpecial" role="submit">Authenticate</button>
            </div>
         </div>
      </form>
   </div>
</div>
</body>
</html>