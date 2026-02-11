<?php
require_once '../../../includes/init.php';
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../../login.php');
}

// Page configuration
$pageTitle = "Program & Training - LGU 3";
$pageHeading = "Programs & Capacity Building";
$activePage = "program-training";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<style>
    /* Custom Programs Styles */
    .programs-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .program-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: all 0.3s;
        display: flex;
        gap: 20px;
    }

    .program-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .program-img {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
    }

    .program-info {
        flex: 1;
    }

    .program-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-training {
        background: rgba(59, 130, 246, 0.1);
        color: #2563EB;
    }

    .badge-event {
        background: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
    }

    .badge-workshop {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div></div>
    <button class="btn-primary" onclick="openCreateProgramModal()"><i class="fas fa-plus"></i> Create New Program</button>
</div>

<div class="programs-container">
    <!-- Training & Trade Fairs -->
    <div>
        <div class="section-title">
            <h3>Available Training Programs</h3>
        </div>

        <div class="program-card">
            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=300" class="program-img">
            <div class="program-info">
                <span class="program-badge badge-training">Skills Development</span>
                <h4>Digital Marketing for MSMEs</h4>
                <p style="font-size: 14px; color: var(--text-muted); margin: 8px 0;">Learn how to leverage social media and e-commerce platforms to scale your reach.</p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                    <div style="font-size: 13px;"><i class="fas fa-calendar"></i> Starts: Feb 20, 2024 | <i class="fas fa-users"></i> 45 Enrolled</div>
                    <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
                </div>
            </div>
        </div>

        <div class="program-card">
            <img src="https://images.unsplash.com/photo-1556761175-4b46a572b786?w=300" class="program-img">
            <div class="program-info">
                <span class="program-badge badge-event">Trade Fair</span>
                <h4>Regional MSME Trade Expo 2024</h4>
                <p style="font-size: 14px; color: var(--text-muted); margin: 8px 0;">Showcase your products to regional buyers and expand your market network.</p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                    <div style="font-size: 13px;"><i class="fas fa-calendar"></i> March 5-7, 2024 | <i class="fas fa-users"></i> 28 Participants</div>
                    <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
                </div>
            </div>
        </div>

        <div class="program-card">
            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=300" class="program-img">
            <div class="program-info">
                <span class="program-badge badge-workshop">Workshop</span>
                <h4>Financial Literacy & Business Planning</h4>
                <p style="font-size: 14px; color: var(--text-muted); margin: 8px 0;">Master the fundamentals of business finance and strategic planning.</p>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                    <div style="font-size: 13px;"><i class="fas fa-calendar"></i> Feb 28, 2024 | <i class="fas fa-users"></i> 32 Enrolled</div>
                    <button class="btn-secondary" style="padding: 8px 16px; font-size: 13px;">Manage</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Stats -->
    <div>
        <div class="card" style="padding: 24px; margin-bottom: 24px;">
            <h4 style="margin-bottom: 20px;">Program Statistics</h4>
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 14px;">Total Programs</span>
                    <strong style="color: var(--primary-color);">12</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 14px;">Active Enrollments</span>
                    <strong style="color: var(--success-color);">105</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 14px;">Completed</span>
                    <strong style="color: var(--text-muted);">8</strong>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 24px;">
            <h5 style="margin-bottom: 15px; font-size: 14px;">Quick Actions</h5>
            <button class="btn-secondary" style="width: 100%; margin-bottom: 10px;">
                <i class="fas fa-file-export"></i> Export Attendance
            </button>
            <button class="btn-secondary" style="width: 100%;">
                <i class="fas fa-certificate"></i> Generate Certificates
            </button>
        </div>
    </div>
</div>

<?php 
$additionalJS = '<script>
    function openCreateProgramModal() {
        alert("Create Program Modal - To be implemented");
    }
</script>';

include '../layouts/footer.php'; 
?>
