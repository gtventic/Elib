<?php
// admin/manage_users.php
require_once '../db.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username    = trim($_POST['username']);
    $rawPassword = $_POST['password'];
    $role        = $_POST['role'];
    $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;

    // Hash the password
    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, department_id) VALUES (:username, :password, :role, :department_id)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':department_id', $department_id);
    if ($stmt->execute()) {
        $message = "User created successfully.";
    } else {
        $message = "Error creating user.";
    }
}

// Get list of departments for the dropdown
$departments = $conn->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container mt-4">
    <h2>Create New User</h2>
    <?php if ($message) { echo '<div class="alert alert-info">' . $message . '</div>'; } ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select class="form-control" name="role" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
          <?php if ($_SESSION['user']['role'] == 'super_admin') { ?> 
          <option value="super_admin">Super Admin</option>
          <?php } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Department</label>
        <select class="form-control" name="department_id">
          <option value="">--Select Department--</option>
          <?php foreach ($departments as $dept) { ?>
            <option value="<?php echo $dept['id']; ?>"><?php echo $dept['department_name']; ?></option>
          <?php } ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Create User</button>
    </form>
  </div>
</body>
</html>

