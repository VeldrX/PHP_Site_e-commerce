<?php
session_start();

try {
    $mysqlClient = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if (!isset($_SESSION['username'])) {
    echo "User not logged in.";
    exit;
}

$username = $_SESSION['username'];

$query = $mysqlClient->prepare("SELECT * FROM user WHERE Username = :username");
$query->bindParam(':username', $username, PDO::PARAM_STR);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    echo "Not authorised";
    exit;
}

if (!isset($_POST["usernameOfEdited"])) {
    echo "Missing user to edit.";
    exit;
}

$usernameOfEdited = $_POST["usernameOfEdited"];

$query = $mysqlClient->prepare("SELECT * FROM user WHERE Username = :edited");
$query->bindParam(':edited', $usernameOfEdited, PDO::PARAM_STR);
$query->execute();
$userToEdit = $query->fetch(PDO::FETCH_ASSOC);

if (!$userToEdit) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        main {
            max-width: 500px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="number"],
        input[type="text"],
        input[type="submit"],
        button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"],
        button {
            background-color: #3498db;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #2980b9;
        }

        .radio-group {
            margin-top: 10px;
        }

        .delete-button {
            background-color: #e74c3c;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <?php include 'header.php'; ?>
    <main>
        <form action="/php_exam/userEditAdminHandler.php" method="post" enctype="multipart/form-data">
            <h2>Modifier l'utilisateur</h2>

            <input type="hidden" name="username" value="<?= htmlspecialchars($userToEdit['Username']) ?>">

            <label for="wallet">Portefeuille (€) (ajustable uniquement à la hausse)</label>
            <input type="number" name="wallet" id="wallet" min="<?= htmlspecialchars($userToEdit['Wallet']) ?>" placeholder="<?= htmlspecialchars($userToEdit['Wallet']) ?>">

            <label>Rôle</label>
            <div class="radio-group">
                <label><input type="radio" name="role" value="user" <?= $userToEdit['role'] === 'user' ? 'checked' : '' ?>> Utilisateur</label><br>
                <label><input type="radio" name="role" value="admin" <?= $userToEdit['role'] === 'admin' ? 'checked' : '' ?>> Administrateur</label>
            </div>

            <input type="submit" value="Enregistrer les modifications">
            <button type="submit" name="delete" value="delete" class="delete-button">Supprimer l'utilisateur</button>
        </form>
    </main>
</body>

</html>