<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get recipe ID from URL
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get available cuisines
$cuisines = [
    'Italian' => '🍝',
    'Mexican' => '🌮',
    'Indian' => '🍛',
    'Chinese' => '🥢',
    'American' => '🍔',
    'Japanese' => '🍱',
    'Mediterranean' => '🫒',
    'Other' => '🍽️'
];

// Fetch existing recipe data
$stmt = $GLOBALS['pdo']->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
$stmt->execute([$recipe_id, $_SESSION['user_id']]);
$recipe = $stmt->fetch();

if (!$recipe) {
    $_SESSION['error_message'] = "Recipe not found or you don't have permission to edit it.";
    header("Location: my-recipes.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $ingredients = sanitizeInput($_POST['ingredients']);
    $instructions = sanitizeInput($_POST['instructions']);
    $prep_time = intval($_POST['prep_time']);
    $cook_time = intval($_POST['cook_time']);
    $servings = intval($_POST['servings']);
    $difficulty = sanitizeInput($_POST['difficulty']);
    $cuisine_type = sanitizeInput($_POST['cuisine_type']);
    
    $errors = [];
    
    // Validate required fields
    if (empty($title)) $errors[] = "Title is required";
    if (empty($ingredients)) $errors[] = "Ingredients are required";
    if (empty($instructions)) $errors[] = "Instructions are required";
    if ($prep_time <= 0) $errors[] = "Prep time must be positive";
    if ($cook_time <= 0) $errors[] = "Cook time must be positive";
    if ($servings <= 0) $errors[] = "Servings must be positive";
    
    // Handle image upload if a new image was provided
    $image_path = $recipe['image_path']; // Default to existing image path
    if (!empty($_FILES['image']['name'])) {
        // Delete the old image if it exists
        if (!empty($recipe['image_path']) && file_exists($recipe['image_path'])) {
            unlink($recipe['image_path']);
        }
        
        // Upload the new image
        $upload_result = uploadImage($_FILES['image']);
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        } else {
            $errors[] = "Error uploading image: " . $upload_result['message'];
        }
    }
    
    // If no errors, update the recipe
    if (empty($errors)) {
        try {
            $stmt = $GLOBALS['pdo']->prepare("
                UPDATE recipes 
                SET title = ?,
                    description = ?,
                    ingredients = ?,
                    instructions = ?,
                    prep_time = ?,
                    cook_time = ?,
                    servings = ?,
                    difficulty = ?,
                    cuisine_type = ?,
                    image_path = ?
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([
                $title,
                $description,
                $ingredients,
                $instructions,
                $prep_time,
                $cook_time,
                $servings,
                $difficulty,
                $cuisine_type,
                $image_path,
                $recipe_id,
                $_SESSION['user_id']
            ]);
            
            $_SESSION['success_message'] = "Recipe updated successfully!";
            header("Location: recipe.php?id=" . $recipe_id);
            exit();
            
        } catch (Exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
            error_log("Recipe update error: " . $e->getMessage());
        }
    }
}

// Function to check if a cuisine is selected
function isCuisineSelected($cuisine) {
    global $recipe;
    return $recipe['cuisine_type'] === $cuisine ? 'selected' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-[#ff6b00]">Edit Recipe</h1>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert-error mx-6 mt-4">
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="edit-recipe.php?id=<?php echo $recipe_id; ?>" method="post" enctype="multipart/form-data" class="p-6 space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipe Title*</label>
                        <input type="text" name="title" required class="form-input"
                               value="<?php echo htmlspecialchars($recipe['title']); ?>">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine Type*</label>
                        <select name="cuisine_type" required class="form-select">
                            <?php foreach ($cuisines as $cuisine => $emoji): ?>
                                <option value="<?php echo $cuisine; ?>" <?php echo isCuisineSelected($cuisine); ?>>
                                    <?php echo $emoji . ' ' . $cuisine; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="form-textarea"><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                </div>
                
                <!-- Recipe Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prep Time (minutes)*</label>
                        <input type="number" name="prep_time" required min="1" class="form-input"
                               value="<?php echo $recipe['prep_time']; ?>">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cook Time (minutes)*</label>
                        <input type="number" name="cook_time" required min="1" class="form-input"
                               value="<?php echo $recipe['cook_time']; ?>">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Servings*</label>
                        <input type="number" name="servings" required min="1" class="form-input"
                               value="<?php echo $recipe['servings']; ?>">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level*</label>
                    <select name="difficulty" required class="form-select">
                        <option value="Easy" <?php echo isCuisineSelected('Easy'); ?>>Easy</option>
                        <option value="Medium" <?php echo isCuisineSelected('Medium'); ?>>Medium</option>
                        <option value="Hard" <?php echo isCuisineSelected('Hard'); ?>>Hard</option>
                    </select>
                </div>
                
                <!-- Recipe Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ingredients*</label>
                    <textarea name="ingredients" rows="6" required class="form-textarea"
                              placeholder="Enter each ingredient on a new line"><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructions*</label>
                    <textarea name="instructions" rows="8" required class="form-textarea"
                              placeholder="Enter each step on a new line"><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
                </div>
                
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipe Image</label>
                    <?php if ($recipe['image_path']): ?>
                        <div class="mb-2">
                            <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                                 alt="Current recipe image" 
                                 class="w-32 h-32 object-cover rounded-lg">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" class="form-input">
                    <p class="text-sm text-gray-500 mt-1">Leave empty to keep the current image</p>
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="recipe.php?id=<?php echo $recipe_id; ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-[#ff6b00] text-white rounded-lg hover:bg-[#e66000]">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>