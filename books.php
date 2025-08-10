<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Add book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_name'])) {
    $book_name = $_POST['book_name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $claimed = $_POST['claimed'];

    $sql = "INSERT INTO books (book_name, author, category, price, claimed) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssds", $book_name, $author, $category, $price, $claimed);
    $stmt->execute();
}

// Search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM books WHERE book_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    $books = $conn->query("SELECT * FROM books");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Books - DIU Book Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="nav">
        <form method="GET">
            <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <form method="POST">
            <input type="text" name="book_name" placeholder="Book Name" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <select name="claimed" required>
                <option value="No">No</option>
                <option value="Yes">Yes</option>
            </select>
            <button type="submit">Add</button>
        </form>
    </div>

    <h2>Available Books</h2>
    <table border="1" cellpadding="10" style="width:100%; background:white;">
        <tr>
            <th>Book Name</th>
            <th>Author</th>
            <th>Category</th>
            <th>Price</th>
            <th>Claimed</th>
        </tr>
        <?php while ($row = $books->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['book_name']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['price']); ?></td>
                <td><?php echo htmlspecialchars($row['claimed']); ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>