<?php 
    session_start();

try {
    $mysqlClient = new PDO(dsn: 'mysql:host=localhost;dbname=php_exam_db;charset=utf8', username: 'root', password: '');
} catch (PDOException $e) {
    die($e->getMessage());
}

if (isset($_SESSION['username'])) {
    
    $usename = $_SESSION['username'];

    $querry = $mysqlClient->prepare("Select * from user where Username = \"$usename\"");

    $querry->execute();
    
    $user = $querry->fetchAll();
    
    $user = $user[0];
    

} else {
    echo "User not logged in.";
    exit;
}
$idOfThingSold = $_POST["idOfThingSold"];

$querry = $mysqlClient->prepare("Select * from article where Id = \"$idOfThingSold\"");

$querry->execute();
    
$postToEdit = $querry->fetchAll();
    
$postToEdit = $postToEdit[0];

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>edit</title>
    </head>
    <body>
        <form action="/editPostHandler.php" method="post" enctype="multipart/form-data">
        <h2>Register</h2>

        <input type="hidden" name="id" value="<?= htmlspecialchars($postToEdit['Id']) ?>">

        <label for="productName">nom du produit</label>
        <input type="text" name="productName" value="<?= htmlspecialchars($postToEdit['Name']) ?>">

        <label for="description">description</label>
        <input type="text" name="description" value="<?= htmlspecialchars($postToEdit['Description']) ?>">

        <label for="price">prix</label>
        <input type="number" name="price" value="<?= htmlspecialchars($postToEdit['Price']) ?>">

        <button type="submit" name="delete" value="delete">Delete</button>
        <input type="submit" value="Register">
    </form>
    </body>
</html>
