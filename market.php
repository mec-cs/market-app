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
        $start = ($page - 1) * PAGESIZE;
        $end = $start + PAGESIZE;
   }

    $user = $_SESSION["user"];
    $address = getAddress($user['email']);
    $role = getUserRole($user['email']);
    $page = $_GET["page"] ?? 1;

    $size = getNumberOfProducts(getMarket($address['id'])["c_id"]);
    setPagings($size);
    $market = getMarket($address['id']);

    if(isset($_POST['query'])){
        $query = $_POST['query'];
        $_SESSION['last_query'] = $query;
        $products = getMarketProductsByQuery($market['c_id'], $query);

        $size = count($products); 
        setPagings($size);
    }
    else{
        if(isset($_SESSION['last_query'])){
            if($_SESSION['last_query'] == ""){
                unset($_SESSION['last_query']);
            }
            if(isset($_SESSION['last_query']))
                $products = getMarketProductsByQuery($market['c_id'], $_SESSION['last_query']);
            else
                $products = getMarketProductsByQuery($market['c_id'], "");


            $size = count($products); 
            setPagings($size);
        }
        else{
            $market = getMarket($address['id']);
            $size = $market['number_of_products']; 
            setPagings($size);

            $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
        }
        
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($query)){
        if(isset($_POST["form"]) && $_POST["form"] == 'add') { //add
            foreach($_FILES as $fb => $file) {
                if ( $file["size"] == 0) {
                    if ( empty($file["name"])) {
                        $error[] = "No file selected for filebox '<b>$fb</b>'" ;
                    } else {
                        $error[] = "{$file['name']} is greater than max upload size in '<b>$fb</b>'" ;
                    } 
                    extract($_POST);
                    addProduct($p_name, $p_stock, $p_expire, $market["c_id"], "default.png", $p_price, $p_altprice);
                } else {
                    extract($_POST);
                    move_uploaded_file($file["tmp_name"], "./assets/product/" . $file["name"]) ;
                    addProduct($p_name, $p_stock, $p_expire, $market["c_id"], $file["name"], $p_price, $p_altprice);
                }
                $size = getNumberOfProducts($market["c_id"]);
                setPagings($size);
                $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
             } 
        } elseif (isset($_POST["discount"])) { //change discounted attribute
            changeProductDiscount(abs($_POST["discount"]));
            $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
        } 
        else { //edit
            updateProduct($_POST);
            $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
        }
    } else {
        if(isset($_GET['delete'])) {
            $market = getMarket($address['id']);
            deleteProduct($market['c_id'], $_GET['delete']);
            $size = getNumberOfProducts($market["c_id"]);
            setPagings($size);
            $products = getMarketProductsByPageNumber($start, $end, $market['c_id']);
        }
    }
    //var_dump($products);
    echo "<h1>";
    echo "Welcome ". $market['c_name'];
    echo "<h1>";

    echo "<h1>Your Products</h1>";
?>
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
        
        if($role['role'] == "M") {
            echo "<th>";
            echo "DISCOUNT";
            echo "</th>";
        }

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
                echo '<input type="text" name="p_name" value="';
                echo $p["p_name"];
                echo '">';
            } else {
                echo $p['p_name'];
            }
            echo "</td>";

            echo "<td>";
            if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                echo '<input type="text" name="p_stock" value="';
                echo $p["p_stock"];
                echo '">';
            } else {
                echo $p['p_stock'];
            }
            echo "</td>";

            echo "<td>";
            if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                echo '<input type="text" name="p_expire" value="';
                echo $p["p_expire"];
                echo '">';
            } else {
                $p_expire = $p["p_expire"];

                // Convert $p_expire to a Unix timestamp
                $p_expire_timestamp = strtotime($p_expire);

                // Get the current Unix timestamp
                $current_timestamp = time();
                if ($p_expire_timestamp > $current_timestamp) {
                    // If $p_expire is greater than today, display red text
                    echo $p["p_expire"];
                } else {
                    // Otherwise, default text color
                    echo "<span>{$p['p_expire']}</span>";
                }
            }
            echo "</td>";

            echo "<td>";
            if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                echo '<input type="text" name="p_price" value="';
                echo $p["p_price"];
                echo '">';
            } else {
                if($p["p_discounted"]) {
                    echo $p["p_altprice"];
                } else {
                    echo $p['p_price'];
                }
            }
            echo "</td>";

            if($role['role'] == "M") {
            echo "<td>";
            if(isset($_GET["edit"]) && $_GET["edit"] == $p["p_id"]){
                echo '<input type="text" name="p_altprice" value="';
                echo $p["p_altprice"];
                echo '">';
            } else {
                if($role['role'] == "M" && !isset($_GET["edit"])){
                    echo '
                    <form method="POST" action="?">
                    <div>
                    <input type="hidden" name="discount" value="';
                    echo $p["p_id"] . '">';
                    echo '<input class="" type="checkbox" id="" name="discount" onclick="this.previousSibling.value=this.value" onchange="this.form.submit()" value="';
                    echo $p["p_id"] . '"';
                    echo $p["p_discounted"] ? "checked" : ""; 
                    echo '></div></form></td>';
                }
            }
            echo "</td>";
            }

            echo "<td>";
            if($role['role'] == "M"){
            echo "
            <a href='?delete=$p[p_id]'>
                <img src='./assets/system/delete.png' alt='Delete' width='30'>
            </a>";
            if(isset($_GET["edit"]) && $p["p_id"] == $_GET["edit"]) {
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
    <div style="border: 2px solid black; width: 50%; height: 50%;"
        id="drop_zone"
        ondrop="dropHandler(event);"
        ondragover="dragOverHandler(event);">
        <img src="" name="preview" style="display:none" alt="" width="70px">
        <input onchange="readURL(event);" type="file" name="p_file" id="file" class="inputfile" style="width: 0.1px;height: 0.1px; opacity: 0; overflow: hidden; position: absolute; z-index: -1;" />
        <label for="file" style="cursor: pointer; color:blue">Choose a file</label> or drag & drop.
        <!-- <input type="file" style="display:none" name="p_file" value="" placeholder="Name"> -->
        <input type="hidden" name="form" value="add">
    </div>
    </td>
    <td><input type="text" name="p_name" value="" placeholder="Name"></td>
    <td><input type="text" name="p_stock" value="" placeholder="Stock"></td>
    <td><input type="text" name="p_expire" value="" placeholder="Expire Date"></td>
    <td><input type="text" name="p_price" value="" placeholder="Price"></td>
    <td><input type="text" name="p_altprice" value="" placeholder="Discounted Price"></td>
    <td><button style="border:0px solid black; background-color: transparent" name="add" action="?"><img src="./assets/system/save.png" alt="Save" width="30"></button></td>
    </tr>
    </form>
<?php elseif($role['role'] === "M"):  ?>
    <tr><td></td><td></td><td></td><td></td><td></td><td></td>
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
<script>var input = document.querySelectorAll('input');for(i=0; i<input.length; i++){input[i].setAttribute('size',input[i].getAttribute('value').length);}   </script>
<script>
    function readURL(input) {  
    console.log(input["target"]["files"]);
    const file = input["target"]["files"][0];
    const preview = document.querySelector('img[name="preview"]');
    preview.src = URL.createObjectURL(file);
    preview.style = "display:inline"
}   
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
                    const preview = document.querySelector('img[name="preview"]');
                    preview.src = URL.createObjectURL(file);
                    preview.style = "display:inline"
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
<style>
    span {
        color: red;
    }
</style>