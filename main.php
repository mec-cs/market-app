<?php 
    session_start() ;
    require "db.php" ;
      
    // check if the user authenticated before
    if( !isUserAuthenticated()) {
        header("Location: index.php?error") ;
        exit ; 
    }
     
    $user = $_SESSION["user"];



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <title>Market App Main Page</title>
</head>
<body>
    <h1>Market App</h1>
    <?php 
        // if user market customer
            // show market.php
        // else if user default customer
            // show customer.php



        // edit profile button
            // directing to the profile.php
    ?>

    <a href="/profile.php">profile</a>

    <a href="/logout.php">logout</a>

</body>
</html>