<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/Mailer.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

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
        'status' => 'active'
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

    $db = db();
    // Check users table first
    $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    $table = 'users';

    if (!$user) {
        // Check admins table
        $user = $db->fetchOne("SELECT * FROM admins WHERE email = ?", [$email]);
        $table = 'admins';
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
        // Get user/admin data again
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        $role = 'user';
        if (!$user) {
            $user = $db->fetchOne("SELECT * FROM admins WHERE email = ?", [$email]);
            $role = $user['role'] ?? 'admin';
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $role;
        $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['business_name'] = $user['business_name'] ?? null;
        $_SESSION['profile_completed'] = ($role === 'admin') ? true : (bool)($user['profile_completed'] ?? false);

        // Clean up OTP
        $db->delete('otp_verifications', 'email = ?', [$email]);

        echo json_encode([
            'success' => true, 
            'message' => 'Verification successful!',
            'role' => $role,
            'profile_completed' => ($role !== 'admin') ? ($user['profile_completed'] ?? false) : true
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP.']);
    }
}

if ($action === 'complete-profile') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $db = db();

    // 1. Update User Record (Business Name)
    $business_name = sanitize($_POST['business_name'] ?? '');
    $db->update('users', ['business_name' => $business_name, 'profile_completed' => 1], 'id = ?', [$userId]);
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
    $db->insert('user_products', [
        'user_id' => $userId,
        'product_name' => sanitize($_POST['product_name'] ?? ''),
        'category' => sanitize($_POST['product_category'] ?? ''),
        'description' => sanitize($_POST['product_description'] ?? ''),
        'production_capacity' => sanitize($_POST['production_capacity'] ?? '') . ' kg'
    ]);
    
    $_SESSION['profile_completed'] = true;
    echo json_encode(['success' => true, 'message' => 'Profile completed successfully!']);
}

