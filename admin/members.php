<?php
include("../config/db.php");

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $members = mysqli_query($conn, "SELECT * FROM members WHERE name LIKE '%$search%' OR email LIKE '%$search%'");
} else {
    $members = mysqli_query($conn, "SELECT * FROM members");
}

$edit_member = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM members WHERE id=$edit_id");
    $edit_member = mysqli_fetch_assoc($edit_query);
}

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    mysqli_query($conn, "INSERT INTO members (name, email, mobile) VALUES ('$name','$email','$mobile')");
    header("Location: members.php");
    exit();
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    mysqli_query($conn, "UPDATE members SET name='$name', email='$email', mobile='$mobile' WHERE id=$id");
    header("Location: members.php");
    exit();
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Check if the member has any borrow history
    $check = mysqli_query($conn, "SELECT * FROM borrowings WHERE member_id = $id");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Cannot delete! This member has borrow history.'); window.location='members.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM members WHERE id = $id");
        header("Location: members.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members</title>
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

        .top-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .top-controls form {
            flex-grow: 1;
        }

        .top-controls input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 100%;
            max-width: 300px;
        }

        .add-btn {
            background-color: #2ecc71;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 10px;
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

        .popup {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 300px;
            position: relative;
        }

        .popup-content input[type="text"],
        .popup-content input[type="email"] {
            width: 80%;
            padding: 10px;
            margin-top:10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .popup-content button[name="add"],
        .popup-content button[name="update"] {
            background-color: #2ecc71;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: red;
            color: white;
            border: none;
            font-size: 16px;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 50%;
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

        .next{
            display:flex;
            flex-direction:row;
            gap:35px;
        }

        a{
            color:white;
            font-size:small;
        }

        td a{
            color:red;
        }
    </style>
</head>
<body>
<div class="topbar">
    <div class="site-name">📚 Library Management</div>
    <div class="next">
        <a href="index.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="borrowings.php">Borrowed</a>
    </div>
</div>

<h2>Manage Members</h2>

<div class="top-controls">
    <form method="GET">
        <input type="text" name="search" placeholder="Search member by name or email..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <button class="add-btn" onclick="document.getElementById('popup').style.display='flex'">+ Add Member</button>
</div>

<div id="popup" class="popup">
    <div class="popup-content">
        <button class="close-btn" onclick="document.getElementById('popup').style.display='none'">×</button>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_member['id'] ?? '' ?>">
            <input type="text" name="name" placeholder="Name" required value="<?= $edit_member['name'] ?? '' ?>">
            <input type="email" name="email" placeholder="Email" required value="<?= $edit_member['email'] ?? '' ?>">
            <input type="text" name="mobile" placeholder="Mobile Number" required value="<?= $edit_member['mobile'] ?? '' ?>">

            <?php if ($edit_member): ?>
                <button name="update">Update Member</button>
            <?php else: ?>
                <button name="add">Add Member</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Status</th><th>Actions</th></tr>
    <?php while ($row = mysqli_fetch_assoc($members)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['mobile'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <a href="?edit=<?= $row['id'] ?>" style="color:blue;">Edit</a> |
            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this member?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
<?php if ($edit_member): ?>
    document.getElementById('popup').style.display = 'flex';
<?php endif; ?>
</script>

</body>
</html>
