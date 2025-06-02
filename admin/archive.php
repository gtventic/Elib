<?php
// admin/archive.php
require_once '../db.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

$message = "";
if (isset($_GET['archive']) && isset($_GET['id'])) {
    $pdf_id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE pdf_files SET archived = 1 WHERE id = :id");
    $stmt->bindParam(':id', $pdf_id);
    if ($stmt->execute()) {
        $message = "PDF archived successfully.";
    }
}

// Fetch active (non-archived) PDFs
$stmt = $conn->query("SELECT * FROM pdf_files WHERE archived = 0");
$pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Archive PDFs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h2>Current PDF Files</h2>
    <?php if ($message) { echo '<div class="alert alert-success">' . $message . '</div>'; } ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>File Name</th>
          <th>Uploaded By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pdfs as $pdf) { ?>
        <tr>
          <td><?php echo $pdf['id']; ?></td>
          <td><?php echo htmlspecialchars($pdf['file_name']); ?></td>
          <td><?php echo htmlspecialchars($pdf['uploaded_by']); ?></td>
          <td>
            <a class="btn btn-danger btn-sm" href="?archive=1&id=<?php echo $pdf['id']; ?>">Archive</a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
