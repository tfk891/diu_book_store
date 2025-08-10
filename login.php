<?php
include 'connect.php';
session_start();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === '' || $password === '') {
        $msg = 'Please provide email and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, name, password FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row['name'];
                $_SESSION['user_id'] = $row['id'];
                header('Location: list.php');
                exit();
            } else {
                $msg = 'Invalid credentials.';
            }
        } else {
            $msg = 'No account found with that email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login - DIU Book Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="center-card">
        <div class="brand">DIU Book Store</div>
        <h2>Welcome back</h2>
        <?php if ($msg): ?>
            <div class="alert"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <form method="POST" class="form-grid">
            <input name="email" type="email" placeholder="Email" required>
            <input name="password" type="password" placeholder="Password" required>
            <button class="btn-main" type="submit">Login</button>
        </form>
        <p class="muted">New here? <a href="signup.php">Create an account</a></p>
    </div>
</body>
</html>
