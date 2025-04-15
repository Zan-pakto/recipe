<?php

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = false;

// Get available cuisines
$cuisines = [
    'Italian', 'Mexican', 'Chinese', 'Indian', 'Japanese',
    'Mediterranean', 'American', 'Thai', 'French', 'Other'
];

// Rest of your existing add-recipe.php code...
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
    
    // Validate required fields
    if (empty($title)) $errors[] = "Title is required";
    if (empty($ingredients)) $errors[] = "Ingredients are required";
    if (empty($instructions)) $errors[] = "Instructions are required";
    if ($prep_time <= 0) $errors[] = "Prep time must be positive";
    if ($cook_time <= 0) $errors[] = "Cook time must be positive";
    if ($servings <= 0) $errors[] = "Servings must be positive";
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image']);
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $user_id = $_SESSION['user_id']; // Get the actual logged-in user ID
        
        $sql = "INSERT INTO recipes (user_id, title, description, ingredients, instructions, 
                prep_time, cook_time, servings, difficulty, cuisine_type, image_path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $GLOBALS['pdo']->prepare($sql);
        $stmt->execute([
            $user_id,
            $title,
            $description,
            $ingredients,
            $instructions,
            $prep_time,
            $cook_time,
            $servings,
            $difficulty,
            $cuisine_type,
            $image_path
        ]);
        
        if ($stmt->rowCount() > 0) {
            $success = true;
            // Redirect to the newly created recipe
            $recipe_id = $GLOBALS['pdo']->lastInsertId();
            header("Location: recipe.php?id=" . $recipe_id);
            exit();
        } else {
            $errors[] = "Error saving recipe";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Recipe | <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold">Add New Recipe</h1>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-6 mt-4" role="alert">
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mx-6 mt-4" role="alert">
                    <p>Recipe added successfully!</p>
                </div>
            <?php endif; ?>
            
            <form action="add-recipe.php" method="post" enctype="multipart/form-data" class="p-6 space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipe Title*</label>
                        <input type="text" name="title" required 
                               class="w-full p-2 border rounded" value="<?php echo $_POST['title'] ?? ''; ?>">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine Type*</label>
                        <select name="cuisine_type" class="form-select" required>
                        <option value="">All Cuisines 🌎</option>
                        <?php foreach ($cuisines as $cuisine): ?>
                            <option value="<?php echo $cuisine; ?>">
                                <?php echo $cuisine; ?> <?php echo getCuisineEmoji($cuisine); ?>
                            </option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full p-2 border rounded"><?php echo $_POST['description'] ?? ''; ?></textarea>
                </div>
                
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipe Image</label>
                    <div class="mt-1 flex items-center">
                        <input type="file" name="image" accept="image/*" class="p-2 border rounded">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Max size: 2MB (JPEG, PNG, GIF)</p>
                </div>
                
                <!-- Times and Servings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prep Time (mins)*</label>
                        <input type="number" name="prep_time" min="1" required 
                               class="w-full p-2 border rounded" value="<?php echo $_POST['prep_time'] ?? ''; ?>">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cook Time (mins)*</label>
                        <input type="number" name="cook_time" min="1" required 
                               class="w-full p-2 border rounded" value="<?php echo $_POST['cook_time'] ?? ''; ?>">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Servings*</label>
                        <input type="number" name="servings" min="1" required 
                               class="w-full p-2 border rounded" value="<?php echo $_POST['servings'] ?? ''; ?>">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty*</label>
                    <select name="difficulty" required class="w-full p-2 border rounded">
                        <option value="">Select difficulty</option>
                        <option value="Easy" <?php echo ($_POST['difficulty'] ?? '') === 'Easy' ? 'selected' : ''; ?>>Easy</option>
                        <option value="Medium" <?php echo ($_POST['difficulty'] ?? '') === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Hard" <?php echo ($_POST['difficulty'] ?? '') === 'Hard' ? 'selected' : ''; ?>>Hard</option>
                    </select>
                </div>
                
                <!-- Ingredients -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ingredients*</label>
                    <p class="text-sm text-gray-500 mb-2">Enter one ingredient per line</p>
                    <textarea name="ingredients" rows="6" required class="w-full p-2 border rounded font-mono"><?php echo $_POST['ingredients'] ?? ''; ?></textarea>
                </div>
                
                <!-- Instructions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instructions*</label>
                    <p class="text-sm text-gray-500 mb-2">Enter one step per line</p>
                    <textarea name="instructions" rows="8" required class="w-full p-2 border rounded"><?php echo $_POST['instructions'] ?? ''; ?></textarea>
                </div>
                
                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="index.php" class="btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Add Recipe
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>