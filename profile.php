<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $query = $pdo->prepare("SELECT * FROM user WHERE Username = :username");
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC); // ✅ D'abord on récupère l'utilisateur

    $queryInvoices = $pdo->prepare("
    SELECT * FROM invoice
    WHERE IdUser = :userId
    ORDER BY TransactionDate DESC");
    $queryInvoices->bindValue(':userId', $user['Id'], PDO::PARAM_INT);
    $queryInvoices->execute();
    $invoices = $queryInvoices->fetchAll(PDO::FETCH_ASSOC);



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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }

        header {
            background-color: #3498db;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        header .back-button {
            background-color: white;
            color: #3498db;
            border: none;
            padding: 8px 14px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .container-page {
            max-width: 1100px;
            margin: auto;
            padding: 30px 20px;
        }

        .user-info-page {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .user-info-page img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .user-info-page h2 {
            margin-top: 0;
        }

        .user-info-page button,
        .user-info-page .delete {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            margin-right: 10px;
        }

        .user-info-page button {
            background-color: #2ecc71;
            color: white;
        }

        .user-info-page .delete {
            background-color: #e74c3c;
            color: white;
        }

        h2.section-title {
            font-size: 1.6rem;
            margin: 30px 0 10px;
        }

        .articles {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .article-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .article-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .article-card h3 {
            margin: 5px 0;
            font-size: 1.2rem;
        }

        .article-card p {
            margin: 4px 0;
            font-size: 0.95rem;
        }

        .pagination {
            margin-top: 30px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 14px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            color: #3498db;
            background-color: #eaeaea;
        }

        .pagination a.active {
            background-color: #3498db;
            color: white;
        }

        @media screen and (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-info {
                text-align: center;
            }

            .user-info img {
                margin: 0 auto 10px;
            }

            .user-info button,
            .user-info .delete {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <?php include 'header.php'; ?>


    <div class="container-page">
        <div class="user-info-page">
            <h2><?= htmlspecialchars($user['Username']) ?></h2>
            <img src="<?= htmlspecialchars($user['ProfilePicture']) ?>" alt="Photo de profil">
            <p><strong>Email :</strong> <?= htmlspecialchars($user['Email']) ?></p>
            <p><strong>Solde :</strong> <?= htmlspecialchars($user['Wallet']) ?> €</p>

            <a href="edit-profile.php"><button type="button">Modifier le profil</button></a>
            <form method="post" onsubmit="return confirm('Es-tu sûr de vouloir supprimer ton compte ? Cette action est irréversible.')">
                <button type="submit" class="delete">Supprimer le compte</button>
            </form>
        </div>

        <h2 class="section-title">Mes articles</h2>
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

        <h2 class="section-title">Mes factures</h2>
        <div class="articles">
            <?php if (count($invoices) > 0): ?>
                <?php foreach ($invoices as $invoice): ?>
                    <div class="article-card">
                        <h3>Facture #<?= htmlspecialchars($invoice['Id']) ?></h3>
                        <p><strong>Montant :</strong> <?= number_format($invoice['Amount'], 2) ?> €</p>
                        <p><strong>Date :</strong> <?= htmlspecialchars($invoice['TransactionDate']) ?></p>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($invoice['BillingAddress']) ?>,
                            <?= htmlspecialchars($invoice['BillingCity']) ?> <?= htmlspecialchars($invoice['ZipCode']) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tu n’as pas encore de facture.</p>
            <?php endif; ?>
        </div>


        <?php if ($pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>