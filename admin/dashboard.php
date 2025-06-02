<?php
// admin/dashboard.php
require_once '../db.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      /* Using the background image from the images folder */
      background: url('../images/library-bg.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    /* Optional: add a semi-transparent background for content readability */
    .dashboard-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 20px;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4 dashboard-container">
    <h1>Welcome to FICOBank ELibrary, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></h1>
    <p>© ictd 2025. </p>
  </div>
</body>
</html>
