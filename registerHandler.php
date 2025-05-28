<?php
try {
    $mysqlClient = new PDO(dsn: 'mysql:host=localhost;dbname=php_exam_db;charset=utf8', username: 'root', password: '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
}

$querry = $mysqlClient->prepare("Select * from user where Username = \"$username\" or Email = \"$email\"");
$querry->execute();

$similarUser = $querry->fetchAll();

if ($similarUser == null) {
    echo ("account can be created");
    $querry = $mysqlClient->prepare("insert into user (Username, Password, Email, Wallet, ProfilePicture, role) values (\"$username\", \"$password\", \"$email\", 0, \"$username.png\", \"user\")");
    $querry->execute();
    header("Location: index.php");
} else {
    if ($similarUser[0][1] == $username) {
        echo ("username already taken");
    } elseif ($similarUser[0][3] == $email) {
        echo ("email already in use");
    }
}
