<?php
require_once 'includes/init.php';
$db = db();

try {
    // Check if column exists in users
    $res = $db->query("SHOW COLUMNS FROM users LIKE 'two_factor_enabled'")->fetch();
    if (!$res) {
        $db->query("ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 1 AFTER status");
        echo "Added two_factor_enabled to users table.\n";
    } else {
        echo "Column two_factor_enabled already exists in users table.\n";
    }

    // Check if column exists in admins
    $res = $db->query("SHOW COLUMNS FROM admins LIKE 'two_factor_enabled'")->fetch();
    if (!$res) {
        $db->query("ALTER TABLE admins ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 1 AFTER role");
        echo "Added two_factor_enabled to admins table.\n";
    } else {
        echo "Column two_factor_enabled already exists in admins table.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
