<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $idArticle = (int) $_GET['id'];
    $idUser = $_SESSION['user_id'];

    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');

    $stmt = $pdo->prepare("INSERT INTO cart (IdUser, IdArticle) VALUES (:idUser, :idArticle)");
    $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    $stmt->bindParam(':idArticle', $idArticle, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: detail.php?id=$idArticle&added=1");
        exit();
    } else {
        echo "Erreur lors de l'ajout au panier.";
    }
} else {
    echo "ID d'article invalide.";
}
