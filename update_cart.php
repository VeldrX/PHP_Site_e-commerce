<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=php_exam_db;charset=utf8", "root", "");

$userId = $_SESSION['user_id'];
$articleId = $_POST['article_id'];
$action = $_POST['action'];

// Compter combien d'occurrences de cet article sont dans le panier
$stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE IdUser = :userId AND IdArticle = :articleId");
$stmt->execute(['userId' => $userId, 'articleId' => $articleId]);
$currentQuantity = $stmt->fetchColumn();

if ($action === 'increase') {
    // Vérifie le stock avant d'ajouter
    $stmt = $pdo->prepare("SELECT NbrInStock FROM stock WHERE IdArticle = :articleId");
    $stmt->execute(['articleId' => $articleId]);
    $stock = $stmt->fetchColumn();

    if ($currentQuantity < $stock) {
        $stmt = $pdo->prepare("INSERT INTO cart (IdUser, IdArticle) VALUES (:userId, :articleId)");
        $stmt->execute(['userId' => $userId, 'articleId' => $articleId]);
    }
} elseif ($action === 'decrease') {
    if ($currentQuantity > 0) {
        // Supprimer UNE occurrence
        $stmt = $pdo->prepare("SELECT Id FROM cart WHERE IdUser = :userId AND IdArticle = :articleId LIMIT 1");
        $stmt->execute(['userId' => $userId, 'articleId' => $articleId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE Id = :id");
            $stmt->execute(['id' => $row['Id']]);
        }
    }
}

header("Location: cart.php");
exit();
