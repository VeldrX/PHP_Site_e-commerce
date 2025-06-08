<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=php_exam_db;charset=utf8", "root", "");
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.Id, a.Name, a.Price, a.Image,
           COUNT(c.IdArticle) AS Quantity,
           IFNULL(s.NbrInStock, 0) AS NbrInStock
    FROM cart c
    JOIN article a ON a.Id = c.IdArticle
    LEFT JOIN stock s ON s.IdArticle = a.Id
    WHERE c.IdUser = :userId
    GROUP BY a.Id
");

$stmt->execute(['userId' => $userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }



        .cart-item {
            display: flex;
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem auto;
            max-width: 750px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            gap: 1rem;
        }

        .cart-item img {
            width: 110px;
            height: auto;
            border-radius: 8px;
        }

        .cart-details {
            flex: 1;
        }

        .cart-details h3 {
            margin: 0;
            margin-bottom: 0.5rem;
        }

        .quantity-control {
            margin: 0.8rem 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .quantity-control form {
            display: inline;
        }

        .quantity-control button {
            padding: 0.4rem 0.8rem;
            font-size: 1.1rem;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .quantity-control button[disabled] {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .subtotal {
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .total {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 2rem;
        }

        .empty-message {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 2rem;
            color: #888;
        }

        .validate-btn {
            display: block;
            margin: 2rem auto;
            padding: 0.8rem 2rem;
            background-color: #27ae60;
            color: white;
            border: none;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .validate-btn:hover {
            background-color: #219150;
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <?php include 'header.php'; ?>

    <h1>Votre Panier</h1>

    <?php if (empty($items)) : ?>
        <p class="empty-message">Votre panier est vide.</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="cart-item">
                <img src="uploads/articles/<?= htmlspecialchars($item['Image']) ?>" alt="<?= htmlspecialchars($item['Name']) ?>">
                <div class="cart-details">
                    <h3><?= htmlspecialchars($item['Name']) ?></h3>
                    <p>Prix unitaire : <?= number_format($item['Price'], 2) ?> €</p>
                    <div class="quantity-control">
                        <form action="update_cart.php" method="post">
                            <input type="hidden" name="action" value="decrease">
                            <input type="hidden" name="article_id" value="<?= $item['Id'] ?>">
                            <button type="submit">−</button>
                        </form>
                        <span><?= $item['Quantity'] ?></span>
                        <form action="update_cart.php" method="post">
                            <input type="hidden" name="action" value="increase">
                            <input type="hidden" name="article_id" value="<?= $item['Id'] ?>">
                            <button type="submit" <?= $item['Quantity'] >= $item['NbrInStock'] ? 'disabled' : '' ?>>+</button>
                        </form>
                    </div>
                    <p class="subtotal">Sous-total : <?= number_format($item['Price'] * $item['Quantity'], 2) ?> €</p>
                </div>
            </div>
            <?php $total += $item['Price'] * $item['Quantity']; ?>
        <?php endforeach; ?>

        <div class="total">Total : <?= number_format($total, 2) ?> €</div>

        <form action="cart/validate.php" method="get">
            <button type="submit" class="validate-btn">Valider le panier</button>
        </form>
    <?php endif; ?>

</body>

</html>