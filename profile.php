<?php 
    session_start() ;

    // Set CSP headers
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");

    require "db.php" ;

    // check if the user authenticated before
    if( !isUserAuthenticated()) {
        header("Location: index.php?error");
        exit ; 
    }

    // Prevent XSS attacks
    function validateInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data); // this function prevents
        return $data;
    }


    $user = $_SESSION["user"];

    $user_addresses = getAddress($user['email']);
    
    $role = getUserRole($user["email"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        extract($_POST);

        // validated parameters against attacks
        $v_name = validateInput($name);
        $v_email = validateInput($email);
        $v_password = validateInput($password);
        $v_city = validateInput($city);
        $v_district = validateInput($district);
        $v_address = validateInput($address);

        // Troubleshoot the values if errors occur
            // var_dump($user_addresses);
            // var_dump($v_name, $v_email, $v_password, $v_city, $v_district, $v_address, $user["email"]);
        //
         
        // update the profile of user whether s/he is a market or customer
        $updated = updateProfile($role, $v_name, $v_email, $v_password, $v_city, $v_district, $v_address, $user["email"]);
        
        if ($updated) {
            // also update session
            $_SESSION["user"] = ["name" => $v_name, "email" => $v_email, "password" => $v_password, "usrtoken" => isset($user["usrtoken"]) ? $user["usrtoken"] : ""];
        } else {
            $error["update"] = "Your changes cannot be saved to the system, please try again later!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/>
    <title>Market App - Profile Page</title>
</head>
<body> 

<div class="content">
    <div class="container mt-15">
        <div class="row justify-content-center">
            <div class="col-md-20">
                <div class="card">
                    <div class="card-header">
                        <h1>Profile <?= isset($profile["name"]) ? " : " . $profile["name"] : "" ?></h1>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($user['name']) ? $user['name'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="address">City</label>
                                <input type="text" name="city" id="address" class="form-control" value="<?php echo isset($user_addresses['city']) ? $user_addresses['city'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">District</label>
                                <input type="text" name="district" id="address" class="form-control" value="<?php echo isset($user_addresses['district']) ? $user_addresses['district'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" name="address" id="address" class="form-control" value="<?php echo isset($user_addresses['addr']) ? $user_addresses['addr'] : ''; ?>" required>
                            </div>
                            <a href="./main.php" class="btn btn-primary">Back</a>
                            <input type="submit" class="btn btn-primary" value="Submit"/>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="text-danger">
                            <?= isset($error["update"]) ? $error["update"] : "" ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/validate.js"></script>
</body>
</html>