<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 

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
    foreach ($docs as $doc) {
        if ($doc['document_type'] === $type) return $doc;
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

        /* Plain Quick Action Buttons */
        .btn-quick-action {
            background: var(--bg-color);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background 0.2s;
            width: 100%;
            text-align: left;
        }

        .btn-quick-action:hover {
            background: var(--border-color);
        }

        .btn-quick-action i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .btn-quick-action.danger {
            color: var(--danger-color);
        }

        .btn-quick-action.danger i {
            color: var(--danger-color);
        }

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
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
            animation: modalSlideUp 0.3s ease-out;
        }

        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-muted);
            cursor: pointer;
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
                    <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="#"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
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
                        <span class="badge">2</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($fullName); ?></span>
                            <span class="user-role">Business Owner</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=00205B&color=fff"
                            alt="User Avatar" class="avatar">
                    </div>
                    <button class="btn-primary" onclick="alert('Changes Saved Successfully!')">Save
                        Changes</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="profile-container">
                    <div class="profile-side">
                        <div class="avatar-upload">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['business_name']); ?>&background=00205B&color=fff&size=200"
                                alt="Enterprise Logo" class="profile-avatar-large">
                            <h4 style="font-size: 18px; font-weight: 700;"><?php echo htmlspecialchars($user['business_name']); ?></h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 25px;">
                                <i class="fas fa-calendar-alt"></i> Founded: <?php echo htmlspecialchars($user['year_started'] ?? 'N/A'); ?>
                            </p>
                            <button class="btn-primary" style="width: 100%;">
                                <i class="fas fa-camera"></i> Change Logo
                            </button>
                        </div>

                        <div class="form-section">
                            <div class="section-title" style="margin-bottom: 20px;">
                                <i class="fas fa-bolt"></i>
                                <h4 style="font-size: 16px; font-weight: 700;">Quick Actions</h4>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <button class="btn-quick-action">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                                <button class="btn-quick-action">
                                    <i class="fas fa-shield-alt"></i> 2FA Settings
                                </button>
                                <div
                                    style="margin-top: 10px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                                    <button class="btn-quick-action danger">
                                        <i class="fas fa-trash-alt"></i> Deactivate Account
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
                                                <button class="btn-action-outline" onclick="openUploadModal('<?php echo $req['type']; ?>')">Replace</button>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn-primary" style="padding: 10px 18px; font-size: 12px;" onclick="openUploadModal('<?php echo $req['type']; ?>')">
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
            <form id="uploadForm">
                <input type="hidden" name="document_type" id="hidden-doc-type">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Select File (PDF, JPG, PNG - Max 5MB)</label>
                    <input type="file" name="document_file" id="document_file" required accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="button" class="btn-secondary" style="flex: 1;" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary" style="flex: 1;">Upload Document</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openUploadModal(type) {
            document.getElementById('modal-doc-type').textContent = type;
            document.getElementById('hidden-doc-type').value = type;
            document.getElementById('uploadModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.remove('active');
            document.getElementById('uploadForm').reset();
        }

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

