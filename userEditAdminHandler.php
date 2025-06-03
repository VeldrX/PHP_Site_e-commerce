<?php
try {
    $mysqlClient = new PDO(dsn: 'mysql:host=localhost;dbname=php_exam_db;charset=utf8', username: 'root', password: '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
}

$querry = $mysqlClient->prepare("Select * from user where Username = \"$username\"");
$querry->execute();

$similarUser = $querry->fetchAll();
 
if ($_POST["delete"] = "delete"){
    $querry = $mysqlClient->prepare("DELETE * From User WHERE username = \"$username\" ");
    $querry->execute();
}

if ($similarUser == null) {
    echo"wut";
} else {
    $Wallet = $_POST['wallet'];
    $Role = $_POST['role'];
    $querry = $mysqlClient->prepare("UPDATE user SET Wallet = \"$Wallet\", role = \"$Role\" where Username = \"$username\"");
    $querry->execute();
}

header("Location: /adminPageUser.php");
?>