<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'You must be logged in to submit a review';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid CSRF token';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Get and validate input
$recipe_id = isset($_POST['recipe_id']) ? intval($_POST['recipe_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($recipe_id <= 0 || $rating < 1 || $rating > 5) {
    $_SESSION['error'] = 'Invalid input parameters';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

try {
    // Check if user has already reviewed this recipe
    $stmt = $GLOBALS['pdo']->prepare("SELECT id FROM reviews WHERE recipe_id = ? AND user_id = ?");
    $stmt->execute([$recipe_id, $_SESSION['user_id']]);
    $existing_review = $stmt->fetch();

    if ($existing_review) {
        // Update existing review
        $stmt = $GLOBALS['pdo']->prepare("UPDATE reviews SET rating = ?, comment = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$rating, $comment, $existing_review['id']]);
        $_SESSION['success'] = 'Your review has been updated successfully!';
    } else {
        // Insert new review
        $stmt = $GLOBALS['pdo']->prepare("INSERT INTO reviews (recipe_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$recipe_id, $_SESSION['user_id'], $rating, $comment]);
        $_SESSION['success'] = 'Thank you for your review!';
    }

    // Update recipe average rating
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE recipes 
        SET avg_rating = (
            SELECT AVG(rating) 
            FROM reviews 
            WHERE recipe_id = ?
        )
        WHERE id = ?
    ");
    $stmt->execute([$recipe_id, $recipe_id]);

    // Redirect back to the recipe page
    header('Location: recipe.php?id=' . $recipe_id . '#reviews');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'Error submitting review: ' . $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>