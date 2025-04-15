<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user's recipes with stats
$stmt = $GLOBALS['pdo']->prepare("
    SELECT r.*, 
           COUNT(DISTINCT rev.id) as review_count,
           AVG(rev.rating) as avg_rating,
           COUNT(DISTINCT rl.id) as like_count
    FROM recipes r
    LEFT JOIN reviews rev ON r.id = rev.recipe_id
    LEFT JOIN recipe_likes rl ON r.id = rl.recipe_id
    WHERE r.user_id = ?
    GROUP BY r.id
    ORDER BY r.created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);
$recipes = $stmt->fetchAll();

$page_title = "My Recipes";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Recipes</h1>
            <a href="add-recipe.php" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Add New Recipe
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert-success mb-6">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert-error mb-6">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <!-- Recipes Grid -->
        <?php if (empty($recipes)): ?>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-gray-500 mb-4">
                    <i class="fas fa-utensils text-4xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">No Recipes Yet</h2>
                <p class="text-gray-600 mb-4">Start sharing your culinary creations with the world!</p>
                <a href="add-recipe.php" class="btn-primary inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add Your First Recipe
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                        <!-- Recipe Image -->
                        <div class="relative h-48">
                            <?php if (!empty($recipe['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-utensils text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Recipe Stats Overlay -->
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 flex justify-around text-sm">
                                <span title="Reviews">
                                    <i class="fas fa-comment mr-1"></i><?php echo $recipe['review_count']; ?>
                                </span>
                                <span title="Average Rating">
                                    <i class="fas fa-star mr-1"></i>
                                    <?php echo number_format($recipe['avg_rating'] ?? 0, 1); ?>
                                </span>
                                <span title="Likes">
                                    <i class="fas fa-heart mr-1"></i><?php echo $recipe['like_count']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Recipe Content -->
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                <?php echo htmlspecialchars($recipe['title']); ?>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4">
                                <?php echo substr(htmlspecialchars($recipe['description']), 0, 100) . '...'; ?>
                            </p>
                            
                            <!-- Recipe Meta -->
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <span class="mr-4">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins
                                </span>
                                <span>
                                    <i class="fas fa-utensils mr-1"></i>
                                    <?php echo htmlspecialchars($recipe['difficulty']); ?>
                                </span>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-between items-center">
                                <a href="recipe.php?id=<?php echo $recipe['id']; ?>" 
                                   class="text-[#ff6b00] hover:text-[#ff8533]">
                                    View Recipe
                                </a>
                                <div class="flex space-x-2">
                                    <a href="edit-recipe.php?id=<?php echo $recipe['id']; ?>" 
                                       class="btn-secondary py-1 px-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $recipe['id']; ?>)" 
                                            class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
function confirmDelete(recipeId) {
    if (confirm('Are you sure you want to delete this recipe? This action cannot be undone.')) {
        window.location.href = 'delete-recipe.php?id=' + recipeId;
    }
}
</script> 