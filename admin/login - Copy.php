<?php
// admin/login.php
require_once '../db.php';

// Redirect if already logged in
if(isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin', 'super_admin'])){
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Query the database for the admin user (allowing both admin and super_admin)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND role IN ('admin', 'super_admin')");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
          <h3 class="text-center">Admin Login</h3>
          <?php if($error) { echo '<div class="alert alert-danger">'.$error.'</div>'; } ?>
          <form method="POST" action="">
            <div class="mb-3">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
      </div>
    </div>
  </div>
</body>
</html>
