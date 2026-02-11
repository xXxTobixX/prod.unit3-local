<?php
require_once '../../../includes/init.php';
if (!isLoggedIn()) { redirect('../../../login.php'); }
// Only admins
$adminRole = $_SESSION['user_role'] ?? '';
if (!in_array($adminRole, ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../index.php');
}

$db = db();
$allProducts = $db->fetchAll("
    SELECT p.*, CONCAT(u.firstname, ' ', u.lastname) as user_name, u.business_name, bp.business_type as enterprise_type
    FROM user_products p 
    JOIN users u ON p.user_id = u.id 
    LEFT JOIN business_profiles bp ON u.id = bp.user_id
    ORDER BY p.created_at DESC
");

// Page configuration
$pageTitle = "Product & MSME Registry - LGU 3";
$pageHeading = "Product & MSME Registry";
$activePage = "product-registry";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<style>
    /* Custom styles for Product/MSME Registry */
    .market-pills {
        display: flex;
        gap: 5px;
    }

    .market-status {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        text-transform: uppercase;
    }

    .status-local {
        background: rgba(59, 130, 246, 0.1);
        color: #2563EB;
    }

    .status-export {
        background: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
    }

    .product-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-img-table {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--border-color);
    }

    .msme-tag {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-approved {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-rejected {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }
</style>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3>Registry Filters</h3>
    </div>
    <div style="padding: 24px; display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display:block; font-size: 13px; font-weight:600; margin-bottom:8px;">Market Status</label>
            <select style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                <option>All Markets</option>
                <option>Local Only</option>
                <option>Export Ready</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="display:block; font-size: 13px; font-weight:600; margin-bottom:8px;">Approval Status</label>
            <select style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                <option>All Status</option>
                <option>Pending Approval</option>
                <option>Approved</option>
                <option>Rejected</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="display:block; font-size: 13px; font-weight:600; margin-bottom:8px;">MSME Category</label>
            <select style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                <option>All Categories</option>
                <option>Agriculture</option>
                <option>Handicrafts</option>
                <option>Food Processing</option>
                <option>Textiles</option>
            </select>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Pending & Registered Products</h3>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Product Details</th>
                    <th>MSME / Enterprise</th>
                    <th>Market Reach</th>
                    <th>Approv. Status</th>
                    <th>Date Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allProducts)): ?>
                    <tr><td colspan="6" style="text-align:center; padding: 20px;">No products found in registry.</td></tr>
                <?php else: ?>
                    <?php foreach ($allProducts as $p): 
                        $images = json_decode($p['product_images'], true);
                        $mainImg = !empty($images) ? '../../../' . $images[0] : 'https://via.placeholder.com/100?text=No+Img';
                        
                        $statusClass = 'status-pending';
                        if($p['status'] === 'approved') $statusClass = 'status-approved';
                        if($p['status'] === 'rejected') $statusClass = 'status-rejected';
                    ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <img src="<?php echo $mainImg; ?>" class="product-img-table">
                                    <div>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                        <div style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($p['category']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--primary-color); cursor: pointer;"
                                    onclick="viewMSME('<?php echo addslashes($p['business_name']); ?>')"><?php echo htmlspecialchars($p['business_name'] ?: $p['user_name']); ?></div>
                                <div class="msme-tag"><i class="fas fa-tag"></i> <?php echo ucfirst($p['enterprise_type'] ?: 'Micro'); ?> Enterprise</div>
                            </td>
                            <td>
                                <div class="market-pills">
                                    <span class="market-status status-local">Local</span>
                                    <?php if($p['intended_market'] === 'export'): ?>
                                        <span class="market-status status-export">Export</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <?php if($p['status'] === 'pending'): ?>
                                        <button class="btn-primary" style="padding: 6px 12px; font-size: 12px; background: var(--success-color);" 
                                                onclick="approveProduct(<?php echo $p['id']; ?>)">Approve</button>
                                        <button class="btn-primary" style="padding: 6px 12px; font-size: 12px; background: var(--danger-color);" 
                                                onclick="rejectProduct(<?php echo $p['id']; ?>)">Reject</button>
                                    <?php else: ?>
                                        <button class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" 
                                                onclick="viewProduct(<?php echo $p['id']; ?>)">View</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
    function approveProduct(productId) {
        Swal.fire({
            title: "Approve Product?",
            text: "This product will be marked as approved and visible to buyers.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            confirmButtonText: "Yes, approve it!"
        }).then((result) => {
            if (result.isConfirmed) {
                updateProductStatus(productId, "approved");
            }
        });
    }

    function rejectProduct(productId) {
        Swal.fire({
            title: "Reject Product?",
            text: "Please provide a reason for rejection.",
            input: "textarea",
            inputPlaceholder: "Enter rejection reason...",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Reject"
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                updateProductStatus(productId, "rejected", result.value);
            }
        });
    }

    function updateProductStatus(productId, status, remarks = "") {
        const formData = new FormData();
        formData.append("productId", productId);
        formData.append("status", status);
        if (remarks) formData.append("remarks", remarks);

        fetch("../../../ajax/products.php?action=update-status", {
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
        });
    }

    function viewProduct(productId) {
        window.location.href = `product-details.php?id=${productId}`;
    }

    function viewMSME(businessName) {
        Swal.fire({
            title: businessName,
            text: "MSME Profile Details",
            icon: "info"
        });
    }
</script>';

include '../layouts/footer.php'; 
?>
