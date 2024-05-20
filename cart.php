<?php
    session_start();
    // Set CSP headers
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");

    require "db.php" ;

    if( !isUserAuthenticated()) {
        header("Location: index.php?error");
        exit; 
    }

    if (isset($_SESSION['auth_code'])) {
        if (!isset($_SESSION["verify"])) {
            header("Location: auth.php?verify");
            exit;
        }
    }

    function refreshProducts(){
        global $products;
        if(isset($_SESSION['p_ids'])) {
            $products = [];
            foreach($_SESSION['p_ids'] as $product_id => $product_amount){
                $product = getProduct($product_id);
                $product['amount'] = $product_amount;
                $product['totalPrice'] = $product_amount * $product['p_price'];
                $products[$product_id] = $product;
            }
        }
    }

    $user = $_SESSION["user"];

    $products = [];
    if(isset($_SESSION['p_ids'])) {
        refreshProducts();
    }

    if(isset($_POST['p_id_delete'])){
        if(isset($_SESSION['p_ids'][$_POST['p_id_delete']])) {
            unset($_SESSION['p_ids'][$_POST['p_id_delete']]); 
            if(empty($_SESSION['p_ids'])) {
                unset($_SESSION['p_ids']);
                $products = [];
            }
            else {
                refreshProducts();
                }
            }
        }

    if(isset($_POST['amount'])){
        extract($_POST);
        //echo "AMOUNT: $amount";
        //echo "PRODUCT ID: $p_id";
        // Check if the 'p_ids' key exists in the session
        if(!isset($_SESSION['p_ids'])) {
            // If not, initialize it as an empty array
            $_SESSION['p_ids'] = array();
            $products = [];
        }
        
        // Add or update a product ID in the associative array
        $new_product_id = $p_id; // Replace 123 with the actual product ID
        $product_amount = $amount; // Replace "Product ABC" with the actual product name
        $_SESSION['p_ids'][$new_product_id] = $product_amount;
        refreshProducts();
    }

    if(isset($_POST['purchase'])){
        refreshProducts();
        shipProducts($products);
        unset($_SESSION['p_ids']);
        $products = [];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market App My Cart</title>
    <link rel="stylesheet" href="./style/main.css">
</head>
<body>
    <div class="nav-links">
        <a href="./main.php">Get Back</a>
        <a href="./profile.php">Profile</a>
        <a href="./logout.php">Logout</a>
    </div>
    <h1>My Cart</h1>
    <table>
    <?php
        $sum = 0;
        echo "<tr>";
            echo "<th>";
                echo "IMAGE";
            echo "</th>";

            echo "<th>";
                echo "NAME";
            echo "</th>";

            echo "<th>";
                echo "STOCK";
            echo "</th>";

            echo "<th>";
                echo "TOTAL PRICE";
            echo "</th>";

            echo "<th>";
                echo "COUNT";
            echo "</th>";
        echo "</tr>";

        foreach($products as $product_id => $p){
            echo "<tr>";
                echo "<td>";
                    echo "<img src='./assets/product/{$p['p_image']}' alt='' width='70'>";
                echo "</td>";

                echo "<td>";
                echo $p['p_name'];
                echo "</td>";

                echo "<td>";
                echo $p['p_stock'];
                echo "</td>";

                echo "<td>";
                echo $p["totalPrice"];
                $sum += $p["totalPrice"];
                echo "</td>";

                echo "<td>";
                    $value = '';
                    $text = 'Add chart';
                    if(isset($_SESSION['p_ids'])){

                        foreach($_SESSION['p_ids'] as $product_id => $product_amount) {
                            $value = '';
                            $text = 'Add chart';
                            if($product_id == $p['p_id']){
                                $value = $product_amount;
                                $text = 'Update chart';
                                break;
                            }
                        }
                    }
                    echo "
                    <form id='form' method='post'>
                    <label for='amount'>Amount: </label>
                    &nbsp;
                    <input type='number' name='amount' id='amount' min='1' max='{$p['p_stock']}' step='1' value='{$value}' required>
                    <input type='hidden' name='p_id' value='{$p['p_id']}'>
                    <span id='error-message' style='color: red; display: none;'>Not enough stock </span>
                    &nbsp;
                    <button class='btnClass' type='submit'>$text</button>
                    </form>
                    ";
                    if($text == 'Update chart'){
                        echo "
                        <form method='post'>
                        <input type='hidden' name='p_id_delete' value='{$p['p_id']}'>
                        <button class='btnClass' type='submit'>Delete</button>
                        </form>
                        ";
                    }
                echo "</td>";
            echo "</tr>";
        }

        if(empty($products)){
            echo "<tr>";
                echo "<td colspan='5'>";
                    echo "No products in the cart";
                echo "</td>";
            echo "</tr>";
        }

        echo "<tr>";
            echo "<td colspan='3'>";
                echo "Total Price";
            echo "</td>";
            echo "<td>";
                echo $sum;
            echo "</td>";
            echo "<td>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='purchase'>";
                if (empty($products)){
                    echo "<button class='btnClass' disabled>Buy</button>";
                }
                else {
                    echo "<button class='btnClass' onclick='alert(`Thank you for your purchase!`)'>Buy</button>";
                }
                echo "</form>";
            echo "</td>";
        echo "</tr>";
    ?>
    </table>
</body>
</html>