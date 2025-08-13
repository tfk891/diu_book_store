<?php
include 'connect.php';
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $phone === '' || $password === '') {
        $error = 'Please fill all fields.';
    } else {
       
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $error = 'Email already registered. Please login.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare('INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)');
            $ins->bind_param('ssss', $name, $email, $phone, $hash);
            if ($ins->execute()) {
                header('Location: login.php');
                exit();
            } else {
                $error = 'Signup failed: ' . $ins->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Signup - DIU Book Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="center-card">
        <div class="brand">DIU Book Store</div>
        <h2>Create an account</h2>
        <?php if ($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" class="form-grid">
            <input name="name" placeholder="Full name" required>
            <input name="email" type="email" placeholder="Email" required>
            <input name="phone" placeholder="Phone" required>
            <input name="password" type="password" placeholder="Password" required>
            <button class="btn-main" type="submit">Sign up</button>
        </form>
        <p class="muted">Already signed up? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
