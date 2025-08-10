<?php
include 'connect.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);
    $price = trim($_POST['price']);
    $claimed = isset($_POST['claimed']) && $_POST['claimed'] === 'Yes' ? 'Yes' : 'No';

    if ($name === '' || $author === '' || $category === '' || $price === '') {
        $error = 'Please fill all fields.';
    } else {
        $stmt = $conn->prepare("INSERT INTO books (name, author, category, price, claimed) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $author, $category, $price, $claimed);
        if ($stmt->execute()) {
            header('Location: list.php');
            exit();
        } else {
            $error = 'Failed to add book: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book - DIU Book Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Add a new book</h2>
    <?php if ($error): ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" class="form-grid">
        <input name="name" placeholder="Book Name" required>
        <input name="author" placeholder="Author" required>
        <input name="category" placeholder="Category" required>
        <input name="price" type="number" step="0.01" placeholder="Price" required>
        <label>Claimed?
            <select name="claimed">
                <option value="No" selected>No</option>
                <option value="Yes">Yes</option>
            </select>
        </label>
        <button class="btn-main" type="submit">Add Book</button>
    </form>
</div>
</body>
</html>