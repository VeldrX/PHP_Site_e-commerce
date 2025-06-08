<?php
session_start();

try {
    $mysqlClient = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if (isset($_SESSION['username'])) {
    $usename = $_SESSION['username'];
    $query = $mysqlClient->prepare("SELECT * FROM user WHERE Username = :username");
    $query->execute(['username' => $usename]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['role'] !== 'admin') {
        echo "Not authorised";
        exit;
    }
} else {
    echo "User not logged in.";
    exit;
}

$currentPage = isset($_GET['page']) && !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$parPage = 10;

$sql = 'SELECT COUNT(*) AS nb_users FROM user';
$query = $mysqlClient->prepare($sql);
$query->execute();
$result = $query->fetch();
$nbUsers = (int) $result['nb_users'];

$pages = ceil($nbUsers / $parPage);
$premier = ($currentPage - 1) * $parPage;

$sql = "SELECT * FROM user LIMIT :premier, :parpage";
$query = $mysqlClient->prepare($sql);
$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$query->execute();
$listUser = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin - Utilisateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 30px;
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .admin-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .admin-buttons a button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .admin-buttons a button:hover {
            background-color: #27ae60;
        }

        .articles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .article-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            transition: transform 0.2s;
            text-align: center;
        }

        .article-card:hover {
            transform: scale(1.02);
        }

        .article-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .article-card h3 {
            margin: 10px 0;
        }

        button[name="usernameOfEdited"] {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            text-align: inherit;
        }

        .pagination {
            text-align: center;
            margin: 30px 0;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: #3498db;
            background-color: #eaeaea;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s, color 0.2s;
        }

        .pagination a.active {
            background-color: #3498db;
            color: white;
        }

        .pagination a:hover {
            background-color: #d0d0d0;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main>


        <h1>Utilisateurs inscrits</h1>

        <form action="/php_exam/userEditAdmin.php" method="post" class="articles">
            <?php foreach ($listUser as $aUser): ?>
                <button name="usernameOfEdited" value="<?= htmlspecialchars($aUser['Username']) ?>">
                    <div class="article-card">
                        <img src="uploads/articles/<?= htmlspecialchars($aUser['ProfilePicture']) ?>" alt="Photo de profil">
                        <h3><?= htmlspecialchars($aUser['Username']) ?></h3>
                        <p><strong>Email :</strong> <?= htmlspecialchars($aUser['Email']) ?></p>
                        <p><strong>Wallet :</strong> <?= htmlspecialchars($aUser['Wallet']) ?> €</p>
                        <p><strong>Rôle :</strong> <?= htmlspecialchars($aUser['role']) ?></p>
                    </div>
                </button>
            <?php endforeach; ?>
        </form>

        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>

</html>