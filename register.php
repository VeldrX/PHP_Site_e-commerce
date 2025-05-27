<css>

</css>

<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
    </head>

    <form action="/home.php" method="post" enctype="multipart/form-data">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Username">

        <label for="Email">Email</label>
        <input type="email" name="email" id="email" placeholder="email@example.com">

        <label for="password">password</label>
        <input type="password" name="password" id="password">

        <label for="profilePicture">profilePicture</label>
        <input type="file" name="profilePicture" id="profilePicture">

        <input type="submit" value="submit">
    </form>
</html> 
<?php 

?> 