<?php
// admin/navbar.php
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">FICOBank E-library Admin</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <?php if($_SESSION['user']['role'] == 'admin') { ?>
          <li class="nav-item"><a href="upload.php" class="nav-link">Upload PDF</a></li>
        <?php } ?>
        <?php if($_SESSION['user']['role'] == 'super_admin') { ?>
          <li class="nav-item"><a href="manage_users.php" class="nav-link">Manage Users</a></li>
          <li class="nav-item"><a href="add_department.php" class="nav-link">Add Department</a></li>
        <?php } ?>
        <li class="nav-item"><a href="archive.php" class="nav-link">Archive PDFs</a></li>
       <!-- <li class="nav-item"><a href="pdf_log.php" class="nav-link">View PDF Log</a></li> -->
        <li class="nav-item"><a href="view_log.php" class="nav-link">View Log</a></li>
      </ul>
      <span class="navbar-text text-white">
        Logged in as: <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
      </span>
      <a href="logout.php" class="btn btn-secondary ms-3">Logout</a>
    </div>
  </div>
</nav>
