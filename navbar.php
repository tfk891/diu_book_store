<?php
session_start();
?>
<div class="topbar">
    <div class="brand">DIU Book Store</div>
    <div class="top-actions">
        <?php if (isset($_SESSION['user'])): ?>
            <span class="welcome">Hello, <?php echo htmlspecialchars($_SESSION['user']); ?></span>
            <a class="link" href="add_book.php">Add Book</a>
            <!-- Removed the List link as requested -->
            <a class="link" href="logout.php">Logout</a>
        <?php else: ?>
            <a class="link" href="signup.php">Signup</a>
            <a class="link" href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>