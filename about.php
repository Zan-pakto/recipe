<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = "About Us";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                <i class="fas fa-utensils text-[#ff6b00] mr-2"></i>About RecipeShare
            </h1>
            <p class="text-xl text-gray-600">
                A community-driven platform for sharing and discovering delicious recipes
            </p>
        </div>

        <!-- Mission Statement -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
            <p class="text-gray-600 mb-4">
                At RecipeShare, we believe that food brings people together. Our mission is to create a vibrant community 
                where food enthusiasts can share their culinary creations, discover new recipes, and connect with fellow 
                cooking enthusiasts from around the world.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="text-center">
                    <div class="text-4xl text-[#ff6b00] mb-2">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Share</h3>
                    <p class="text-gray-600">Share your favorite recipes with the world</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl text-[#ff6b00] mb-2">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Discover</h3>
                    <p class="text-gray-600">Find new recipes from various cuisines</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl text-[#ff6b00] mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Connect</h3>
                    <p class="text-gray-600">Join a community of food lovers</p>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">What We Offer</h2>
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="text-[#ff6b00] text-xl mr-4">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Recipe Management</h3>
                        <p class="text-gray-600">
                            Create, edit, and organize your recipes with detailed instructions, ingredients, and photos.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="text-[#ff6b00] text-xl mr-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Reviews & Ratings</h3>
                        <p class="text-gray-600">
                            Share your experiences and discover what others think about recipes through our review system.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="text-[#ff6b00] text-xl mr-4">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Smart Filtering</h3>
                        <p class="text-gray-600">
                            Find recipes easily with our advanced filtering system by cuisine, difficulty, and more.
                        </p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="text-[#ff6b00] text-xl mr-4">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Community Features</h3>
                        <p class="text-gray-600">
                            Like, save, and share recipes with other members of our growing community.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Join Our Community</h2>
            <p class="text-gray-600 mb-6">
                Start sharing your recipes and connecting with fellow food enthusiasts today!
            </p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="flex justify-center space-x-4">
                    <a href="register.php" class="btn-primary">
                        <i class="fas fa-user-plus mr-2"></i>Sign Up
                    </a>
                    <a href="login.php" class="btn-secondary">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            <?php else: ?>
                <a href="add-recipe.php" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Share Your First Recipe
                </a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?> 