<?php
// export_log.php
require_once '../db.php';
session_start();

// Allow only admin and super_admin to view logs
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

// Include Dompdf's autoloader (adjust the path if needed)
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

// Retrieve log entries
$sql = "SELECT pl.id, u.username, pf.file_name, pl.view_time
        FROM pdf_log pl 
        LEFT JOIN users u ON pl.user_id = u.id
        LEFT JOIN pdf_files pf ON pl.pdf_file_id = pf.id
        ORDER BY pl.view_time DESC";
$stmt = $conn->query($sql);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Start output buffering to capture HTML content for PDF
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PDF View Log Export</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      color: #333;
    }
    h2 {
      text-align: center;
    }
    .log-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .log-table th, .log-table td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    .log-table th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <h2>PDF View Log</h2>
  <table class="log-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>PDF File</th>
        <th>View Time</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($logs)): ?>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?php echo htmlspecialchars($log['id']); ?></td>
            <td><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></td>
            <td><?php echo htmlspecialchars($log['file_name'] ?? 'Unknown'); ?></td>
            <td><?php echo htmlspecialchars($log['view_time']); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center;">No log entries found.</td>
          </tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
<?php
$html = ob_get_clean();

// Initialize Dompdf and load HTML
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML content as PDF
$dompdf->render();

// Output the generated PDF to the browser
$dompdf->stream("pdf_view_log.pdf", array("Attachment" => false));
?>
