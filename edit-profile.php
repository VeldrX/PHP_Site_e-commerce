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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $wallet = $_POST['Wallet'];
    $oldPassword = $_POST['Old-Password'];
    $newPassword = $_POST['Password'];

    $imagePath = '';
    if (!empty($_FILES['image']['name'])) {
        $imageName = uniqid() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/users/' . $imageName);
        $imagePath = 'uploads/users/' . $imageName;
        $stmt = $pdo->prepare("UPDATE user SET ProfilePicture = :image WHERE Id = :id");
        $stmt->bindValue(':image', $imagePath, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['profile_image'] = $imagePath; // Update session profile image
    }

    if (!empty($username)) {
        $stmt = $pdo->prepare("UPDATE user SET Username = :username WHERE Id = :id");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['username'] = $username; // Update session username
    }

    if (!empty($email)) {
        $stmt = $pdo->prepare("UPDATE user SET Email = :email WHERE Id = :id");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['email'] = $email; // Update session email
    }

    if (!empty($wallet) && $wallet > $user['Wallet']) {
        $stmt = $pdo->prepare("UPDATE user SET Wallet = :wallet WHERE Id = :id");
        $stmt->bindValue(':wallet', $wallet, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['wallet'] = $wallet; // Update session wallet
    }

    if (!empty($oldPassword) && !empty($newPassword)) {
        if (password_verify($oldPassword, $user['Password'])) {
            $newPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE user SET Password = :password WHERE Id = :id");
            $stmt->bindValue(':password', $newPassword, PDO::PARAM_STR);
            $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['password'] = $newPassword; // Update session password
        }
    }

    $stmt->execute();

    header('Location: profile.php');
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
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        .back-button {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .username {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 200px;
        }

        .email {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 200px;
        }

        .password {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 200px;
        }

        .wallet {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 200px;
        }

        .profile-image {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 200px;
        }
    </style>
</head>
<body>
    <header>
        <div>
            <h1 style="padding: 20px;">Edit Profile</h1>
            <a href="index.php"><button type="button" class="back-button" style="display:inline;">← Home</button></a>
            <a href="profile.php"><button type="button" class="profile-button" style="display:inline;">← Profile</button></a>
        </div>
    </header>
    <form method="post" enctype="multipart/form-data">
        <h2>Modifier votre profil</h2>

        <?php if ($message): ?>
            <p class="success"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <?php if (!empty($user['ProfilePicture'])): ?>
                <img src="<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Image" style="width: 100px; height: 100px; border-radius: 50%;">
        <?php endif; ?>
        <div class="username">
            <label for="Username">Nom d'utilisateur:</label>
            <input type="text" id="Username" name="Username" placeholder="<?php echo htmlspecialchars($user['Username']); ?>">
        </div>
        <div class="profile-image">
            <label for="image">Image de profil:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="email">
            <label for="Email">Email:</label>
            <input type="email" id="Email" name="Email" placeholder="<?php echo htmlspecialchars($user['Email']); ?>">
        </div>
        <div class="wallet">
            <label for="Wallet">Portefeuille:</label>
            <input type="number" id="Wallet" name="Wallet" placeholder="<?php echo htmlspecialchars($user['Wallet']); ?>€">
        </div>
        <div class="password">
            <label for="Password">Mot de passe:</label>
            <input type="password" id="Old-Password" name="Old-Password" placeholder="ancien mot de passe">
            <input type="password" id="New-Password" name="Password" placeholder="nouveau mot de passe">
        </div>
        <input type="submit" value="Mettre à jour le profil">
    </form>
</body>
</html>
