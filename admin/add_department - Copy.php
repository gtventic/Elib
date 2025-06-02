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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Department</title>
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
  </div>
</body>
</html>
