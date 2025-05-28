<css>

</css>

<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
    </head>

    <form action="/registerHandler.php" method="post" enctype="multipart/form-data">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Username" required>

        <label for="Email">Email</label>
        <input type="email" name="email" id="email" placeholder="email@example.com" required>

        <label for="password">password</label>
        <input type="password" name="password" id="password" required>

        <label for="profilePicture">profilePicture</label>
        <input type="file" name="profilePicture" id="profilePicture">

        <input type="submit" value="submit">
    </form>
</html> 
<?php 

?> 