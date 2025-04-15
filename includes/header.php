<?php
// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .nav-item {
            position: relative;
            padding: 0.5rem 1rem;
            color: #4B5563;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item:hover {
            color: #ff6b00;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: #ff6b00;
            transition: width 0.3s ease;
        }

        .nav-item:hover::after {
            width: 100%;
        }

        .nav-item.active {
            color: #ff6b00;
        }

        .nav-item.active::after {
            width: 100%;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: bold;
            background: linear-gradient(135deg, #ff6b00, #ff8533);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            padding-right: 2rem;
        }

        .logo::after {
            content: '🍳';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            -webkit-text-fill-color: initial;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(-50%) rotate(0deg); }
            50% { transform: translateY(-50%) rotate(10deg); }
        }

        .nav-group {
            position: relative;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-group::before {
            content: '';
            position: absolute;
            left: -1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 60%;
            background: #e5e7eb;
        }

        .profile-menu {
            position: relative;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }

        .profile-trigger:hover {
            background: #fff0e6;
        }

        .profile-avatar {
            width: 2.5rem;
            height: 2.5rem;
            background: #ff6b00;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .profile-menu-content {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 12rem;
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .profile-menu:hover .profile-menu-content {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #4B5563;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .menu-item:hover {
            background: #fff0e6;
            color: #ff6b00;
        }

        .menu-item.danger {
            color: #DC2626;
        }

        .menu-item.danger:hover {
            background: #FEE2E2;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    RecipeShare
                </a>

                <!-- Main Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    
                    <!-- Categories Dropdown -->
                    <div class="nav-group">
                        <a href="categories.php" class="nav-item <?php echo $current_page === 'categories' ? 'active' : ''; ?>">
                            <i class="fas fa-th"></i>
                            <span>Categories</span>
                        </a>
                        <a href="about.php" class="nav-item <?php echo $current_page === 'about' ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i>
                            <span>About</span>
                        </a>
                        <a href="recipes.php" class="nav-item <?php echo $current_page === 'recipes' ? 'active' : ''; ?>">
                            <i class="fas fa-utensils"></i>
                            <span>Recipes</span>
                        </a>
                    </div>

                    <!-- User Navigation -->
                    <div class="nav-group">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="add-recipe.php" class="nav-item <?php echo $current_page === 'add-recipe' ? 'active' : ''; ?>">
                                <i class="fas fa-plus"></i>
                                <span>Add Recipe</span>
                            </a>
                            <a href="my-recipes.php" class="nav-item <?php echo $current_page === 'my-recipes' ? 'active' : ''; ?>">
                                <i class="fas fa-book"></i>
                                <span>My Recipes</span>
                            </a>
                            
                            <!-- Profile Menu -->
                            <div class="profile-menu relative">
                                <button class="nav-item flex items-center space-x-2">
                                    <i class="fas fa-user-circle"></i>
                                    <span>Profile</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                <div class="profile-menu-content absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 opacity-0 transform -translate-y-2 invisible transition-all duration-200">
                                    <a href="profile.php" class="menu-item">
                                        <i class="fas fa-user"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="my-reviews.php" class="menu-item">
                                        <i class="fas fa-star"></i>
                                        <span>My Reviews</span>
                                    </a>
                                    <a href="settings.php" class="menu-item">
                                        <i class="fas fa-cog"></i>
                                        <span>Settings</span>
                                    </a>
                                    <div class="border-t my-2"></div>
                                    <a href="logout.php" class="menu-item danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="nav-item <?php echo $current_page === 'login' ? 'active' : ''; ?>">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="register.php" class="nav-item <?php echo $current_page === 'register' ? 'active' : ''; ?>">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition" id="mobile-menu-button">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </nav>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-4 py-3 space-y-3">
                <a href="index.php" class="block py-2 text-gray-700 hover:text-[#ff6b00]">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="add-recipe.php" class="block py-2 text-gray-700 hover:text-[#ff6b00]">
                        <i class="fas fa-plus mr-2"></i> Add Recipe
                    </a>
                    <a href="my-recipes.php" class="block py-2 text-gray-700 hover:text-[#ff6b00]">
                        <i class="fas fa-book mr-2"></i> My Recipes
                    </a>
                    <a href="profile.php" class="block py-2 text-gray-700 hover:text-[#ff6b00]">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="logout.php" class="block py-2 text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="block py-2 text-gray-700 hover:text-[#ff6b00]">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="register.php" class="block py-2 text-gray-700 hover:text-[#ff6b00]">
                        <i class="fas fa-user-plus mr-2"></i> Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
<!-- Remove the entire search form that was here -->