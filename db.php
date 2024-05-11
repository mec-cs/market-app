<?php

const DSN = "mysql:host=localhost;dbname=market-php-db;charset=utf8mb4";
const USER = "root";
const PASSWD = "Ayhan1989";

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

function registerUser($type, $name, $mail, $passwd, $city, $district, $addr) {
     global $db;

     try {
          $stmt = $db->prepare("insert into auth_table(email, passwd, usrtoken) values (?, ?, NULL)");
          $stmt->execute([$mail, $passwd]);
          
          $stmt = $db->prepare("insert into user_table(email, u_name) values (?, ?)");
          $stmt->execute([$mail, $name]);
          
          $stmt = $db->prepare("insert into role_table(u_name, u_role) values (?, ?)");
          $stmt->execute([$name, $type]);

          $stmt = $db->prepare("insert into address_table(email, city, district, addr) values (?, ?, ?, ?)");
          $stmt->execute([$mail, $city, $district, $addr]);

          return true;
     } catch (PDOException $e) {
          return $e;
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

function getProductsByPageNumber($start, $end, $id){
     global $db;
     $stmt = $db->prepare("SELECT * FROM product_table WHERE c_id=? LIMIT $start, $end;");
     $stmt->execute([$id]);

     return $stmt->fetchAll();
}

function getProductsByPageNumberQuery($id, $query){
     global $db;
     $stmt = $db->prepare("SELECT * FROM product_table WHERE c_id=? AND p_name LIKE ?");
     $searchTerm = "%$query%"; // Assuming you're searching for the term within the product name
     $stmt->execute([$id, $searchTerm]);

     return $stmt->fetchAll();
}


function getMarket($id){
     global $db;
     $stmt = $db->prepare("SELECT * FROM company_table where c_address=?");
     $stmt->execute([$id]);
     
     return $stmt->fetch();
}

function getAddress($email){
     global $db;
     $stmt = $db->prepare("SELECT * FROM address_table where email=?");
     $stmt->execute([$email]);
     
     return $stmt->fetch();
}
?>
