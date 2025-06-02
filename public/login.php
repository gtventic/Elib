<?php
// public/login.php
require_once '../db.php';

// Redirect if already logged in as a regular user
if(isset($_SESSION['user']) && $_SESSION['user']['role'] == 'user'){
  header("Location: index.php");
  exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Only allow regular users (role = 'user') to log in here
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND role = 'user'");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Check if the account is active
        if (!$user['is_active']) {
            $message = "Your account is deactivated.";
        }
        // Check if the password expiry has passed
        elseif (!empty($user['password_expiry']) && strtotime($user['password_expiry']) < time()) {
            $message = "Your password has expired. Please reset your password or contact support.";
        }
        else {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        }
    } else {
        $message = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FICOBank Elibrary User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('../images/library-bg.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    /* Optional: Style the login container for better readability */
    .login-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4 login-container">
          <h3 class="text-center">User Login</h3>
          <?php if($message) { echo '<div class="alert alert-danger">'.$message.'</div>'; } ?>
          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>
      </div>
    </div>
  </div>
</body>
</html>
