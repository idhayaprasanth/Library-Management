<?php
include("../config/db.php");

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $returns = mysqli_query($conn, "SELECT b.id, m.name, bo.title, b.borrow_date, b.due_date, b.return_date 
        FROM borrowings b 
        JOIN members m ON b.member_id = m.id 
        JOIN books bo ON b.book_id = bo.id 
        WHERE b.return_date IS NOT NULL 
        AND (bo.title LIKE '%$search%' OR m.name LIKE '%$search%')");
} else {
    $returns = mysqli_query($conn, "SELECT b.id, m.name, bo.title, b.borrow_date, b.due_date, b.return_date 
        FROM borrowings b 
        JOIN members m ON b.member_id = m.id 
        JOIN books bo ON b.book_id = bo.id 
        WHERE b.return_date IS NOT NULL");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Returned Books</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-box input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 300px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background: #3498db;
            color: white;
        }

        .link-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .link-box a {
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2> Returned Books</h2>

<div class="search-box">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by book or member..." value="<?= htmlspecialchars($search) ?>">
    </form>
</div>

<div class="link-box">
    <a href="borrowings.php">🔙 Back to Borrowings</a>
</div>

<table>
    <tr><th>ID</th><th>Member</th><th>Book</th><th>Borrowed</th><th>Due</th><th>Returned</th></tr>
    <?php if (mysqli_num_rows($returns) > 0): ?>
        <?php while ($r = mysqli_fetch_assoc($returns)): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['name'] ?></td>
            <td><?= $r['title'] ?></td>
            <td><?= $r['borrow_date'] ?></td>
            <td><?= $r['due_date'] ?></td>
            <td><?= $r['return_date'] ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">No returned books found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
