<?php
require_once '../../../includes/init.php';

// Check if user is logged in and has admin-level role
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../../login.php');
}

$db = db();

// Fetch users with business profiles who are not yet active or need approval
$query = "SELECT u.id, u.firstname, u.lastname, u.email, u.business_name as user_business_name, u.status,
                 bp.business_type, bp.sector, bp.address, bp.registration_number, bp.year_started, bp.number_of_workers,
                 bp.compliance_type, bp.logo_path
          FROM users u
          JOIN business_profiles bp ON u.id = bp.user_id
          WHERE u.role = 'user'
          ORDER BY u.created_at DESC";
$profiles = $db->fetchAll($query);

// Stats
$pendingCount = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user' AND status = 'pending'")['count'];
$activeCount = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user' AND status = 'active'")['count'];

// Page configuration
$pageTitle = "Profile Verifications - LGU 3";
$pageHeading = "User Profile Verification";
$activePage = "profile-verifications";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<style>
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 24px;
    }

    .profile-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        border: 1px solid var(--border-color);
        transition: all 0.3s;
    }

    .profile-card:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--primary-color);
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-user-info {
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .business-logo-preview {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid var(--border-color);
        background: #f8fafc;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-item label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 4px;
    }

    .info-item span {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
    }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-rejected {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-approve {
        background: var(--success-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        flex: 1;
        transition: all 0.2s;
    }

    .btn-approve:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    .btn-reject {
        background: var(--danger-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        flex: 1;
        transition: all 0.2s;
    }

    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }
    
    body.dark-mode .profile-card {
        background: #1e293b;
    }
</style>

<div class="profile-grid">
    <div class="profile-list">
        <h3 style="margin-bottom: 24px;">MSME Profile Applications</h3>
        
        <?php if (empty($profiles)): ?>
            <div class="card" style="text-align: center; padding: 60px;">
                <i class="fas fa-user-check" style="font-size: 48px; color: var(--text-muted); margin-bottom: 20px; opacity: 0.3;"></i>
                <p style="color: var(--text-muted);">No profiles awaiting verification at this time.</p>
            </div>
        <?php else: ?>
            <?php foreach ($profiles as $p): ?>
                <div class="profile-card" id="profile-<?php echo $p['id']; ?>">
                    <div class="profile-header">
                        <div class="profile-user-info">
                            <img src="../../../<?php echo $p['logo_path'] ?: 'images/default-business.png'; ?>" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($p['user_business_name'] ?: 'MSME'); ?>&background=00205B&color=fff'"
                                 class="business-logo-preview">
                            <div>
                                <h4 style="margin: 0; font-size: 18px; color: var(--primary-color);">
                                    <?php echo htmlspecialchars($p['user_business_name'] ?: 'Unnamed Business'); ?>
                                </h4>
                                <p style="margin: 4px 0 0; font-size: 13px; color: var(--text-muted);">
                                    By: <?php echo htmlspecialchars($p['firstname'] . ' ' . $p['lastname']); ?> (<?php echo htmlspecialchars($p['email']); ?>)
                                </p>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($p['status']); ?>">
                            <?php echo ucfirst($p['status']); ?>
                        </span>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <label>Business Type</label>
                            <span><?php echo htmlspecialchars($p['business_type'] ?: 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Sector</label>
                            <span><?php echo htmlspecialchars($p['sector'] ?: 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Registration Status</label>
                            <span><?php echo htmlspecialchars($p['registration_number'] ?: 'Verification Required'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Founded Year</label>
                            <span><?php echo $p['year_started'] ?: 'N/A'; ?></span>
                        </div>
                        <div class="info-item">
                            <label>Address</label>
                            <span><?php echo htmlspecialchars($p['address'] ?: 'N/A'); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Employees</label>
                            <span><?php echo $p['number_of_workers'] ?: '0'; ?> Staff</span>
                        </div>
                    </div>

                    <?php if ($p['status'] === 'pending' || $p['status'] === 'active'): ?>
                    <div class="actions">
                        <?php if ($p['status'] !== 'active'): ?>
                        <button class="btn-approve" onclick="updateProfileStatus(<?php echo $p['id']; ?>, 'active')">
                            <i class="fas fa-check"></i> Approve Profile
                        </button>
                        <?php endif; ?>
                        <button class="btn-reject" onclick="updateProfileStatus(<?php echo $p['id']; ?>, 'rejected')">
                            <i class="fas fa-times"></i> Reject / Needs Modification
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="profile-sidebar">
        <div class="card" style="padding: 24px; position: sticky; top: 24px;">
            <h4 style="margin-bottom: 20px;">Registry Stats</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span>Pending Approval</span>
                <strong style="color: var(--warning-color);"><?php echo $pendingCount; ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px;">
                <span>Verified Members</span>
                <strong style="color: var(--success-color);"><?php echo $activeCount; ?></strong>
            </div>
            
            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border-color);">
                <h5 style="margin-bottom: 12px; font-size: 12px; text-transform: uppercase; color: var(--text-muted);">Quick Actions</h5>
                <button class="btn-primary" style="width: 100%; margin-bottom: 10px; font-size: 13px;" onclick="location.href='user-management.php'">
                    <i class="fas fa-users"></i> Manage All Users
                </button>
                <button class="btn-secondary" style="width: 100%; font-size: 13px;" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> Export List
                </button>
            </div>
        </div>
    </div>
</div>

<?php 
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
    function updateProfileStatus(userId, status) {
        const actionText = status === "active" ? "approve" : "reject";
        const color = status === "active" ? "#10b981" : "#ef4444";

        Swal.fire({
            title: `Are you sure?`,
            text: `You are about to ${actionText} this user\'s business profile.`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: color,
            confirmButtonText: `Yes, ${actionText} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append("userId", userId);
                formData.append("status", status);

                fetch("../../../ajax/auth.php?action=update-status-simple", {
                    method: "POST",
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire("Error", "Network error occurred.", "error");
                });
            }
        });
    }

    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark-mode");
    }
</script>';

include '../layouts/footer.php'; 
?>
