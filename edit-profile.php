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
$updates = [];

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
        $_SESSION['profile_image'] = $imagePath;
        $updates[] = "Image de profil mise à jour";
    }

    if (!empty($username) && $username !== $user['Username']) {
        $stmt = $pdo->prepare("UPDATE user SET Username = :username WHERE Id = :id");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['username'] = $username;
        $updates[] = "Nom d'utilisateur modifié";
    }

    if (!empty($email) && $email !== $user['Email']) {
        $stmt = $pdo->prepare("UPDATE user SET Email = :email WHERE Id = :id");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['email'] = $email;
        $updates[] = "Email modifié";
    }

    if (!empty($wallet)) {
        if (!is_numeric($wallet)) {
            $_SESSION['error'] = 'Le portefeuille doit être un nombre.';
        } elseif ($wallet <= $user['Wallet']) {
            $_SESSION['error'] = 'La somme doit être supérieure à votre solde actuel.';
        } else {
            $stmt = $pdo->prepare("UPDATE user SET Wallet = :wallet WHERE Id = :id");
            $stmt->bindValue(':wallet', $wallet, PDO::PARAM_STR);
            $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['wallet'] = $wallet;
            $updates[] = "Solde du portefeuille mis à jour";
        }
    }

    if (!empty($oldPassword) && !empty($newPassword)) {
        if (password_verify($oldPassword, $user['Password'])) {
            $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE user SET Password = :password WHERE Id = :id");
            $stmt->bindValue(':password', $newPasswordHashed, PDO::PARAM_STR);
            $stmt->bindValue(':id', $user['Id'], PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['password'] = $newPasswordHashed;
            $updates[] = "Mot de passe modifié";
        } else {
            $_SESSION['error'] = "Ancien mot de passe incorrect. Le mot de passe n’a pas été modifié.";
        }
    } elseif (!empty($newPassword)) {
        $_SESSION['error'] = "Veuillez saisir l'ancien mot de passe pour modifier le mot de passe.";
    }

    if (!empty($updates)) {
        $_SESSION['message'] = implode('<br>', $updates);
    } else {
        $_SESSION['message'] = "Aucune modification effectuée.";
    }
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Profile</title>
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --background: #f4f4f4;
            --text: #333;
            --white: #fff;
            --success: #2ecc71;
            --error: #e74c3c;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--text);
        }

        header {
            background-color: var(--primary);
            color: var(--white);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.5em;
        }

        .container-page {
            max-width: 800px;
            margin: 30px auto;
            background-color: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .profile-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        form h2 {
            margin-bottom: 20px;
            color: var(--primary-dark);
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="password"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary);
            outline: none;
        }

        input[type="submit"] {
            background-color: var(--primary);
            color: var(--white);
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: var(--primary-dark);
        }

        .back-button,
        .profile-button {
            background-color: var(--white);
            color: var(--primary-dark);
            border: 2px solid var(--primary-dark);
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .back-button:hover,
        .profile-button:hover {
            background-color: var(--primary-dark);
            color: var(--white);
        }

        .message {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success {
            background-color: var(--success);
            color: var(--white);
        }

        .error {
            background-color: var(--error);
            color: var(--white);
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            header {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <?php include 'header.php'; ?>
    <br>
    <a href="profile.php" class="back-button">Retour au profil</a>

    <div class="container-page">

        <form method="post" enctype="multipart/form-data">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success"><?= htmlspecialchars($_SESSION['message']);
                                                unset($_SESSION['message']); ?></div>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="message error"><?= htmlspecialchars($_SESSION['error']);
                                            unset($_SESSION['error']); ?></div>
            <?php endif; ?>


            <?php if (!empty($user['ProfilePicture'])): ?>
                <div class="profile-image">
                    <img src="<?= htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="Username">Nom d'utilisateur:</label>
                <input type="text" id="Username" name="Username" placeholder="<?= htmlspecialchars($user['Username']); ?>">
            </div>

            <div class="form-group">
                <label for="image">Image de profil:</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" placeholder="<?= htmlspecialchars($user['Email']); ?>">
            </div>

            <div class="form-group">
                <label for="Wallet">Portefeuille:</label>
                <input type="number" id="Wallet" name="Wallet" placeholder="<?= htmlspecialchars($user['Wallet']); ?>€">
            </div>

            <div class="form-group">
                <label for="Password">Mot de passe:</label>
                <input type="password" id="Old-Password" name="Old-Password" placeholder="Ancien mot de passe">
                <input type="password" id="New-Password" name="Password" placeholder="Nouveau mot de passe">
            </div>

            <input type="submit" value="Mettre à jour le profil">
        </form>
    </div>
</body>

</html>