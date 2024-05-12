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
    
    $user = $_SESSION["user"];

    $role = getUserRole($user['email']);
    //var_dump($role);
    if($role['role'] == "M"){
        $address = getAddress($user['email']);
        //var_dump($address);
        $market = getMarket($address['id']);
        //var_dump($market);


        $page = $_GET["page"] ?? 1 ; 
        if(isset($_POST['query'])){
            $query = $_POST['query'];
            $_SESSION['last_query'] = $query;
            $products = getMarketProductsByPageNumberQuery($market['c_id'], $query);

            $size = count($products); 

            $totalPages = ceil($size/PAGESIZE) ;

            $start = ($page - 1) * PAGESIZE ; 
            $end = $start + PAGESIZE ; 
            $end = $end > $size ? $size : $end ; 

            
        }
        else{
            if(isset($_SESSION['last_query'])){
                if($_SESSION['last_query'] == ""){
                    unset($_SESSION['last_query']);
                }
                if(isset($_SESSION['last_query']))
                    $products = getMarketProductsByPageNumberQuery($market['c_id'], $_SESSION['last_query']);
                else
                    $products = getMarketProductsByPageNumberQuery($market['c_id'], "");


                $size = count($products); 

                $totalPages = ceil($size/PAGESIZE) ;

                $start = ($page - 1) * PAGESIZE ; 
                $end = $start + PAGESIZE ; 
                $end = $end > $size ? $size : $end ; 
            }
            else{
                var_dump($market);
                $size = $market['number_of_products']; 
                $totalPages = ceil($size/PAGESIZE) ;

                $start = ($page - 1) * PAGESIZE ; 
                $end = $start + PAGESIZE ; 
                $end = $end > $size ? $size : $end ; 

                $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
            }
            
        }
            
         
        
        //var_dump($products);
        echo "<h1>";
        echo "Welcome ". $market['c_name'];
        echo "<h1>";

        echo "<a href=''>
        <img src='./assets/system/add.png' alt='Add' width='30'> Add New Product 
        </a>;";
        echo "<h1>Your Products</h1>";
    }
    else {
        $products = getAllProductsByPageNumber(0, 5);

        $size = count($products); 
        $totalPages = ceil($size/PAGESIZE) ;
    }

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
    

    <form action="" method="post">
    <input type="text" name="query" value="<?= isset($_SESSION['last_query']) ? $_SESSION['last_query'] : ''; ?>" placeholder="Search a product">
    </form>

    <table>
    <?php 
        echo "<tr>";
            echo "<th>";
                echo "";
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


        foreach($products as $p){
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
                    echo $p['p_expire'];
                echo "</td>";

                echo "<td>";
                    echo $p['p_price'];
                echo "</td>";

                echo "<td>";
                if($role['role'] == "M"){
                echo "
                <a href=''>
                    <img src='./assets/system/delete.png' alt='Delete' width='30'>
                </a>
                
                <a href=''>
                    <img src='./assets/system/edit.png' alt='Update' width='30'>
                </a>";
                } else {
                    echo "
                    <a href=''>
                        <img src='./assets/system/add.png' alt='Add' width='30'>
                    </a>";
                }
                echo "</td>";

            echo "</tr>";
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