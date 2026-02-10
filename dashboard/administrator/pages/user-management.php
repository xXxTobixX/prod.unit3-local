<?php 
require_once '../../../includes/init.php'; 

// Check if user is logged in and has admin-level role
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../../login.php');
}

$db = db();

// Fetch only MSME/Vendor users
$users = $db->fetchAll("SELECT id, firstname, lastname, email, role, status, business_name FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        /* Custom styles for User Management */
        .page-actions {
            display: flex;
            gap: 12px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 32, 91, 0.2);
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-active, .status-pending {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-inactive, .status-rejected, .status-deactivated {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .role-badge {
            background: var(--bg-color);
            color: var(--text-main);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .source-badge {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
            margin-top: 4px;
        }

        .access-level {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex !important;
            opacity: 1;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 32px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
            position: relative;
            color: var(--text-main);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 32, 91, 0.05);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-secondary {
            background: #334155;
            color: #F8FAFC;
            border: 1px solid #475569;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #475569;
            border-color: #64748B;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
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
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--success-color);
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action.view {
            color: var(--primary-color);
            background: rgba(0, 32, 91, 0.05);
        }

        .btn-action.view:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-action.edit {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.05);
        }

        .btn-action.edit:hover {
            background: #f59e0b;
            color: white;
        }

        /* Detail View Styles */
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-muted);
        }

        .detail-value {
            font-weight: 500;
            color: var(--text-main);
        }

        /* Notification Toast System */
        .toast-container {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 10000;
        }

        .toast {
            background: var(--card-bg);
            border-left: 5px solid var(--primary-color);
            color: var(--text-main);
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 320px;
            animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            border: 1px solid var(--border-color);
        }

        .toast.success {
            border-left-color: var(--success-color);
        }

        .toast.error {
            border-left-color: var(--danger-color);
        }

        .toast i {
            font-size: 22px;
        }

        .toast.success i {
            color: var(--success-color);
        }

        .toast.error i {
            color: var(--danger-color);
        }

        .toast-content h4 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 2px;
            color: var(--text-main);
        }

        .toast-content p {
            font-size: 13px;
            color: var(--text-muted);
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }

        .toast.fade-out {
            animation: toastSlideOut 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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
                    <p>Administrative Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../index.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-user-shield"></i> <span>User Management</span></a>
                    </li>
                    <li><a href="product-registry.php"><i class="fas fa-building"></i> <span>Product & MSME
                                Registry</span></a></li>
                    <li><a href="compliance-monitoring.php"><i class="fas fa-clipboard-check"></i> <span>Compliance
                                Monitoring</span></a></li>
                    <li><a href="program-training.php"><i class="fas fa-graduation-cap"></i> <span>Program &
                                Training</span></a></li>
                    <li><a href="market-opportunities.php"><i class="fas fa-handshake"></i> <span>Market & Trade
                                Management</span></a></li>
                    <li><a href="incentives-assistance.php"><i class="fas fa-gift"></i> <span>Incentives &
                                Support</span></a></li>
                    <li><a href="reports-analytics.php"><i class="fas fa-chart-bar"></i> <span>Reports &
                                Analytics</span></a></li>
                </ul>

                <div class="nav-divider"></div>

                <ul>
                    <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="#"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
                    <li class="logout"><a href="../../../ajax/auth.php?action=logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>User Management</h2>
                </div>

                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search users by name, email or role...">
                    </div>
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
                        <span class="badge" style="opacity: 0;">0</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=00205B&color=fff" alt="User Avatar"
                            class="avatar">
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="card">
                    <div class="card-header">
                        <h3>System Users Registry</h3>
                        <div class="page-actions">
                            <button class="btn-primary" onclick="openModal('add')">
                                <i class="fas fa-plus"></i> Create New User
                            </button>
                        </div>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>User Details</th>
                                    <th>Business Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="user-table-body">
                                <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                        No registered MSMEs found.
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td>U-<?php echo $u['id']; ?></td>
                                        <td>
                                            <div class="table-user">
                                                <div class="name"><?php echo htmlspecialchars(html_entity_decode($u['firstname'] . ' ' . $u['lastname'])); ?></div>
                                                <div class="email"><?php echo htmlspecialchars(html_entity_decode($u['email'])); ?></div>
                                            </div>
                                        </td>
                                        <td><span class="access-level"><?php echo htmlspecialchars(html_entity_decode($u['business_name'])); ?></span></td>
                                        <td><span class="role-badge"><?php echo htmlspecialchars(html_entity_decode(ucfirst($u['role']))); ?></span></td>
                                        <td>
                                            <span class="status-pill status-<?php echo strtolower($u['status']); ?>">
                                                <?php echo ucfirst($u['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action view" title="View Details" onclick="viewUser(<?php echo $u['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-action edit" title="Edit User" onclick="editUser(<?php echo $u['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Create New User</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="userForm">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" placeholder="Enter user's full name" required>
                </div>
                <div class="form-group">
                    <label for="businessName">Business Name</label>
                    <input type="text" id="businessName" placeholder="Enter business name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="Enter official email" required>
                </div>
                <div class="form-group">
                    <label for="role">User Role</label>
                    <select id="role" required>
                        <option value="user">Vendor/Producer</option>
                        <option value="MSME Coordinator">MSME Coordinator</option>
                        <option value="Market Analyst">Market Analyst</option>
                    </select>
                </div>
                <div id="statusGroup" class="form-group" style="display: none;">
                    <label for="status">Account Status</label>
                    <select id="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>MSME Profile Details</h3>
                <button class="close-modal" onclick="closeViewModal()">&times;</button>
            </div>
            <div id="userDetailContent">
                <!-- Details populated by JS -->
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="../../../js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('userModal');
            const viewModal = document.getElementById('viewModal');
            const userForm = document.getElementById('userForm');
            
            // Raw data from PHP
            const usersData = <?php echo json_encode($users, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

            // Helper to decode HTML entities
            const decodeHTMLEntities = (text) => {
                const textArea = document.createElement('textarea');
                textArea.innerHTML = text;
                return textArea.value;
            };

            window.viewUser = function(id) {
                const user = usersData.find(u => u.id == id);
                if (user) {
                    const content = document.getElementById('userDetailContent');
                    const safeStatus = (user.status || 'unknown').toLowerCase();
                    
                    const fullName = decodeHTMLEntities(`${user.firstname} ${user.lastname}`);
                    const businessName = decodeHTMLEntities(user.business_name || 'N/A');
                    const email = decodeHTMLEntities(user.email);
                    
                    content.innerHTML = `
                        <div class="detail-row">
                            <span class="detail-label">Full Name:</span>
                            <span class="detail-value">${fullName}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">${email}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Business Name:</span>
                            <span class="detail-value">${businessName}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Current Role:</span>
                            <span class="detail-value">${(user.role || 'user').charAt(0).toUpperCase() + (user.role || 'user').slice(1)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value"><span class="status-pill status-${safeStatus}">${user.status || 'Unknown'}</span></span>
                        </div>
                    `;
                    viewModal.classList.add('active');
                }
            };

            window.editUser = function(id) {
                const user = usersData.find(u => u.id == id);
                if (user) {
                    document.getElementById('modalTitle').innerText = 'Edit MSME Details';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('fullName').value = decodeHTMLEntities(`${user.firstname} ${user.lastname}`);
                    document.getElementById('businessName').value = decodeHTMLEntities(user.business_name || '');
                    document.getElementById('email').value = decodeHTMLEntities(user.email);
                    document.getElementById('role').value = user.role;
                    document.getElementById('status').value = user.status;
                    
                    document.getElementById('statusGroup').style.display = 'block';
                    editModal.classList.add('active');
                }
            };

            window.openModal = function(type) {
                if (type === 'add') {
                    document.getElementById('modalTitle').innerText = 'Create New User';
                    userForm.reset();
                    document.getElementById('userId').value = '';
                    document.getElementById('statusGroup').style.display = 'none';
                    editModal.classList.add('active');
                }
            };

            window.closeModal = function() {
                editModal.classList.remove('active');
            };

            window.closeViewModal = function() {
                viewModal.classList.remove('active');
            };

            // Global click to close
            window.addEventListener('click', function(event) {
                if (event.target == editModal) closeModal();
                if (event.target == viewModal) closeViewModal();
            });

            // Form submission
            if (userForm) {
                userForm.onsubmit = function (e) {
                    e.preventDefault();
                    
                    const id = document.getElementById('userId').value;
                    if (!id) {
                        showNotification('Feature Pending', 'Adding new users from this dashboard is not yet fully implemented securely.', 'error');
                        return;
                    }

                    const submitBtn = userForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerText;
                    
                    // Show processing state
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Updating Registry...';

                    const formData = new FormData();
                    formData.append('userId', id);
                    formData.append('fullName', document.getElementById('fullName').value);
                    formData.append('businessName', document.getElementById('businessName').value);
                    formData.append('email', document.getElementById('email').value);
                    formData.append('role', document.getElementById('role').value);
                    formData.append('status', document.getElementById('status').value);

                    fetch('../../../ajax/auth.php?action=update-msme', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP Error: ${response.status}`);
                        }
                        return response.text(); // Get as text first for debugging
                    })
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                showNotification('Update Successful', data.message, 'success');
                                setTimeout(() => {
                                    closeModal();
                                    window.location.reload();
                                }, 1500);
                            } else {
                                showNotification('Update Failed', data.message, 'error');
                                submitBtn.disabled = false;
                                submitBtn.innerText = originalText;
                            }
                        } catch (e) {
                            console.error('JSON Parse Error:', text);
                            showNotification('System Error', 'Server returned an invalid response format.', 'error');
                            submitBtn.disabled = false;
                            submitBtn.innerText = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        showNotification('Network Error', error.message || 'Could not communicate with the database server.', 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    });
                };
            }

            /**
             * Animated Notification System Implementation
             */
            function showNotification(title, message, type = 'success') {
                let container = document.querySelector('.toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'toast-container';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

                toast.innerHTML = `
                    <i class="fas ${icon}"></i>
                    <div class="toast-content">
                        <h4>${title}</h4>
                        <p>${message}</p>
                    </div>
                `;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('fade-out');
                    setTimeout(() => {
                        toast.remove();
                        if (container.childNodes.length === 0) container.remove();
                    }, 400);
                }, 4000);
            }
        });
    </script>
</body>

</html>
