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
            font-family: Arial, sans-serif;
            padding: 0;
            margin: 0;
            background-color: #f4f4f4;
        }

        .article-detail {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        img {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        h1 {
            margin-bottom: 10px;
        }

        p {
            line-height: 1.6;
        }

        .edit-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover {
            background-color: #d68910;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .success-msg {
            max-width: 800px;
            margin: 20px auto;
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            font-weight: bold;
        }

        .cart-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .cart-button:hover {
            background-color: #219150;
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <?php include 'header.php'; ?>

    <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
        <div class="success-msg">
            ✅ L'article a bien été ajouté au panier.
        </div>
    <?php endif; ?>

    <div class="article-detail">
        <h1><?= htmlspecialchars($article['Name']) ?></h1>
        <img src="uploads/articles/<?= htmlspecialchars($article['Image']) ?>" alt="Image de l'article">

        <p><strong>Prix :</strong> <?= htmlspecialchars($article['Price']) ?> €</p>
        <p><strong>Stock disponible :</strong> <?= htmlspecialchars($quantity) ?></p>
        <p><strong>Publié par :</strong> <?= htmlspecialchars($article['Username']) ?></p>
        <p><strong>Description :</strong></p>
        <p><?= nl2br(htmlspecialchars($article['Description'])) ?></p>

        <?php if (isset($_SESSION['username'])): ?>
            <a href="add_to_cart.php?id=<?= $article['Id'] ?>" class="cart-button">Ajouter au panier</a>
        <?php else: ?>
            <p class="login-prompt"><a href="login.php">Connectez-vous</a> pour ajouter cet article au panier.</p>
        <?php endif; ?>
        <?php
        if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $article['UserId'] || $user['Role'] === "admin")) {
            echo '
        <form method="post" action="editPost.php" style="display:inline-block;">
            <button type="submit" name="idOfThingSold" value="' . htmlspecialchars($article['Id']) . '" class="edit-button">
                ✏️ Modifier
            </button>
        </form>
    ';
        }
        ?>
    </div>




</body>

</html>