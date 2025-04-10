<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

include("../config/db.php");

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $borrowings = mysqli_query($conn, "SELECT b.id, m.name, bo.title, b.borrow_date, b.due_date 
        FROM borrowings b 
        JOIN members m ON b.member_id = m.id 
        JOIN books bo ON b.book_id = bo.id 
        WHERE b.return_date IS NULL 
        AND (bo.title LIKE '%$search%' OR m.name LIKE '%$search%')");
} else {
    $borrowings = mysqli_query($conn, "SELECT b.id, m.name, bo.title, b.borrow_date, b.due_date 
        FROM borrowings b 
        JOIN members m ON b.member_id = m.id 
        JOIN books bo ON b.book_id = bo.id 
        WHERE b.return_date IS NULL");
}

if (isset($_POST['borrow'])) {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+7 days'));

    mysqli_query($conn, "INSERT INTO borrowings (book_id, member_id, borrow_date, due_date) VALUES ('$book_id','$member_id','$borrow_date','$due_date')");
    mysqli_query($conn, "UPDATE books SET status='borrowed' WHERE id='$book_id'");

    $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, author, id FROM books WHERE id='$book_id'"));
    $member = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM members WHERE id='$member_id'"));

    $to = $member['email'];
    $subject = "📚 Book Borrowed Confirmation - Library";
    $message = "
Hello " . $member['name'] . ",

You have successfully borrowed the following book:

Book Title: " . $book['title'] . "
Author: " . $book['author'] . "
Book ID: " . $book['id'] . "
Borrowed Date: " . $borrow_date . "
Due Date: " . $due_date . "

Please return it on or before the due date to avoid fines.

Thank you,
Library Management System
";

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'webweaversacademy@gmail.com';       // ✅ Your Gmail
        $mail->Password   = 'avzu fvnd zifj ictw';          // ✅ Your App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('webweaversacademy@gmail.com', 'Library Management');
        $mail->addAddress($to, $member['name']);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "<script>alert('Email could not be sent. Error: {$mail->ErrorInfo}');</script>";
    }
}

if (isset($_GET['return'])) {
    $id = $_GET['return'];
    $today = date('Y-m-d');
    $borrow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id FROM borrowings WHERE id=$id"));
    mysqli_query($conn, "UPDATE borrowings SET return_date='$today' WHERE id=$id");
    mysqli_query($conn, "UPDATE books SET status='available' WHERE id=" . $borrow['book_id']);
}

$books = mysqli_query($conn, "SELECT * FROM books WHERE status='available'");
$members = mysqli_query($conn, "SELECT * FROM members WHERE status='active'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowed Books</title>
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
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .search-box input {
            padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;
        }
        .add-button {
            background: #27ae60; color: white; padding: 10px 20px; border: none;
            border-radius: 8px; cursor: pointer; font-weight: 600;
        }
        .add-button:hover { background: #2c80b4; }
        .popup-form {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); justify-content: center; align-items: center; z-index: 1000;
        }
        .popup-content {
            background: white; padding: 30px; border-radius: 12px; width: 400px; position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .popup-content h3 { margin-top: 0; }
        .popup-content select {
            width: 100%; margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 6px;
        }
        .popup-content button[type="submit"] {
            background: #2ecc71; color: white; padding: 10px 18px; border: none;
            border-radius: 6px; cursor: pointer; font-weight: 600;
        }
        .close-btn {
            position: absolute; top: 10px; right: 10px; background: #e74c3c; color: white;
            border: none; border-radius: 50%; width: 28px; height: 28px;
            font-weight: bold; font-size: 18px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        table {
            width: 100%; border-collapse: collapse; background: white;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        table th, table td {
            padding: 12px; border: 1px solid #ddd; text-align: center;
        }
        table th { background: #3498db; color: white; }
        table td a {
            color: #e67e22; text-decoration: none; font-weight: 600;
        }
        table td a:hover { text-decoration: underline; }
        .link-box {
            text-align: center; margin-bottom: 20px;
        }
        .link-box a {
            text-decoration: none; color: #2980b9; font-weight: bold;
        }
        .topbar {
            background: #2c3e50; color: white; padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center; font-size: 20px;
        }
        .topbar .site-name { font-weight: 600; }
        .next { display:flex; flex-direction:row; gap:35px; }
        a { color:white; font-size:small; }
    </style>
</head>
<body>
<div class="topbar">
    <div class="site-name">📚 Library Management</div>
    <div class="next">
        <a href="index.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="members.php">Members</a>
    </div>
</div>

<h2> Borrowed Books (Not Returned)</h2>

<div class="top-bar">
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by book or member..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <button class="add-button" onclick="document.getElementById('popup').style.display='flex'">+ Borrow Book</button>
</div>

<div class="link-box">
    <a href="returns.php">📁 View Returned Books</a>
</div>

<div class="popup-form" id="popup">
    <div class="popup-content">
        <button class="close-btn" onclick="document.getElementById('popup').style.display='none'">&times;</button>
        <h3>Borrow Book</h3>
        <form method="POST">
            <select name="book_id" required>
                <option value="">🔍 Select Book</option>
                <?php while ($b = mysqli_fetch_assoc($books)): ?>
                    <option value="<?= $b['id'] ?>"><?= $b['title'] ?></option>
                <?php endwhile; ?>
            </select>
            <select name="member_id" required>
                <option value="">🔍 Select Member</option>
                <?php while ($m = mysqli_fetch_assoc($members)): ?>
                    <option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="borrow">Confirm Borrow</button>
        </form>
    </div>
</div>

<table>
    <tr><th>ID</th><th>Member</th><th>Book</th><th>Borrowed</th><th>Due</th><th>Action</th></tr>
    <?php if (mysqli_num_rows($borrowings) > 0): ?>
        <?php while ($r = mysqli_fetch_assoc($borrowings)): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= $r['name'] ?></td>
            <td><?= $r['title'] ?></td>
            <td><?= $r['borrow_date'] ?></td>
            <td><?= $r['due_date'] ?></td>
            <td><a href="?return=<?= $r['id'] ?>" onclick="return confirm('Mark as returned?')">Return</a></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No active borrowings found.</td>
        </tr>
    <?php endif; ?>
</table>
</body>
</html>
