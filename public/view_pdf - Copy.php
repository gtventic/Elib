<?php
// public/view_pdf.php
require_once '../db.php';

// Ensure the user is logged in
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user'){
  header("Location: login.php");
  exit;
}

// Validate PDF id
if (!isset($_GET['id'])) {
    die("Invalid request.");
}
$pdf_id = intval($_GET['id']);

// Fetch PDF details
$stmt = $conn->prepare("SELECT * FROM pdf_files WHERE id = :id AND archived = 0");
$stmt->bindParam(':id', $pdf_id);
$stmt->execute();
$pdf = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pdf) {
    die("PDF not found.");
}

// Log the PDF view for audit purposes (user_id from session)
$stmt = $conn->prepare("INSERT INTO pdf_log (user_id, pdf_file_id, view_time) VALUES (:user_id, :pdf_id, NOW())");
$stmt->bindParam(':user_id', $_SESSION['user']['id']);
$stmt->bindParam(':pdf_id', $pdf_id);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View PDF - <?php echo htmlspecialchars($pdf['file_name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
    .pdf-viewer {
      height: 90vh;
      width: 100%;
    }
    /* Hides content during printing */
    @media print {
      body {
        display: none;
      }
    }
  </style>
</head>
<body oncontextmenu="return false;">
  <div class="container mt-4">
    <!-- <h2><?php echo htmlspecialchars($pdf['file_name']); ?></h2> -->
    <!-- The parameter "#toolbar=0" removes the default PDF toolbar in some browsers -->
    <!-- <iframe src="../<?php echo $pdf['file_path']; ?>#toolbar=0" width="100%" height="600px" style="border: none;"></iframe> -->
 <iframe src="../uploads/<?php echo htmlspecialchars($pdf['file_name']); ?>#toolbar=0" 
      style="overflow: hidden; overflow-x: hidden; overflow-y: hidden; height: 100%; width: 100%; position: absolute; top: 0; left: 0; right: 0; bottom: 0;"
      width="100%" height="100%" type='application/pdf'>
    </iframe>
  </div>

  <script>
    // Disable right-click
    document.addEventListener('contextmenu', event => event.preventDefault());
    // Disable key shortcuts for printing and saving
    document.onkeydown = function(e) {
        if (e.ctrlKey && (e.key === 'p' || e.key === 'P' || e.key === 's' || e.key === 'S') || e.key === 'F12') {
            e.preventDefault();
            return false;
        }
    };
  </script>
</body>
</html>
