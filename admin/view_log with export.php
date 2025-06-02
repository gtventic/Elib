<?php
// view_log.php
require_once '../db.php';

// Allow only admin and super_admin to view logs
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

// Query to retrieve log entries joined with user and PDF file details
$sql = "SELECT pl.id, u.username, pf.file_name, pl.view_time
        FROM pdf_log pl 
        LEFT JOIN users u ON pl.user_id = u.id
        LEFT JOIN pdf_files pf ON pl.pdf_file_id = pf.id
        ORDER BY pl.view_time DESC";
$stmt = $conn->query($sql);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('../images/library-bg.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .log-container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 20px;
      border-radius: 8px;
      margin-top: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container log-container">
    <h2 class="mb-4">PDF View Log</h2>
    <?php if (!empty($logs)): ?>
      <table class="table table-striped table-responsive">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>PDF File</th>
            <th>View Time</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($logs as $log): ?>
          <tr>
            <td><?php echo htmlspecialchars($log['id']); ?></td>
            <td><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></td>
            <td><?php echo htmlspecialchars($log['file_name'] ?? 'Unknown'); ?></td>
            <td><?php echo htmlspecialchars($log['view_time']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No log entries found.</div>
    <?php endif; ?>

    <!-- Export Button for PDF -->
    <div class="mt-4">
      <a href="export_log.php" class="btn btn-success">
        Export Log to PDF
      </a>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
