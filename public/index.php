<?php
// public/index.php
require_once '../db.php';

// Check login and that the user is a regular user
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
  header("Location: login.php");
  exit;
}

// Retrieve the logged in user id and the user's department id
$user_id   = $_SESSION['user']['id'];
$dept_id   = $_SESSION['user']['department_id'];

// Only display PDF files that are not archived, belong to the user's department,
// and either have no specific target users or include the user in the comma-separated target_users field.
$stmt = $conn->prepare("
    SELECT * FROM pdf_files 
    WHERE archived = 0 
      AND department_id = :dept_id 
      AND (target_users IS NULL OR target_users = '' OR FIND_IN_SET(:user_id, target_users))
");
$stmt->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PDF Viewer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Available PDF Files</h2>
      <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>File Name</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($pdfs) > 0): ?>
          <?php foreach ($pdfs as $pdf): ?>
          <tr>
            <td><?php echo htmlspecialchars($pdf['id']); ?></td>
            <td><?php echo htmlspecialchars($pdf['file_name']); ?></td>
            <td>
              <a class="btn btn-primary btn-sm" href="view_pdf.php?id=<?php echo $pdf['id']; ?>">View</a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="text-center">No PDF files available for your department.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
