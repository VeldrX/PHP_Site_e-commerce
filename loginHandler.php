<?php 
    try {
        $mysqlClient = new PDO(dsn:'mysql:host=localhost;dbname=php_exam_db;charset=utf8', username:'root', password:'');
    } catch (PDOException $e){
        die($e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];
    }

    $querry = $mysqlClient->prepare("Select * from user where Username = \"$username\"");
    $querry->execute();

    $similarUser = $querry->fetchAll();

    if ($similarUser == null){
        echo("account not found");
        
    } else {
        if (password_verify($password, $similarUser[0][2])){
            echo("connect");
        } else {
            echo("wrong password");
        }
    }
    
?>