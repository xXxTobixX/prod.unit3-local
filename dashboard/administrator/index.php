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

// Page configuration
$pageTitle = "LGU 3 - Administrative Dashboard";
$pageHeading = "Overview Dashboard";
$activePage = "dashboard";
$baseUrl = "pages/";

include 'layouts/header.php';
include 'layouts/sidebar.php';
include 'layouts/navbar.php';
?>

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
                    <span class="project-name">Region I - Ilocos Norte</span>
                    <span class="project-progress">75% Complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 75%; background: var(--success-color);"></div>
                </div>
            </div>

            <div class="project-item">
                <div class="project-info">
                    <span class="project-name">Region II - Cagayan Valley</span>
                    <span class="project-progress">60% Complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%; background: var(--warning-color);"></div>
                </div>
            </div>

            <div class="project-item">
                <div class="project-info">
                    <span class="project-name">Region III - Central Luzon</span>
                    <span class="project-progress">90% Complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 90%; background: var(--success-color);"></div>
                </div>
            </div>

            <div class="project-item">
                <div class="project-info">
                    <span class="project-name">NCR - Metro Manila</span>
                    <span class="project-progress">45% Complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 45%; background: var(--danger-color);"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Announcements -->
<div class="system-announcement">
    <i class="fas fa-bullhorn"></i>
    <div>
        <h4>System Announcement</h4>
        <p>New MSME registration portal is now live. Encourage local businesses to register and avail government support programs.</p>
    </div>
</div>

<?php 
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
    function reviewApplication(appId) {
        window.location.href = "pages/user-management.php?id=" + appId;
    }
    
    function updateAppStatus(newStatus) {
        if (!currentReviewId) return;
        
        const btn = event.currentTarget;
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = "Processing...";

        const formData = new FormData();
        formData.append("userId", currentReviewId);
        formData.append("status", newStatus);
        
        fetch(`../../ajax/auth.php?action=update-status-simple`, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `userId=${currentReviewId}&status=${newStatus}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert("Error: " + data.message);
                btn.disabled = false;
                btn.innerText = originalText;
            }
        });
    }
</script>';

include 'layouts/footer.php'; 
?>
