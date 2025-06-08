<?php
session_start();

try {
    $mysqlClient = new PDO('mysql:host=localhost;dbname=php_exam_db;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
}

$querry = $mysqlClient->prepare("SELECT * FROM user WHERE Username = :username OR Email = :email");
$querry->execute(['username' => $username, 'email' => $email]);
$similarUser = $querry->fetchAll();

$imageName = '';

if (empty($similarUser)) {
    if (!empty($_FILES['profilePicture']['name'])) {
        $extension = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['profilePicture']['tmp_name'], 'uploads/users/' . $imageName);
    }

    $querry = $mysqlClient->prepare("
        INSERT INTO user (Username, Password, Email, Wallet, ProfilePicture, role)
        VALUES (:username, :password, :email, 0, :imageName, 'user')
    ");
    $querry->execute([
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'imageName' => $imageName
    ]);

    $_SESSION['user_id'] = $mysqlClient->lastInsertId();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'user';

    header("Location: /index.php");
    exit();
} else {
    if ($similarUser[0]['Username'] === $username) {
        $_SESSION['register_error'] = "Nom d'utilisateur déjà utilisé.";
    } elseif ($similarUser[0]['Email'] === $email) {
        $_SESSION['register_error'] = "Adresse e-mail déjà utilisée.";
    } else {
        $_SESSION['register_error'] = "Erreur inconnue.";
    }

    header("Location: /register.php");
    exit();
}
