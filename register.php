<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            margin: 0;
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
        input[type="password"],
        input[type="file"] {
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
</head>

<body>

    <form action="/registerHandler.php" method="post" enctype="multipart/form-data">
        <h2>Register</h2>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Username" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="email@example.com" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="profilePicture">Profile Picture</label>
        <input type="file" name="profilePicture" id="profilePicture">

        <input type="submit" value="Register">
    </form>

</body>

</html>