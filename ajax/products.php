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

if ($action === 'register-product') {
    $productName = sanitize($_POST['product_name'] ?? '');
    $category = sanitize($_POST['product_category'] ?? '');
    
    if (empty($productName) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Product name anda category are required.']);
        exit;
    }

    $data = [
        'user_id' => $userId,
        'product_name' => $productName,
        'category' => $category,
        'intended_market' => sanitize($_POST['intended_market'] ?? ''),
        'description' => sanitize($_POST['product_description'] ?? ''),
        'ingredients' => sanitize($_POST['ingredients'] ?? ''),
        'production_method' => sanitize($_POST['production_method'] ?? ''),
        'shelf_life' => sanitize($_POST['shelf_life'] ?? ''),
        'packaging_type' => sanitize($_POST['packaging_type'] ?? ''),
        'production_location' => sanitize($_POST['production_location'] ?? ''),
        'production_capacity' => sanitize($_POST['production_capacity'] ?? ''),
        'available_volume' => sanitize($_POST['available_volume'] ?? ''),
        'cost_production' => (float)($_POST['cost_production'] ?? 0),
        'srp' => (float)($_POST['srp'] ?? 0),
        'wholesale_price' => (float)($_POST['wholesale_price'] ?? 0),
        'export_price' => (float)($_POST['export_price'] ?? 0),
        'compliance_type' => sanitize($_POST['product_compliance_type'] ?? ''),
        'permit_number' => sanitize($_POST['permit_number'] ?? ''),
        'export_exp' => sanitize($_POST['export_exp'] ?? 'no'),
        'target_country' => sanitize($_POST['target_country'] ?? ''),
        'certifications' => isset($_POST['certs']) ? json_encode($_POST['certs']) : null,
        'status' => 'pending'
    ];

    // Handle Image Uploads (Simplifying to one main image for now or storing multiple paths)
    $imagePaths = [];
    if (!empty($_FILES['product_images']['name'][0])) {
        $files = $_FILES['product_images'];
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === 0) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed)) {
                    $newFileName = time() . '_' . $userId . '_' . $key . '.' . $ext;
                    $dest = '../uploads/products/' . $newFileName;
                    if (move_uploaded_file($files['tmp_name'][$key], $dest)) {
                        $imagePaths[] = 'uploads/products/' . $newFileName;
                    }
                }
            }
        }
    }
    $data['product_images'] = !empty($imagePaths) ? json_encode($imagePaths) : null;

    $productId = $db->insert('user_products', $data);

    if ($productId) {
        // Notify Admin
        addNotification(
            'New Product for Approval',
            "A new product '{$productName}' has been submitted for approval by " . $_SESSION['user_name'],
            'info',
            'admin'
        );

        echo json_encode(['success' => true, 'message' => 'Product registered successfully and pending approval.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register product.']);
    }
}

if ($action === 'process-product') {
    // Only admins
    $adminRole = $_SESSION['user_role'] ?? '';
    if (!in_array($adminRole, ['admin', 'staff', 'superadmin', 'manager'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
        exit;
    }

    $productId = (int)($_POST['product_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $remarks = sanitize($_POST['remarks'] ?? '');

    if (!$productId || !in_array($status, ['approved', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    $product = $db->fetchOne("SELECT p.*, u.id as owner_id FROM user_products p JOIN users u ON p.user_id = u.id WHERE p.id = ?", [$productId]);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    $updated = $db->update('user_products', [
        'status' => $status,
        'remarks' => $remarks,
        'approved_by' => $_SESSION['user_id'],
        'approved_at' => date('Y-m-d H:i:s')
    ], 'id = ' . $productId);

    if ($updated) {
        $msg = ($status === 'approved') 
            ? "Your product '{$product['product_name']}' has been approved and listed in the registry." 
            : "Your product '{$product['product_name']}' was rejected. Reason: {$remarks}";
            
        addNotification(
            'Product Registration Update',
            $msg,
            ($status === 'approved' ? 'success' : 'error'),
            'user',
            $product['owner_id']
        );

        echo json_encode(['success' => true, 'message' => 'Product status updated and user notified.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update product status.']);
    }
}
