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
        <form action="/userEditAdminHandler.php" method="post" enctype="multipart/form-data">
        <h2>Register</h2>

        <input type="hidden" name="username" value="<?= htmlspecialchars($userToEdit['Username']) ?>">

        
        <label for="wallet">Argent (ne peut étre qu'augementer)</label>
        <input type="number" name="wallet" id="wallet" min=<?= htmlspecialchars($userToEdit['Wallet']) ?>  placeholder=<?= htmlspecialchars($userToEdit['Wallet']) ?>>

        <p>Role</p>
        <?php 
            if($userToEdit['role'] == "admin"){
                echo "<label for=\"user\">user</label>
                    <input type=\"radio\" id=\"user\" name=\"role\" value=\"user\"  > <br>

                    <label for=\"user\">admin</label>
                    <input type=\"radio\" id=\"admin\" name=\"role\" value=\"admin\" checked><br> ";
                } else {
                    echo 
                    "<label for=\"user\">user</label>
                    <input type=\"radio\" id=\"user\" name=\"role\" value=\"user\" checked > <br>

                    <label for=\"user\">admin</label>
                    <input type=\"radio\" id=\"admin\" name=\"role\" value=\"admin\" ><br> ";
                };
        ?>


        <button type="submit" name="delete" value="delete">Delete</button>
        <input type="submit" value="Register">
    </form>
    </body>
</html>
