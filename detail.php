<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Article invalide.');
}


if (isset($_GET['added']) && $_GET['added'] == 1) {
    echo '<div style="padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 15px;">
            ✅ L\'article a bien été ajouté au panier.
          </div>';
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if (isset($_SESSION['username'])) {
    
    $usename = $_SESSION['username'];

    $querry = $pdo->prepare("Select * from user where Username = \"$usename\"");

    $querry->execute();
    
    $user = $querry->fetchAll();
    
    $user = $user[0];

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
        <?php if (isset($_SESSION['username'])): ?>
            <a href="add_to_cart.php?id=<?= $article['Id'] ?>">Ajouter au panier</a>

        <?php else: ?>
            <p><a href="login.php">Connectez-vous</a> pour ajouter au panier.</p>
        <?php endif; ?>


        <a class="back-link" href="index.php">← Retour à l'accueil</a>
    </div>
            <?php 
            if ($_SESSION['user_id'] == $article['UserId'] or $user[6] == "admin") {
                $placeholder = $article['Id'];
                echo ("
                    <form method=\"post\" action=\"/editPost.php\">
                    <button name=\"idOfThingSold\" value=\"$placeholder\"> Modifier</button>
                    </form>
                ");
            }
            ?>
            

</body>

</html>