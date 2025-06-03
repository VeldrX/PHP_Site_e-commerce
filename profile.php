<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $query = $pdo->prepare("SELECT * FROM user WHERE Username = :username");
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();

    $queryArticles = $pdo->prepare("
        SELECT article.*, user.Username 
        FROM article 
        INNER JOIN user ON article.UserId = user.Id 
        WHERE user.Username = :username
        ORDER BY article.PublishDate DESC
    ");
    $queryArticles->bindValue(':username', $username, PDO::PARAM_STR);
    $queryArticles->execute();
    $articles = $queryArticles->fetchAll(PDO::FETCH_ASSOC);

    $user = $query->fetch(PDO::FETCH_ASSOC);

} else {
    echo "User not logged in.";
    exit;
}

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
    SELECT *
    FROM user  
    LIMIT :premier, :parpage
";
$query = $pdo->prepare($sql);
$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$query->execute();
$listUser = $query->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM user WHERE Id = :id");
    $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
    $stmt->execute();
    
    // Détruire la session et rediriger vers la page d'accueil
    session_destroy();
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
        }

        header h1 {
            margin: 0;
        }

        .container {
            padding: 20px;
        }

        .user-info {
            margin-bottom: 20px;
        }

        .user-info p {
            margin: 5px 0;
        }

        .articles {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
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

        .delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Profile</h1>
        <a href="index.php"><button type="button" class="back-button" style="display:inline;">← Home</button></a>
    </header>
    <div class="container">
        <div class="user-info">
            <h2><?php echo htmlspecialchars($user['Username']); ?></h2>
            <img src="<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Picture" style="width: 100px; height: 100px; border-radius: 50%;"></img>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
            <p><strong>Wallet:</strong> <?php echo htmlspecialchars($user['Wallet']); ?>€</p>
            <a href="edit-profile.php"><button type="button">Edit Profile</button></a>
            <form method="post" onsubmit="return confirm('est-tu sur de vouloir supprimer ton compte ?\ncette action est irreversible')">
                <button type="submit" class="delete">Supprimer le compte</button>
            </form>
        </div>
        <h2 style="padding: 20px;">Articles</h2>
        <!-- articles de l'utilisateur -->
        <div class="articles">
            <?php foreach ($articles as $userArticle): ?>
                <div class="article-card">
                    <img src="uploads/articles/<?= htmlspecialchars($userArticle['Image']) ?>" alt="Image de l'article">
                    <h3><?= htmlspecialchars($userArticle['Name']) ?></h3>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($userArticle['Price']) ?> €</p>
                    <p><strong>Publié par :</strong> <?= htmlspecialchars($userArticle['Username']) ?></p>
                    <p><?= htmlspecialchars($userArticle['Description']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" style="
                        margin: 0 5px;
                        padding: 8px 12px;
                        text-decoration: none;
                        color: <?php echo $i == $currentPage ? 'white' : '#3498db'; ?>;
                        background-color: <?php echo $i == $currentPage ? '#3498db' : '#eaeaea'; ?>;
                        border-radius: 5px;
                        font-weight: bold;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>