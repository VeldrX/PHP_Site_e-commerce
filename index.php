<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Home</title>
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
            height: 50px;
        }

        .user-info {
            font-weight: bold;
            margin-right: 20px;
        }

        .auth-buttons a,
        .disconnect-button {
            color: white;
            text-decoration: none;
            background-color: #2980b9;
            padding: 8px 15px;
            border-radius: 5px;
            margin-right: 10px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .auth-buttons a:hover,
        .disconnect-button:hover {
            background-color: #1f6391;
        }
    </style>
</head>

<body>

    <header>
        <div>
            <?php if (isset($_SESSION['username'])): ?>
                <span class="user-info">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="disconnect-button">Disconnect</button>
                </form>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main style="padding: 20px;">
        <h1>Page d'accueil</h1>
        <p>Bienvenue sur notre site e-commerce !</p>
    </main>

</body>

</html>