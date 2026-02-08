<?php
/**
 * Example Usage of Database Connection
 * 
 * This file demonstrates how to use the Database class
 */

// Include the bootstrap file
require_once __DIR__ . '/../includes/init.php';

// Example 1: Get database instance
$db = db();

// Example 2: Fetch all users
$users = $db->fetchAll("SELECT * FROM users WHERE status = :status", ['status' => 'active']);

// Example 3: Fetch single user
$user = $db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => 1]);

// Example 4: Insert new user
$newUserId = $db->insert('users', [
    'email' => 'user@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'name' => 'John Doe',
    'role' => 'vendor',
    'status' => 'active',
    'created_at' => date('Y-m-d H:i:s')
]);

// Example 5: Update user
$updated = $db->update(
    'users',
    [
        'name' => 'Jane Doe',
        'updated_at' => date('Y-m-d H:i:s')
    ],
    'id = :id',
    ['id' => 1]
);

// Example 6: Delete user
$deleted = $db->delete('users', 'id = :id', ['id' => 999]);

// Example 7: Custom query with parameters
$products = $db->fetchAll(
    "SELECT p.*, u.name as vendor_name 
     FROM products p 
     JOIN users u ON p.vendor_id = u.id 
     WHERE p.category = :category 
     AND p.status = :status 
     ORDER BY p.created_at DESC 
     LIMIT :limit",
    [
        'category' => 'handicrafts',
        'status' => 'approved',
        'limit' => 10
    ]
);

// Example 8: Transaction
try {
    $db->beginTransaction();
    
    // Insert order
    $orderId = $db->insert('orders', [
        'user_id' => 1,
        'total_amount' => 1500.00,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Insert order items
    $db->insert('order_items', [
        'order_id' => $orderId,
        'product_id' => 5,
        'quantity' => 2,
        'price' => 750.00
    ]);
    
    $db->commit();
    echo "Order created successfully!";
    
} catch (Exception $e) {
    $db->rollback();
    echo "Error: " . $e->getMessage();
}

// Example 9: Count records
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = :role", ['role' => 'vendor']);
$count = $stmt->fetch()['total'];

// Example 10: Check if record exists
$exists = $db->fetchOne("SELECT id FROM users WHERE email = :email", ['email' => 'test@example.com']);
if ($exists) {
    echo "User exists!";
}
