<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <title>Market App Main Page</title>
</head>
<body>


<?php 
    session_start() ;

    // Set CSP headers
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com;");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");

    require "db.php" ;
      const PAGESIZE = 4 ;
    // check if the user authenticated before
    if( !isUserAuthenticated()) {
        header("Location: index.php?error") ;
        exit ; 
    }

    function setPagings($size){
        global $page;
        global $totalPages;
        global $start;
        global $end;

        $totalPages = ceil($size/PAGESIZE);
        $start = ($page - 1) * PAGESIZE ; 
        $end = $start + PAGESIZE ; 
        $end = $end > $size ? $size : $end ; 
   }

    $user = $_SESSION["user"];
    $address = getAddress($user['email']);
    $role = getUserRole($user['email']);
    $page = $_GET["page"] ?? 1;

    $address = getConsumerAddress($user['email']);
    //var_dump($address);

    $marketList = getMarketListInAddress($address['city'], $address['district']);
    //var_dump($marketList);

    $size = 0;
    $products = [];
    $markets = []; // Initialize an array to store market details
    foreach ($marketList as $m) {
        // Assuming $m represents a market ID
        $marketDetails = getCompanyByName($m['name']);
        if ($marketDetails) {
            // If market details are retrieved successfully, add them to the $market array
            $markets[] = $marketDetails;
            //var_dump($marketDetails);
            $email = getEmailByCompanyName($m['name']);
            $address = getAddress($email['email']);
            $size += getNumberOfProducts(getMarket($address['id'])["c_id"]);
            setPagings($size);
            //var_dump($address);

            if(isset($_POST['query'])){
                $query = $_POST['query'];
                $_SESSION['last_query'] = $query;
                $products[] = getMarketProductsByQuery(getMarket($address['id'])["c_id"], $query);

                $size = count($products); 
                setPagings($size);
            }
            else{
                if(isset($_SESSION['last_query'])){
                    if($_SESSION['last_query'] == ""){
                        unset($_SESSION['last_query']);
                    }
                    if(isset($_SESSION['last_query']))
                        $products[] = getMarketProductsByQuery(getMarket($address['id'])["c_id"], $_SESSION['last_query']);
                    else
                        $products[] = getMarketProductsByQuery(getMarket($address['id'])["c_id"], "");


                    $size = count($products); 
                    setPagings($size);
                }
                else{
                    $market = getMarket($address['id']);
                    $size = $market['number_of_products']; 
                    setPagings($size);

                    $products[] = getMarketProductsByPageNumber($start, $end, getMarket($address['id'])["c_id"]);
                }

            }

        }
    }

    //var_dump($markets);
    //var_dump($size);
    //var_dump($products);

    if(isset($_POST['p_id_delete'])){
        if(isset($_SESSION['p_ids'][$_POST['p_id_delete']])) {
            unset($_SESSION['p_ids'][$_POST['p_id_delete']]); 
            if(empty($_SESSION['p_ids'])) {
                unset($_SESSION['p_ids']);
                echo "Array 'p_ids' is empty. Removed from session.<br>";
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
        }
        
        // Add or update a product ID in the associative array
        $new_product_id = $p_id; // Replace 123 with the actual product ID
        $product_amount = $amount; // Replace "Product ABC" with the actual product name
        $_SESSION['p_ids'][$new_product_id] = $product_amount;

        // Display all product IDs and their associated names
        echo "All product IDs and their associated amounts: <br>";
        foreach($_SESSION['p_ids'] as $product_id => $product_amount) {
            echo "Product ID: " . $product_id . ", Product Amount: " . $product_amount . "<br>";
        }
    }
?>
<div class="nav-links">
        <a href="./chart.php">View Chart</a>
        <a href="./profile.php">Profile</a>
        <a href="./logout.php">Logout</a>
    </div>
<form method="post">
    <input type="text" name="query" value="<?= isset($_SESSION['last_query']) ? $_SESSION['last_query'] : ''; ?>" placeholder="Search a product">
    </form>
<table>

    <?php 

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
                echo "EXPIRE DATE";
            echo "</th>";

            echo "<th>";
                echo "PRICE";
            echo "</th>";

            echo "<th>";
                echo "OPERATION";
            echo "</th>";
        echo "</tr>";
        foreach($products as $prod){
            foreach($prod as $p){
                $p_expire = $p["p_expire"];

                // Convert $p_expire to a Unix timestamp
                $p_expire_timestamp = strtotime($p_expire);

                // Get the current Unix timestamp
                $current_timestamp = time();
                if ($p_expire_timestamp <= $current_timestamp) {

                } else {
                    // Otherwise, default text color
                    //echo "<span>{$p['p_expire']}</span>";


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
                        echo $p["p_expire"];
                echo "</td>";

                echo "<td>";

                    echo $p['p_price'];

                echo "</td>";

                echo "<td>";
                 //if customer
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
        <button type='submit'>$text</button>
    </form>
                 ";
                if($text == 'Update chart'){
                    echo "
                    <form method='post'>
                    <input type='hidden' name='p_id_delete' value='{$p['p_id']}'>
                    <button type='submit'>Delete</button>
                </form>
                    ";
                }

                echo "</td>";

            echo "</tr>";
                }
        }
    }
?>


    </table>

    
    <script>
        console.log("Script is running");
    document.getElementById('form').addEventListener('submit', function(event) {
        const amountInput = document.getElementById('amount');
        const errorMessage = document.getElementById('error-message');
        const value = parseFloat(amountInput.value);

        // Log the value entered by the user
        console.log("Entered value:", value);

        // Prevent form submission for now
        //event.preventDefault();
    });
</script>
    

    <div class="pagination">
        <?php
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = isset($_GET['page']) && $_GET['page'] == $i ? 'active' : '';
                echo "<a href='?page=$i' class='$active'>$i</a>";
            }
        ?>
    </div>
</body>
<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        img {
            display: block;
            margin: 0 auto;
        }
        a img {
            vertical-align: middle;
        }
        form {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }
        input[type="text"] {
            width: 300px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="text"]::placeholder {
            color: #aaa;
        }
        input[type="text"]:focus {
            border-color: #007BFF;
            outline: none;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination a {
            color: #007BFF;
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
            font-size: 18px;
        }
        .pagination a:hover {
            background-color: #007BFF;
            color: #fff;
        }
        .pagination a.active {
            background-color: #007BFF;
            color: #fff;
            border-color: #007BFF;
        }
        .nav-links {
            display: flex;
            justify-content: flex-end;
            margin: 20px;
        }
        .nav-links a {
            color: #007BFF;
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 18px; /* Increased font size */
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-links a:hover {
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</html>