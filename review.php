<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "reviews_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Hostel names array
$hostel_names = [
  "d'vas pg hostel for women",
  "Navya Sri Women's Hostels",
  "Ravi Elegant Womens Pg Hostel",
  "N S Womens Hostels",
  "Siri Deluxe Women's Hostel",
  "Sri Sai Ladies Deluxe Hostel",
  "Om Sai ladies Hostel",
  "Sri Sai Ram Ladies Hostel",
  "Sri Sai Manikanta Women's Hostel",
  "Vanitha Ladies Hostel"
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $hostel = $_POST['hostel_name'];
  $review = $_POST['review'];
  $rating = $_POST['rating'];

  // Validate GRIET email
  if (!preg_match("/^[a-zA-Z0-9._%+-]+@grietcollege\\.com$/", $email)) {
    echo "<script>alert('Only GRIET emails are allowed!'); window.location.href='review.php';</script>";
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO reviews (email, hostel_name, review, rating) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("sssi", $email, $hostel, $review, $rating);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Thank you for your review!'); window.location.href='review.php';</script>";
}

// Handle filters
$hostel_filter = isset($_GET['filter_hostel']) ? $_GET['filter_hostel'] : "";
$min_rating = isset($_GET['filter_rating']) ? (int)$_GET['filter_rating'] : 0;

if (!empty($hostel_filter) && $min_rating > 0) {
  $stmt = $conn->prepare("SELECT hostel_name, review, rating FROM reviews WHERE hostel_name = ? AND rating >= ? ORDER BY id DESC");
  $stmt->bind_param("si", $hostel_filter, $min_rating);
  $stmt->execute();
  $result = $stmt->get_result();
} elseif (!empty($hostel_filter)) {
  $stmt = $conn->prepare("SELECT hostel_name, review, rating FROM reviews WHERE hostel_name = ? ORDER BY id DESC");
  $stmt->bind_param("s", $hostel_filter);
  $stmt->execute();
  $result = $stmt->get_result();
} elseif ($min_rating > 0) {
  $stmt = $conn->prepare("SELECT hostel_name, review, rating FROM reviews WHERE rating >= ? ORDER BY id DESC");
  $stmt->bind_param("i", $min_rating);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query("SELECT hostel_name, review, rating FROM reviews ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reviews - DormConnect</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <!-- Navbar -->
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex-shrink-0 flex items-center">
          <img src="dormconnect logo.png" class="h-14 w-18" />
        </div>
        <div class="flex space-x-4 items-center">
          <a href="home.html" class="text-gray-600 hover:text-gray-800">Home</a>
          <a href="about.html" class="text-gray-600 hover:text-gray-800">About</a>
          <a href="review.php" class="text-gray-600 hover:text-gray-800 font-semibold">Reviews</a>
          <a href="contact.html" class="text-gray-600 hover:text-gray-800">Contact</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Review Section -->
  <section class="py-10">
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Give Your Review ~ you will be anonymous</h2>
      <form action="review.php" method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Your GRIET Email" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">

        <select name="hostel_name" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="" disabled selected>Select the hostel you want to give review on</option>
          <?php foreach ($hostel_names as $hostel) echo "<option value=\"$hostel\">$hostel</option>"; ?>
        </select>

        <select name="rating" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
          <option value="" disabled selected>Select a rating</option>
          <option value="5">★★★★★ - Excellent</option>
          <option value="4">★★★★☆ - Good</option>
          <option value="3">★★★☆☆ - Average</option>
          <option value="2">★★☆☆☆ - Poor</option>
          <option value="1">★☆☆☆☆ - Bad</option>
        </select>

        <textarea name="review" placeholder="Write your review here..." rows="5" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>

        <div class="text-center">
          <button type="submit"
            class="bg-green-600 text-white rounded-lg px-6 py-2 hover:bg-green-700 transition">Submit Review</button>
        </div>
      </form>

      <!-- Filter Section -->
      <form method="GET" class="mt-8 mb-4">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
          <select name="filter_hostel"
            class="w-full sm:w-1/2 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Select Hostel --</option>
            <?php foreach ($hostel_names as $hostel) {
              $selected = ($hostel == $hostel_filter) ? "selected" : "";
              echo "<option value=\"$hostel\" $selected>$hostel</option>";
            } ?>
          </select>
          <div class="filter-container">
  
          <select name="rating" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
          <option value="">-- Select Rating --</option>
          <option value="5"> 5 ★ </option>
          <option value="4">4 and up ★</option>
          <option value="3">3 and up ★</option>
          <option value="2">2 and up ★</option>
          <option value="1">1 and up ★ </option>
        </select>


</div>

<script>
  function filterByReview() {
    var selectedReview = document.getElementById("review").value;
    var hostels = document.querySelectorAll(".hostel");

    hostels.forEach(function(hostel) {
      var review = parseFloat(hostel.getAttribute("data-review"));
      if (selectedReview === "all") {
        hostel.style.display = "block";
      } else if (selectedReview === "5") {
        hostel.style.display = (review === 5) ? "block" : "none";
      } else {
        hostel.style.display = (review >= parseFloat(selectedReview)) ? "block" : "none";
      }
    });
  }
</script>


          <button type="submit"
            class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition">Filter</button>
        </div>
      </form>

      <h3 class="text-xl font-semibold text-gray-700 mt-4 mb-4 text-center">All Reviews</h3>

      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $stars = str_repeat("⭐", $row['rating']) . str_repeat("☆", 5 - $row['rating']);
          echo "<div class='bg-gray-50 border-l-4 border-green-500 text-gray-700 p-4 mb-3 rounded-md shadow-sm'>
                  <div class='flex justify-between items-center mb-1'>
                    <strong class='text-green-700'>" . htmlspecialchars($row['hostel_name']) . "</strong>
                    <span class='text-yellow-500'>$stars</span>
                  </div>
                  <p>" . htmlspecialchars($row['review']) . "</p>
                </div>";
        }
      } else {
        echo "<p class='text-center text-gray-500'>No reviews found.</p>";
      }
      $conn->close();
      ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-4">
    <div class="max-w-7xl mx-auto text-center">
      <p>&copy; 2025 DormConnect. All rights reserved.</p>
    </div>
  </footer>
</body>

</html>
