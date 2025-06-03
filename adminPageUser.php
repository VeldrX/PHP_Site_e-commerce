<?php 
session_start();

try {
    $mysqlClient = new PDO(dsn: 'mysql:host=localhost;dbname=php_exam_db;charset=utf8', username: 'root', password: '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if (isset($_SESSION['username'])) {
    
    $usename = $_SESSION['username'];

    $querry = $mysqlClient->prepare("Select * from user where Username = \"$usename\"");

    $querry->execute();
    
    $user = $querry->fetchAll();
    
    $user = $user[0];

    if ($user[6] != "admin"){
        echo "not authorised";
        exit;
    }
    

} else {
    echo "User not logged in.";

    exit;
}

$currentPage = isset($_GET['page']) && !empty($_GET['page']) ? (int) $_GET['page'] : 1;

// Nombre d'articles par page
$parPage = 10;

// Récupérer le nombre total d'articles
$sql = 'SELECT COUNT(*) AS nb_articles FROM `article`;';
$query = $mysqlClient->prepare($sql);
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
$query = $mysqlClient->prepare($sql);
$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);
$query->execute();
$listUser = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <main>

    <a href="adminPagePost.php">
        <button>Post</button>
    </a>    
        <h1 style="padding: 20px;">Articles en vente</h1>
        <div class="articles">
            <form action="/userEditAdmin.php" method="post">
            <?php foreach ($listUser as $aUser): ?>
                <button name="usernameOfEdited" value=<?= htmlspecialchars($aUser['Username']) ?>>
                <div class="article-card">
                    <img src="uploads/articles/<?= htmlspecialchars($aUser['ProfilePicture']) ?>" alt="Photo de Profil de l'utilisateur">
                        <h3><?= htmlspecialchars($aUser['Username']) ?></h3>
                        <p><strong>Email : </strong> <?= htmlspecialchars($aUser['Email']) ?></p>
                        <p><strong>Wallet :</strong> <?= htmlspecialchars($aUser['Wallet']) ?>€</p>
                        <p><strong>Role :</strong><?= htmlspecialchars($aUser['role']) ?></p>
                    </div>
                </button>
            <?php endforeach; ?> 
            </form>
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