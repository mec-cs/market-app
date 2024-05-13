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
     $passwd = password_hash($password, $PASSWORD_DEFAULT);
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
     $stmt = $db->prepare("SELECT * FROM product_table WHERE c_id=? LIMIT $start, $end;");
     $stmt->execute([$id]);

     return $stmt->fetchAll();
}

function getMarketProductsByPageNumberQuery($id, $query){
     global $db;
     $stmt = $db->prepare("SELECT * FROM product_table WHERE c_id=? AND p_name LIKE ?");
     $searchTerm = "%$query%"; // Assuming you're searching for the term within the product name
     $stmt->execute([$id, $searchTerm]);

     return $stmt->fetchAll();
}

function getAllProductsByPageNumber($start, $end, $city, $district){
     global $db;
     $stmt = $db->prepare("SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price FROM
     (SELECT p_id, p_name, p_stock, p_expire, c_id, p_image, p_price, MIN(UnionSet) UnionSetOrder FROM
     (SELECT p_id, p_name, p_stock, p_expire, c.c_id, p_image, p_price, 1 as UnionSet FROM product_table p JOIN company_table c ON c.c_id = p.c_id JOIN address_table a ON c.c_address_table = a.id WHERE a.district = ? UNION DISTINCT
     SELECT p_id, p_name, p_stock, p_expire, e.c_id, p_image, p_price, 2 as UnionSet FROM product_table e JOIN company_table b ON b.c_id = e.c_id JOIN address_table a ON b.c_address_table = a.id WHERE a.city = ?) x 
     GROUP BY p_id, p_name, p_stock, p_expire, c_id, p_image, p_price) y ORDER BY UnionSetOrder LIMIT $start, $end;");
     $stmt->execute([$district, $city]);
     
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
          if($value != "") {
               $stmt = $db->prepare("UPDATE product_table SET $key=$value WHERE p_id=$p_id");
               $stmt->execute();
               $flag = true;
          }
     }
     
     return $flag;
}

function deleteProduct($p_id){
     global $db;
     $stmt = $db->prepare("DELETE FROM product_table WHERE p_id=$p_id");
     $stmt->execute();
}
?>
