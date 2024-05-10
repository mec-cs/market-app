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

     $stmt = $db->prepare("select * from auth where email=?");
     $stmt->execute([$mail]);
     $user = $stmt->fetch();

     if ($user) {
          return password_verify($pwd, $user["password"]);
     }

     return false;
}

function checkExists($mail) {
     global $db;

     $stmt = $db->prepare("select * from auth where email=?");
     $stmt->execute([$mail]);
     return $stmt->fetch();
}

function registerUser($type, $name, $mail, $passwd, $city, $district, $addr) {
     global $db;

     try {
          $stmt = $db->prepare("insert into auth(role, name, email, password, city, district, address, usrtoken) values (?, ?, ?, ?, ?, ?, ?, NULL)");
          $stmt->execute([$type, $name, $mail, $passwd, $city, $district, $addr]);
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
     $stmt = $db->prepare("select * from auth where usrtoken=?");
     $stmt->execute([$token]);
     return $stmt->fetch();
}

function setTokenToUser($token, $mail) {
     global $db;
     $stmt = $db->prepare("update auth set usrtoken = ? where email = ?");
     $stmt->execute([$token, $mail]);
}

?>