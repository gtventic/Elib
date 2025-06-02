<?php
// admin/add_department.php
require_once '../db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'super_admin') {
    header("Location: login.php");
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_name = trim($_POST['department_name']);
    $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (:department_name)");
    $stmt->bindParam(':department_name', $department_name);
    if ($stmt->execute()) {
        $message = "Department added successfully.";
    } else {
        $message = "Error adding department.";
    }
}

// Retrieve all departments ordered by department name (descending)
$stmt = $conn->query("SELECT * FROM departments ORDER BY department_name DESC");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Departments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h2>Add Department</h2>
    <?php if ($message) { echo '<div class="alert alert-info">' . $message . '</div>'; } ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Department Name</label>
        <input type="text" name="department_name" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Department</button>
    </form>

    <!-- Department List -->
    <hr>
    <h2>Department List (Ordered by Name)</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Department Name</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($departments)) {
          foreach ($departments as $dept) { ?>
              <tr>
                <td><?php echo htmlspecialchars($dept['id']); ?></td>
                <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
              </tr>
          <?php }
        } else { ?>
          <tr>
            <td colspan="2" class="text-center">No departments found.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
