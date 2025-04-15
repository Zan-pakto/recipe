<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$sort = $_GET['sort'] ?? 'newest';
$filter = [
    'cuisine' => $_GET['cuisine'] ?? '',
    'difficulty' => $_GET['difficulty'] ?? '',
    'ingredient' => $_GET['ingredient'] ?? ''
];

$recipes = getRecipes($sort, $filter);
$cuisines = ['Italian', 'Mexican', 'Indian', 'Chinese', 'American', 'Japanese', 'Mediterranean', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Share Your Recipes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        .category-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #f97316;
            color: white;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 to-orange-100 min-h-screen">
    <?php include 'includes/header.php'; ?>
    
    <!-- Navigation Menu -->
    <nav class="bg-white shadow-md mb-8">
        <div class="container mx-auto px-4">
            <ul class="flex space-x-6 overflow-x-auto py-4">
                <li>
                    <a href="#categories" class="text-gray-600 hover:text-orange-500 font-medium transition flex items-center">
                        <i class="fas fa-th-large mr-2"></i> Categories
                    </a>
                </li>
                <li>
                    <a href="#about" class="text-gray-600 hover:text-orange-500 font-medium transition flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> About
                    </a>
                </li>
                <li>
                    <a href="#recipes" class="text-gray-600 hover:text-orange-500 font-medium transition flex items-center">
                        <i class="fas fa-utensils mr-2"></i> Recipes
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-orange-400 to-red-500 text-white overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
            <div class="absolute left-1/3 top-3/4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute right-1/4 top-1/2 w-32 h-32 bg-white opacity-10 rounded-full"></div>
            <div class="absolute -left-10 top-1/2 w-36 h-36 bg-white opacity-10 rounded-full"></div>
        </div>

        <!-- Main Content -->
        <div class="relative">
            <!-- Top Wave -->
            <div class="absolute top-0 left-0 w-full">
                <svg class="w-full h-12 text-orange-400 opacity-10" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
                </svg>
            </div>

            <div class="container mx-auto px-4 py-20">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <!-- Left Column -->
                    <div class="text-center md:text-left space-y-6">
                        <h1 class="text-4xl md:text-6xl font-bold mb-4 animate__animated animate__fadeIn">
                            Welcome to <span class="text-yellow-300">Recipe</span><span class="text-white">Share</span>
                        </h1>
                        <p class="text-xl mb-8 animate__animated animate__fadeIn animate__delay-1s opacity-90">
                            Where every recipe has a story, and every meal brings joy! 🌟
                        </p>
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="register.php" class="bg-white text-orange-500 py-3 px-8 rounded-full font-bold text-lg hover:bg-orange-100 transition transform hover:scale-105 inline-flex items-center animate__animated animate__bounceIn animate__delay-2s">
                                    <span>Join Now</span>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                <a href="#recipes" class="bg-transparent border-2 border-white text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-white hover:text-orange-500 transition transform hover:scale-105 inline-flex items-center animate__animated animate__bounceIn animate__delay-2s">
                                    <span>Explore Recipes</span>
                                    <i class="fas fa-utensils ml-2"></i>
                                </a>
                            <?php else: ?>
                                <a href="add-recipe.php" class="bg-white text-orange-500 py-3 px-8 rounded-full font-bold text-lg hover:bg-orange-100 transition transform hover:scale-105 inline-flex items-center animate__animated animate__bounceIn">
                                    <span>Share Recipe</span>
                                    <i class="fas fa-plus ml-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 mt-12">
                            <?php
                            $total_recipes = count($recipes);
                            $total_users = 100; // You can replace this with actual count from database
                            $total_reviews = 250; // You can replace this with actual count from database
                            ?>
                            <div class="text-center">
                                <div class="text-3xl font-bold animate__animated animate__fadeInUp"><?php echo $total_recipes; ?>+</div>
                                <div class="text-sm opacity-75">Recipes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold animate__animated animate__fadeInUp animate__delay-1s"><?php echo $total_users; ?>+</div>
                                <div class="text-sm opacity-75">Chefs</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold animate__animated animate__fadeInUp animate__delay-2s"><?php echo $total_reviews; ?>+</div>
                                <div class="text-sm opacity-75">Reviews</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Featured Image -->
                    <div class="hidden md:block relative animate__animated animate__fadeInRight">
                        <div class="relative">
                            <!-- Decorative circles -->
                            <div class="absolute -top-4 -right-4 w-24 h-24 bg-yellow-300 rounded-full opacity-50"></div>
                            <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-orange-300 rounded-full opacity-50"></div>
                            
                            <!-- Main image container -->
                            <div class="relative bg-white p-4 rounded-2xl shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                                <img src="https://images.unsplash.com/photo-1499028344343-cd173ffc68a9?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80" 
                                     alt="Delicious Food" 
                                     class="rounded-xl w-full h-[400px] object-cover"
                                     onerror="this.src='images/default-recipe.jpg'">
                                
                                <!-- Floating recipe card -->
                                <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-xl shadow-lg transform hover:scale-105 transition-transform">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-star text-orange-500 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800">Top Rated</div>
                                            <div class="text-sm text-gray-500">Community Favorites</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Wave -->
            <div class="absolute bottom-0 left-0 w-full">
                <svg class="w-full h-12 text-orange-50" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="currentColor"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="container mx-auto px-4 -mt-8 mb-12 relative z-10">
        <div class="bg-white rounded-2xl shadow-xl p-6 animate__animated animate__fadeInUp">
            <form action="search.php" method="get" class="flex flex-wrap md:flex-nowrap gap-4">
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">What would you like to cook?</label>
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search recipes..." 
                               class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine Type</label>
                    <select name="cuisine" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Cuisines 🌎</option>
                        <?php foreach ($cuisines as $cuisine): ?>
                            <option value="<?php echo $cuisine; ?>">
                                <?php echo $cuisine; ?> <?php echo getCuisineEmoji($cuisine); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                    <select name="difficulty" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Any Level 📊</option>
                        <option value="Easy">Easy 😊</option>
                        <option value="Medium">Medium 😅</option>
                        <option value="Hard">Hard 🤔</option>
                    </select>
                </div>
                <div class="w-full md:w-auto md:self-end">
                    <button type="submit" class="w-full md:w-auto bg-orange-500 text-white py-2 px-8 rounded-lg hover:bg-orange-600 transition transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Showcase -->
    <div id="categories" class="container mx-auto px-4 mb-12 scroll-mt-16">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">
            <i class="fas fa-th-large text-orange-500 mr-2"></i> Popular Categories
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            $cuisine_counts = [];
            foreach ($recipes as $recipe) {
                $cuisine = $recipe['cuisine_type'];
                $cuisine_counts[$cuisine] = ($cuisine_counts[$cuisine] ?? 0) + 1;
            }
            ?>
            <a href="index.php?cuisine=Italian" class="group">
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-6 text-center transform group-hover:scale-105 relative">
                    <div class="text-5xl mb-4 animate__animated animate__bounce">🍝</div>
                    <h3 class="font-bold text-gray-800 group-hover:text-orange-500">Italian</h3>
                    <p class="text-sm text-gray-600 mt-2">Pasta, Pizza & More</p>
                    <?php if (isset($cuisine_counts['Italian'])): ?>
                        <span class="category-count"><?php echo $cuisine_counts['Italian']; ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <a href="index.php?cuisine=Asian" class="group">
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-6 text-center transform group-hover:scale-105 relative">
                    <div class="text-5xl mb-4 animate__animated animate__bounce">🍜</div>
                    <h3 class="font-bold text-gray-800 group-hover:text-orange-500">Asian</h3>
                    <p class="text-sm text-gray-600 mt-2">Stir-fry & Noodles</p>
                    <?php if (isset($cuisine_counts['Chinese']) || isset($cuisine_counts['Japanese'])): ?>
                        <span class="category-count"><?php echo ($cuisine_counts['Chinese'] ?? 0) + ($cuisine_counts['Japanese'] ?? 0); ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <a href="index.php?cuisine=Mexican" class="group">
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-6 text-center transform group-hover:scale-105 relative">
                    <div class="text-5xl mb-4 animate__animated animate__bounce">🌮</div>
                    <h3 class="font-bold text-gray-800 group-hover:text-orange-500">Mexican</h3>
                    <p class="text-sm text-gray-600 mt-2">Tacos & Burritos</p>
                    <?php if (isset($cuisine_counts['Mexican'])): ?>
                        <span class="category-count"><?php echo $cuisine_counts['Mexican']; ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <a href="index.php?cuisine=Mediterranean" class="group">
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-6 text-center transform group-hover:scale-105 relative">
                    <div class="text-5xl mb-4 animate__animated animate__bounce">🫒</div>
                    <h3 class="font-bold text-gray-800 group-hover:text-orange-500">Mediterranean</h3>
                    <p class="text-sm text-gray-600 mt-2">Healthy & Fresh</p>
                    <?php if (isset($cuisine_counts['Mediterranean'])): ?>
                        <span class="category-count"><?php echo $cuisine_counts['Mediterranean']; ?></span>
                    <?php endif; ?>
                </div>
            </a>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="bg-white py-16 mb-12 scroll-mt-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 animate__animated animate__fadeInLeft">
                    <h2 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-heart text-orange-500 mr-2"></i> About Our Community
                    </h2>
                    <p class="text-gray-700 leading-relaxed">
                        Welcome to our vibrant cooking community! We're passionate about bringing people together through the joy of cooking. Whether you're a seasoned chef or just starting your culinary journey, there's a place for you here.
                    </p>
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="bg-orange-50 p-4 rounded-lg text-center">
                            <div class="text-3xl text-orange-500 mb-2">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="font-bold text-gray-800">Growing Community</div>
                            <p class="text-sm text-gray-600">Join fellow food lovers</p>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg text-center">
                            <div class="text-3xl text-orange-500 mb-2">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="font-bold text-gray-800">Diverse Recipes</div>
                            <p class="text-sm text-gray-600">From all around the world</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 animate__animated animate__fadeInRight">
                    <div class="space-y-4">
                        <div class="bg-orange-100 p-4 rounded-lg text-center transform hover:scale-105 transition">
                            <div class="text-4xl mb-2">👨‍🍳</div>
                            <div class="font-bold text-gray-800">Share Recipes</div>
                        </div>
                        <div class="bg-orange-200 p-4 rounded-lg text-center transform hover:scale-105 transition">
                            <div class="text-4xl mb-2">⭐</div>
                            <div class="font-bold text-gray-800">Rate & Review</div>
                        </div>
                    </div>
                    <div class="space-y-4 mt-8">
                        <div class="bg-orange-200 p-4 rounded-lg text-center transform hover:scale-105 transition">
                            <div class="text-4xl mb-2">💡</div>
                            <div class="font-bold text-gray-800">Get Inspired</div>
                        </div>
                        <div class="bg-orange-100 p-4 rounded-lg text-center transform hover:scale-105 transition">
                            <div class="text-4xl mb-2">🤝</div>
                            <div class="font-bold text-gray-800">Connect</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                <div class="bg-orange-50 p-6 rounded-xl text-center transform hover:scale-105 transition">
                    <div class="text-4xl mb-4">🎯</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-800">Easy to Follow</h3>
                    <p class="text-gray-600">Step-by-step instructions make cooking a breeze</p>
                </div>
                <div class="bg-orange-50 p-6 rounded-xl text-center transform hover:scale-105 transition">
                    <div class="text-4xl mb-4">🌍</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-800">Global Cuisine</h3>
                    <p class="text-gray-600">Explore dishes from around the world</p>
                </div>
                <div class="bg-orange-50 p-6 rounded-xl text-center transform hover:scale-105 transition">
                    <div class="text-4xl mb-4">💫</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-800">Active Community</h3>
                    <p class="text-gray-600">Share tips and get inspired by others</p>
                </div>
            </div>
        </div>
    </div>

    <main id="recipes" class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full md:w-1/4">
                <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-filter mr-2 text-orange-500"></i> Filter Recipes
                    </h2>
                
                <form method="get" class="space-y-4">
                        <div class="hover:transform hover:scale-102 transition">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine Type</label>
                            <select name="cuisine" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <option value="">All Cuisines 🌎</option>
                            <?php foreach ($cuisines as $cuisine): ?>
                                <option value="<?php echo $cuisine; ?>" <?php echo ($filter['cuisine'] === $cuisine) ? 'selected' : ''; ?>>
                                        <?php echo $cuisine; ?> <?php echo getCuisineEmoji($cuisine); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                        <div class="hover:transform hover:scale-102 transition">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                            <select name="difficulty" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <option value="">All Levels 📊</option>
                                <option value="Easy" <?php echo ($filter['difficulty'] === 'Easy') ? 'selected' : ''; ?>>Easy 😊</option>
                                <option value="Medium" <?php echo ($filter['difficulty'] === 'Medium') ? 'selected' : ''; ?>>Medium 😅</option>
                                <option value="Hard" <?php echo ($filter['difficulty'] === 'Hard') ? 'selected' : ''; ?>>Hard 🤔</option>
                        </select>
                    </div>
                    
                        <button type="submit" class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i> Find Recipes
                        </button>
                    </form>

                    <!-- Fun Cooking Meme -->
                    <div class="mt-6 p-4 bg-orange-50 rounded-lg text-center">
                        <p class="text-sm text-gray-600 mb-2">Random Cooking Wisdom:</p>
                        <div class="text-orange-600 font-medium" id="cooking-meme">
                            Loading wisdom... 🤔
                        </div>
                    </div>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="w-full md:w-3/4">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Latest Recipes ✨</h1>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Sort by:</span>
                        <select id="sort-select" class="text-sm border rounded-lg p-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest 🆕</option>
                            <option value="rating" <?php echo ($sort === 'rating') ? 'selected' : ''; ?>>Top Rated ⭐</option>
                            <option value="prep_time" <?php echo ($sort === 'prep_time') ? 'selected' : ''; ?>>Quickest ⚡</option>
                        </select>
                    </div>
                </div>
                
                <!-- Recipe Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:scale-102 duration-300">
                            <a href="recipe.php?id=<?php echo $recipe['id']; ?>" class="block">
                                <div class="relative h-48">
                                    <?php if (!empty($recipe['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($recipe['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($recipe['title']); ?>" 
                                             class="w-full h-full object-cover hover:opacity-90 transition"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full bg-orange-100 flex items-center justify-center\'><i class=\'fas fa-utensils text-4xl text-orange-400\'></i></div>';">
                                <?php else: ?>
                                        <div class="w-full h-full bg-orange-100 flex items-center justify-center">
                                            <i class="fas fa-utensils text-4xl text-orange-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-white px-2 py-1 rounded-full text-sm font-medium text-orange-500">
                                            <?php echo getDifficultyEmoji($recipe['difficulty']); ?> <?php echo $recipe['difficulty']; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-2 text-gray-800 hover:text-orange-500 transition">
                                        <?php echo htmlspecialchars($recipe['title']); ?>
                                    </h3>
                                    <div class="flex items-center mb-2">
                                        <div class="flex text-yellow-400">
                                            <?php 
                                            $rating = round($recipe['avg_rating'] ?? 0);
                                            for ($i = 1; $i <= 5; $i++): 
                                            ?>
                                                <i class="fas fa-star<?php echo ($i <= $rating) ? '' : '-empty'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-sm text-gray-600 ml-2">
                                            <?php echo $recipe['avg_rating'] ? number_format($recipe['avg_rating'], 1) : 'No ratings'; ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                                        <span><i class="fas fa-clock text-orange-400"></i> <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                                        <span><i class="fas fa-user-chef text-orange-400"></i> By <?php echo htmlspecialchars($recipe['username']); ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                    
                    <?php if (empty($recipes)): ?>
                    <div class="text-center py-10">
                        <div class="text-6xl mb-4">🤷‍♂️</div>
                        <p class="text-gray-500 mb-4">No recipes found. Time to get creative!</p>
                        <a href="add-recipe.php" class="inline-block bg-orange-500 text-white py-2 px-6 rounded-full hover:bg-orange-600 transition transform hover:scale-105">
                            Add Your First Recipe 🎉
                            </a>
                        </div>
                    <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Handle sort selection change
        document.getElementById('sort-select').addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Random cooking memes
        const cookingMemes = [
            "When the recipe says 'season to taste' 🤔<br>Me: *dumps entire spice rack*",
            "Nobody:<br>Cooking shows: 'Just a touch of olive oil' *pours half the bottle*",
            "Recipe: Prep time 10 minutes<br>Me: *2 hours later* 😅",
            "When you nail the recipe on the first try:<br>'I'm basically a chef now' 👨‍🍳",
            "Me following a recipe:<br>Step 1 ✅<br>Step 2 ✅<br>Step 3: Panic 😱",
            "When the recipe says 'cook until golden brown'<br>Me: *stares intensely* 👀"
        ];

        function updateCookingMeme() {
            const memeElement = document.getElementById('cooking-meme');
            const randomMeme = cookingMemes[Math.floor(Math.random() * cookingMemes.length)];
            memeElement.innerHTML = randomMeme;
        }

        updateCookingMeme();
        setInterval(updateCookingMeme, 10000); // Change meme every 10 seconds
    </script>
</body>
</html>