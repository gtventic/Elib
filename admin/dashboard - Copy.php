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
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></h1>
    <p>Use the navigation menu to manage PDF files and system users.</p>
  </div>
</body>
</html>
