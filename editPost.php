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

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}

if (!isset($_POST["idOfThingSold"])) {
    echo "ID du produit manquant.";
    exit;
}

$idOfThingSold = $_POST["idOfThingSold"];
$query = $mysqlClient->prepare("SELECT * FROM article WHERE Id = :id");
$query->bindParam(':id', $idOfThingSold, PDO::PARAM_INT);
$query->execute();
$postToEdit = $query->fetch(PDO::FETCH_ASSOC);

if (!$postToEdit) {
    echo "Article introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier l'article</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 0;
        }

        main {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"],
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        button.delete-button {
            background-color: #e74c3c;
            color: white;
        }

        button.delete-button:hover {
            background-color: #c0392b;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main>
        <form action="/php_exam/editPostHandler.php" method="post" enctype="multipart/form-data">
            <h2>Modifier l'article</h2>

            <input type="hidden" name="id" value="<?= htmlspecialchars($postToEdit['Id']) ?>">

            <label for="productName">Nom du produit</label>
            <input type="text" name="productName" id="productName" value="<?= htmlspecialchars($postToEdit['Name']) ?>" required>

            <label for="description">Description</label>
            <input type="text" name="description" id="description" value="<?= htmlspecialchars($postToEdit['Description']) ?>" required>

            <label for="price">Prix (€)</label>
            <input type="number" name="price" id="price" step="0.01" value="<?= htmlspecialchars($postToEdit['Price']) ?>" required>

            <input type="submit" value="Enregistrer les modifications">
            <button type="submit" name="delete" value="delete" class="delete-button">Supprimer l'article</button>
        </form>
    </main>
</body>

</html>