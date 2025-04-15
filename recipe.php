<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$recipe_id = intval($_GET['id']);

// Get recipe details
$stmt = $GLOBALS['pdo']->prepare("SELECT r.*, u.username 
    FROM recipes r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header("Location: index.php");
    exit();
}

// Get reviews
$stmt = $GLOBALS['pdo']->prepare("SELECT r.*, u.username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.recipe_id = ? 
    ORDER BY r.created_at DESC");
$stmt->execute([$recipe_id]);
$reviews = $stmt->fetchAll();

// Check if user has liked this recipe
$has_liked = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) FROM recipe_likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    $has_liked = $stmt->fetchColumn() > 0;
}

// Calculate average rating
$avg_rating = 0;
if (count($reviews) > 0) {
    $total = array_sum(array_column($reviews, 'rating'));
    $avg_rating = $total / count($reviews);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> | <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .rating-star {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .rating-star:hover {
            transform: scale(1.2);
        }
        .rating-star.selected {
            color: #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Recipe Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="relative h-96">
                    <?php if ($recipe['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-utensils text-6xl text-orange-300"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6">
                        <h1 class="text-4xl font-bold text-white mb-2">
                            <?php echo htmlspecialchars($recipe['title']); ?>
                        </h1>
                        <div class="flex items-center text-white/90">
                            <i class="fas fa-user mr-2"></i>
                            <span>By <?php echo htmlspecialchars($recipe['username']); ?></span>
                            <span class="mx-2">•</span>
                            <i class="fas fa-clock mr-2"></i>
                            <span><?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                            <span class="mx-2">•</span>
                            <i class="fas fa-signal mr-2"></i>
                            <span><?php echo htmlspecialchars($recipe['difficulty']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="ml-2">
                                    <?php echo number_format($avg_rating, 1); ?> (<?php echo count($reviews); ?> reviews)
                                </span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-500 mr-1"></i>
                                <span class="ml-2">Prep: <?php echo $recipe['prep_time']; ?> mins</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-fire text-orange-500 mr-1"></i>
                                <span class="ml-2">Cook: <?php echo $recipe['cook_time']; ?> mins</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-500 mr-1"></i>
                                <span class="ml-2">Serves: <?php echo $recipe['servings']; ?></span>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['user_id']): ?>
                            <div class="flex space-x-2">
                                <a href="edit-recipe.php?id=<?php echo $recipe_id; ?>" 
                                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                                <a href="delete-recipe.php?id=<?php echo $recipe_id; ?>" 
                                   class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition"
                                   onclick="return confirm('Are you sure you want to delete this recipe?')">
                                    <i class="fas fa-trash mr-2"></i> Delete
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="prose max-w-none">
                        <p class="text-gray-700 mb-6"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
                        
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">
                            <i class="fas fa-list text-orange-500 mr-2"></i> Ingredients
                        </h2>
                        <div class="bg-orange-50 p-6 rounded-lg mb-8">
                            <?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?>
                        </div>
                        
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">
                            <i class="fas fa-utensils text-orange-500 mr-2"></i> Instructions
                        </h2>
                        <div class="bg-orange-50 p-6 rounded-lg">
                            <?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Section -->
            <div class="p-6 border-t bg-orange-50" id="reviews">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">
                    <i class="fas fa-comments text-orange-500 mr-2"></i> Reviews
                </h2>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <p><?php echo $_SESSION['success']; ?></p>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                            <p><?php echo $_SESSION['error']; ?></p>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">🤔</div>
                        <p class="text-gray-500">No reviews yet. Be the first to share your thoughts!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($reviews as $review): ?>
                            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white mr-3">
                                                <?php echo strtoupper(substr($review['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-800"><?php echo htmlspecialchars($review['username']); ?></span>
                                                <div class="text-sm text-gray-500">
                                                    <?php 
                                                    $date = new DateTime($review['created_at']);
                                                    echo $date->format('M j, Y');
                                                    if ($review['updated_at'] !== $review['created_at']) {
                                                        echo ' (edited)';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex text-yellow-400">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo ($i <= $review['rating']) ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add Review Form -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">
                        <i class="fas fa-star text-orange-500 mr-2"></i> Add Your Review
                    </h3>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="submit_review.php" method="post" class="bg-white p-6 rounded-lg shadow-sm">
                            <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating*</label>
                                <div class="flex space-x-4">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <label class="cursor-pointer transform hover:scale-110 transition">
                                            <input type="radio" name="rating" value="<?= $i ?>" class="hidden" required>
                                            <div class="text-3xl rating-star" data-rating="<?= $i ?>">⭐</div>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Review*</label>
                                <textarea name="comment" rows="4" required 
                                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Share your cooking experience..."></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg hover:bg-orange-600 transition transform hover:scale-105">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Review
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
                            <p>Please <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI'])?>" 
                                class="text-orange-500 hover:underline font-medium">login</a> to submit a review.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Rating star selection
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                document.querySelector(`input[value="${rating}"]`).checked = true;
                
                // Update star colors
                document.querySelectorAll('.rating-star').forEach(s => {
                    s.classList.toggle('selected', s.dataset.rating <= rating);
                });
            });
        });

        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.bg-green-50, .bg-red-50');
            messages.forEach(msg => {
                msg.style.opacity = '0';
                msg.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => msg.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>