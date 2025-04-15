<?php
require_once 'db.php';

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateInput($data, $type = 'string', $min = null, $max = null) {
    switch ($type) {
        case 'int':
            $data = filter_var($data, FILTER_VALIDATE_INT);
            if ($data === false) return false;
            if ($min !== null && $data < $min) return false;
            if ($max !== null && $data > $max) return false;
            return $data;
        case 'string':
            if ($min !== null && strlen($data) < $min) return false;
            if ($max !== null && strlen($data) > $max) return false;
            return $data;
        default:
            return false;
    }
}

function getRecipes($sort = 'newest', $filter = []) {
    global $pdo;
    
    if (!isset($GLOBALS['pdo'])) {
        error_log("Database connection not available in getRecipes function");
        return [];
    }
    
    $sql = "SELECT r.*, u.username, 
           (SELECT AVG(rating) FROM reviews WHERE recipe_id = r.id) as avg_rating
           FROM recipes r 
           JOIN users u ON r.user_id = u.id";
    
    $params = [];
    
    // Apply filters
    if (!empty($filter['cuisine'])) {
        $sql .= " WHERE r.cuisine_type = ?";
        $params[] = $filter['cuisine'];
    }
    
    if (!empty($filter['difficulty'])) {
        $sql .= empty($params) ? " WHERE " : " AND ";
        $sql .= "r.difficulty = ?";
        $params[] = $filter['difficulty'];
    }
    
    if (!empty($filter['ingredient'])) {
        $sql .= empty($params) ? " WHERE " : " AND ";
        $sql .= "r.ingredients LIKE ?";
        $params[] = '%' . $filter['ingredient'] . '%';
    }
    
    // Apply sorting
    switch ($sort) {
        case 'rating':
            $sql .= " ORDER BY avg_rating DESC";
            break;
        case 'prep_time':
            $sql .= " ORDER BY r.prep_time ASC";
            break;
        default:
            $sql .= " ORDER BY r.created_at DESC";
    }
    
    try {
        $stmt = $GLOBALS['pdo']->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getRecipes: " . $e->getMessage());
        return [];
    }
}

function uploadImage($file) {
    $target_dir = UPLOAD_DIR;
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File is too large. Maximum size is ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB'];
    }
    
    // Check if upload directory exists and is writable
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            error_log("Failed to create uploads directory at: " . $target_dir);
            return ['success' => false, 'message' => 'Failed to create uploads directory. Please check server permissions.'];
        }
    }
    
    // Check directory permissions
    if (!is_writable($target_dir)) {
        error_log("Upload directory is not writable: " . $target_dir);
        return ['success' => false, 'message' => 'Upload directory is not writable. Please check server permissions.'];
    }
    
    // Generate unique filename
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . '/' . $new_filename;
    
    // Check file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.'];
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        error_log("Failed to move uploaded file to: " . $target_file);
        return ['success' => false, 'message' => 'Failed to upload file. Please try again.'];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function getCuisineEmoji($cuisine) {
    $emojis = [
        'Italian' => '🍝',
        'Mexican' => '🌮',
        'Chinese' => '🥢',
        'Japanese' => '🍱',
        'Indian' => '🍛',
        'Thai' => '🍜',
        'American' => '🍔',
        'Mediterranean' => '🥙',
        'French' => '🥐',
        'Greek' => '🥗',
        'Vietnamese' => '🍜',
        'Korean' => '🥘',
        'Spanish' => '🥘',
        'Middle Eastern' => '🥙',
        'Other' => '🍽️'
    ];
    
    return isset($emojis[$cuisine]) ? $emojis[$cuisine] : '🍽️';
}
?>