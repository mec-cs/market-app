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
    if($role['role'] == "M"){
        $market = getMarket($address['id']);

        if(isset($_POST['query'])){
            $query = $_POST['query'];
            $_SESSION['last_query'] = $query;
            $products = getMarketProductsByPageNumberQuery($market['c_id'], $query);

            $size = count($products); 
            setPagings($size);
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
                setPagings($size);
            }
            else{
                $size = $market['number_of_products']; 
                setPagings($size);

                $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
            }
            
        }
         
        //var_dump($products);
        echo "<h1>";
        echo "Welcome ". $market['c_name'];
        echo "<h1>";

        echo "<h1>Your Products</h1>";
    }
    else {
        extract($address);
        $products = getAllProductsByPageNumber(0, 5, $city, $district);

        $size = count($products); 
        $totalPages = ceil($size/PAGESIZE) ;
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST["form"]) && $_POST["form"] == 'file') {
            foreach($_FILES as $fb => $file) {
                if ( $file["size"] == 0) {
                    if ( empty($file["name"])) {
                        $error[] = "No file selected for filebox '<b>$fb</b>'" ;
                    } else {
                        $error[] = "{$file['name']} is greater than max upload size in '<b>$fb</b>'" ;
                    } 
                } else {
                    move_uploaded_file($file["tmp_name"], "./files/" . $file["name"]) ;
                }
             } 
            var_dump($_POST);
        } else {
            updateProduct($_POST);
            $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
        }
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



    <form method="post">
    <input type="text" name="query" value="<?= isset($_SESSION['last_query']) ? $_SESSION['last_query'] : ''; ?>" placeholder="Search a product">
    </form>

    <table>
    <?=
    isset($_GET["edit"]) ? '<form action="?" method="post">' : '';
    ?>
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
                if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                    echo '<input type="text" name="p_name" value="" placeholder="';
                    echo $p["p_name"];
                    echo '">';
                } else {
                    echo $p['p_name'];
                }
                echo "</td>";

                echo "<td>";
                if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                    echo '<input type="text" name="p_stock" value="" placeholder="';
                    echo $p["p_stock"];
                    echo '">';
                } else {
                    echo $p['p_stock'];
                }
                echo "</td>";

                echo "<td>";
                if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                    echo '<input type="text" name="p_expire" value="" placeholder="';
                    echo $p["p_expire"];
                    echo '">';
                } else {
                    echo $p['p_expire'];
                }
                echo "</td>";

                echo "<td>";
                if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                    echo '<input type="text" name="p_price" value="" placeholder="';
                    echo $p["p_price"];
                    echo '">';
                } else {
                    echo $p['p_price'];
                }
                echo "</td>";

                echo "<td>";
                if($role['role'] == "M"){
                echo "
                <a href=''>
                    <img src='./assets/system/delete.png' alt='Delete' width='30'>
                </a>";
                if(isset($_GET["edit"])) {
                    echo "
                    <button style='border:0px solid black; background-color: transparent' name='p_id' value='$p[p_id]'action='?'><img src='./assets/system/save.png' alt='Save' width='30'></button>
                    <a href='?edit=$p[p_id]'>

                    </a>";
                } else {
                    echo "
                    <a href='?edit=$p[p_id]'>
                        <img src='./assets/system/edit.png' alt='Update' width='30'>
                    </a>";
                }

                } else { //if customer
                    echo "
                    <a href=''>
                        <img src='./assets/system/add.png' alt='Add' width='30'>
                    </a>";
                }
                echo "</td>";

            echo "</tr>";
        }
?>
    <?php isset($_GET["edit"]) ? '</form>' : ''; if($role['role'] === "M" && isset($_GET["add"]) ): ?>
            <form action="?" method="post" enctype="multipart/form-data">
            <tr>
            <td>
            <div style=" border: 5px solid blue; width: 50%; height: 50%;"
                id="drop_zone"
                ondrop="dropHandler(event);"
                ondragover="dragOverHandler(event);">
                <p>Drag one or more files to this <i>drop zone</i>.</p>
                <input type="hidden" type="file" name="p_file" value="" placeholder="Name">
                <input type="hidden" name="form" value="file" placeholder="Name">
            </div>
            </td>
            <td><input type="text" name="p_name" value="" placeholder="Name"></td>
            <td><input type="text" name="p_stock" value="" placeholder="Stock"></td>
            <td><input type="text" name="p_expire" value="" placeholder="Expire Date"></td>
            <td><input type="text" name="p_price" value="" placeholder="Price"></td>
            <td><button style="border:0px solid black; background-color: transparent" name="add" action="?"><img src="./assets/system/save.png" alt="Save" width="30"></button></td>
            </tr>
            </form>
    <?php else:  ?>
        <tr><td></td><td></td><td></td><td></td><td></td>
        <td><a href="?add"><img src="./assets/system/add.png" alt="Add" width="30"></a></td>
    <?php endif;  ?>
    </table>
    <a href="./profile.php">profile</a>
    <a href="./logout.php">logout</a>

    <br><br><br><br>

    <?php
         for ( $i=1; $i<= $totalPages; $i++) {
                echo "<a href='?page=$i'>$i</a> " ;
         }
    ?>
    <script>var input = document.querySelectorAll('input');for(i=0; i<input.length; i++){input[i].setAttribute('size',input[i].getAttribute('placeholder').length);}   </script>
    <script>
        function dropHandler(ev) {
             ev.preventDefault();
             if (ev.dataTransfer.items) {
                [...ev.dataTransfer.items].forEach((item, i) => {
                    if (item.kind === "file") {
                        const file = item.getAsFile();
                        const fileInput = document.querySelector('input[type="file"]');
    
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;
                    }
            });
            }
        }
        function dragOverHandler(ev) {
            ev.preventDefault();

        }

    </script>
</body>
</html>