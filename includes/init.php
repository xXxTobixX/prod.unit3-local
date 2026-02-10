<?php
/**
 * Application Bootstrap File
 * 
 * This file initializes the application and loads necessary files.
 * Include this file at the top of your PHP pages.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../config/database.php';

// Load core classes
require_once __DIR__ . '/Database.php';

// Autoloader for future classes (optional)
spl_autoload_register(function ($class) {
    // Handle namespace or different file paths if needed
    $file = __DIR__ . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Turnstile configuration and helper
require_once __DIR__ . '/../config/turnstile.php';

/**
 * Get database instance
 * 
 * @return Database
 */
function db() {
    return Database::getInstance();
}

/**
 * Sanitize input data
 * 
 * @param mixed $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 * 
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 * 
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'profile_completed' => $_SESSION['profile_completed'] ?? false
    ];
}

/**
 * Set flash message
 * 
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Add a new notification
 * 
 * @param string $title
 * @param string $message
 * @param string $type (success, error, warning, info)
 * @param string $role (target role)
 * @param int|null $user_id (specific user id)
 * @return int|bool
 */
function addNotification($title, $message, $type = 'info', $role = 'admin', $user_id = null) {
    try {
        $db = db();
        return $db->insert('notifications', [
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'role' => $role,
            'user_id' => $user_id
        ]);
    } catch (Exception $e) {
        error_log("Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get unread notifications for current user
 * 
 * @return array
 */
function getUnreadNotifications() {
    if (!isLoggedIn()) return [];
    
    $user = getCurrentUser();
    $db = db();
    
    // Notifications for specific user OR for the user's role
    // Using role like 'admin' to target multiple users
    $query = "SELECT * FROM notifications 
              WHERE is_read = 0 
              AND (user_id = ? OR role = ?) 
              ORDER BY created_at DESC";
    
    return $db->fetchAll($query, [$user['id'], $user['role']]);
}

/**
 * Get all notifications for current user (limit to 20)
 * 
 * @return array
 */
function getAllNotifications() {
    if (!isLoggedIn()) return [];
    
    $user = getCurrentUser();
    $db = db();
    
    $query = "SELECT * FROM notifications 
              WHERE (user_id = ? OR role = ?) 
              ORDER BY created_at DESC LIMIT 20";
    
    return $db->fetchAll($query, [$user['id'], $user['role']]);
}
