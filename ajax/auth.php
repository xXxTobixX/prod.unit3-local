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
    $verification = $db->fetchOne(
        "SELECT * FROM otp_verifications WHERE email = ? AND otp_code = ? AND expires_at > NOW()",
        [$email, $otp]
    );

    if ($verification) {
        // Valid OTP
        // Get user/admin data again - Prioritize admins
        $user = $db->fetchOne("SELECT * FROM admins WHERE email = ?", [$email]);
        $role = $user['role'] ?? 'admin';
        
        if (!$user) {
            $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            $role = $user['role'] ?? 'user';
        }

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User account no longer exists.']);
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $role;
        $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['business_name'] = $user['business_name'] ?? null;
        
        // Administrative roles automatically bypass profile completion
        $isAdminRole = in_array($role, ['admin', 'staff', 'superadmin', 'manager']);
        $_SESSION['profile_completed'] = $isAdminRole ? true : (bool)($user['profile_completed'] ?? false);

        // Clean up OTP
        $db->delete('otp_verifications', 'email = ?', [$email]);

        echo json_encode([
            'success' => true, 
            'message' => 'Verification successful!',
            'role' => $role,
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

