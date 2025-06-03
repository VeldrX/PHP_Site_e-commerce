<header class="site-header">
    <div class="container">
        <a href="/" class="logo" style="color: white; font-weight: bold; text-decoration: none;">MonSite</a>
    </div>
    <div class="container">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="user-info">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span>

            <a href="logout.php"><button type="button" class="disconnect-button">Disconnect</button></a>
            <a href="sell.php"><button type="button" class="disconnect-button">Sell Articles</button></a>
            <a href="profile.php"><button type="button" class="disconnect-button">My Profile</button></a>

            <a href="cart.php"><button type="button" class="disconnect-button">🛒 Panier</button></a>
        <?php else: ?>
            <div class="container">
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>
        <?php endif; ?>
    </div>
</header>