<header class="site-header">
    <div class="container">
        <a href="/php_exam" class="logo" style="color: white; font-weight: bold; text-decoration: none;">MonSite</a>
    </div>
    <div class="container">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="user-info">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span>

            <a href="/php_exam/logout.php"><button type="button" class="disconnect-button">Disconnect</button></a>
            <a href="/php_exam/sell.php"><button type="button" class="disconnect-button">Sell Articles</button></a>
            <a href="/php_exam/profile.php"><button type="button" class="disconnect-button">My Profile</button></a>
            <a href="/php_exam/cart.php"><button type="button" class="disconnect-button">🛒 Panier</button></a>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/php_exam/adminPageUser.php"><button type="button" class="admin-button">All Users</button></a>
                <a href="/php_exam/adminPagePost.php"><button type="button" class="admin-button">All Posts</button></a>
            <?php endif; ?>

        <?php else: ?>
            <div class="container">
                <a href="/php_exam/login.php">Login</a>
                <a href="/php_exam/register.php">Register</a>
            </div>
        <?php endif; ?>
    </div>
</header>