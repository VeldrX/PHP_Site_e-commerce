<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Register</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <?php include 'header.php'; ?>

    <main>
        <?php session_start(); ?>

        <?php if (isset($_SESSION['register_error'])): ?>
            <div style="color: red; margin-bottom: 15px; text-align: center;">
                <?= htmlspecialchars($_SESSION['register_error']) ?>
            </div>
            <?php unset($_SESSION['register_error']); ?>
        <?php endif; ?>

        <form action="/php_exam/registerHandler.php" method="post">
            <h2>Register</h2>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Register">
        </form>
    </main>


</body>

</html>