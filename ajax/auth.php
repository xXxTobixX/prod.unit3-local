<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/Mailer.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'signup') {
    $firstname = sanitize($_POST['firstname'] ?? '');
    $lastname = sanitize($_POST['lastname'] ?? '');
    $business_name = sanitize($_POST['business-name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Simple validation
    if (empty($firstname) || empty($lastname) || empty($business_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Check if email exists
    $db = db();
    $existing = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $userId = $db->insert('users', [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'business_name' => $business_name,
        'email' => $email,
        'password' => $hashed_password,
        'role' => 'user',
        'status' => 'pending'
    ]);

    if ($userId) {
        // Notify admin of new registration
        addNotification("New Registration", "A new user account ({$firstname} {$lastname}) has been created.", 'info', 'admin');
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
}

if ($action === 'login') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    // Verify CAPTCHA
    $captchaVerify = verifyTurnstile($_POST['cf-turnstile-response'] ?? null, $_SERVER['REMOTE_ADDR']);
    if (!$captchaVerify['success']) {
        echo json_encode(['success' => false, 'message' => $captchaVerify['message']]);
        exit;
    }

    $db = db();
    // Check admins table first for administrative priority
    $user = $db->fetchOne("SELECT * FROM admins WHERE email = ?", [$email]);
    $table = 'admins';

    if (!$user) {
        // Check users table
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        $table = 'users';
    }

    if ($user && password_verify($password, $user['password'])) {
        // Check if 2FA is enabled (Default to 1/Enabled)
        $is2FAEnabled = (int)($user['two_factor_enabled'] ?? 1);

        if ($is2FAEnabled === 0) {
            // Bypass OTP if disabled
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = strtolower($user['role'] ?? 'user');
            $_SESSION['user_name'] = ($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '');
            $_SESSION['business_name'] = $user['business_name'] ?? null;
            
            // Refresh Logo
            $profile = $db->fetchOne("SELECT logo_path FROM business_profiles WHERE user_id = ?", [$user['id']]);
            $_SESSION['business_logo'] = $profile['logo_path'] ?? null;

            $_SESSION['profile_completed'] = (bool)($user['profile_completed'] ?? false);
            if (in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin'])) $_SESSION['profile_completed'] = true;

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful (2FA Bypassed).',
                'no_otp' => true,
                'role' => $_SESSION['user_role'],
                'profile_completed' => $_SESSION['profile_completed']
            ]);
            exit;
        }

        // Generate OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Delete old OTPs for this email
        $db->delete('otp_verifications', 'email = ?', [$email]);
        
        // Store new OTP
        $db->insert('otp_verifications', [
            'email' => $email,
            'otp_code' => $otp,
            'expires_at' => $expires
        ]);

        // Send OTP via Email
        if (Mailer::sendOTP($email, $otp)) {
            echo json_encode([
                'success' => true, 
                'message' => 'OTP sent to your email.'
            ]);
        } else {
            // For development, if mailing fails, you might still want to see the OTP 
            // but for production, this should be handled properly.
            echo json_encode([
                'success' => true, 
                'message' => 'Login success, but failed to send email. (Check server logs)',
                'temp_otp' => $otp // Keeping for your testing until SMT is configured
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
}

if ($action === 'verify-otp') {
    $email = sanitize($_POST['email'] ?? '');
    $otp = sanitize($_POST['otp'] ?? '');

    if (empty($email) || empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    $db = db();
    $now = date('Y-m-d H:i:s');
    $verification = $db->fetchOne(
        "SELECT * FROM otp_verifications WHERE email = ? AND otp_code = ? AND expires_at > ?",
        [$email, $otp, $now]
    );

    if ($verification) {
        // Valid OTP
        // Get user/admin data again - Prioritize admins
        $user = $db->fetchOne("SELECT * FROM admins WHERE email = ?", [$email]);
        $role = $user ? ($user['role'] ?? 'admin') : null;
        
        if (!$user) {
            $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            $role = $user ? ($user['role'] ?? 'user') : null;
        }

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User account no longer exists.']);
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = strtolower($role);
        $_SESSION['user_name'] = ($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '');
        $_SESSION['business_name'] = $user['business_name'] ?? null;
        
        // Fetch logo path from business_profiles if exists
        $profile = $db->fetchOne("SELECT logo_path FROM business_profiles WHERE user_id = ?", [$user['id']]);
        $_SESSION['business_logo'] = $profile['logo_path'] ?? null;
        
        // Administrative roles automatically bypass profile completion
        $isAdminRole = in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager']);
        $_SESSION['profile_completed'] = $isAdminRole ? true : (bool)($user['profile_completed'] ?? false);

        // Debug: Log session before saving
        error_log("OTP Verified - Setting Session for: " . $_SESSION['user_email']);
        error_log("Session Data at Login: " . print_r($_SESSION, true));

        // Regenerate session ID for security (Disabled temporarily for debugging)
        // session_regenerate_id(true);

        // Clean up OTP
        $db->delete('otp_verifications', 'email = ?', [$email]);

        // Ensure session is saved before redirecting
        session_write_close();

        echo json_encode([
            'success' => true, 
            'message' => 'Verification successful!',
            'role' => $_SESSION['user_role'],
            'is_admin' => $isAdminRole,
            'profile_completed' => $_SESSION['profile_completed']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP.']);
    }
}

if ($action === 'complete-profile') {
    // Debug logging
    error_log("Complete Profile Action Triggered");
    error_log("POST Data: " . print_r($_POST, true));
    error_log("User ID: " . ($_SESSION['user_id'] ?? 'Not Set'));

    // Add aggressive error reporting for this block
    ini_set('display_errors', 0); // Don't output errors to HTML, handle them in JSON
    error_reporting(E_ALL);

    try {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $db = db();

        // Start transaction for atomicity
        $db->beginTransaction();

        // 1. Update User Record (Business Name, Status)
        $business_name = sanitize($_POST['business_name'] ?? '');
        $db->update('users', [
            'business_name' => $business_name, 
            'profile_completed' => 1,
            'status' => 'active'
        ], 'id = :id', ['id' => $userId]);
        $_SESSION['business_name'] = $business_name;

        // 2. Insert Business Profile
        $sector = sanitize($_POST['sector'] ?? '');
        if ($sector === 'Others' && !empty($_POST['sector_other'])) {
            $sector = sanitize($_POST['sector_other']);
        }

        $db->insert('business_profiles', [
            'user_id' => $userId,
            'business_type' => sanitize($_POST['business_type'] ?? ''),
            'sector' => $sector,
            'address' => sanitize($_POST['business_address'] ?? ''),
            'registration_number' => sanitize($_POST['registration_number'] ?? ''),
            'year_started' => (int)($_POST['year_started'] ?? 0),
            'number_of_workers' => (int)($_POST['number_of_workers'] ?? 0),
            'compliance_type' => sanitize($_POST['compliance_type'] ?? ''),
            'data_consent' => isset($_POST['privacy_consent']) ? 1 : 0
        ]);

        // 3. Insert Product
        $prodCategory = sanitize($_POST['product_category'] ?? '');
        if ($prodCategory === 'Others' && !empty($_POST['product_category_other'])) {
            $prodCategory = sanitize($_POST['product_category_other']);
        }

        $db->insert('user_products', [
            'user_id' => $userId,
            'product_name' => sanitize($_POST['product_name'] ?? ''),
            'category' => $prodCategory,
            'description' => sanitize($_POST['product_description'] ?? ''),
            'production_capacity' => sanitize($_POST['production_capacity'] ?? '') . ' kg'
        ]);
        
        $db->commit();
        $_SESSION['profile_completed'] = true;

        // Notify admin of profile completion
        addNotification("New MSME Profile", "{$_SESSION['user_name']} has completed their business profile for '{$business_name}'.", 'success', 'admin');

        echo json_encode(['success' => true, 'message' => 'Profile completed successfully!']);
    } catch (Throwable $e) {
        if (isset($db)) {
            $db->rollback();
        }
        error_log("Profile Completion Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
    }
}

/**
 * Get full user details for review (Admin only)
 */
if ($action === 'get-user-details-review') {
    if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    $id = (int)($_GET['userId'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
        exit;
    }

    $db = db();
    $user = $db->fetchOne("SELECT id, firstname, lastname, email, role, status, business_name, created_at FROM users WHERE id = ?", [$id]);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    $profile = $db->fetchOne("SELECT * FROM business_profiles WHERE user_id = ?", [$id]);
    $products = $db->fetchAll("SELECT * FROM user_products WHERE user_id = ?", [$id]);

    echo json_encode([
        'success' => true,
        'user' => $user,
        'profile' => $profile,
        'products' => $products
    ]);
    exit;
}

/**
 * Handle MSME Registry Updates (Admin only)
 */
if ($action === 'update-msme') {
    if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    $id = (int)($_POST['userId'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
        exit;
    }

    $fullName = sanitize($_POST['fullName'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $businessName = sanitize($_POST['businessName'] ?? '');
    $role = sanitize($_POST['role'] ?? 'user');
    $status = sanitize($_POST['status'] ?? 'active');

    if (empty($fullName) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and Email are required.']);
        exit;
    }

    // Split name into first and last
    $parts = explode(' ', $fullName, 2);
    $firstname = $parts[0];
    $lastname = $parts[1] ?? '';

    try {
        $db = db();
        
        $success = $db->update('users', [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'business_name' => $businessName,
            'role' => $role,
            'status' => $status
        ], 'id = :target_id', ['target_id' => $id]);

        if ($success || $success === 0) {
            echo json_encode(['success' => true, 'message' => 'MSME profile updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to execute update on database.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
    }
}

/**
 * Simple status update for administrators (Dashboard review)
 */
if ($action === 'update-status-simple') {
    if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    $id = (int)($_POST['userId'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');

    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing required data.']);
        exit;
    }

    try {
        $db = db();
        $success = $db->update('users', ['status' => $status], 'id = :id', ['id' => $id]);
        
        if ($success || $success === 0) {
            // Log for notification
            $user = $db->fetchOne("SELECT firstname, lastname FROM users WHERE id = ?", [$id]);
            $msg = "User application for " . $user['firstname'] . " " . $user['lastname'] . " has been " . $status . ".";
            addNotification("Application Updated", $msg, ($status == 'active' ? 'success' : 'warning'), 'admin');

            echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user status.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    }
    exit;
}

/**
 * Handle Forgot Password Request (Send Code)
 */
if ($action === 'forgot-password') {
    $email = sanitize($_POST['email'] ?? '');

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email address is required.']);
        exit;
    }

    $db = db();
    // Check if email exists
    $user = $db->fetchOne("SELECT id FROM admins WHERE email = ?", [$email]);
    if (!$user) {
        $user = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    }

    if ($user) {
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Delete old OTPs
        $db->delete('otp_verifications', 'email = ?', [$email]);
        
        // Store new OTP
        $db->insert('otp_verifications', [
            'email' => $email,
            'otp_code' => $otp,
            'expires_at' => $expires
        ]);

        if (Mailer::sendPasswordReset($email, $otp)) {
            echo json_encode(['success' => true, 'message' => 'Reset code sent to your email.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again later.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email address not found.']);
    }
}

/**
 * Handle Reset Password (Verify Code & Change Password)
 */
if ($action === 'reset-password') {
    $email = sanitize($_POST['email'] ?? '');
    $otp = sanitize($_POST['otp'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($otp) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $db = db();
    
    // Verify OTP first
    $verification = $db->fetchOne(
        "SELECT * FROM otp_verifications WHERE email = ? AND otp_code = ? AND expires_at > NOW()",
        [$email, $otp]
    );

    if ($verification) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $updated = false;

        // Try updating admin first if email exists there
        $admin = $db->fetchOne("SELECT * FROM admins WHERE email = ?", [$email]);
        if ($admin) {
            $updated = $db->update('admins', ['password' => $hashed_password], 'email = ?', [$email]);
        } else {
            // Try updating user
            $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            if ($user) {
                $updated = $db->update('users', ['password' => $hashed_password], 'email = ?', [$email]);
            }
        }

        if ($updated) {
            // Clean up OTP
            $db->delete('otp_verifications', 'email = ?', [$email]);
            echo json_encode(['success' => true, 'message' => 'Password has been reset successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password. Account not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired code.']);
    }
}

/**
 * Handle Password Change from Profile
 */
if ($action === 'change-password') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $db = db();
    $user = $db->fetchOne("SELECT password FROM users WHERE id = ?", [$userId]);

    if ($user && password_verify($currentPassword, $user['password'])) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $db->update('users', ['password' => $hashed], 'id = :id', ['id' => $userId]);
        echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
    }
}

/**
 * Handle 2FA Settings Update
 */
if ($action === 'update-2fa') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $enabled = (int)($_POST['enabled'] ?? 1);
    $db = db();

    try {
        $db->update('users', ['two_factor_enabled' => $enabled], 'id = :id', ['id' => $userId]);
        echo json_encode(['success' => true, 'message' => '2FA settings updated.']);
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Unknown column') !== false || strpos($e->getMessage(), 'two_factor_enabled') !== false) {
            try {
                $db->query("ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 1 AFTER status");
                $db->update('users', ['two_factor_enabled' => $enabled], 'id = :id', ['id' => $userId]);
                echo json_encode(['success' => true, 'message' => '2FA settings updated (Table repaired).']);
                exit;
            } catch (Exception $e2) {
                 echo json_encode(['success' => false, 'message' => 'Database error during repair: ' . $e2->getMessage()]);
                 exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Handle Account Deactivation
 */
if ($action === 'deactivate-account') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $db = db();
    
    // Set status to deactivated instead of deleting
    $success = $db->update('users', ['status' => 'deactivated'], 'id = :id', ['id' => $userId]);
    
    if ($success) {
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Account deactivated.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to deactivate account.']);
    }
}
