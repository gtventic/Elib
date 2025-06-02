<?php
// admin/manage_users.php
require_once '../db.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // New User Creation
    if (isset($_POST['create_user'])) {
        $username      = trim($_POST['username']);
        $rawPassword   = $_POST['password'];
        $role          = $_POST['role'];
        $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
        // Set password expiry to 90 days from now
        $password_expiry = date("Y-m-d H:i:s", strtotime("+90 days"));

        $stmt = $conn->prepare("INSERT INTO users (username, password, role, department_id, is_active, password_expiry) VALUES (:username, :password, :role, :department_id, 1, :password_expiry)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->bindParam(':password_expiry', $password_expiry);
        if ($stmt->execute()) {
            $message = "User created successfully.";
        } else {
            $message = "Error creating user.";
        }
    } 
    // Toggle Activation Status
    elseif (isset($_POST['toggle_activation'])) {
        $user_id        = intval($_POST['user_id']);
        $current_status = intval($_POST['current_status']);
        $new_status     = ($current_status == 1) ? 0 : 1;
        $stmt = $conn->prepare("UPDATE users SET is_active = :new_status WHERE id = :user_id");
        $stmt->bindParam(':new_status', $new_status, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $message = "User activation status updated.";
        } else {
            $message = "Error updating status.";
        }
    } 
    // Change Password (also resets the password expiry to 90 days from now)
    elseif (isset($_POST['change_password'])) {
        $user_id      = intval($_POST['user_id']);
        $new_password = $_POST['new_password'];
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $new_password_expiry = date("Y-m-d H:i:s", strtotime("+90 days"));
        $stmt = $conn->prepare("UPDATE users SET password = :password, password_expiry = :password_expiry WHERE id = :user_id");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':password_expiry', $new_password_expiry);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $message = "Password updated successfully.";
        } else {
            $message = "Error updating password.";
        }
    }
}

// Retrieve list of departments for dropdown
$departments = $conn->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Retrieve list of all users (with department names if available)
$stmt = $conn->query("SELECT u.*, d.department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <form method="POST" class="mb-5">
      <input type="hidden" name="create_user" value="1">
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
        <select class="form-select" name="role" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
          <?php if ($_SESSION['user']['role'] == 'super_admin') { ?>
            <option value="super_admin">Super Admin</option>
          <?php } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Department</label>
        <select class="form-select" name="department_id">
          <option value="">--Select Department--</option>
          <?php foreach ($departments as $dept): ?>
            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Create User</button>
    </form>

    <h2>User List</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Role</th>
          <th>Department</th>
          <th>Status</th>
          <th>Password Expiry</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($users) > 0): ?>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td><?php echo htmlspecialchars($user['department_name'] ?? ''); ?></td>
            <td><?php echo $user['is_active'] ? 'Active' : 'Deactivated'; ?></td>
            <td><?php echo $user['password_expiry'] ? htmlspecialchars($user['password_expiry']) : 'N/A'; ?></td>
            <td>
              <!-- Toggle Activation Form -->
              <form method="POST" style="display:inline-block;">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <input type="hidden" name="current_status" value="<?php echo $user['is_active']; ?>">
                <button type="submit" name="toggle_activation" class="btn btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?> btn-sm">
                  <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                </button>
              </form>

              <!-- Change Password: Collapse Toggle -->
              <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#changePass<?php echo $user['id']; ?>" aria-expanded="false" aria-controls="changePass<?php echo $user['id']; ?>">
                Change Password
              </button>
              <div class="collapse mt-2" id="changePass<?php echo $user['id']; ?>">
                <form method="POST">
                  <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                  <div class="input-group">
                    <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                    <button class="btn btn-primary" type="submit" name="change_password">Update</button>
                  </div>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">No users found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
