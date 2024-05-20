<?php 
    // to resume or start session
    session_start();
    require "db.php";

    if (!isUserAuthenticated()) {
        header("Location: index.php");
        exit;
    }

    if (isset($_SESSION['auth_code'])) {
        if (!isset($_SESSION["verify"])) {
            header("Location: auth.php?verify");
            exit;
        }
    }

    // delete remembering cache 
    setTokenToUser(null, $_SESSION["user"]["email"]);
    setcookie("remember_token", "", 1);

    // delete session file
    session_destroy();

    setcookie("PHPSESSID", "", 1, "/"); // delete memory cookie

    // redirecting to login page
    header("Location: index.php");
?>