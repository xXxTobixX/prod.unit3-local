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

if ($action === 'upload-document') {
    $docType = sanitize($_POST['document_type'] ?? '');
    
    if (empty($docType) || !isset($_FILES['document_file'])) {
        echo json_encode(['success' => false, 'message' => 'Missing document type or file.']);
        exit;
    }

    $file = $_FILES['document_file'];
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);

    if (!in_array($extension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: PDF, JPG, PNG.']);
        exit;
    }

    // Maximum 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB.']);
        exit;
    }

    $fileName = time() . '_' . $userId . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $docType) . '.' . $extension;
    $uploadPath = '../uploads/documents/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Save to database
        $db->insert('business_documents', [
            'user_id' => $userId,
            'document_type' => $docType,
            'file_path' => 'uploads/documents/' . $fileName,
            'status' => 'pending'
        ]);

        // Add notification for admin
        addNotification(
            'New Document Upload',
            'A new document (' . $docType . ') has been uploaded by ' . $_SESSION['user_name'] . '.',
            'info',
            'admin'
        );

        echo json_encode(['success' => true, 'message' => 'Document uploaded successfully and is now pending verification.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    }
}

if ($action === 'process-document') {
    // Only admins
    $adminRole = $_SESSION['user_role'] ?? '';
    if (!in_array($adminRole, ['admin', 'staff', 'superadmin', 'manager'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
        exit;
    }

    $docId = (int)($_POST['document_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $remarks = sanitize($_POST['remarks'] ?? '');

    if (!$docId || !in_array($status, ['verified', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    // Get document and user info for notification
    $doc = $db->fetchOne("SELECT bd.*, u.id as owner_id FROM business_documents bd JOIN users u ON bd.user_id = u.id WHERE bd.id = ?", [$docId]);
    if (!$doc) {
        echo json_encode(['success' => false, 'message' => 'Document not found.']);
        exit;
    }

    $updated = $db->update('business_documents', [
        'status' => $status,
        'remarks' => $remarks,
        'verified_at' => date('Y-m-d H:i:s'),
        'verified_by' => $_SESSION['user_id']
    ], 'id = ' . $docId);

    if ($updated) {
        // Notify user
        $msg = ($status === 'verified') 
            ? "Your document '{$doc['document_type']}' has been verified." 
            : "Your document '{$doc['document_type']}' was rejected. Reason: {$remarks}";
        
        addNotification(
            'Document Verification Update',
            $msg,
            ($status === 'verified' ? 'success' : 'error'),
            'user',
            $doc['owner_id']
        );

        echo json_encode(['success' => true, 'message' => 'Document status updated and user notified.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update document status.']);
    }
}
