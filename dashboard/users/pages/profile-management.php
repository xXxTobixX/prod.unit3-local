<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 

$unreadNotifs = getUnreadNotifications();
$notifCount = count($unreadNotifs);

$userId = $_SESSION['user_id'];
$db = db();

// Fetch user and business profile data
$query = "SELECT u.*, bp.* 
          FROM users u 
          LEFT JOIN business_profiles bp ON u.id = bp.user_id 
          WHERE u.id = :id";
$user = $db->fetchOne($query, ['id' => $userId]);

if (!$user) {
    // Fallback if something is wrong
    redirect('../../../logout.php');
}

$fullName = $user['firstname'] . ' ' . $user['lastname'];

// Fetch user documents
$docsFullQuery = "SELECT * FROM business_documents WHERE user_id = :id ORDER BY uploaded_at DESC";
$documents = $db->fetchAll($docsFullQuery, ['id' => $userId]);

// Helper to get doc by type
function getDocByType($docs, $type) {
    $sanitizedType = sanitize($type);
    foreach ($docs as $doc) {
        if ($doc['document_type'] === $type || $doc['document_type'] === $sanitizedType) return $doc;
    }
    return null;
}

$requiredDocs = [
    ['type' => "Mayor's Permit", 'icon' => 'fa-file-invoice'],
    ['type' => 'DTI Registration', 'icon' => 'fa-file-contract'],
    ['type' => 'BIR Certificate', 'icon' => 'fa-file-signature'],
    ['type' => 'Export Certification', 'icon' => 'fa-ship']
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management - LGU 3 MSME Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 30px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-side,
        .profile-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .avatar-upload {
            text-align: center;
            padding: 40px 32px;
            background: var(--card-bg);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .avatar-upload::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .profile-avatar-large {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid var(--bg-color);
            box-shadow: 0 0 0 3px var(--primary-color);
            transition: transform 0.3s ease;
        }

        .avatar-upload:hover .profile-avatar-large {
            transform: scale(1.05);
        }

        .form-section {
            background: var(--card-bg);
            padding: 35px;
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .form-section:hover {
            box-shadow: var(--shadow-md);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 18px;
        }

        body.dark-mode .section-title i {
            background: rgba(96, 165, 250, 0.15);
            color: #60a5fa;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group label i {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 14px 18px;
            border-radius: 12px;
            border: 1.5px solid var(--border-color);
            background: var(--bg-color);
            color: var(--text-main);
            font-family: inherit;
            font-size: 15px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 32, 91, 0.08);
            outline: none;
            background: var(--card-bg);
        }

        .doc-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background: var(--bg-color);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .doc-card:hover {
            transform: translateX(5px);
            border-color: var(--primary-color);
            background: var(--card-bg);
        }

        .doc-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .doc-icon {
            width: 48px;
            height: 48px;
            background: rgba(0, 32, 91, 0.05);
            color: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: all 0.3s ease;
        }

        .doc-card:hover .doc-icon {
            background: var(--primary-color);
            color: white;
        }

        .btn-action-outline {
            background: transparent;
            color: var(--primary-color);
            border: 1.5px solid var(--primary-color);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-action-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Premium Quick Action Buttons */
        .btn-quick-action {
            background: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            text-align: left;
            margin-bottom: 5px;
        }

        .btn-quick-action:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-color);
        }

        .btn-quick-action .action-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-quick-action i:first-child {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 14px;
            background: rgba(0, 32, 91, 0.05);
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .btn-quick-action:hover i:first-child {
            background: var(--primary-color);
            color: white;
            transform: rotate(5deg);
        }

        .btn-quick-action .chevron {
            font-size: 10px;
            color: var(--text-muted);
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .btn-quick-action:hover .chevron {
            transform: translateX(3px);
            color: var(--primary-color);
            opacity: 1;
        }

        /* Distinct Action Colors */
        .btn-quick-action.password:hover { border-color: #3b82f6; }
        .btn-quick-action.password i:first-child { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .btn-quick-action.password:hover i:first-child { background: #3b82f6; }

        .btn-quick-action.security:hover { border-color: #f59e0b; }
        .btn-quick-action.security i:first-child { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .btn-quick-action.security:hover i:first-child { background: #f59e0b; }

        .btn-quick-action.danger:hover { border-color: #ef4444; color: #ef4444; }
        .btn-quick-action.danger i:first-child { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .btn-quick-action.danger:hover i:first-child { background: #ef4444; }

        /* Dark mode specific overrides */
        body.dark-mode .form-group input,
        body.dark-mode .form-group select {
            background: rgba(15, 23, 42, 0.5);
        }

        body.dark-mode .form-group input:focus {
            background: var(--bg-color);
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.15);
        }

        body.dark-mode .doc-icon {
            background: rgba(255, 255, 255, 0.08);
            color: #60a5fa;
        }

        body.dark-mode .form-group label i {
            color: #60a5fa;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 550px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            transform: scale(0.95) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid var(--border-color);
            overflow: hidden;
            padding: 0;
        }

        .modal.active .modal-content {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 20px 32px;
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            margin-bottom: 0;
        }

        body.dark-mode .modal-header {
            background: linear-gradient(to right, #1e293b, #334155);
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 16px;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        /* Toggle Switch Styling */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        input:checked + .slider {
            background-color: #10b981;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #10b981;
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        .modal-body {
            padding: 32px;
            background: var(--bg-color);
        }

        /* File Upload Styling */
        .file-upload-wrapper {
            position: relative;
            width: 100%;
            padding: 40px 20px;
            border: 2px dashed var(--border-color);
            border-radius: 16px;
            background: var(--card-bg);
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary-color);
            background: rgba(59, 130, 246, 0.05);
        }

        body.dark-mode .file-upload-wrapper:hover {
            border-color: #60a5fa;
            background: rgba(96, 165, 250, 0.05);
        }

        .file-upload-wrapper i {
            font-size: 40px;
            color: var(--text-muted);
            margin-bottom: 15px;
        }

        .file-upload-wrapper p {
            margin: 0;
            font-size: 14px;
            color: var(--text-main);
            font-weight: 500;
        }

        .file-upload-wrapper span {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .modal-footer {
            padding: 24px 32px;
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-cancel {
            padding: 12px 24px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-main);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .submit-btn {
            padding: 12px 32px;
            border-radius: 10px;
            border: none;
            background: var(--primary-color);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }
        
        body.dark-mode .submit-btn {
            background: #3b82f6;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../../../images/logo.png" alt="PH Logo" class="gov-logo">
                <div class="header-text">
                    <h1>LGU 3</h1>
                    <p>MSME Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../index.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-id-card"></i> <span>My Profile</span></a></li>
                    <li><a href="my-products.php"><i class="fas fa-box"></i> <span>My Products</span></a></li>
                    <li><a href="compliance-status.php"><i class="fas fa-check-double"></i> <span>Compliance
                                Status</span></a></li>
                    <li><a href="my-training.php"><i class="fas fa-certificate"></i> <span>My Training</span></a></li>
                    <li><a href="applied-incentives.php"><i class="fas fa-hand-holding-usd"></i> <span>Applied
                                Incentives</span></a></li>
                    <li><a href="market-insights.php"><i class="fas fa-chart-line"></i> <span>Market
                                Insights</span></a></li>
                </ul>

                <div class="nav-divider"></div>

                <ul>
                    <li><a href="profile-management.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="help-center.php"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
                    <li class="logout"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>Profile Management</h2>
                </div>
                <div class="header-right">
                    <div class="theme-toggle" title="Toggle Theme">
                        <div class="theme-switch">
                            <div class="theme-switch-handle">
                                <i class="fas fa-sun"></i>
                                <i class="fas fa-moon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge" style="<?php echo $notifCount > 0 ? '' : 'display: none;'; ?>"><?php echo $notifCount; ?></span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($fullName); ?></span>
                            <span class="user-role">Business Owner</span>
                        </div>
                        <?php 
                            $topbarLogo = !empty($_SESSION['business_logo']) 
                                ? '../../../' . $_SESSION['business_logo'] 
                                : "https://ui-avatars.com/api/?name=" . urlencode($fullName) . "&background=00205B&color=fff";
                        ?>
                        <img src="<?php echo $topbarLogo; ?>"
                            alt="User Avatar" class="avatar" id="topbar-avatar">
                    </div>
                    <button class="btn-primary" onclick="alert('Changes Saved Successfully!')">Save
                        Changes</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="profile-container">
                    <div class="profile-side">
                        <div class="avatar-upload">
                            <?php 
                                $logoSrc = !empty($user['logo_path']) 
                                    ? '../../../' . $user['logo_path'] 
                                    : "https://ui-avatars.com/api/?name=" . urlencode($user['business_name']) . "&background=00205B&color=fff&size=200";
                            ?>
                            <img src="<?php echo $logoSrc; ?>"
                                alt="Enterprise Logo" class="profile-avatar-large" id="business-logo-preview">
                            <h4 style="font-size: 18px; font-weight: 700;"><?php echo htmlspecialchars($user['business_name']); ?></h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 25px;">
                                <i class="fas fa-calendar-alt"></i> Founded: <?php echo htmlspecialchars($user['year_started'] ?? 'N/A'); ?>
                            </p>
                            <input type="file" id="logo-input" style="display: none;" accept="image/*">
                            <button class="btn-primary" style="width: 100%;" onclick="document.getElementById('logo-input').click()">
                                <i class="fas fa-camera"></i> Change Logo
                            </button>
                        </div>

                        <div class="form-section">
                            <div class="section-title" style="margin-bottom: 20px;">
                                <i class="fas fa-bolt"></i>
                                <h4 style="font-size: 16px; font-weight: 700;">Quick Actions</h4>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button class="btn-quick-action password" onclick="openChangePassword()">
                                    <span class="action-left">
                                        <i class="fas fa-key"></i>
                                        <span>Change Password</span>
                                    </span>
                                    <i class="fas fa-chevron-right chevron"></i>
                                </button>
                                <button class="btn-quick-action security" onclick="open2FASettings()">
                                    <span class="action-left">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>2FA Settings</span>
                                    </span>
                                    <i class="fas fa-chevron-right chevron"></i>
                                </button>
                                <div
                                    style="margin-top: 5px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                                    <button class="btn-quick-action danger" onclick="confirmDeactivate()">
                                        <span class="action-left">
                                            <i class="fas fa-trash-alt"></i>
                                            <span>Deactivate Account</span>
                                        </span>
                                        <i class="fas fa-chevron-right chevron"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-main">
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <i class="fas fa-building"></i>
                                    <h3>Enterprise Information</h3>
                                </div>
                            </div>

                            <form class="form-grid">
                                <div class="form-group full-width">
                                    <label><i class="fas fa-signature"></i> Registered Business Name</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user['business_name']); ?>"
                                        placeholder="Enter business name">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-tags"></i> Sector / Category</label>
                                    <select>
                                        <option <?php echo ($user['sector'] == 'Agriculture') ? 'selected' : ''; ?>>Agriculture</option>
                                        <option <?php echo ($user['sector'] == 'Food Processing') ? 'selected' : ''; ?>>Food Processing</option>
                                        <option <?php echo ($user['sector'] == 'Retail') ? 'selected' : ''; ?>>Retail</option>
                                        <option <?php echo ($user['sector'] == 'Manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
                                        <option <?php echo ($user['sector'] == 'Services') ? 'selected' : ''; ?>>Services</option>
                                        <option <?php echo (!in_array($user['sector'], ['Agriculture', 'Food Processing', 'Retail', 'Manufacturing', 'Services'])) ? 'selected' : ''; ?>>Others</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-id-card-alt"></i> DTI / SEC Number</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user['registration_number'] ?? ''); ?>" placeholder="Enter registration number">
                                </div>
                                <div class="form-group full-width">
                                    <label><i class="fas fa-map-marker-alt"></i> Business Address</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"
                                        placeholder="Enter complete address">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone-alt"></i> Contact Number</label>
                                    <input type="tel" value="" placeholder="Enter contact number">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-envelope"></i> Email Address</label>
                                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                                        placeholder="Enter email address">
                                </div>
                            </form>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <i class="fas fa-user-tie"></i>
                                    <h3>Owner / Representative Details</h3>
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Representative Name</label>
                                    <input type="text" value="<?php echo htmlspecialchars($fullName); ?>" placeholder="Enter full name">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-briefcase"></i> Position</label>
                                    <input type="text" value="Owner / Manager" placeholder="Enter position">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <i class="fas fa-file-shield"></i>
                                    <h3>Verified Documents</h3>
                                </div>
                            </div>
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">
                                Manage your business legal documents for verification.
                            </p>

                            <div id="document-list">
                                <?php foreach ($requiredDocs as $req): 
                                    $existingDoc = getDocByType($documents, $req['type']);
                                ?>
                                    <div class="doc-card <?php echo !$existingDoc ? 'dashed' : ''; ?>" 
                                         style="<?php echo !$existingDoc ? 'border-style: dashed; background: transparent; border-color: var(--border-color);' : ''; ?>">
                                        <div class="doc-info">
                                            <div class="doc-icon" style="<?php echo !$existingDoc ? 'background: var(--bg-color); color: var(--text-muted);' : ''; ?>">
                                                <i class="fas <?php echo $req['icon']; ?>"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;"><?php echo $req['type']; ?></div>
                                                <?php if ($existingDoc): ?>
                                                    <?php if ($existingDoc['status'] === 'verified'): ?>
                                                        <div style="font-size: 11px; color: var(--success-color);">
                                                            <i class="fas fa-check-circle"></i> Verified & Active
                                                        </div>
                                                    <?php elseif ($existingDoc['status'] === 'pending'): ?>
                                                        <div style="font-size: 11px; color: var(--warning-color);">
                                                            <i class="fas fa-clock"></i> Pending Verification
                                                        </div>
                                                    <?php else: ?>
                                                        <div style="font-size: 11px; color: var(--danger-color);">
                                                            <i class="fas fa-times-circle"></i> Rejected: <?php echo htmlspecialchars($existingDoc['remarks'] ?? 'Invalid file'); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div style="font-size: 11px; color: var(--text-muted);">Not Uploaded</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if ($existingDoc): ?>
                                            <div style="display: flex; gap: 10px;">
                                                <a href="../../../<?php echo $existingDoc['file_path']; ?>" target="_blank" class="btn-action-outline">View</a>
                                                <button class="btn-action-outline" onclick="openUploadModal('<?php echo addslashes($req['type']); ?>')">Replace</button>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn-primary" style="padding: 10px 18px; font-size: 12px;" onclick="openUploadModal('<?php echo addslashes($req['type']); ?>')">
                                                <i class="fas fa-upload"></i> Upload File
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload <span id="modal-doc-type">Document</span></h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="uploadForm">
                    <input type="hidden" name="document_type" id="hidden-doc-type">
                    <div class="file-upload-wrapper">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to browse or drag and drop</p>
                        <span>Supported formats: PDF, JPG, PNG (Max 5MB)</span>
                        <input type="file" name="document_file" id="document_file" required accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div id="file-name-preview" style="margin-top: 15px; font-size: 13px; color: var(--success-color); font-weight: 600; display: none; text-align: center;">
                        <i class="fas fa-file-alt"></i> <span id="selected-file-name"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" form="uploadForm" class="submit-btn" id="uploadBtn">Upload Document</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Logo Upload Logic
        document.getElementById('logo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('business-logo-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);

            // Upload
            const formData = new FormData();
            formData.append('logo_file', file);
            formData.append('action', 'upload-logo');

            fetch('../../../ajax/profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Ensure preview uses the final server path
                    if (data.logo_url) {
                        document.getElementById('business-logo-preview').src = data.logo_url;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.'
                });
            });
        });

        // Helper for theme-aware SweetAlert2
        function getSwalTheme() {
            const isDark = document.body.classList.contains('dark-mode');
            return {
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                confirmButtonColor: isDark ? '#3b82f6' : '#00205B',
                cancelButtonColor: isDark ? '#475569' : '#64748b'
            };
        }

        function openChangePassword() {
            const theme = getSwalTheme();
            const isDark = document.body.classList.contains('dark-mode');
            
            Swal.fire({
                title: 'Change Password',
                background: theme.background,
                color: theme.color,
                html: `
                    <div style="text-align: left;">
                        <label style="display: block; margin-bottom: 5px; font-size: 14px; color: ${theme.color}">Current Password</label>
                        <input type="password" id="current-pw" class="swal2-input" placeholder="Current password" 
                            style="width: 80%; margin: 5px auto 15px auto; display: block; background: ${isDark ? '#334155' : '#fff'}; color: ${theme.color}; border: 1px solid ${isDark ? '#475569' : '#d9d9d9'}">
                        
                        <label style="display: block; margin-bottom: 5px; font-size: 14px; color: ${theme.color}">New Password</label>
                        <input type="password" id="new-pw" class="swal2-input" placeholder="New password" 
                            style="width: 80%; margin: 5px auto 15px auto; display: block; background: ${isDark ? '#334155' : '#fff'}; color: ${theme.color}; border: 1px solid ${isDark ? '#475569' : '#d9d9d9'}">
                        
                        <label style="display: block; margin-bottom: 5px; font-size: 14px; color: ${theme.color}">Confirm New Password</label>
                        <input type="password" id="confirm-pw" class="swal2-input" placeholder="Confirm new password" 
                            style="width: 80%; margin: 5px auto 5px auto; display: block; background: ${isDark ? '#334155' : '#fff'}; color: ${theme.color}; border: 1px solid ${isDark ? '#475569' : '#d9d9d9'}">
                    </div>
                `,
                confirmButtonText: 'Update Password',
                confirmButtonColor: theme.confirmButtonColor,
                cancelButtonColor: theme.cancelButtonColor,
                showCancelButton: true,
                focusConfirm: false,
                preConfirm: () => {
                    const current = Swal.getPopup().querySelector('#current-pw').value;
                    const newPw = Swal.getPopup().querySelector('#new-pw').value;
                    const confirm = Swal.getPopup().querySelector('#confirm-pw').value;
                    
                    if (!current || !newPw || !confirm) {
                        Swal.showValidationMessage(`Please fill in all fields`);
                        return false;
                    }
                    if (newPw !== confirm) {
                        Swal.showValidationMessage(`New passwords do not match`);
                        return false;
                    }
                    if (newPw.length < 6) {
                        Swal.showValidationMessage(`Password must be at least 6 characters`);
                        return false;
                    }
                    return { current, newPw };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'change-password');
                    formData.append('current_password', result.value.current);
                    formData.append('new_password', result.value.newPw);

                    fetch('../../../ajax/auth.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                background: theme.background,
                                color: theme.color,
                                confirmButtonColor: theme.confirmButtonColor
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                background: theme.background,
                                color: theme.color,
                                confirmButtonColor: theme.confirmButtonColor
                            });
                        }
                    });
                }
            });
        }

        function open2FASettings() {
            const theme = getSwalTheme();
            const currentStatus = <?php echo (int)($user['two_factor_enabled'] ?? 1); ?>;
            const isDark = document.body.classList.contains('dark-mode');

            Swal.fire({
                title: '2FA Settings',
                background: theme.background,
                color: theme.color,
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <p style="margin-bottom: 20px; font-size: 14px; opacity: 0.8; line-height: 1.5;">
                            Two-Factor Authentication adds an extra layer of security. When enabled, a verification code will be sent to your email for every login attempt.
                        </p>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: ${isDark ? 'rgba(255,255,255,0.05)' : '#f8fafc'}; border-radius: 12px; border: 1px solid ${isDark ? '#334155' : '#e2e8f0'}">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 15px; font-weight: 600;">Email OTP</h4>
                                    <p style="margin: 0; font-size: 12px; color: ${isDark ? '#94a3b8' : '#64748b'}">Receive code via Email</p>
                                </div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="2fa-toggle" ${currentStatus ? 'checked' : ''}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save Changes',
                cancelButtonText: 'Cancel',
                confirmButtonColor: theme.confirmButtonColor,
                cancelButtonColor: theme.cancelButtonColor,
                showCloseButton: true,
                preConfirm: () => {
                    const isEnabled = Swal.getPopup().querySelector('#2fa-toggle').checked ? 1 : 0;
                    return { isEnabled };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const isEnabled = result.value.isEnabled;
                    
                    const formData = new FormData();
                    formData.append('action', 'update-2fa');
                    formData.append('enabled', isEnabled);

                    fetch('../../../ajax/auth.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: data.message,
                                background: theme.background,
                                color: theme.color,
                                confirmButtonColor: theme.confirmButtonColor
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: data.message,
                                background: theme.background,
                                color: theme.color,
                                confirmButtonColor: theme.confirmButtonColor
                            });
                        }
                    })
                    .catch(err => {
                        console.error('2FA Update Error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred.',
                            background: theme.background,
                            color: theme.color
                        });
                    });
                }
            });
        }

        function confirmDeactivate() {
            const theme = getSwalTheme();
            Swal.fire({
                title: 'Are you sure?',
                text: "Your account will be deactivated. You will need to contact support to reactivate it.",
                icon: 'warning',
                background: theme.background,
                color: theme.color,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: theme.cancelButtonColor,
                confirmButtonText: 'Yes, deactivate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'deactivate-account');

                    fetch('../../../ajax/auth.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deactivated',
                                text: 'Your account has been deactivated.',
                                icon: 'success',
                                background: theme.background,
                                color: theme.color,
                                confirmButtonColor: theme.confirmButtonColor
                            }).then(() => {
                                window.location.href = '../../../logout.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                background: theme.background,
                                color: theme.color,
                                confirmButtonColor: theme.confirmButtonColor
                            });
                        }
                    });
                }
            });
        }

        function openUploadModal(type) {
            document.getElementById('modal-doc-type').textContent = type;
            document.getElementById('hidden-doc-type').value = type;
            document.getElementById('uploadModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.remove('active');
            document.getElementById('uploadForm').reset();
            document.getElementById('file-name-preview').style.display = 'none';
        }

        document.getElementById('document_file').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                document.getElementById('selected-file-name').textContent = this.files[0].name;
                document.getElementById('file-name-preview').style.display = 'block';
            }
        });

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'upload-document');

            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            btn.disabled = true;

            fetch('../../../ajax/compliance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.message
                    });
                    btn.innerHTML = 'Upload Document';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'An error occurred during upload.'
                });
                btn.innerHTML = 'Upload Document';
                btn.disabled = false;
            });
        });
    </script>

    <script src="../../../js/main.js"></script>
</body>

</html>

