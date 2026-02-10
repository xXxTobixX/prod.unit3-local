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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product & MSME Registry - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
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
            color: #7C3AED;
        }

        .msme-tag {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }

        .btn-approve {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-approve:hover {
            background: #059669;
            transform: scale(1.05);
        }

        .btn-reject {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-reject:hover {
            background: #DC2626;
            transform: scale(1.05);
        }

        /* MSME Profile Sidebar/Modal */
        .profile-container {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .msme-logo {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .profile-details h4 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .profile-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .info-item span {
            font-size: 14px;
            font-weight: 600;
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
            max-width: 650px;
            box-shadow: var(--shadow-lg);
            position: relative;
            color: var(--text-main);
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .product-img-table {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 12px;
        }

        .product-cell {
            display: flex;
            align-items: center;
        }

        /* Product Detail Modal Specifics */
        .detail-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 24px;
        }

        .product-large-img {
            width: 100%;
            border-radius: 12px;
            aspect-ratio: 1;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .spec-list {
            list-style: none;
            margin-top: 12px;
        }

        .spec-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted var(--border-color);
            font-size: 14px;
        }

        .spec-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .spec-value {
            font-weight: 600;
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
                    <li class="active"><a href="#"><i class="fas fa-building"></i> <span>Product & MSME
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
                    <h2>Product & MSME Registry</h2>
                </div>

                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search products or enterprises...">
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
                        <span class="badge">12</span>
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
                <!-- Filters -->
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-header">
                        <h3>Registry Filters</h3>
                    </div>
                    <div style="padding: 24px; display: flex; gap: 20px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display:block; font-size: 13px; font-weight:600; margin-bottom:8px;">Market
                                Status</label>
                            <select
                                style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                                <option>All Markets</option>
                                <option>Local Only</option>
                                <option>Export Ready</option>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display:block; font-size: 13px; font-weight:600; margin-bottom:8px;">Approval
                                Status</label>
                            <select
                                style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
                                <option>All Status</option>
                                <option>Pending Approval</option>
                                <option>Approved</option>
                                <option>Rejected</option>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display:block; font-size: 13px; font-weight:600; margin-bottom:8px;">MSME
                                Category</label>
                            <select
                                style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border-color);">
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
                                                        <button class="btn-approve" onclick="processProduct(<?php echo $p['id']; ?>, 'approved')">Approve</button>
                                                        <button class="btn-reject" onclick="processProduct(<?php echo $p['id']; ?>, 'rejected')">Reject</button>
                                                    <?php endif; ?>
                                                    <button class="icon-btn" onclick="viewProductDetails(<?php echo htmlspecialchars(json_encode($p)); ?>)">
                                                        <i class="fas fa-eye"></i>
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

    <!-- Product Detail Modal -->
    <div id="productModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 750px;">
            <button class="close-modal" onclick="closeProductModal()">&times;</button>
            <h3 style="margin-bottom: 24px;">Product Specifications & Data</h3>

            <div class="detail-grid">
                <div class="product-visuals">
                    <img src="" id="pModalImg" class="product-large-img">
                    <div style="margin-top: 16px;">
                        <span class="market-status status-local" id="pModalMarket">Local</span>
                    </div>
                </div>
                <div class="product-details">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h4 id="pModalName" style="font-size: 22px; color: var(--primary-color);">Product Name</h4>
                            <p id="pModalCategory"
                                style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">Category</p>
                        </div>
                        <div style="text-align: right;">
                            <div id="pModalPrice"
                                style="font-size: 20px; font-weight: 800; color: var(--secondary-color);">₱ 0.00</div>
                            <small style="color: var(--text-muted);">MSRP Registry</small>
                        </div>
                    </div>

                    <div class="spec-list">
                        <div class="spec-item">
                            <span class="spec-label">Enterprise Partner</span>
                            <span class="spec-value" id="pModalMSME">Enterprise Name</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Unit Variant</span>
                            <span class="spec-value" id="pModalVariant">Default</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Compliance Cert</span>
                            <span class="spec-value" style="color: var(--success-color);"><i
                                    class="fas fa-check-circle"></i> FDA Verified</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Current Inventory</span>
                            <span class="spec-value">450 Units</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Last Inspection</span>
                            <span class="spec-value">Jan 15, 2024</span>
                        </div>
                    </div>

                    <div
                        style="margin-top: 24px; padding: 16px; background: #F8FAFC; border-radius: 12px; border: 1px solid var(--border-color);">
                        <h5 style="margin-bottom: 8px;"><i class="fas fa-info-circle"></i> Administrative Note</h5>
                        <p style="font-size: 13px; color: var(--text-muted);">This product has passed the LGU quality
                            standard for regional trade fairs. Eligible for export subsidy program.</p>
                    </div>
                </div>
            </div>

            <div
                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border-color); display:flex; justify-content: flex-end; gap: 12px;">
                <button class="btn-secondary" onclick="closeProductModal()">Close Window</button>
                <button class="btn-primary"><i class="fas fa-edit"></i> Edit Registry Data</button>
            </div>
        </div>
    </div>

    <!-- MSME Profile Modal -->
    <div id="msmeModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal()">&times;</button>
            <h3 style="margin-bottom: 24px;">MSME Enterprise Profile</h3>

            <div class="profile-container">
                <img src="https://ui-avatars.com/api/?name=Bukidnon+Farms&background=random" class="msme-logo"
                    id="modalLogo">
                <div class="profile-details">
                    <h4 id="modalMSMEName">Bukidnon Farms Co.</h4>
                    <p style="color: var(--text-muted); font-size: 14px;">Registration No: REG-2024-8821</p>
                    <div style="margin-top:10px;">
                        <span class="status-pill status-active">Verified Member</span>
                    </div>
                </div>
            </div>

            <div class="profile-info-grid">
                <div class="info-item">
                    <label>Enterprise Type</label>
                    <span id="modalType">Micro Enterprise (Agriculture)</span>
                </div>
                <div class="info-item">
                    <label>Owner / Manager</label>
                    <span>Roberto Mangahas</span>
                </div>
                <div class="info-item">
                    <label>Contact Number</label>
                    <span>+63 912 345 6789</span>
                </div>
                <div class="info-item">
                    <label>Location</label>
                    <span>Malaybalay, Bukidnon</span>
                </div>
                <div class="info-item">
                    <label>Total Products Listed</label>
                    <span>14 Products</span>
                </div>
                <div class="info-item">
                    <label>Export Readiness</label>
                    <span style="color: var(--success-color);">High Potential</span>
                </div>
            </div>

            <div
                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border-color); display:flex; justify-content: flex-end; gap: 12px;">
                <button class="btn-secondary" onclick="closeModal()">Close Profile</button>
                <button class="btn-primary">Download Registry Copy</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../js/main.js"></script>
    <script>
        const msmeModal = document.getElementById('msmeModal');
        const productModal = document.getElementById('productModal');

        function viewMSME(name) {
            document.getElementById('modalMSMEName').innerText = name;
            document.getElementById('modalLogo').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=00205B&color=fff`;
            msmeModal.style.display = 'flex';
        }

        function closeModal() {
            msmeModal.style.display = 'none';
        }

        function viewProductDetails(p) {
            const images = JSON.parse(p.product_images || '[]');
            document.getElementById('pModalImg').src = images.length ? '../../../' + images[0] : 'https://via.placeholder.com/400?text=No+Image';
            document.getElementById('pModalName').innerText = p.product_name;
            document.getElementById('pModalCategory').innerText = p.category;
            document.getElementById('pModalPrice').innerText = '₱ ' + parseFloat(p.srp).toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('pModalMSME').innerText = p.business_name || p.user_name;
            document.getElementById('pModalVariant').innerText = p.packaging_type || 'N/A';
            document.getElementById('pModalMarket').innerText = p.intended_market.charAt(0).toUpperCase() + p.intended_market.slice(1);
            
            productModal.style.display = 'flex';
        }

        function closeProductModal() {
            productModal.style.display = 'none';
        }

        async function processProduct(id, status) {
            let remarks = '';
            if (status === 'rejected') {
                const { value: text } = await Swal.fire({
                    title: 'Reason for Rejection',
                    input: 'textarea',
                    inputPlaceholder: 'Enter reason here...',
                    showCancelButton: true
                });
                if (!text) return;
                remarks = text;
            } else {
                const confirmed = await Swal.fire({
                    title: 'Approve Product?',
                    text: 'This will list the product in the official registry.',
                    icon: 'question',
                    showCancelButton: true
                });
                if (!confirmed.isConfirmed) return;
            }

            const formData = new FormData();
            formData.append('action', 'process-product');
            formData.append('product_id', id);
            formData.append('status', status);
            formData.append('remarks', remarks);

            fetch('../../../ajax/products.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }

        window.onclick = function (event) {
            if (event.target == msmeModal) closeModal();
            if (event.target == productModal) closeProductModal();
        }
    </script>
</body>

</html>
