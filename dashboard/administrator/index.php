<?php 
require_once '../../includes/init.php'; 
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../login.php');
}

$db = db();

// Fetch Real Statistics
$total_msmes = $db->fetchOne("SELECT COUNT(*) as c FROM users WHERE role = 'user'")['c'];
$pending_approvals = $db->fetchOne("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")['c'];
$total_products = $db->fetchOne("SELECT COUNT(*) as c FROM user_products")['c'];
$active_profiles = $db->fetchOne("SELECT COUNT(*) as c FROM business_profiles")['c'];

// Fetch Recent Applications (Users who registered recently)
$recent_apps = $db->fetchAll("
    SELECT * FROM users 
    WHERE role = 'user' 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Logic for trends (hardcoded for now as we don't have historical data)
$msme_trend = "5.2% vs last month";
$pending_trend = "Urgent Action Required";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGU 3 - Administrative Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../../images/logo.png" alt="PH Logo" class="gov-logo">
                <div class="header-text">
                    <h1>LGU 3</h1>
                    <p>Administrative Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="#"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
                    <li><a href="pages/user-management.php"><i class="fas fa-user-shield"></i> <span>User
                                Management</span></a></li>
                    <li><a href="pages/product-registry.php"><i class="fas fa-building"></i> <span>Product & MSME
                                Registry</span></a></li>
                    <li><a href="pages/compliance-monitoring.php"><i class="fas fa-clipboard-check"></i>
                            <span>Compliance Monitoring</span></a></li>
                    <li><a href="pages/program-training.php"><i class="fas fa-graduation-cap"></i> <span>Program &
                                Training</span></a></li>
                    <li><a href="pages/market-opportunities.php"><i class="fas fa-handshake"></i> <span>Market & Trade
                                Management</span></a></li>
                    <li><a href="pages/incentives-assistance.php"><i class="fas fa-gift"></i> <span>Incentives &
                                Support</span></a></li>
                    <li><a href="pages/reports-analytics.php"><i class="fas fa-chart-bar"></i> <span>Reports &
                                Analytics</span></a></li>
                </ul>

                <div class="nav-divider"></div>

                <ul>
                    <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="#"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
                    <li class="logout"><a href="../../ajax/auth.php?action=logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>Overview Dashboard</h2>
                </div>

                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search records...">
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
                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-users-viewfinder"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Total MSMEs</h3>
                            <p class="stat-number"><?php echo number_format($total_msmes); ?></p>
                            <span class="stat-trend positive"><i class="fas fa-arrow-up"></i> <?php echo $msme_trend; ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Pending Approvals</h3>
                            <p class="stat-number"><?php echo number_format($pending_approvals); ?></p>
                            <span class="stat-trend negative"><i class="fas fa-clock"></i> <?php echo $pending_trend; ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon yellow">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Total Products</h3>
                            <p class="stat-number"><?php echo number_format($total_products); ?></p>
                            <span class="stat-trend positive"><i class="fas fa-check-circle"></i> Registered Products</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Active Profiles</h3>
                            <p class="stat-number"><?php echo number_format($active_profiles); ?></p>
                            <span class="stat-trend positive"><i class="fas fa-smile"></i> Completed Profiles</span>
                        </div>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="main-grid">
                    <div class="recent-requests card">
                        <div class="card-header">
                            <h3>Recent Applications</h3>
                            <a href="#" class="view-all">View All</a>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ref No.</th>
                                        <th>Applicant</th>
                                        <th>Service Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_apps)): ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 30px;">No recent applications found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_apps as $app): ?>
                                            <tr>
                                                <td class="ref">#APP-<?php echo str_pad($app['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                                <td>
                                                    <div class="table-user">
                                                        <div class="name"><?php echo htmlspecialchars($app['firstname'] . ' ' . $app['lastname']); ?></div>
                                                        <div class="email"><?php echo htmlspecialchars($app['email']); ?></div>
                                                    </div>
                                                </td>
                                                <td><?php echo !empty($app['business_name']) ? htmlspecialchars($app['business_name']) : 'New Registration'; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                                                <td>
                                                    <?php 
                                                        $statusClass = 'status-' . strtolower($app['status']);
                                                        echo '<span class="status ' . $statusClass . '">' . ucfirst($app['status']) . '</span>';
                                                    ?>
                                                </td>
                                                <td><button class="btn-action" onclick="reviewApplication(<?php echo $app['id']; ?>)">Review</button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="project-tracker card">
                        <div class="card-header">
                            <h3>Regional Progress</h3>
                        </div>
                        <div class="project-list">
                            <div class="project-item">
                                <div class="project-info">
                                    <span>Luzon Digitalization</span>
                                    <span>85%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: 85%;"></div>
                                </div>
                            </div>
                            <div class="project-item">
                                <div class="project-info">
                                    <span>Visayas Infra Upgrade</span>
                                    <span>42%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress orange" style="width: 42%;"></div>
                                </div>
                            </div>
                            <div class="project-item">
                                <div class="project-info">
                                    <span>Mindanao Health System</span>
                                    <span>68%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress blue" style="width: 68%;"></div>
                                </div>
                            </div>
                            <div class="project-item">
                                <div class="project-info">
                                    <span>National ID Rollout</span>
                                    <span>92%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress green" style="width: 92%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="system-announcement">
                            <h4><i class="fas fa-info-circle"></i> System Notice</h4>
                            <p>Monthly auditing period starts tomorrow. Please ensure all records are updated.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="reviewModal" class="modal-overlay">
        <div class="logout-modal" style="width: 600px; text-align: left; padding: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Review Application</h2>
                <button class="btn-close-notif" onclick="closeReviewModal()"><i class="fas fa-times"></i></button>
            </div>
            
            <div id="reviewContent" class="notif-modal-body" style="max-height: 50vh;">
                <!-- Content will be loaded here -->
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin"></i> Loading details...
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button class="btn-logout-cancel" style="background: var(--danger-color); color: white;" onclick="updateAppStatus('rejected')">Reject Application</button>
                <button class="btn-logout-confirm" style="background: var(--success-color); flex: 2;" onclick="updateAppStatus('active')">Approve & Activate</button>
            </div>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script>
        let currentReviewId = null;

        function reviewApplication(userId) {
            currentReviewId = userId;
            const modal = document.getElementById('reviewModal');
            const content = document.getElementById('reviewContent');
            
            modal.classList.add('active');
            content.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading details...</div>';

            fetch(`../../ajax/auth.php?action=get-user-details-review&userId=${userId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const u = data.user;
                        const p = data.profile;
                        const prods = data.products;

                        content.innerHTML = `
                            <div class="user-details" style="margin-bottom: 20px;">
                                <h4 style="color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 5px; margin-bottom: 10px;">Personal Information</h4>
                                <p><strong>Name:</strong> ${u.firstname} ${u.lastname}</p>
                                <p><strong>Email:</strong> ${u.email}</p>
                                <p><strong>Status:</strong> <span class="status status-${u.status}">${u.status.toUpperCase()}</span></p>
                            </div>

                            ${p ? `
                            <div class="business-details" style="margin-bottom: 20px;">
                                <h4 style="color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 5px; margin-bottom: 10px;">Business Profile</h4>
                                <p><strong>Business Name:</strong> ${p.business_name || u.business_name || 'N/A'}</p>
                                <p><strong>Sector:</strong> ${p.sector || 'N/A'}</p>
                                <p><strong>Address:</strong> ${p.address || 'N/A'}</p>
                                <p><strong>Reg Number:</strong> ${p.registration_number || 'N/A'}</p>
                                <p><strong>Workers:</strong> ${p.number_of_workers || '0'}</p>
                            </div>
                            ` : '<p style="color: var(--text-muted); font-style: italic;">No business profile completed yet.</p>'}

                            ${prods && prods.length > 0 ? `
                            <div class="products-details">
                                <h4 style="color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 5px; margin-bottom: 10px;">Products</h4>
                                ${prods.map(pr => `
                                    <div style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 5px;">
                                        <strong>${pr.product_name}</strong> (${pr.category})<br>
                                        <small>${pr.description}</small>
                                    </div>
                                `).join('')}
                            </div>
                            ` : ''}
                        `;
                    } else {
                        content.innerHTML = `<p style="color: var(--danger-color);">${data.message}</p>`;
                    }
                });
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.remove('active');
        }

        function updateAppStatus(newStatus) {
            if (!currentReviewId) return;
            
            const btn = event.currentTarget;
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Processing...';

            // We use update-msme but only changing status
            const formData = new FormData();
            formData.append('userId', currentReviewId);
            formData.append('status', newStatus);
            
            // Fetch existing data for update-msme requirements if needed
            // But let's check auth.php update-msme again. It needs fullName and email.
            // I'll modify auth.php to allow partial updates if just ID and status are provided.
            
            fetch(`../../ajax/auth.php?action=update-status-simple`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `userId=${currentReviewId}&status=${newStatus}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/main.js"></script>
</body>

</html>
