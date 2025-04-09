<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin') {
        $_SESSION['admin'] = true;
        header("Location: admin/index.php");
        exit(); // Always good to call exit after header redirect
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | Library System</title>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Lexend', sans-serif;
      background: linear-gradient(120deg, #f6f8ff 0%, #dbeafe 100%);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
      
    }

    .login-container {
      background: white;
      padding: 40px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      text-align: center;
      width: 350px;
      position: relative;
      overflow: hidden;
    }

    .login-container::before {
      content: "📚";
      font-size: 64px;
      position: absolute;
      top: -30px;
      left: -30px;
      color: #dbeafe;
      transform: rotate(-15deg);
    }

    .login-container h2 {
      margin-bottom: 8px;
      color: #1e3a8a;
      font-weight: 600;
    }

    .login-container p {
      font-size: 14px;
      color: #555;
      margin-bottom: 24px;
    }

    .login-form input {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: 1px solid #c7d2fe;
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .login-form input:focus {
      outline: none;
      border-color: #6366f1;
      box-shadow: 0 0 8px rgba(99, 102, 241, 0.3);
    }

    .login-form button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background-color: #4f46e5;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .login-form button:hover {
      background-color: #4338ca;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Welcome Back</h2>
    <p>Login to access your digital library</p>
    <form method="POST" class="login-form">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
