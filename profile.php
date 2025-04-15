<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user's profile information
$stmt = $GLOBALS['pdo']->prepare("
    SELECT u.*, 
           COUNT(DISTINCT r.id) as recipe_count,
           COUNT(DISTINCT rev.id) as review_count,
           COUNT(DISTINCT rl.id) as like_count
    FROM users u
    LEFT JOIN recipes r ON u.id = r.user_id
    LEFT JOIN reviews rev ON u.id = rev.user_id
    LEFT JOIN recipe_likes rl ON u.id = rl.user_id
    WHERE u.id = ?
    GROUP BY u.id
");

$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's recent recipes
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
    LIMIT 5
");

$stmt->execute([$_SESSION['user_id']]);
$recent_recipes = $stmt->fetchAll();

// Get user's recent reviews
$stmt = $GLOBALS['pdo']->prepare("
    SELECT rev.*, r.title as recipe_title, r.image_path
    FROM reviews rev
    JOIN recipes r ON rev.recipe_id = r.id
    WHERE rev.user_id = ?
    ORDER BY rev.created_at DESC
    LIMIT 5
");

$stmt->execute([$_SESSION['user_id']]);
$recent_reviews = $stmt->fetchAll();

$page_title = "My Profile";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center space-x-6">
                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                             alt="Profile" 
                             class="w-full h-full rounded-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h1>
                    <p class="text-gray-600">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
                <a href="edit-profile.php" class="btn-secondary">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-[#ff6b00] mb-2"><?php echo $user['recipe_count']; ?></div>
                <div class="text-gray-600">Recipes</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-[#ff6b00] mb-2"><?php echo $user['review_count']; ?></div>
                <div class="text-gray-600">Reviews</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <div class="text-3xl font-bold text-[#ff6b00] mb-2"><?php echo $user['like_count']; ?></div>
                <div class="text-gray-600">Likes Received</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Recipes -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Recipes</h2>
                    <a href="my-recipes.php" class="text-[#ff6b00] hover:text-[#ff8533]">View All</a>
                </div>
                
                <?php if (empty($recent_recipes)): ?>
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-utensils text-4xl mb-2"></i>
                        <p>No recipes yet</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_recipes as $recipe): ?>
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200">
                                    <?php if (!empty($recipe['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($recipe['title']); ?>
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <span class="mr-4">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            <?php echo number_format($recipe['avg_rating'] ?? 0, 1); ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-heart text-red-500 mr-1"></i>
                                            <?php echo $recipe['like_count']; ?>
                                        </span>
                                    </div>
                                </div>
                                <a href="recipe.php?id=<?php echo $recipe['id']; ?>" 
                                   class="text-[#ff6b00] hover:text-[#ff8533]">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Reviews -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Reviews</h2>
                    <a href="my-reviews.php" class="text-[#ff6b00] hover:text-[#ff8533]">View All</a>
                </div>
                
                <?php if (empty($recent_reviews)): ?>
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-comment text-4xl mb-2"></i>
                        <p>No reviews yet</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_reviews as $review): ?>
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200">
                                    <?php if (!empty($review['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($review['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($review['recipe_title']); ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($review['recipe_title']); ?>
                                    </h3>
                                    <div class="flex items-center text-sm">
                                        <div class="text-yellow-400 mr-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-o'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-gray-500">
                                            <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <a href="recipe.php?id=<?php echo $review['recipe_id']; ?>" 
                                   class="text-[#ff6b00] hover:text-[#ff8533]">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 