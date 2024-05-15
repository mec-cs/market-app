<?php 
    session_start() ;

    // Set CSP headers
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");

    require "db.php" ;
    
    function getCustomer($mail) {
        global $db;
        $stmt = $db->prepare("select * from user_table where email=?");
        $stmt->execute([$mail]);
        return $stmt->fetch();
    }

    // check if the user authenticated before
    if( !isUserAuthenticated()) {
        header("Location: index.php?error");
        exit ; 
    }

    $user = $_SESSION["user"];
    $usr = getUserRole($user['email']);
    $role = $usr["role"];

    if($role == "C"){
        $customer = getCustomer($user["email"]);
        //var_dump($customer);
    }
    if($role == "M"){
        $marketUser = getMarket($user['email']);
        //var_dump($marketUser['c_name']);
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
                        <h1>Profile Page</h1>
                    </div>
                    <div class="card-body">
                        <form action="?" method="post">

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Adress</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Enter your address" required>
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
    

    <?php
    
        echo "<table>";
            echo "<tr>";
                echo "<td>";
                if($market["c_image"] != NULL){
                    echo "<img src='./assets/company/{$market[c_image]}'>";
                }
                else{
                    echo "<img src='./assets/company/{default.png}'>";
                }
                echo "</td>";
            echo "</tr>";

            echo "<tr>";
                echo "<td>";
                    echo "NAME";
                    echo "<br>";
                    //echo "$market['c_name']";
                echo "</td>";
            echo "</tr>";

            echo "<tr>";
                echo "<td>";
                    echo "ADDRESS";
                    echo "<br>";
                    //echo "$market['c_address_table']";
                echo "</td>";
            echo "</tr>";
        echo "</table>";


        if(isset($_GET["edit"])){
            
            echo "<tr>";
                echo "<th>";
                    echo "IMAGE";
                echo "</th>";
            echo "</tr>";

            echo "<tr>";
                echo "<th>";
                    echo "NAME";
                echo "</th>";
            echo "</tr>";

            echo "<tr>";
                echo "<th>";
                    echo "ADDRESS";
                echo "</th>";
            echo "</tr>";
        }
    ?>


</body>
</html>