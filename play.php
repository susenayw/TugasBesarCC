<?php
session_start();
if(!isset($_SESSION['user'])) header("Location: login.php");

$category = $_GET['category'] ?? 'General';

// Sample 10 questions for one category (Repeat structure for others)
$questions = [
    ["q" => "What is 5 + 5?", "options" => ["8", "10", "12"], "ans" => "10"],
    ["q" => "Capital of France?", "options" => ["Berlin", "London", "Paris"], "ans" => "Paris"],
    // ... add 8 more questions here
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = 0;
    foreach($questions as $index => $q) {
        if($_POST['q'.$index] == $q['ans']) $score++;
    }
    // Save to Leaderboard
    include 'db.php';
    $user = $_SESSION['user'];
    mysqli_query($conn, "INSERT INTO leaderboard (username, score, category) VALUES ('$user', '$score', '$category')");
    echo "<script>alert('Your score: $score'); window.location='quiz.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <h2>Category: <?php echo $category; ?></h2>
        <form method="POST">
            <?php foreach($questions as $i => $q): ?>
                <p><?php echo ($i+1) . ". " . $q['q']; ?></p>
                <?php foreach($q['options'] as $opt): ?>
                    <input type="radio" name="q<?php echo $i; ?>" value="<?php echo $opt; ?>" required> <?php echo $opt; ?><br>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <button type="submit" class="btn">Submit Quiz</button>
        </form>
    </div>
</body>
</html>