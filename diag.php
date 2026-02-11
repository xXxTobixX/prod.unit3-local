<?php
require_once 'includes/init.php';
$db = db();
$userId = $_SESSION['user_id'] ?? 0;

echo "User ID: " . $userId . "\n";

try {
    $res = $db->fetchAll("DESCRIBE business_profiles");
    echo "Columns in business_profiles:\n";
    foreach($res as $r) {
        echo "- " . $r['Field'] . " (" . $r['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error describing table: " . $e->getMessage() . "\n";
}

try {
    $profile = $db->fetchOne("SELECT * FROM business_profiles WHERE user_id = ?", [$userId]);
    echo "Profile row exists: " . ($profile ? "YES" : "NO") . "\n";
    if ($profile) {
        print_r($profile);
    }
} catch (Exception $e) {
    echo "Error fetching profile: " . $e->getMessage() . "\n";
}
