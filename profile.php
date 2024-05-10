<?php 
    session_start() ;
    require "db.php" ;
      
    // check if the user authenticated before
    if( !isUserAuthenticated()) {
        header("Location: index.php?error");
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
    <title>Market App Profile Page</title>
</head>
<body>
    <h1>Profile Page</h1>
</body>
</html>