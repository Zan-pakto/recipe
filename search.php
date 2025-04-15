<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Get search parameters
$search_query = isset($_GET['query']) ? sanitizeInput($_GET['query']) : '';
$cuisine_type = isset($_GET['cuisine']) ? sanitizeInput($_GET['cuisine']) : 'All Cuisines';
$difficulty = isset($_GET['difficulty']) ? sanitizeInput($_GET['difficulty']) : 'Any Level';

// Base query
$sql = "SELECT r.*, u.username 
        FROM recipes r 
        JOIN users u ON r.user_id = u.id 
        WHERE 1=1";
$params = [];

// Add search conditions
if (!empty($search_query)) {
    $sql .= " AND (r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($cuisine_type !== 'All Cuisines') {
    $sql .= " AND r.cuisine_type = ?";
    $params[] = $cuisine_type;
}

if ($difficulty !== 'Any Level') {
    $sql .= " AND r.difficulty = ?";
    $params[] = $difficulty;
}

// Add order by
$sql .= " ORDER BY r.created_at DESC";

try {
    // Prepare and execute query
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Search query error: " . $e->getMessage());
    $results = [];
}

// Start HTML output
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Search Results</h1>
        
        <?php if (empty($results)): ?>
            <div class="text-center py-8">
                <div class="text-6xl mb-4">🔍</div>
                <p class="text-gray-600">No recipes found matching your search criteria.</p>
                <a href="index.php" class="inline-block mt-4 text-orange-500 hover:text-orange-600">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($results as $recipe): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="block">
                            <?php if (!empty($recipe['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                     class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-orange-100 flex items-center justify-center">
                                    <i class="fas fa-utensils text-4xl text-orange-300"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-4">
                                <h2 class="text-xl font-semibold mb-2 text-gray-800 hover:text-orange-500 transition">
                                    <?php echo htmlspecialchars($recipe['title']); ?>
                                </h2>
                                <p class="text-gray-600 mb-2">
                                    <?php echo substr(htmlspecialchars($recipe['description']), 0, 100) . '...'; ?>
                                </p>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">
                                        <i class="fas fa-user mr-1"></i>
                                        <?php echo htmlspecialchars($recipe['username']); ?>
                                    </span>
                                    <span class="text-orange-500">
                                        <?php echo getCuisineEmoji($recipe['cuisine_type']); ?>
                                        <?php echo htmlspecialchars($recipe['cuisine_type']); ?>
                                    </span>
                                    <span class="text-gray-500">
                                        <?php echo getDifficultyEmoji($recipe['difficulty']); ?>
                                        <?php echo htmlspecialchars($recipe['difficulty']); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>