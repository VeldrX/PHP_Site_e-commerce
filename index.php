<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

// Récupérer le numéro de page courant
$currentPage = isset($_GET['page']) && !empty($_GET['page']) ? (int) $_GET['page'] : 1;

// Nombre d'articles par page
$parPage = 10;

// Récupérer le nombre total d'articles
$sql = 'SELECT COUNT(*) AS nb_articles FROM `article`;';
$query = $pdo->prepare($sql);
$query->execute();
$result = $query->fetch();
$nbArticles = (int) $result['nb_articles'];

// Calcul du nombre total de pages
$pages = ceil($nbArticles / $parPage);

// Calcul de la première entrée à récupérer
$premier = ($currentPage - 1) * $parPage;

// Récupérer les articles avec pagination + jointure utilisateur
$sql = "
    SELECT article.*, user.Username 
    FROM article 
    INNER JOIN user ON article.UserId = user.Id 
    ORDER BY article.PublishDate DESC 
    LIMIT :premier, :parpage
";
$query = $pdo->prepare($sql);
$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$query->execute();
$articles = $query->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3498db;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info {
            font-weight: bold;
            margin-right: 20px;
        }

        .auth-buttons a,
        .disconnect-button {
            color: white;
            text-decoration: none;
            background-color: #2980b9;
            padding: 8px 15px;
            border-radius: 5px;
            margin-right: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .auth-buttons a:hover,
        .disconnect-button:hover {
            background-color: #1f6391;
        }

        .articles {
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .article-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .article-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }

        .article-card h3 {
            margin: 10px 0 5px;
        }

        .article-card p {
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <header>
        <div>
            <?php if (isset($_SESSION['username'])): ?>
                <span class="user-info">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="disconnect-button">Disconnect</button>
                </form>
                <a href="sell.php"><button type="button" class="disconnect-button">Sell Articles</button></a>
                <a href="cart.php"><button type="button" class="disconnect-button">🛒 Panier</button></a>

            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h1 style="padding: 20px;">Articles en vente</h1>
        <div class="articles">
            <?php foreach ($articles as $article): ?>
                <a href="detail.php?id=<?= $article['Id'] ?>" style="text-decoration: none; color: inherit;">
                    <div class="article-card">
                        <img src="uploads/articles/<?= htmlspecialchars($article['Image']) ?>" alt="Image de l'article">
                        <h3><?= htmlspecialchars($article['Name']) ?></h3>
                        <p><strong>Prix :</strong> <?= htmlspecialchars($article['Price']) ?> €</p>
                        <p><strong>Publié par :</strong> <?= htmlspecialchars($article['Username']) ?></p>
                        <p><?= htmlspecialchars($article['Description']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
    <div style="text-align: center; margin: 20px;">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=<?= $i ?>"
                style="
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: <?= $i == $currentPage ? 'white' : '#3498db' ?>;
            background-color: <?= $i == $currentPage ? '#3498db' : '#eaeaea' ?>;
            border-radius: 5px;
            font-weight: bold;
        ">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>


</body>

</html>