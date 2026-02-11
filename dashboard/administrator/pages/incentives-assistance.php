<?php
require_once '../../../includes/init.php';
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../../login.php');
}

// Page configuration
$pageTitle = "Incentives & Support - LGU 3";
$pageHeading = "Incentives & Support Programs";
$activePage = "incentives-assistance";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<style>
    /* Incentives Specific Styles */
    .assistance-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }

    .grant-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--border-color);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
    }

    .grant-card:hover {
        box-shadow: var(--shadow-md);
        transform: scale(1.01);
    }

    .grant-info {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .grant-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .grant-icon.blue {
        background: rgba(59, 130, 246, 0.1);
        color: #2563EB;
    }

    .grant-icon.green {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .grant-icon.purple {
        background: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
    }

    .grant-icon.orange {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }
</style>

<div class="assistance-grid">
    <!-- Main Content -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3>Available Incentive Programs</h3>
            <button class="btn-primary"><i class="fas fa-plus"></i> Create New Program</button>
        </div>

        <div class="grant-card">
            <div class="grant-info">
                <div class="grant-icon blue">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div>
                    <h4 style="margin: 0;">Startup Capital Grant</h4>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-muted);">Up to ₱50,000 for new MSMEs</p>
                    <div style="margin-top: 8px; font-size: 12px;">
                        <span style="color: var(--success-color);"><i class="fas fa-check-circle"></i> 12 Approved</span>
                        <span style="margin-left: 15px; color: var(--warning-color);"><i class="fas fa-clock"></i> 8 Pending</span>
                    </div>
                </div>
            </div>
            <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
        </div>

        <div class="grant-card">
            <div class="grant-info">
                <div class="grant-icon green">
                    <i class="fas fa-seedling"></i>
                </div>
                <div>
                    <h4 style="margin: 0;">Agriculture Modernization Fund</h4>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-muted);">Equipment & technology support</p>
                    <div style="margin-top: 8px; font-size: 12px;">
                        <span style="color: var(--success-color);"><i class="fas fa-check-circle"></i> 18 Approved</span>
                        <span style="margin-left: 15px; color: var(--warning-color);"><i class="fas fa-clock"></i> 5 Pending</span>
                    </div>
                </div>
            </div>
            <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
        </div>

        <div class="grant-card">
            <div class="grant-info">
                <div class="grant-icon purple">
                    <i class="fas fa-globe"></i>
                </div>
                <div>
                    <h4 style="margin: 0;">Export Market Development</h4>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-muted);">Trade fair participation & certification</p>
                    <div style="margin-top: 8px; font-size: 12px;">
                        <span style="color: var(--success-color);"><i class="fas fa-check-circle"></i> 9 Approved</span>
                        <span style="margin-left: 15px; color: var(--warning-color);"><i class="fas fa-clock"></i> 3 Pending</span>
                    </div>
                </div>
            </div>
            <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
        </div>

        <div class="grant-card">
            <div class="grant-info">
                <div class="grant-icon orange">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div>
                    <h4 style="margin: 0;">Digital Transformation Subsidy</h4>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-muted);">E-commerce & digital tools support</p>
                    <div style="margin-top: 8px; font-size: 12px;">
                        <span style="color: var(--success-color);"><i class="fas fa-check-circle"></i> 15 Approved</span>
                        <span style="margin-left: 15px; color: var(--warning-color);"><i class="fas fa-clock"></i> 11 Pending</span>
                    </div>
                </div>
            </div>
            <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <div class="card" style="padding: 24px; margin-bottom: 24px;">
            <h4 style="margin-bottom: 20px;">Program Overview</h4>
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 14px;">Total Programs</span>
                    <strong style="color: var(--primary-color);">8</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 14px;">Active Applications</span>
                    <strong style="color: var(--warning-color);">27</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 14px;">Approved This Month</span>
                    <strong style="color: var(--success-color);">54</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 14px;">Total Disbursed</span>
                    <strong style="color: var(--text-main);">₱ 2.4M</strong>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 24px;">
            <h5 style="margin-bottom: 15px; font-size: 14px;">Quick Actions</h5>
            <button class="btn-secondary" style="width: 100%; margin-bottom: 10px;">
                <i class="fas fa-file-export"></i> Export Report
            </button>
            <button class="btn-secondary" style="width: 100%;">
                <i class="fas fa-chart-pie"></i> View Analytics
            </button>
        </div>
    </div>
</div>

<?php 
$additionalJS = '<script>
    // Placeholder for future functionality
    console.log("Incentives & Assistance page loaded");
</script>';

include '../layouts/footer.php'; 
?>
