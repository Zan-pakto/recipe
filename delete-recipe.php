<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get recipe ID
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($recipe_id > 0) {
    // First, check if the recipe exists and belongs to the user
    $stmt = $GLOBALS['pdo']->prepare("SELECT image_path FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->execute([$recipe_id, $_SESSION['user_id']]);
    $recipe = $stmt->fetch();
    
    if ($recipe) {
        // Start transaction
        $GLOBALS['pdo']->beginTransaction();
        
        try {
            // Delete reviews
            $stmt = $GLOBALS['pdo']->prepare("DELETE FROM reviews WHERE recipe_id = ?");
            $stmt->execute([$recipe_id]);
            
            // Delete likes
            $stmt = $GLOBALS['pdo']->prepare("DELETE FROM recipe_likes WHERE recipe_id = ?");
            $stmt->execute([$recipe_id]);
            
            // Delete recipe
            $stmt = $GLOBALS['pdo']->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?");
            $stmt->execute([$recipe_id, $_SESSION['user_id']]);
            
            if ($stmt->rowCount() > 0) {
                // Delete the image file if it exists
                if (!empty($recipe['image_path']) && file_exists($recipe['image_path'])) {
                    unlink($recipe['image_path']);
                }
                
                $GLOBALS['pdo']->commit();
                $_SESSION['success_message'] = "Recipe deleted successfully!";
                header("Location: my-recipes.php");
                exit();
            } else {
                throw new Exception("Failed to delete recipe");
            }
        } catch (Exception $e) {
            $GLOBALS['pdo']->rollBack();
            $_SESSION['error_message'] = "Error deleting recipe: " . $e->getMessage();
            header("Location: recipe.php?id=" . $recipe_id);
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Recipe not found or you don't have permission to delete it.";
        header("Location: my-recipes.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid recipe ID.";
    header("Location: my-recipes.php");
    exit();
}
?> 