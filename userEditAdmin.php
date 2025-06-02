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

    if ($user[6] != "admin"){
        echo "not authorised";
        exit;
    }
    

} else {
    echo "User not logged in.";

    exit;
}
$usernameOfEdited = $_POST["usernameOfEdited"];

$querry = $mysqlClient->prepare("Select * from user where Username = \"$usernameOfEdited\"");

$querry->execute();
    
$userToEdit = $querry->fetchAll();
    
$userToEdit = $userToEdit[0];

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>edit</title>
    </head>
    <body>
        
    </body>
</html>