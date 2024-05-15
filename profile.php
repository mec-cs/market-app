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

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $user = $_SESSION["user"];
        $u_data = getUserRole($user['email']);
        $role = $u_data["role"];
    
        if($role == "C"){
            $profile = getCustomer($user["email"]);
            //var_dump($customer);
        }
    
        if($role == "M"){
            $profile = getMarket($user['email']);
            //var_dump($marketUser['c_name']);
        }
    
        $address = getAddress($profile['email']);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        extract($_POST);

        // validated inputs will be comin

        
    }
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


<div class="content">
    <div class="container mt-15">
        <div class="row justify-content-center">
            <div class="col-md-20">
                <div class="card">
                    <div class="card-header">
                        <h1>Profile <?= isset($profile["name"]) ? " : " . $profile["name"] : "" ?></h1>
                    </div>
                    <div class="card-body">
                        <form action="?" method="post">

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($profile['email']) ? $profile['email'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($profile['name']) ? $profile['name'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">City</label>
                                <input type="text" name="city" id="address" class="form-control" value="<?php echo isset($address['city']) ? $address['city'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">District</label>
                                <input type="text" name="district" id="address" class="form-control" value="<?php echo isset($address['district']) ? $address['district'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" name="address" id="address" class="form-control" value="<?php echo isset($address['addr']) ? $address['addr'] : ''; ?>" required>
                            </div>
                            <button class="btnSpecial" role="button">Save</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="text-danger"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>