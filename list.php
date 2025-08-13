<?php
include 'connect.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user'];

if (isset($_GET['claim'])) {
    $book_id = intval($_GET['claim']);
    $stmt = $conn->prepare("INSERT IGNORE INTO user_claims (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    header('Location: list.php');
    exit();
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT b.*, 
        (SELECT 1 FROM user_claims uc WHERE uc.user_id = ? AND uc.book_id = b.id LIMIT 1) AS claimed_by_user 
        FROM books b 
        WHERE b.name LIKE ? 
        ORDER BY b.id DESC");
    $like = "%" . $search . "%";
    $stmt->bind_param("is", $user_id, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT b.*, 
        (SELECT 1 FROM user_claims uc WHERE uc.user_id = ? AND uc.book_id = b.id LIMIT 1) AS claimed_by_user 
        FROM books b 
        ORDER BY b.id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>DIU Book Store - Welcome</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        /* Additional styling for welcome section */
        .welcome-section {
            background: linear-gradient(135deg, #4a90e2 0%, #50e3c2 100%);
            color: white;
            padding: 40px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .welcome-section h1 {
            font-size: 3em;
            margin-bottom: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        }
        .welcome-section p {
            font-size: 1.2em;
            max-width: 700px;
            margin: 0 auto 20px;
            line-height: 1.6;
            font-style: italic;
        }
        .highlight {
            font-weight: bold;
            color: #ffda44;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <section class="welcome-section" role="banner" aria-label="Welcome message">
        <h1>Welcome to DIU Book Store, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>
            Discover a curated collection of books tailored for <span class="highlight">Daffodil International University</span> students and faculty.
            Whether you're looking for textbooks, research materials, or leisure reading, we strive to bring you the best online bookstore experience.
        </p>
        <p>
            Claim your favorite books with ease, explore new arrivals, and stay connected with our vibrant academic community.
        </p>
    </section>

    <div class="top-row">
        <h2>Available Books</h2>
        <form method="GET" class="search-form" role="search" aria-label="Search books">
            <input 
                name="search" 
                placeholder="Search by book name" 
                value="<?php echo htmlspecialchars($search); ?>" 
                aria-label="Search books by name"
            />
            <button type="submit" class="btn-small">Search</button>
        </form>
    </div>

    <div class="table-wrap" role="region" aria-live="polite" aria-label="List of available books">
        <table class="book-table" role="table">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Author</th>
                    <th scope="col">Category</th>
                    <th scope="col">Price</th>
                    <th scope="col">Claimed</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; font-style: italic;">No books found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?> ৳</td>
                        <td><?php echo $row['claimed_by_user'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <?php if (!$row['claimed_by_user']) { ?>
                                <a class="btn-small" href="?claim=<?php echo $row['id']; ?>">Claim</a>
                            <?php } else { ?>
                                <span aria-label="Already claimed">—</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
