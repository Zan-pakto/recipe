<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Get filter parameters
$cuisine = $_GET['cuisine'] ?? '';
$difficulty = $_GET['difficulty'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build the query
$query = "
    SELECT r.*, 
           u.username,
           COUNT(DISTINCT rev.id) as review_count,
           AVG(rev.rating) as avg_rating,
           COUNT(DISTINCT rl.id) as like_count
    FROM recipes r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN reviews rev ON r.id = rev.recipe_id
    LEFT JOIN recipe_likes rl ON r.id = rl.recipe_id
    WHERE 1=1
";

$params = [];

if ($cuisine) {
    $query .= " AND r.cuisine_type = ?";
    $params[] = $cuisine;
}

if ($difficulty) {
    $query .= " AND r.difficulty = ?";
    $params[] = $difficulty;
}

$query .= " GROUP BY r.id";

// Add sorting
switch ($sort) {
    case 'rating':
        $query .= " ORDER BY avg_rating DESC, review_count DESC";
        break;
    case 'popular':
        $query .= " ORDER BY like_count DESC, review_count DESC";
        break;
    default: // newest
        $query .= " ORDER BY r.created_at DESC";
}

$stmt = $GLOBALS['pdo']->prepare($query);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

// Get available cuisines for filter
$stmt = $GLOBALS['pdo']->query("SELECT DISTINCT cuisine_type FROM recipes ORDER BY cuisine_type");
$cuisines = $stmt->fetchAll(PDO::FETCH_COLUMN);

$page_title = "All Recipes";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full md:w-1/4">
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-filter text-[#ff6b00] mr-2"></i>Filter Recipes
                    </h2>
                    
                    <form method="get" class="space-y-4">
                        <!-- Cuisine Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine Type</label>
                            <select name="cuisine" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                <option value="">All Cuisines</option>
                                <?php foreach ($cuisines as $c): ?>
                                    <option value="<?php echo $c; ?>" <?php echo $cuisine === $c ? 'selected' : ''; ?>>
                                        <?php echo $c; ?> <?php echo getCuisineEmoji($c); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Difficulty Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                            <select name="difficulty" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                <option value="">All Difficulties</option>
                                <option value="Easy" <?php echo $difficulty === 'Easy' ? 'selected' : ''; ?>>Easy 😊</option>
                                <option value="Medium" <?php echo $difficulty === 'Medium' ? 'selected' : ''; ?>>Medium 😅</option>
                                <option value="Hard" <?php echo $difficulty === 'Hard' ? 'selected' : ''; ?>>Hard 🤔</option>
                            </select>
                        </div>
                        
                        <!-- Sort Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full btn-primary">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </form>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="w-full md:w-3/4">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php if ($cuisine): ?>
                            <?php echo getCuisineEmoji($cuisine); ?> <?php echo htmlspecialchars($cuisine); ?> Recipes
                        <?php else: ?>
                            All Recipes
                        <?php endif; ?>
                    </h1>
                    <span class="text-gray-600">
                        <?php echo count($recipes); ?> recipes found
                    </span>
                </div>
                
                <?php if (empty($recipes)): ?>
                    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                        <div class="text-6xl mb-4">🤷‍♂️</div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">No Recipes Found</h2>
                        <p class="text-gray-600 mb-4">Try adjusting your filters or browse all recipes.</p>
                        <a href="recipes.php" class="btn-primary">
                            <i class="fas fa-utensils mr-2"></i>View All Recipes
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($recipes as $recipe): ?>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                                <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="block">
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
                                    
                                    <div class="p-4">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                            <?php echo htmlspecialchars($recipe['title']); ?>
                                        </h3>
                                        <p class="text-gray-600 text-sm mb-4">
                                            <?php echo substr(htmlspecialchars($recipe['description']), 0, 100) . '...'; ?>
                                        </p>
                                        
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins
                                            </span>
                                            <span>
                                                <i class="fas fa-user mr-1"></i>
                                                <?php echo htmlspecialchars($recipe['username']); ?>
                                            </span>
                                        </div>
                                    </div>
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