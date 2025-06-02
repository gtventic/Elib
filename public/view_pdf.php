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
    <!-- Display the PDF file in an iframe -->
    <iframe src="../uploads/<?php echo htmlspecialchars($pdf['file_name']); ?>#toolbar=0" 
      class="pdf-viewer"
      style="overflow: hidden; overflow-x: hidden; overflow-y: hidden; height: 100%; width: 100%; position: absolute; top: 0; left: 0; right: 0; bottom: 0;"
      type="application/pdf">
    </iframe>
  </div>

  <script>
    // Disable right-click on the page
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    }, false);

    // Disable key shortcuts
    document.onkeydown = function(e) {
        // Disable F12
        if(e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        // Disable Ctrl+Shift+I
        if(e.ctrlKey && e.shiftKey && e.keyCode === 73){
            e.preventDefault();
            return false;
        }
        // Disable Ctrl+U (View Source)
        if(e.ctrlKey && e.keyCode === 85){
            e.preventDefault();
            return false;
        }
        // Disable Ctrl+S (Save)
        if(e.ctrlKey && e.keyCode === 83){
            e.preventDefault();
            return false;
        }
        // Disable Ctrl+P (Print)
        if(e.ctrlKey && e.keyCode === 80){
            e.preventDefault();
            return false;
        }
        // Optionally: disable Print Screen (PrtSc) - though not always effective
        if(e.keyCode === 44){
            e.preventDefault();
            return false;
        }
    };




  </script>
</body>
</html>
