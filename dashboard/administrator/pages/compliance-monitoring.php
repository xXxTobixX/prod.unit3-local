<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Monitoring - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        /* Custom Compliance Styles */
        .compliance-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 24px;
        }

        .checklist-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .checklist-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .checklist-item:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        body.dark-mode .checklist-item:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .checklist-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .check-btn {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .check-btn.checked {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .doc-preview {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            font-size: 13px;
            text-decoration: none;
            font-weight: 600;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .status-compliant {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .status-expired {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .validation-item {
            padding: 12px;
            border-radius: 10px;
            background: var(--bg-color);
            margin-bottom: 12px;
        }

        .validation-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .progress-mini {
            height: 6px;
            background: var(--border-color);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-mini-bar {
            height: 100%;
            background: var(--primary-color);
        }

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
            max-width: 600px;
            position: relative;
            color: var(--text-main);
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
                    <li><a href="user-management.php"><i class="fas fa-user-shield"></i> <span>User
                                Management</span></a></li>
                    <li><a href="product-registry.php"><i class="fas fa-building"></i> <span>Product & MSME
                                Registry</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-clipboard-check"></i> <span>Compliance
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

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>Compliance & Permit Monitoring</h2>
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
                        <span class="badge">3</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name">Hon. Admin</span>
                            <span class="user-role">Administrator</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Admin&background=00205B&color=fff" alt="User Avatar"
                            class="avatar">
                    </div>
                    <div class="search-bar"><i class="fas fa-search"></i><input type="text"
                            placeholder="Search MSME Records..."></div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="compliance-grid">
                    <!-- Permit Checklist -->
                    <div class="checklist-card">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h3>Permit Validation Checklist</h3>
                            <div style="color: var(--text-muted); font-size: 14px;">Enterprise: <strong
                                    style="color: var(--primary-color)">Bukidnon Farms Co.</strong></div>
                        </div>

                        <div class="checklist-item">
                            <div class="checklist-info">
                                <div class="check-btn checked" onclick="toggleCheck(this)"><i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600;">DTI Business Name Registration</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">Validity: Dec 2028</div>
                                </div>
                            </div>
                            <a href="#" class="doc-preview"><i class="fas fa-file-pdf"></i> View Doc</a>
                        </div>

                        <div class="checklist-item">
                            <div class="checklist-info">
                                <div class="check-btn checked" onclick="toggleCheck(this)"><i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600;">Mayor's Business Permit 2024</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">Validity: Dec 2024</div>
                                </div>
                            </div>
                            <a href="#" class="doc-preview"><i class="fas fa-file-pdf"></i> View Doc</a>
                        </div>

                        <div class="checklist-item">
                            <div class="checklist-info">
                                <div class="check-btn" onclick="toggleCheck(this)"><i class="fas fa-check"
                                        style="display:none"></i></div>
                                <div>
                                    <div style="font-weight: 600;">FDA Certificate of Product Registration</div>
                                    <div style="font-size: 12px; color: var(--danger-color);">Requirement for Export
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn-action" style="font-size: 11px;">Request Upload</button>
                            </div>
                        </div>

                        <div class="checklist-item" style="border: none;">
                            <div class="checklist-info">
                                <div class="check-btn checked" onclick="toggleCheck(this)"><i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600;">Tax Clearance Certificate</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">Current Status: Cleared
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="doc-preview"><i class="fas fa-file-pdf"></i> View Doc</a>
                        </div>

                        <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px;">
                            <button class="btn-secondary"><i class="fas fa-flag"></i> Flag Revision</button>
                            <button class="btn-primary" onclick="validateAll()"><i class="fas fa-check-circle"></i>
                                Finalize Validation</button>
                        </div>
                    </div>

                    <!-- Compliance Status Summary -->
                    <div>
                        <div class="card" style="padding: 24px; margin-bottom: 24px;">
                            <h4 style="margin-bottom: 20px;">Permit Health Status</h4>
                            <div style="text-align: center;">
                                <div style="font-size: 48px; font-weight: 800; color: var(--success-color);">85%</div>
                                <p style="color: var(--text-muted); font-size: 13px;">Compliance Rate (Regional)</p>
                            </div>
                            <div style="margin-top: 24px;">
                                <div class="validation-item">
                                    <div class="validation-header"><span>Document Completion</span><span>3/4</span>
                                    </div>
                                    <div class="progress-mini">
                                        <div class="progress-mini-bar" style="width: 75%;"></div>
                                    </div>
                                </div>
                                <div class="validation-item">
                                    <div class="validation-header"><span>Export Eligibility</span><span
                                            style="color: var(--warning-color)">Pending FDA</span></div>
                                    <div class="progress-mini">
                                        <div class="progress-mini-bar"
                                            style="width: 40%; background: var(--warning-color);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" style="padding: 24px;">
                            <h4 style="margin-bottom: 16px;">Monitoring Alerts</h4>
                            <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                                <div style="color: var(--danger-color); font-size: 20px;"><i
                                        class="fas fa-exclamation-triangle"></i></div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600;">Sanitary Permit Expiring</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Luzon Crafts Hub - 15 days
                                        left</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <div style="color: var(--primary-color); font-size: 20px;"><i
                                        class="fas fa-info-circle"></i></div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600;">Renewal Notice Sent</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Visayas Delicacies - Feb 08
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../../js/main.js"></script>
    <script>
        function toggleCheck(btn) {
            btn.classList.toggle('checked');
            const icon = btn.querySelector('i');
            icon.style.display = btn.classList.contains('checked') ? 'block' : 'none';
        }

        function validateAll() {
            alert('Enterprise Compliance Validation successful. Registry has been updated.');
        }
    </script>
</body>

</html>
