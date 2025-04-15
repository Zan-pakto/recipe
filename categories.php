<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Get all recipes grouped by cuisine type
$stmt = $GLOBALS['pdo']->prepare("
    SELECT r.cuisine_type, 
           COUNT(*) as recipe_count,
           AVG(rev.rating) as avg_rating
    FROM recipes r
    LEFT JOIN reviews rev ON r.id = rev.recipe_id
    GROUP BY r.cuisine_type
    ORDER BY recipe_count DESC
");

$stmt->execute();
$categories = $stmt->fetchAll();

$page_title = "Recipe Categories";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
            <i class="fas fa-th-large text-[#ff6b00] mr-2"></i>Recipe Categories
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($categories as $category): ?>
                <a href="recipes.php?cuisine=<?php echo urlencode($category['cuisine_type']); ?>" 
                   class="group">
                    <div class="bg-white rounded-xl shadow-lg p-6 text-center transform group-hover:scale-105 transition duration-300">
                        <div class="text-5xl mb-4">
                            <?php echo getCuisineEmoji($category['cuisine_type']); ?>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 group-hover:text-[#ff6b00] mb-2">
                            <?php echo htmlspecialchars($category['cuisine_type']); ?>
                        </h3>
                        <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
                            <span>
                                <i class="fas fa-utensils mr-1"></i>
                                <?php echo $category['recipe_count']; ?> recipes
                            </span>
                            <?php if ($category['avg_rating']): ?>
                                <span>
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <?php echo number_format($category['avg_rating'], 1); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 