<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

// Récupérer l'ID de l'utilisateur à partir de son username
$stmt = $pdo->prepare('SELECT Id FROM user WHERE Username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

if (!$user) {
    die('Utilisateur introuvable.');
}

$userId = $user['Id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $imageName = '';
    if (!empty($_FILES['image']['name'])) {
        $imageName = uniqid() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/articles/' . $imageName);
    }

    // Insertion dans la table article
    $stmt = $pdo->prepare('INSERT INTO article (Name, Description, Price, UserId, Image) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $desc, $price, $userId, $imageName]);

    $articleId = $pdo->lastInsertId();

    // Insertion dans la table stock
    $stmt2 = $pdo->prepare('INSERT INTO stock (IdArticle, NbrInStock) VALUES (?, ?)');
    $stmt2->execute([$articleId, $stock]);

    $message = "Article publié avec succès !";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Vendre un article</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }


        form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background: #27ae60;
            color: white;
            cursor: pointer;
            margin-top: 20px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <?php include 'header.php'; ?>

    <main>
        <form method="post" enctype="multipart/form-data">
            <h2>Vendre un article</h2>

            <?php if ($message): ?>
                <p class="success"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <label for="name">Nom de l'article</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="price">Prix (€)</label>
            <input type="number" id="price" name="price" required>

            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" min=1 required>

            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <input type="submit" value="Mettre en vente">
        </form>
    </main>

</body>

</html>