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

        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-inactive {
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
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
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
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar (Reused from dashboard/administrator/index.php) -->
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
                    <li class="logout"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
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
                        <span class="badge">5</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name">Hon. Admin</span>
                            <span class="user-role">Administrator</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Admin&background=00205B&color=fff" alt="User Avatar"
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
                                    <th>Role</th>
                                    <th>Access Level</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="user-table-body">
                                <tr>
                                    <td>U-1001</td>
                                    <td>
                                        <div class="table-user">
                                            <div class="name">Juan Dela Cruz</div>
                                            <div class="email">juan.dc@lgu3.gov.ph</div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge">Super Admin</span></td>
                                    <td><span class="access-level">Full Permissions</span></td>
                                    <td><span class="status-pill status-active">Active</span></td>
                                    <td>
                                        <button class="btn-action" title="Edit User" onclick="openModal('edit', 1001)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>U-1002</td>
                                    <td>
                                        <div class="table-user">
                                            <div class="name">Maria Santos</div>
                                            <div class="email">m.santos@lgu3.gov.ph</div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge">Content Editor</span></td>
                                    <td><span class="access-level">Update Records</span></td>
                                    <td><span class="status-pill status-active">Active</span></td>
                                    <td>
                                        <button class="btn-action" title="Edit User" onclick="openModal('edit', 1002)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>U-1003</td>
                                    <td>
                                        <div class="table-user">
                                            <div class="name">Ricardo Reyes</div>
                                            <div class="email">r.reyes@vendor.com</div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge">Vendor Admin</span></td>
                                    <td><span class="access-level">Shop Management</span></td>
                                    <td><span class="status-pill status-inactive">Deactivated</span></td>
                                    <td>
                                        <button class="btn-action" title="Edit User" onclick="openModal('edit', 1003)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
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
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="Enter official email" required>
                </div>
                <div class="form-group">
                    <label for="role">User Role</label>
                    <select id="role" required>
                        <option value="">Select Role</option>
                        <option value="LGU Administrator">LGU Administrator</option>
                        <option value="Export Development Officer">Export Development Officer</option>
                        <option value="Product Quality Specialist">Product Quality Specialist</option>
                        <option value="MSME Coordinator">MSME Coordinator</option>
                        <option value="Training & Compliance Officer">Training & Compliance Officer</option>
                        <option value="Market Analyst">Market Analyst</option>
                        <option value="Vendor/Producer">Vendor/Producer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="accessLevel">Access Level</label>
                    <select id="accessLevel" required>
                        <option value="">Select Access Level</option>
                        <option value="Full System Access">Full System Access</option>
                        <option value="Product Registry Management">Product Registry Management</option>
                        <option value="Export Documentation">Export Documentation</option>
                        <option value="Training Program Management">Training Program Management</option>
                        <option value="Market Opportunities">Market Opportunities</option>
                        <option value="Compliance Monitoring">Compliance Monitoring</option>
                        <option value="Vendor Portal Access">Vendor Portal Access</option>
                        <option value="View Reports Only">View Reports Only</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save User Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../js/main.js"></script>
    <script>
        const modal = document.getElementById('userModal');
        const userForm = document.getElementById('userForm');

        function openModal(type, id = null) {
            const title = document.getElementById('modalTitle');
            if (type === 'add') {
                title.innerText = 'Create New User';
                userForm.reset();
                document.getElementById('userId').value = '';
            } else {
                title.innerText = 'Edit User Details';
                // Mock data population
                if (id == 1001) {
                    document.getElementById('fullName').value = 'Juan Dela Cruz';
                    document.getElementById('email').value = 'juan.dc@lgu3.gov.ph';
                    document.getElementById('role').value = 'Super Admin';
                    document.getElementById('accessLevel').value = 'Full Permissions';
                }
            }
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function toggleUserStatus(id, isActive) {
            // Find the status pill in the same row
            const rows = document.querySelectorAll('#user-table-body tr');
            rows.forEach(row => {
                if (row.cells[0].innerText.includes(id)) {
                    const statusPill = row.querySelector('.status-pill');
                    if (isActive) {
                        statusPill.innerText = 'Active';
                        statusPill.className = 'status-pill status-active';
                    } else {
                        statusPill.innerText = 'Deactivated';
                        statusPill.className = 'status-pill status-inactive';
                    }
                }
            });
            console.log(`User ${id} status changed to: ${isActive ? 'Active' : 'Inactive'}`);
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        userForm.onsubmit = function (e) {
            e.preventDefault();
            alert('User information updated successfully!');
            closeModal();
        }
    </script>
</body>

</html>
