<?php
require_once '../../../includes/init.php';
if (!isLoggedIn()) { redirect('../../../login.php'); }

// Only admins can access
$role = $_SESSION['user_role'] ?? '';
if (!in_array($role, ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../users/index.php');
}

$db = db();

// Fetch pending documents
$query = "SELECT bd.*, u.firstname, u.lastname, u.business_name, u.email 
          FROM business_documents bd
          JOIN users u ON bd.user_id = u.id
          WHERE bd.status = 'pending'
          ORDER BY bd.uploaded_at ASC";
$pendingDocs = $db->fetchAll($query);

// Fetch recent verification history
$historyQuery = "SELECT bd.*, u.business_name 
                FROM business_documents bd
                JOIN users u ON bd.user_id = u.id
                WHERE bd.status != 'pending'
                ORDER BY bd.verified_at DESC LIMIT 10";
$historyDocs = $db->fetchAll($historyQuery);

// Stats
$allPendingCount = $db->fetchOne("SELECT COUNT(*) as count FROM business_documents WHERE status = 'pending'")['count'];
$allVerifiedCount = $db->fetchOne("SELECT COUNT(*) as count FROM business_documents WHERE status = 'verified'")['count'];

// Page configuration
$pageTitle = "Compliance Monitoring - LGU 3";
$pageHeading = "Compliance & Permit Monitoring";
$activePage = "compliance-monitoring";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

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
        background: var(--success-color);
        transition: width 0.3s;
    }
</style>

<div class="compliance-grid">
    <!-- Pending Documents List -->
    <div class="checklist-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3>Pending Document Verifications</h3>
            <div class="badge-count" style="background: var(--primary-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 13px;">
                <?php echo count($pendingDocs); ?> Pending
            </div>
        </div>

        <?php if (empty($pendingDocs)): ?>
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <p>All documents have been processed!</p>
            </div>
        <?php else: ?>
            <?php foreach ($pendingDocs as $doc): ?>
                <div class="checklist-item" id="doc-row-<?php echo $doc['id']; ?>">
                    <div class="checklist-info">
                        <div class="doc-icon-circle" style="width: 40px; height: 40px; background: rgba(0, 32, 91, 0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($doc['document_type']); ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                From: <strong><?php echo htmlspecialchars($doc['business_name']); ?></strong> (<?php echo htmlspecialchars($doc['firstname'] . ' ' . $doc['lastname']); ?>)
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                Uploaded: <?php echo date('M d, Y h:i A', strtotime($doc['uploaded_at'])); ?>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="../../../<?php echo $doc['file_path']; ?>" target="_blank" class="doc-preview">
                            <i class="fas fa-external-link-alt"></i> View
                        </a>
                        <button class="btn-primary" style="padding: 6px 12px; font-size: 12px; background: var(--success-color);" 
                                onclick="processDocument(<?php echo $doc['id']; ?>, 'verified')">
                            Approve
                        </button>
                        <button class="btn-primary" style="padding: 6px 12px; font-size: 12px; background: var(--danger-color);" 
                                onclick="promptReject(<?php echo $doc['id']; ?>)">
                            Reject
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="margin-top: 40px; border-top: 1px solid var(--border-color); padding-top: 24px;">
            <h4 style="margin-bottom: 15px;">Recent History</h4>
            <div style="font-size: 13px;">
                <?php foreach ($historyDocs as $h): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed var(--border-color);">
                        <span><?php echo htmlspecialchars($h['business_name']); ?> - <?php echo htmlspecialchars($h['document_type']); ?></span>
                        <span class="status-badge <?php echo $h['status'] === 'verified' ? 'status-compliant' : 'status-expired'; ?>">
                            <?php echo $h['status']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Compliance Status Summary -->
    <div>
        <div class="card" style="padding: 24px; margin-bottom: 24px;">
            <h4 style="margin-bottom: 20px;">Compliance Registry Health</h4>
            <div style="text-align: center;">
                <?php
                $totalCompQuery = "SELECT COUNT(*) as total FROM business_documents";
                $docResult = $db->fetchOne($totalCompQuery);
                $totalDocs = $docResult ? $docResult['total'] : 0;
                $complianceRate = $totalDocs > 0 ? round(($allVerifiedCount / $totalDocs) * 100) : 0;
                ?>
                <div style="font-size: 48px; font-weight: 800; color: var(--success-color);"><?php echo $complianceRate; ?>%</div>
                <p style="color: var(--text-muted); font-size: 13px;">Document Approval Rate</p>
            </div>
            <div style="margin-top: 24px;">
                <div class="validation-item">
                    <div class="validation-header"><span>Global Verification</span><span><?php echo $allVerifiedCount; ?> / <?php echo $totalDocs; ?></span>
                    </div>
                    <div class="progress-mini">
                        <div class="progress-mini-bar" style="width: <?php echo $complianceRate; ?>%;"></div>
                    </div>
                </div>
                <div class="validation-item">
                    <div class="validation-header"><span>Pending Actions</span><span
                            style="color: var(--warning-color)"><?php echo $allPendingCount; ?> Documents</span></div>
                    <div class="progress-mini">
                        <div class="progress-mini-bar"
                            style="width: <?php echo ($totalDocs > 0) ? ($allPendingCount / $totalDocs * 100) : 0; ?>%; background: var(--warning-color);"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 24px;">
            <h5 style="margin-bottom: 15px; font-size: 14px;">Quick Actions</h5>
            <button class="btn-secondary" style="width: 100%; margin-bottom: 10px;" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <button class="btn-secondary" style="width: 100%;" onclick="alert('Export feature coming soon')">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
        </div>
    </div>
</div>

<?php 
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
    function processDocument(docId, newStatus, remarks = "") {
        const formData = new FormData();
        formData.append("docId", docId);
        formData.append("status", newStatus);
        if (remarks) formData.append("remarks", remarks);

        fetch("../../../ajax/documents.php?action=update-status", {
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
                    window.location.reload();
                });
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch(error => console.error("Error:", error));
    }

    async function promptReject(docId) {
        const { value: remarks } = await Swal.fire({
            title: "Reject Document",
            input: "textarea",
            inputLabel: "Reason for rejection",
            inputPlaceholder: "Type your reason here...",
            inputAttributes: {
                "aria-label": "Type your reason here"
            },
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Confirm Rejection"
        });

        if (remarks) {
            processDocument(docId, "rejected", remarks);
        }
    }
</script>';

include '../layouts/footer.php'; 
?>
