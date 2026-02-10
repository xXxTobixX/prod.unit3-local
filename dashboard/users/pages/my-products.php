<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 

$userId = $_SESSION['user_id'];
$db = db();
$products = $db->fetchAll("SELECT * FROM user_products WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - LGU 3 MSME Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        .product-grid-user {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .product-card-user {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }

        .product-card-user:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .product-img-wrapper {
            position: relative;
            height: 200px;
        }

        .product-img-user {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-status-tag {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: var(--card-bg);
            box-shadow: var(--shadow-sm);
        }

        .product-body-user {
            padding: 20px;
        }

        .product-meta-user {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
        }

        .export-badge-user {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: var(--card-bg);
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--danger-color);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 24px 0 16px;
            padding-bottom: 8px;
            border-bottom: 1px dashed var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section-title:first-child {
            margin-top: 0;
        }

        .form-grid-modal {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-color);
            color: var(--text-main);
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .file-upload-box {
            border: 2px dashed var(--border-color);
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-box:hover {
            border-color: var(--primary-color);
            background: rgba(59, 130, 246, 0.05);
        }

        .radio-group {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }
        
        .radio-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .modal-footer {
            padding: 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
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
                    <p>MSME Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../index.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
                    <li><a href="profile-management.php"><i class="fas fa-id-card"></i> <span>My Profile</span></a>
                    </li>
                    <li class="active"><a href="#"><i class="fas fa-box"></i> <span>My Products</span></a></li>
                    <li><a href="compliance-status.php"><i class="fas fa-check-double"></i> <span>Compliance
                                Status</span></a></li>
                    <li><a href="my-training.php"><i class="fas fa-certificate"></i> <span>My Training</span></a></li>
                    <li><a href="applied-incentives.php"><i class="fas fa-hand-holding-usd"></i> <span>Applied
                                Incentives</span></a></li>
                    <li><a href="market-insights.php"><i class="fas fa-chart-line"></i> <span>Market
                                Insights</span></a></li>
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
                    <h2>My Product Portfolio</h2>
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
                        <span class="badge">2</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <span class="user-role">Business Owner</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=00205B&color=fff"
                            alt="User Avatar" class="avatar">
                    </div>
                    <button class="btn-primary" onclick="openModal()"><i
                            class="fas fa-plus"></i> Register New Product</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="product-grid-user">
                    <?php if (empty($products)): ?>
                        <div class="card" style="grid-column: 1/-1; padding: 40px; text-align: center;">
                            <i class="fas fa-box-open" style="font-size: 48px; color: var(--border-color); margin-bottom: 16px;"></i>
                            <h3>No products registered yet</h3>
                            <p style="color: var(--text-muted);">Click "Register New Product" to get started.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $p): 
                            $images = json_decode($p['product_images'], true);
                            $mainImg = !empty($images) ? '../../../' . $images[0] : 'https://via.placeholder.com/500?text=No+Image';
                            $statusColor = 'var(--warning-color)';
                            $statusText = 'Under Review';
                            if ($p['status'] === 'approved') { $statusColor = 'var(--success-color)'; $statusText = 'Active'; }
                            if ($p['status'] === 'rejected') { $statusColor = 'var(--danger-color)'; $statusText = 'Rejected'; }
                        ?>
                            <div class="product-card-user">
                                <div class="product-img-wrapper">
                                    <img src="<?php echo $mainImg; ?>" class="product-img-user">
                                    <span class="product-status-tag" style="color: <?php echo $statusColor; ?>;"><?php echo $statusText; ?></span>
                                </div>
                                <div class="product-body-user">
                                    <?php if ($p['intended_market'] === 'export'): ?>
                                        <span class="export-badge-user">Export Ready</span>
                                    <?php endif; ?>
                                    <h4 style="margin-top: 8px;"><?php echo htmlspecialchars($p['product_name']); ?></h4>
                                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                                        <?php echo htmlspecialchars(substr($p['description'], 0, 80)) . (strlen($p['description']) > 80 ? '...' : ''); ?>
                                    </p>
                                    <div class="product-meta-user">
                                        <span style="font-weight: 700; color: var(--primary-color);">₱ <?php echo number_format($p['srp'], 2); ?></span>
                                        <div style="display: flex; gap: 8px;">
                                            <button class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="icon-btn" title="Details" onclick="viewDetails(<?php echo $p['id']; ?>)"><i class="fas fa-info-circle"></i></button>
                                        </div>
                                    </div>
                                    <?php if ($p['status'] === 'rejected'): ?>
                                        <div style="font-size: 11px; color: var(--danger-color); margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border-color);">
                                            <strong>Reason:</strong> <?php echo htmlspecialchars($p['remarks']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Product Registration Modal -->
    <div class="modal-overlay" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Product Registration Form</h2>
                <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <!-- 1. Basic Product Information -->
                    <div class="form-section-title"><i class="fas fa-box"></i> 1. Basic Product Information</div>
                    <div class="form-grid-modal">
                        <div class="form-group full-width">
                            <label>Product Name</label>
                            <input type="text" placeholder="Enter complete product name" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label>Product Category</label>
                            <select name="product_category" required>
                                <option value="" disabled selected>Select category</option>
                                <option value="food">Food Processing</option>
                                <option value="non-food">Non-Food</option>
                                <option value="agri">Agri-Business</option>
                                <option value="handicraft">Handicrafts / Souvenirs</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Intended Market</label>
                            <select name="intended_market">
                                <option value="local">Local (Municipality/Provincial)</option>
                                <option value="national">National</option>
                                <option value="export">International / Export</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label>Product Description</label>
                            <textarea rows="3" placeholder="Describe your product features and benefits" name="product_description"></textarea>
                        </div>
                    </div>

                    <!-- 2. Product Details -->
                    <div class="form-section-title"><i class="fas fa-info-circle"></i> 2. Product Details</div>
                    <div class="form-grid-modal">
                        <div class="form-group full-width">
                            <label>Ingredients / Raw Materials</label>
                            <textarea rows="2" placeholder="List major ingredients or materials used" name="ingredients"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Production Method</label>
                            <select name="production_method">
                                <option value="manual">Manual / Hand-crafted</option>
                                <option value="semi">Semi-Automated</option>
                                <option value="automated">Fully Automated</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Shelf Life / Durability</label>
                            <input type="text" placeholder="e.g. 6 months, 1 year" name="shelf_life">
                        </div>
                        <div class="form-group full-width">
                            <label>Packaging Type</label>
                            <input type="text" placeholder="e.g. Vacuum sealed plastic, Glass jar, Box" name="packaging_type">
                        </div>
                    </div>

                    <!-- 3. Production & Capacity -->
                    <div class="form-section-title"><i class="fas fa-industry"></i> 3. Production & Capacity</div>
                    <div class="form-grid-modal">
                        <div class="form-group full-width">
                            <label>Production Location</label>
                            <input type="text" placeholder="Complete address of production facility" name="production_location">
                        </div>
                        <div class="form-group">
                            <label>Production Capacity</label>
                            <input type="text" placeholder="e.g. 1000 units per month" name="production_capacity">
                        </div>
                        <div class="form-group">
                            <label>Available Volume</label>
                            <input type="text" placeholder="Current stock available" name="available_volume">
                        </div>
                    </div>

                    <!-- 4. Pricing Information -->
                    <div class="form-section-title"><i class="fas fa-tag"></i> 4. Pricing Information</div>
                    <div class="form-grid-modal">
                        <div class="form-group">
                            <label>Cost of Production (PHP)</label>
                            <input type="number" placeholder="0.00" name="cost_production">
                        </div>
                        <div class="form-group">
                            <label>Suggested Retail Price (PHP)</label>
                            <input type="number" placeholder="0.00" name="srp">
                        </div>
                        <div class="form-group">
                            <label>Wholesale Price (PHP)</label>
                            <input type="number" placeholder="0.00" name="wholesale_price">
                        </div>
                        <div class="form-group">
                            <label>Export Price ($) - If applicable</label>
                            <input type="number" placeholder="0.00" name="export_price">
                        </div>
                    </div>

                    <!-- 5. Compliance & Safety Declaration -->
                    <div class="form-section-title"><i class="fas fa-file-contract"></i> 5. Compliance & Safety</div>
                    <div class="form-grid-modal">
                        <div class="form-group">
                            <label>Type Confirmation</label>
                             <div class="radio-group">
                                <label class="radio-label"><input type="radio" name="product_compliance_type" value="food"> Food</label>
                                <label class="radio-label"><input type="radio" name="product_compliance_type" value="non-food"> Non-Food</label>
                             </div>
                        </div>
                        <div class="form-group">
                            <label>FDA LTO / Permit #</label>
                            <input type="text" placeholder="Enter Permit Number if available" name="permit_number">
                        </div>
                        <div class="form-group full-width">
                             <label class="radio-label" style="font-weight: 600; color: var(--primary-color);">
                                <input type="checkbox" required> I hereby certify that the product information provided is true and correct.
                             </label>
                        </div>
                    </div>

                    <!-- 6. Product Media Upload -->
                    <div class="form-section-title"><i class="fas fa-images"></i> 6. Product Media</div>
                    <div class="form-grid-modal">
                        <div class="form-group full-width">
                            <label>Product Photos</label>
                            <input type="file" name="product_images[]" multiple accept="image/*" class="form-control" required>
                            <small style="color: var(--text-muted);">You can select multiple images.</small>
                        </div>
                    </div>

                    <!-- 7. Optional (For Export) -->
                    <div class="form-section-title"><i class="fas fa-globe"></i> 7. Optional (For Export-Ready)</div>
                    <div class="form-grid-modal">
                        <div class="form-group full-width">
                            <label>Certifications Held</label>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 5px;">
                                <label class="radio-label"><input type="checkbox" name="certs[]" value="halal"> Halal</label>
                                <label class="radio-label"><input type="checkbox" name="certs[]" value="organic"> Organic</label>
                                <label class="radio-label"><input type="checkbox" name="certs[]" value="gmp"> GMP</label>
                                <label class="radio-label"><input type="checkbox" name="certs[]" value="haccp"> HACCP</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Export Experience?</label>
                            <select name="export_exp">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Target Export Country</label>
                            <input type="text" placeholder="e.g. USA, Japan, UAE" name="target_country">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" form="productForm" id="submitBtn" class="btn-primary">Submit Registration</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../js/main.js"></script>
    <script>
        const modal = document.getElementById('productModal');

        function openModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; 
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = ''; 
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'register-product');

            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            btn.disabled = true;

            fetch('../../../ajax/products.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred during submission.', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        function viewDetails(id) {
            // Future implementation: Load product details in a modal
            Swal.fire('Info', 'Product details view is coming soon.', 'info');
        }
    </script>
</body>
</html>

