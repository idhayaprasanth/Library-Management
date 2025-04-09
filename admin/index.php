<?php
include("../config/db.php");
$book_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM books"))['total'];
$member_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM members"))['total'];
$borrowed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings WHERE return_date IS NULL"))['total'];

// Count of returned books
$returned_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings WHERE return_date IS NOT NULL"))['total'];?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Admin Dashboard</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #eef2f5;
            color: #333;
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

        .topbar .site-name {
            font-weight: 600;
        }

        .topbar .logout a {
            color: white;
            text-decoration: none;
            font-size : small;
            font-weight:bold;
            padding: 8px 12px;
            background: #e74c3c;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .topbar .logout a:hover {
            background: #c0392b;
        }
        .next{
            display:flex;
            flex-direction:row;
            gap:15px;
        }
        #analysis { 
            color: white;
            text-decoration: none;
            font-size : small;
            font-weight:bold;
            padding: 8px 12px;
            background: blue;
            border-radius: 4px;
            transition: background 0.3s;

        }

        h2 {
            text-align: center;
            margin-top: 30px;
            font-size: 34px;
            color: #2c3e50;
        }

        .dashboard-box {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 50px auto;
            max-width: 1200px;
            flex-wrap: wrap;
        }

        .card {
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            border:2px groove whitesmoke;
            border-radius: 8px;
            box-shadow: 8px 8px 20px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
            padding: 18px 25px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 12px 12px 25px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            margin-bottom: 15px;
            color: #1f2f46;
            font-size: 20px;
        }

        .card strong {
            font-size: 25px;
            color: #3498db;
            margin-bottom: 25px;
            display: block;
        }

        .card a {
            margin-top: auto;
            background: #2c3e50;
            color: white;
            padding: 12px 26px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .card a:hover {
            background: #2c80b4;
        }

        nav {
            display: none;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="site-name">📚 Library Management</div>
    <div class="next">
    <a id="analysis" href="analysis.php"> View Overview</a>
    <div class="logout"><a href="../logout.php"> Logout</a></div></div>
</div>

<h2>Admin Dashboard</h2>

<div class="dashboard-box">
    <div class="card">
        <h3>Total Books</h3>
        <strong><?= $book_count ?></strong>
        <a href="books.php"> Manage Books</a>
    </div>
    <div class="card">
        <h3>Total Members</h3>
        <strong><?= $member_count ?></strong>
        <a href="members.php"> Manage Members</a>
    </div>
    <div class="card">
        <h3>Books Borrowed</h3>
        <strong><?= $borrowed_count ?></strong>
        <a href="borrowings.php"> View Borrowings</a>
    </div>
    
</div>

</body>
</html>
