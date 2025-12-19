<?php
include 'db.php';
// Example categories
$categories = ['Science', 'History', 'Tech'];
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="container">
        <h2>Select a Category</h2>
        <?php foreach($categories as $cat): ?>
            <a href="play.php?category=<?php echo $cat; ?>" class="btn"><?php echo $cat; ?></a>
        <?php endforeach; ?>
        <br>
        <a href="leaderboard.php">View Leaderboard</a>
    </div>
</body>
</html>