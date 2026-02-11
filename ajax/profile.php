<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db = db();
$userId = $_SESSION['user_id'];

if ($action === 'upload-logo') {
    if (!isset($_FILES['logo_file'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
        exit;
    }

    $file = $_FILES['logo_file'];
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);

    if (!in_array($extension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG.']);
        exit;
    }

    // Maximum 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 2MB.']);
        exit;
    }

    $fileName = 'logo_' . $userId . '_' . time() . '.' . $extension;
    $uploadDir = __DIR__ . '/../uploads/logos/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $logoPath = 'uploads/logos/' . $fileName;
        error_log("Logo uploaded to: " . $uploadPath);
        
        try {
            // Check if profile exists
            $profile = $db->fetchOne("SELECT id FROM business_profiles WHERE user_id = ?", [$userId]);
            
            if ($profile) {
                error_log("Updating existing profile for user " . $userId);
                $db->update('business_profiles', ['logo_path' => $logoPath], 'user_id = :u_id', ['u_id' => $userId]);
            } else {
                error_log("Creating new profile for user " . $userId);
                $db->insert('business_profiles', [
                    'user_id' => $userId,
                    'logo_path' => $logoPath
                ]);
            }

            // Update session
            $_SESSION['business_logo'] = $logoPath;

            echo json_encode([
                'success' => true, 
                'message' => 'Logo updated successfully!',
                'logo_url' => '../../../' . $logoPath
            ]);
        } catch (Exception $e) {
            error_log("Database error in ajax/profile.php: " . $e->getMessage());
            // If column doesn't exist, try to create it (self-healing)
            if (strpos($e->getMessage(), 'Unknown column') !== false || strpos($e->getMessage(), 'logo_path') !== false) {
                try {
                    error_log("Attempting migration: Adding logo_path to business_profiles");
                    $db->query("ALTER TABLE business_profiles ADD COLUMN logo_path VARCHAR(255) AFTER user_id");
                    $db->update('business_profiles', ['logo_path' => $logoPath], 'user_id = :u_id', ['u_id' => $userId]);
                    $_SESSION['business_logo'] = $logoPath;
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Logo updated successfully (Table updated)!',
                        'logo_url' => '../../../' . $logoPath
                    ]);
                    exit;
                } catch (Exception $e2) {
                    error_log("Migration failed: " . $e2->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Database error during migration: ' . $e2->getMessage()]);
                    exit;
                }
            }
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        error_log("Failed to move uploaded file from " . $file['tmp_name'] . " to " . $uploadPath);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file. Check server permissions.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
