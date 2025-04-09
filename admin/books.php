<?php
include("../config/db.php");

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $isbn = $_POST['isbn'];
    mysqli_query($conn, "INSERT INTO books (title, author, publisher, isbn) VALUES ('$title','$author','$publisher','$isbn')");
}

if (isset($_POST['edit'])) {
    $id = $_POST['edit_id'];
    $title = $_POST['edit_title'];
    $author = $_POST['edit_author'];
    $publisher = $_POST['edit_publisher'];
    $isbn = $_POST['edit_isbn'];
    mysqli_query($conn, "UPDATE books SET title='$title', author='$author', publisher='$publisher', isbn='$isbn' WHERE id=$id");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Check if this book is referenced in the borrowings table
    $check = mysqli_query($conn, "SELECT * FROM borrowings WHERE book_id = $id");

    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('This book is currently borrowed. Cannot delete!'); window.location='books.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM books WHERE id = $id");
        header("Location: books.php");
        exit();
    }
}


$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $books = mysqli_query($conn, "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR publisher LIKE '%$search%' OR isbn LIKE '%$search%'");
} else {
    $books = mysqli_query($conn, "SELECT * FROM books");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 20px;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-box input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .add-button {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .add-button:hover { background: #2c80b4; }

        .popup-form {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .popup-content h3 { margin-top: 0; }
        .popup-content input {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .popup-content button[type="submit"] {
            background: #2ecc71;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
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
        table td a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 600;
        }
        table td a:hover {
            text-decoration: underline;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            background: #fff3cd;
            color: #856404;
            font-weight: bold;
        }
        .topbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 20px;
        }
        .topbar .site-name { font-weight: 600; }
        .next {
            display:flex;
            flex-direction:row;
            gap:35px;
        }
        a {
            color:white;
            font-size:small;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="site-name">📚 Library Management</div>
    <div class="next">
        <a href="index.php">Dashboard</a>
        <a href="borrowings.php">Borrowed</a>
        <a href="members.php">Members</a>
    </div>
</div>

<h2> Manage Books</h2>

<div class="top-bar">
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <button class="add-button" onclick="document.getElementById('popup').style.display='flex'">+ Add Book</button>
</div>

<!-- Add Book Form -->
<div class="popup-form" id="popup">
    <div class="popup-content">
        <button class="close-btn" onclick="document.getElementById('popup').style.display='none'">&times;</button>
        <h3>Add New Book</h3>
        <form method="POST">
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="publisher" placeholder="Publisher" required>
            <input type="text" name="isbn" placeholder="ISBN" required>
            <button type="submit" name="add">Add Book</button>
        </form>
    </div>
</div>

<!-- Edit Book Form -->
<div class="popup-form" id="editPopup">
    <div class="popup-content">
        <button class="close-btn" onclick="document.getElementById('editPopup').style.display='none'">&times;</button>
        <h3>Edit Book</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="text" name="edit_title" id="edit_title" placeholder="Title" required>
            <input type="text" name="edit_author" id="edit_author" placeholder="Author" required>
            <input type="text" name="edit_publisher" id="edit_publisher" placeholder="Publisher" required>
            <input type="text" name="edit_isbn" id="edit_isbn" placeholder="ISBN" required>
            <button type="submit" name="edit">Update Book</button>
        </form>
    </div>
</div>

<table>
    <tr><th>ID</th><th>Title</th><th>Author</th><th>Publisher</th><th>ISBN</th><th>Status</th><th>Action</th></tr>
    <?php if (mysqli_num_rows($books) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($books)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['author'] ?></td>
                <td><?= $row['publisher'] ?></td>
                <td><?= $row['isbn'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                <a href="#" style="color:blue;" onclick="openEditPopup(
                        <?= $row['id'] ?>,
                        '<?= addslashes($row['title']) ?>',
                        '<?= addslashes($row['author']) ?>',
                        '<?= addslashes($row['publisher']) ?>',
                        '<?= addslashes($row['isbn']) ?>'
                    )">Edit</a> |
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this book?')">Delete</a> 
                    
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7" class="no-data">No books found.</td></tr>
    <?php endif; ?>
</table>

<script>
function openEditPopup(id, title, author, publisher, isbn) {
    document.getElementById('editPopup').style.display = 'flex';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_author').value = author;
    document.getElementById('edit_publisher').value = publisher;
    document.getElementById('edit_isbn').value = isbn;
}
</script>

</body>
</html>
