<?php
// admin/upload.php
require_once '../db.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// If the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file'])) {
    // Retrieve additional input values
    $department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : null;
    $target_users = isset($_POST['target_users']) ? $_POST['target_users'] : array();
    // Convert selected user IDs into a comma-separated string (or leave null if none selected)
    $allowed_users = !empty($target_users) ? implode(",", $target_users) : null;

    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $fileName    = $_FILES['pdf_file']['name'];
        $tmpName     = $_FILES['pdf_file']['tmp_name'];
        $destination = "../uploads/" . basename($fileName);

        // Validate that the uploaded file is a PDF
        if (mime_content_type($tmpName) == 'application/pdf') {
            if (move_uploaded_file($tmpName, $destination)) {
                // Insert values into the pdf_files table along with department and target users info
                $stmt = $conn->prepare("INSERT INTO pdf_files (file_name, file_path, uploaded_by, department_id, target_users) VALUES (:file_name, :file_path, :uploaded_by, :department_id, :target_users)");
                $stmt->bindParam(':file_name', $fileName);
                $stmt->bindParam(':file_path', $destination);
                $stmt->bindParam(':uploaded_by', $_SESSION['user']['id']);
                $stmt->bindParam(':department_id', $department_id);
                $stmt->bindParam(':target_users', $allowed_users);
                $stmt->execute();
                $message = "PDF file uploaded successfully.";
            } else {
                $message = "Failed to move uploaded file.";
            }
        } else {
            $message = "Only PDF files are allowed.";
        }
    } else {
        $message = "Error uploading file.";
    }
}

// Retrieve departments for the dropdown
$departments = $conn->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Retrieve system users (for example, only those with role 'user')
$users = $conn->query("SELECT * FROM users WHERE role = 'user'")->fetchAll(PDO::FETCH_ASSOC);

// --- New Code: Retrieve PDF File List ---
// Retrieve all uploaded PDF files, along with associated department names (if any)
$sql = "SELECT pf.*, d.department_name 
        FROM pdf_files pf 
        LEFT JOIN departments d ON pf.department_id = d.id 
        ORDER BY pf.id DESC";
$stmt = $conn->query($sql);
$pdf_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload PDF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h2>Upload PDF</h2>
    <?php if ($message) { echo '<div class="alert alert-info">' . $message . '</div>'; } ?>
    <form method="POST" enctype="multipart/form-data">
      <!-- PDF file input -->
      <div class="mb-3">
        <label for="pdf_file" class="form-label">Select PDF File</label>
        <input class="form-control" name="pdf_file" type="file" id="pdf_file" accept="application/pdf" required>
      </div>
      
      <!-- Department selection -->
      <div class="mb-3">
        <label for="department_id" class="form-label">Select Department</label>
        <select class="form-control" name="department_id" id="department_id" required>
          <option value="">-- Select Department --</option>
          <?php foreach ($departments as $dept) { ?>
            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
          <?php } ?>
        </select>
      </div>
      
      <!-- Allowed Users multi-select -->
      <div class="mb-3">
        <label for="target_users" class="form-label">Assign To Specific Users</label>
        <select class="form-control" name="target_users[]" id="target_users" multiple>
          <?php foreach ($users as $user_item) { ?>
            <option value="<?php echo $user_item['id']; ?>"><?php echo htmlspecialchars($user_item['username']); ?></option>
          <?php } ?>
        </select>
        <small class="form-text text-muted">Hold down the Ctrl (Windows) or Command (Mac) key to select multiple users.</small>
      </div>
      
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    
    <!-- New Section: PDF File List -->
    <hr>
    <h2>PDF File List</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>File Name</th>
          <th>Department</th>
          <th>Allowed Users</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pdf_files)) {
          foreach ($pdf_files as $pdf) {
              // Process allowed users string into names.
              $allowedNames = "";
              if (!empty($pdf['target_users'])) {
                  // Convert comma-separated IDs into an array.
                  $user_ids = explode(',', $pdf['target_users']);
                  // Prepare placeholders for query
                  $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
                  $stmtUsers = $conn->prepare("SELECT username FROM users WHERE id IN ($placeholders)");
                  $stmtUsers->execute($user_ids);
                  $userNames = $stmtUsers->fetchAll(PDO::FETCH_COLUMN);
                  // Join names into a string.
                  $allowedNames = implode(', ', $userNames);
              } else {
                  $allowedNames = "All";
              }
              ?>
              <tr>
                <td><?php echo htmlspecialchars($pdf['id']); ?></td>
                <td><?php echo htmlspecialchars($pdf['file_name']); ?></td>
                <td><?php echo htmlspecialchars($pdf['department_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($allowedNames); ?></td>
                <!-- <td><a href="../<?php echo $pdf['file_path']; ?>" target="_blank" class="btn btn-sm btn-primary">View PDF</a></td> -->
 <td><a href="../uploads/<?php echo htmlspecialchars($pdf['file_name']); ?>" target="_blank" class="btn btn-sm btn-primary">View PDF</a></td>
              </tr>
          <?php }
        } else { ?>
          <tr>
            <td colspan="5" class="text-center">No PDF files found.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
