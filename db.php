<?php

const DSN = "mysql:host=localhost;dbname=market-php-db;charset=utf8mb4";
const USER = "root";
const PASSWD = "";

try {
     $db = new PDO(DSN, USER, PASSWD); 
} catch(PDOException $e) {
     echo "Username OR Password mismatch, set constants clearly!";
     exit;
}

function checkUser($mail, $pwd, &$user) {
     global $db;

     $stmt = $db->prepare("select * from auth_table where email=?");
     $stmt->execute([$mail]);
     $user = $stmt->fetch();

     if ($user) {
          return password_verify($pwd, $user["password"]);
     }

     return false;
}

function checkExists($mail) {
     global $db;
     $stmt = $db->prepare("select * from auth_table where email=?");
     $stmt->execute([$mail]);

     return $stmt->fetch();
}

function checkSpecialChar($passwd){
     $specialChars = array('!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '+', '=', '~', '`', '[', ']', '{', '}', '|', ';', ':', ',', '.', '<', '>', '?');;
     $hasSpecialChar = false;

     for($i=0; $i < strlen($passwd); $i++){
          $char = $passwd[$i];
          if(in_array($char, $specialChars)){
               $hasSpecialChar = true;
               break;
          }
     }
     return $hasSpecialChar;
}

function registerUser($type, $name, $mail, $password, $city, $district, $addr) {
     global $db;
     $passwd = password_hash($password, PASSWORD_DEFAULT);
     try {
          $stmt = $db->prepare("insert into auth_table(email, password, usrtoken) values (?, ?, NULL)");
          $stmt->execute([$mail, $passwd]);
          $stmt = $db->prepare("insert into user_table(email, name) values (?, ?)");
          $stmt->execute([$mail, $name]);
          $stmt = $db->prepare("insert into address_table(email, city, district, addr) values (?, ?, ?, ?)");
          $stmt->execute([$mail, $city, $district, $addr]);
          $stmt = $db->prepare("insert into role_table(email, role) values (?, ?)");
          $stmt->execute([$mail, $type]);

          if ($type == "M") {
               $stmt = $db->prepare("select id from address_table where email = ?");
               $stmt->execute([$mail]);
               $address_id = $stmt->fetch();
               // var_dump($address_id);

               $stmt = $db->prepare("insert into company_table(c_name, c_address_table, number_of_products, c_image) values(?, ?, 0, 'default.png')");
               $stmt->execute([$name, $address_id["id"]]);
          }

          return true;
     } catch (PDOException $e) {
          echo $e;
          return false;
     }
}

function isUserAuthenticated() {
     return isset($_SESSION["user"]);
}

function getUserByToken($token) {
     global $db;
     $stmt = $db->prepare("select * from auth_table where usrtoken=?");
     $stmt->execute([$token]);

     return $stmt->fetch();
}

function setTokenToUser($token, $mail) {
     global $db;
     $stmt = $db->prepare("update auth_table set usrtoken = ? where email = ?");
     $stmt->execute([$token, $mail]);
}

function getUserRole($email){
     global $db;
     $stmt = $db->prepare("select * from role_table where email=?");
     $stmt->execute([$email]);

     return $stmt->fetch();
}

function getMarketProductsByPageNumber($start, $end, $id){
     global $db;
     $offset = $end - $start;
     $stmt = $db->prepare("(SELECT * FROM product_table WHERE c_id=?) LIMIT $start,$offset;");
     $stmt->execute([$id]);

     return $stmt->fetchAll();
}

function getMarketProductsByQuery($id, $query){
     global $db;
     $stmt = $db->prepare("SELECT * FROM product_table WHERE c_id=? AND p_name LIKE ?");
     $searchTerm = "%$query%"; // Assuming you're searching for the term within the product name
     $stmt->execute([$id, $searchTerm]);

     return $stmt->fetchAll();
}

function getName($email){
     global $db;
     $stmt = $db->prepare("SELECT name FROM user_table WHERE email=?");
     $stmt->execute([$email]);

     return $stmt->fetch()["name"];

}

function getNumberOfProducts($c_id){
     global $db;
     $stmt = $db->prepare("SELECT COUNT(*) as count FROM product_table WHERE c_id=?");
     $stmt->execute([$c_id]);

     return $stmt->fetchAll()[0]['count'];
}

function getAllProductsByPageNumberQuery($start, $end, $city, $district, $query){
     global $db;
     $offset = $end - $start;
     $stmt = $db->prepare("SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted FROM
     (SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted, MIN(UnionSet) UnionSetOrder FROM
     (SELECT p_id, p_name, p_stock, p_expire, c.c_id, p_image, p_price, p_altprice, p_discounted, 1 as UnionSet FROM product_table p JOIN company_table c ON c.c_id = p.c_id JOIN address_table a ON c.c_address_table = a.id WHERE a.district = ? AND a.city = ? AND p.p_name LIKE ? UNION DISTINCT
     SELECT p_id, p_name, p_stock, p_expire, e.c_id, p_image, p_price, p_altprice, p_discounted, 2 as UnionSet FROM product_table e JOIN company_table b ON b.c_id = e.c_id JOIN address_table a ON b.c_address_table = a.id WHERE a.city = ? AND e.p_name LIKE ?) x 
     GROUP BY p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted) y WHERE UNIX_TIMESTAMP(p_expire) >= ? ORDER BY UnionSetOrder LIMIT $start, $offset;");
     $query = "%$query%";
     $stmt->execute([$district, $city, $query, $city, $query, time()]);
     
     return $stmt->fetchAll();
}

function getNumberOfAllProductsQuery($city, $district, $query) {
     global $db;
     $stmt = $db->prepare("SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted FROM
     (SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted, MIN(UnionSet) UnionSetOrder FROM
     (SELECT p_id, p_name, p_stock, p_expire, c.c_id, p_image, p_price, p_altprice, p_discounted, 1 as UnionSet FROM product_table p JOIN company_table c ON c.c_id = p.c_id JOIN address_table a ON c.c_address_table = a.id WHERE a.district = ? AND a.city = ? AND p.p_name LIKE ? UNION DISTINCT
     SELECT p_id, p_name, p_stock, p_expire, e.c_id, p_image, p_price, p_altprice, p_discounted, 2 as UnionSet FROM product_table e JOIN company_table b ON b.c_id = e.c_id JOIN address_table a ON b.c_address_table = a.id WHERE a.city = ? AND e.p_name LIKE ?) x 
     GROUP BY p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted) y WHERE UNIX_TIMESTAMP(p_expire) >= ? ORDER BY UnionSetOrder;");
     $query = "%$query%";
     $stmt->execute([$district, $city, $query, $city, $query, time()]);
     
     return $stmt->fetchAll();
}

function getAllProductsByPageNumber($start, $end, $city, $district){
     global $db;
     $offset = $end - $start;
     $stmt = $db->prepare("SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted FROM
     (SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted, MIN(UnionSet) UnionSetOrder FROM
     (SELECT p_id, p_name, p_stock, p_expire, c.c_id, p_image, p_price, p_altprice, p_discounted, 1 as UnionSet FROM product_table p JOIN company_table c ON c.c_id = p.c_id JOIN address_table a ON c.c_address_table = a.id WHERE a.district = ? AND a.city = ? UNION DISTINCT
     SELECT p_id, p_name, p_stock, p_expire, e.c_id, p_image, p_price, p_altprice, p_discounted, 2 as UnionSet FROM product_table e JOIN company_table b ON b.c_id = e.c_id JOIN address_table a ON b.c_address_table = a.id WHERE a.city = ?) x 
     GROUP BY p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted) y WHERE UNIX_TIMESTAMP(p_expire) >= ? ORDER BY UnionSetOrder LIMIT $start, $offset;");
     $stmt->execute([$district, $city, $city, time()]);
     
     return $stmt->fetchAll();
}

function getNumberOfAllProducts($city, $district) {
     global $db;
     $stmt = $db->prepare("SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted FROM
     (SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted, MIN(UnionSet) UnionSetOrder FROM
     (SELECT p_id, p_name, p_stock, p_expire, c.c_id, p_image, p_price, p_altprice, p_discounted, 1 as UnionSet FROM product_table p JOIN company_table c ON c.c_id = p.c_id JOIN address_table a ON c.c_address_table = a.id WHERE a.district = ? AND a.city = ? UNION DISTINCT
     SELECT p_id, p_name, p_stock, p_expire, e.c_id, p_image, p_price, p_altprice, p_discounted, 2 as UnionSet FROM product_table e JOIN company_table b ON b.c_id = e.c_id JOIN address_table a ON b.c_address_table = a.id WHERE a.city = ?) x 
     GROUP BY p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice, p_discounted) y WHERE UNIX_TIMESTAMP(p_expire) >= ? ORDER BY UnionSetOrder;");
     $stmt->execute([$district, $city, $city, time()]);
     
     return $stmt->fetchAll();
}

function getMarket($id){
     global $db;
     $stmt = $db->prepare("SELECT * FROM company_table where c_address_table=?");
     $stmt->execute([$id]);
     
     return $stmt->fetch();
}

function getAddress($email){
     global $db;
     $stmt = $db->prepare("SELECT * FROM address_table where email=?");
     $stmt->execute([$email]);
     
     return $stmt->fetch();
}

function isValidName($name) {
return preg_match("/^[a-zA-Z ]+$/", $name);
}

function isValidEmail($email) {
return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function updateProduct($post){
     global $db;
     $flag=false;

     $p_id = $post["p_id"];
     foreach($post as $key => $value) {
          $stmt = $db->prepare("UPDATE product_table SET $key=? WHERE p_id=$p_id");
          $stmt->execute([$value]);
          $flag = true;
     }
     
     return $flag;
}

function deleteProduct($c_id, $p_id){
     global $db;
     if(isProductExist($p_id)) {
     $stmt = $db->prepare("SELECT p_image FROM product_table WHERE p_id=$p_id");
     $stmt->execute([]);
     $imageName = $stmt->fetch()['p_image'];

     if($imageName != "default.png") {
          if(file_exists($_SERVER['DOCUMENT_ROOT']."market-app")) {
               unlink($_SERVER['DOCUMENT_ROOT']."/market-app/assets/product/$imageName");
          } else {
               unlink($_SERVER['DOCUMENT_ROOT']."/assets/product/$imageName");
          }    
     }
          $stmt = $db->prepare("DELETE FROM product_table WHERE p_id=$p_id");
          $stmt->execute([]);
          $stmt = $db->prepare("UPDATE company_table SET number_of_products = number_of_products - 1 WHERE c_id = $c_id");
          $stmt->execute([]);
     }
}

function addProduct($p_name, $p_stock, $p_expire, $c_id, $p_image, $p_price, $p_altprice){
     global $db;
     $stmt = $db->prepare("INSERT INTO product_table(p_name, p_stock, p_expire, c_id, p_image, p_price, p_altprice) VALUES(?, ?, ?, ?, ?, ?, ?)");
     $stmt->execute([$p_name, intval($p_stock), $p_expire, $c_id, $p_image, floatval($p_price), floatval($p_altprice)]);
     $stmt = $db->prepare("UPDATE company_table SET number_of_products = number_of_products + 1 WHERE c_id = $c_id");
     $stmt->execute([]);
}

function changeProductDiscount($p_id){
     global $db;
     $stmt = $db->prepare("UPDATE product_table SET p_discounted = NOT p_discounted WHERE p_id = ?");
     $stmt->execute([$p_id]);
}

function getConsumerAddress($email){
     global $db;
     $stmt = $db->prepare("SELECT * FROM address_table WHERE email=?");
     $stmt->execute([$email]);
     return $stmt->fetch();
}

function getMarketListInAddress($city, $district){
     global $db;
     $stmt = $db->prepare("SELECT u.name, a.city, a.district
     FROM user_table u
     JOIN role_table r ON u.email = r.email
     JOIN address_table a ON u.email = a.email
     WHERE r.role = 'M'
     AND EXISTS (
         SELECT 1
         FROM address_table a2
         WHERE a.city = ?
         AND a.district = ?
         AND a2.email = u.email)");

     $stmt->execute([$city, $district]);
     return $stmt->fetchAll();
}

function getCompanyByName($name){
     global $db;
     $stmt = $db->prepare("select * from company_table where c_name=?");

     $stmt->execute([$name]);
     return $stmt->fetch();
}

function getEmailByCompanyName($name){
     global $db;
     $stmt = $db->prepare("select * from user_table where name=?");

     $stmt->execute([$name]);
     return $stmt->fetch();
}

function isProductExist($p_id) {
     global $db;
     $stmt = $db->prepare("SELECT count(*) FROM product_table WHERE p_id = $p_id");
     $stmt->execute([]);
     return $stmt->fetch()[0];
}

function getCustomer($mail) {
     global $db;
     $stmt = $db->prepare("select * from user_table where email=?");
     $stmt->execute([$mail]);
     return $stmt->fetch();
}

function updateProfile($type, $name, $email, $password, $city, $district, $address, $oldEmail, $usrtoken) {
     global $db;
     $passwd = password_hash($password, PASSWORD_DEFAULT);

     try {
          $stmt = $db->prepare("update auth_table set email = ?, password = ? where email = ?");
          $stmt->execute([$email, $passwd, $oldEmail]);
          
          $stmt = $db->prepare("update user_table set email = ?, name = ? where email = ?");
          $stmt->execute([$email, $name, $oldEmail]);
          
          $stmt = $db->prepare("update role_table set email = ? where email = ?");
          $stmt->execute([$email, $oldEmail]);
          
          $stmt = $db->prepare("update address_table set email = ?, city = ?, district = ?, addr = ? where email = ?");
          $stmt->execute([$email, $city, $district, $address, $oldEmail]);
          
          if ($type == "M") {
               $stmt = $db->prepare("update company_table set c_name = ? where c_address_table = (SELECT id FROM address_table WHERE email = ?)");
               $stmt->execute([$name, $email]);
          }

          return true;
     } catch(PDOException $e) {
          var_dump($e);
          return false;
     }
}

function getProduct($p_id){
     global $db;
     $stmt = $db->prepare("SELECT * FROM product_table WHERE p_id=?");
     $stmt->execute([$p_id]);
     return $stmt->fetch();
}

function shipProducts($products){
     global $db;
     foreach($products as $product) {
          $stmt = $db->prepare("UPDATE product_table SET p_stock = p_stock - ? WHERE p_id = ?");
          $stmt->execute([$product['amount'], $product['p_id']]);
     }
}

?>
