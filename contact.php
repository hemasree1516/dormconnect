<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact"; // Ensure this DB exists

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $review = $_POST['review'];
    $rating = $_POST['rating'];

    // Prepare SQL
    $sql = "INSERT INTO contact_messages (name, email, review, rating) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssi", $name, $email, $review, $rating);
        if ($stmt->execute()) {
            echo "<script>alert('Thank you for contacting us!'); window.location.href='contact.html';</script>";
        } else {
            echo "Execute failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error;
    }
}

$conn->close();
?>
