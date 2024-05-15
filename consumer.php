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



?>
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
                    echo "
                    <a href=''>
                        <img src='./assets/system/add.png' alt='Add' width='30'>
                    </a>";

                echo "</td>";

            echo "</tr>";
                }
        }
    }
?>


    </table>

    <a href="./profile.php">profile</a>
    <a href="./logout.php">logout</a>

    <br><br><br><br>

    <?php
         for ( $i=1; $i<= $totalPages; $i++) {
                echo "<a href='?page=$i'>$i</a> " ;
         }
    ?>
</body>
</html>