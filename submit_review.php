<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "reviews_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$review = $_POST['review'];

$sql = "INSERT INTO reviews (review) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $review);

$message = "";

if ($stmt->execute()) {
  $message = "Thank you for your review!";
} else {
  $message = "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>

<!-- Output the HTML + footer -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review Submitted</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 40px;
      text-align: center;
    }
  </style>
</head>
<body>

  <h2><?php echo $message; ?></h2>

  <footer class="bg-gray-800 text-white py-4" style="position: fixed; bottom: 0; width: 100%;">
    <div class="max-w-7xl mx-auto text-center">
        <p>&copy; 2025 DormConnect. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>
