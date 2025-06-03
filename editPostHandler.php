<?php
try {
    $mysqlClient = new PDO(dsn: 'mysql:host=localhost;dbname=php_exam_db;charset=utf8', username: 'root', password: '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $articleId = $_POST['id'];
}

$querry = $mysqlClient->prepare("Select * from article where Id = \"$articleId\"");
$querry->execute();

$article = $querry->fetchAll();

$imageName = '';


if ($_POST["delete"] === "delete"){
    $querry = $mysqlClient->prepare("DELETE From article WHERE Id = \"$articleId\" ");
    $querry->execute();
}

if ($article == null) {
    echo"wut";
} else {
    $name = $_POST['productName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $querry = $mysqlClient->prepare("UPDATE article SET Name = \"$name\", Description = \"$description\", Price = \"$price\" where Id = \"$articleId\"");
    $querry->execute();
}

header("Location: /");
?>