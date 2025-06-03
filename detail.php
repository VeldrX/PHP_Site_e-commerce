<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Article invalide.');
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

$id = (int) $_GET['id'];

// Récupération des infos de l'article
$stmt = $pdo->prepare("
    SELECT article.*, user.Username 
    FROM article 
    JOIN user ON article.UserId = user.Id 
    WHERE article.Id = :id
");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die('Article non trouvé.');
}

// Récupération du stock
$stmtStock = $pdo->prepare("SELECT NbrInStock FROM stock WHERE IdArticle = :id");
$stmtStock->bindParam(':id', $id, PDO::PARAM_INT);
$stmtStock->execute();
$stock = $stmtStock->fetch(PDO::FETCH_ASSOC);
$quantity = $stock ? $stock['NbrInStock'] : 'Inconnu';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails de l'article</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .article-detail {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        h1 {
            margin-bottom: 10px;
        }

        p {
            line-height: 1.6;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="article-detail">
        <h1><?= htmlspecialchars($article['Name']) ?></h1>
        <img src="uploads/articles/<?= htmlspecialchars($article['Image']) ?>" alt="Image de l'article">
        <p><strong>Prix :</strong> <?= htmlspecialchars($article['Price']) ?> €</p>
        <p><strong>Stock disponible :</strong> <?= htmlspecialchars($quantity) ?></p>
        <p><strong>Publié par :</strong> <?= htmlspecialchars($article['Username']) ?></p>
        <p><strong>Description :</strong></p>
        <p><?= nl2br(htmlspecialchars($article['Description'])) ?></p>

        <a class="back-link" href="index.php">← Retour à l'accueil</a>
    </div>
</body>

</html>