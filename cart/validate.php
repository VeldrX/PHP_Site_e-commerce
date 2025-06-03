<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=php_exam_db;charset=utf8", "root", "");

$userId = $_SESSION['user_id'];

// Récupérer solde de l’utilisateur
$stmt = $pdo->prepare("SELECT Wallet FROM user WHERE Id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$balance = $user['Wallet'] ?? 0;

// Récupérer les articles du panier
$stmt = $pdo->prepare("
    SELECT a.Id, a.Name, a.Price, COUNT(c.IdArticle) AS Quantity, IFNULL(s.NbrInStock, 0) AS NbrInStock
    FROM cart c
    JOIN article a ON a.Id = c.IdArticle
    LEFT JOIN stock s ON s.IdArticle = a.Id
    WHERE c.IdUser = ?
    GROUP BY a.Id
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($items as $item) {
    $total += $item['Price'] * $item['Quantity'];
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zipcode = $_POST['zipcode'] ?? '';

    if (empty($address) || empty($city) || empty($zipcode)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if ($total > $balance) {
        $errors[] = "Solde insuffisant pour valider la commande.";
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            // Créer la facture
            $stmt = $pdo->prepare("
                INSERT INTO invoice (IdUser, TransactionDate, Amount, BillingAddress, BillingCity, ZipCode)
                VALUES (?, NOW(), ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $total, $address, $city, $zipcode]);
            $invoiceId = $pdo->lastInsertId();

            // Pour chaque article : retirer du stock
            foreach ($items as $item) {
                if ($item['Quantity'] > $item['NbrInStock']) {
                    throw new Exception("Stock insuffisant pour l’article " . $item['Name']);
                }

                $stmt = $pdo->prepare("UPDATE stock SET NbrInStock = NbrInStock - ? WHERE IdArticle = ?");
                $stmt->execute([$item['Quantity'], $item['Id']]);
            }

            // Supprimer le panier
            $stmt = $pdo->prepare("DELETE FROM cart WHERE IdUser = ?");
            $stmt->execute([$userId]);

            // Déduire le solde utilisateur
            $stmt = $pdo->prepare("UPDATE user SET Wallet = Wallet - ? WHERE Id = ?");
            $stmt->execute([$total, $userId]);

            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Erreur lors de la validation : " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Validation du panier</title>
    <style>
        body {
            font-family: Arial;
            padding: 2rem;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input[type=text] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        button {
            padding: 0.7rem 1.2rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .summary {
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Confirmation de commande</h2>

        <?php if ($success): ?>
            <div class="success">Commande validée avec succès. Une facture a été générée.</div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="summary">
                <p><strong>Total du panier :</strong> <?= number_format($total, 2) ?> €</p>
                <p><strong>Votre solde :</strong> <?= number_format($balance, 2) ?> €</p>
            </div>

            <form method="post">
                <label>Adresse de facturation</label>
                <input type="text" name="address" required>

                <label>Ville</label>
                <input type="text" name="city" required>

                <label>Code postal</label>
                <input type="text" name="zipcode" required>

                <button type="submit">Valider la commande</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>