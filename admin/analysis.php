<?php
include("../config/db.php");

$filter_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

$total_books = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM books"))['total'];
$total_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM members"))['total'];
$total_borrowed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings WHERE return_date IS NULL"))['total'];
$total_returned = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings WHERE return_date IS NOT NULL"))['total'];

$months_result = mysqli_query($conn, "SELECT DISTINCT DATE_FORMAT(borrow_date, '%Y-%m') as month FROM borrowings ORDER BY month");
$months = [];
while ($row = mysqli_fetch_assoc($months_result)) {
    $months[] = $row['month'];
}

$monthly_stats = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(borrow_date, '%Y-%m') as month,
        COUNT(*) as borrowed,
        SUM(CASE WHEN return_date IS NOT NULL THEN 1 ELSE 0 END) as returned
    FROM borrowings
    GROUP BY month
    ORDER BY month
");

$chart_months = [];
$borrowed_data = [];
$returned_data = [];
while ($row = mysqli_fetch_assoc($monthly_stats)) {
    $chart_months[] = $row['month'];
    $borrowed_data[] = $row['borrowed'];
    $returned_data[] = $row['returned'];
}

$top_books_query = mysqli_query($conn, "
    SELECT bo.title, COUNT(*) as count 
    FROM borrowings b 
    JOIN books bo ON b.book_id = bo.id 
    WHERE DATE_FORMAT(borrow_date, '%Y-%m') = '$filter_month'
    GROUP BY b.book_id 
    ORDER BY count DESC 
    LIMIT 5
");

$top_books = [];
$top_counts = [];
while ($row = mysqli_fetch_assoc($top_books_query)) {
    $top_books[] = $row['title'];
    $top_counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📊 Library Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f7f9fc;
            padding: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .stats {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            width: 230px;
            text-align: center;
        }
        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #555;
        }
        .card strong {
            font-size: 26px;
            color: #2e7d32;
        }
        .charts {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 60px;
            margin-top: 40px;
        }
        canvas {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 10px;
            width: 500px !important;
            height: 450px !important;
        }
        .filter-form {
            text-align: center;
            margin-bottom: 20px;
        }
        select {
            padding: 8px 12px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
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

<h2>📈 Library Analytics Dashboard</h2>

<div class="stats">
    <div class="card">
        <h3>Total Books</h3>
        <strong><?= $total_books ?></strong>
    </div>
    <div class="card">
        <h3>Total Members</h3>
        <strong><?= $total_members ?></strong>
    </div>
    <div class="card">
        <h3>Currently Borrowed</h3>
        <strong><?= $total_borrowed ?></strong>
    </div>
    <div class="card">
        <h3>Total Returned</h3>
        <strong><?= $total_returned ?></strong>
    </div>
</div>

<div class="filter-form">
    <form method="GET">
        <label for="month">📅 Select Month: </label>
        <select name="month" onchange="this.form.submit()">
            <?php foreach ($months as $month): ?>
                <option value="<?= $month ?>" <?= $month == $filter_month ? 'selected' : '' ?>><?= $month ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="charts">
    <canvas id="monthlyChart"></canvas>
    <canvas id="topBooksChart"></canvas>
</div>

<script>
    // Bar chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_months) ?>,
            datasets: [
                {
                    label: 'Borrowed',
                    data: <?= json_encode($borrowed_data) ?>,
                    backgroundColor: '#42a5f5'
                },
                {
                    label: 'Returned',
                    data: <?= json_encode($returned_data) ?>,
                    backgroundColor: '#66bb6a'
                }
            ]
        },
        options: {
            responsive: false,
            plugins: {
                title: {
                    display: true,
                    text: '📅 Monthly Borrowing & Returning'
                }
            }
        }
    });

    // Pie chart
    new Chart(document.getElementById('topBooksChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($top_books) ?>,
            datasets: [{
                data: <?= json_encode($top_counts) ?>,
                backgroundColor: ['#4caf50', '#2196f3', '#ff9800', '#e91e63', '#9c27b0']
            }]
        },
        options: {
            responsive: false,
            plugins: {
                title: {
                    display: true,
                    text: '📘 Top 5 Borrowed Books (<?= $filter_month ?>)'
                }
            }
        }
    });
</script>

</body>
</html>
