<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 

$unreadNotifs = getUnreadNotifications();
$notifCount = count($unreadNotifs);
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card-user:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .product-img-wrapper {
            position: relative;
            height: 200px;
            background: #f1f5f9;
        }

        body.dark-mode .product-img-wrapper {
            background: #0f172a;
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
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            transform: scale(0.95) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 24px 32px;
            background: linear-gradient(to right, var(--primary-color), var(--primary-light));
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        body.dark-mode .modal-header {
            background: linear-gradient(to right, #1e293b, #334155);
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 32px;
            overflow-y: auto;
            flex: 1;
            background: var(--bg-color);
        }

        .form-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .form-section-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        body.dark-mode .form-section-title {
            color: #60a5fa; /* Vibrant blue for dark mode */
        }

        .form-grid-modal {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 14px;
            transition: color 0.2s;
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            background: var(--card-bg);
            color: var(--text-main);
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 32, 91, 0.05);
            background: var(--card-bg);
        }

        body.dark-mode .form-group input:focus, 
        body.dark-mode .form-group select:focus, 
        body.dark-mode .form-group textarea:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
        }

        .form-group input:focus + i, 
        .form-group select:focus + i, 
        .form-group textarea:focus + i {
            color: var(--primary-color);
        }

        body.dark-mode .form-group input:focus + i, 
        body.dark-mode .form-group select:focus + i, 
        body.dark-mode .form-group textarea:focus + i {
            color: #60a5fa;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            padding: 10px 0;
        }
        
        .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            color: var(--text-main);
        }

        .radio-label input[type="radio"],
        .radio-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
        }

        .modal-footer {
            padding: 24px 32px;
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 16px;
        }

        .btn-cancel {
            padding: 12px 24px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-main);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: var(--bg-color);
            border-color: var(--text-muted);
        }

        .submit-btn {
            padding: 12px 32px;
            border-radius: 10px;
            border: none;
            background: var(--primary-color);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 32, 91, 0.2);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 32, 91, 0.3);
            background: var(--primary-light);
        }

        body.dark-mode .submit-btn {
            background: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        body.dark-mode .submit-btn:hover {
            background: #2563eb;
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
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
                    <li><a href="profile-management.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="help-center.php"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
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
                        <span class="badge" style="<?php echo $notifCount > 0 ? '' : 'display: none;'; ?>"><?php echo $notifCount; ?></span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <span class="user-role">Business Owner</span>
                        </div>
                        <?php 
                            $topbarLogo = !empty($_SESSION['business_logo']) 
                                ? '../../../' . $_SESSION['business_logo'] 
                                : "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['user_name'] ?? 'User') . "&background=00205B&color=fff";
                        ?>
                        <img src="<?php echo $topbarLogo; ?>"
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
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-box"></i> 1. Basic Product Information</div>
                        <div class="form-grid-modal">
                            <div class="form-group full-width">
                                <label>Product Name</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="Enter complete product name" name="product_name" required>
                                    <i class="fas fa-tag"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Product Category</label>
                                <div class="input-wrapper">
                                    <select name="product_category" required>
                                        <option value="" disabled selected>Select category</option>
                                        <option value="food">Food Processing</option>
                                        <option value="non-food">Non-Food</option>
                                        <option value="agri">Agri-Business</option>
                                        <option value="handicraft">Handicrafts / Souvenirs</option>
                                    </select>
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Intended Market</label>
                                <div class="input-wrapper">
                                    <select name="intended_market">
                                        <option value="local">Local (Municipality/Provincial)</option>
                                        <option value="national">National</option>
                                        <option value="export">International / Export</option>
                                    </select>
                                    <i class="fas fa-globe"></i>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Product Description</label>
                                <div class="input-wrapper">
                                    <textarea rows="3" placeholder="Describe your product features and benefits" name="product_description"></textarea>
                                    <i class="fas fa-align-left" style="top: 15px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Product Details -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-info-circle"></i> 2. Product Details</div>
                        <div class="form-grid-modal">
                            <div class="form-group full-width">
                                <label>Ingredients / Raw Materials</label>
                                <div class="input-wrapper">
                                    <textarea rows="2" placeholder="List major ingredients or materials used" name="ingredients"></textarea>
                                    <i class="fas fa-mortar-pestle" style="top: 15px;"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Production Method</label>
                                <div class="input-wrapper">
                                    <select name="production_method">
                                        <option value="manual">Manual / Hand-crafted</option>
                                        <option value="semi">Semi-Automated</option>
                                        <option value="automated">Fully Automated</option>
                                    </select>
                                    <i class="fas fa-cogs"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Shelf Life / Durability</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="e.g. 6 months, 1 year" name="shelf_life">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label>Packaging Type</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="e.g. Vacuum sealed plastic, Glass jar, Box" name="packaging_type">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Production & Capacity -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-industry"></i> 3. Production & Capacity</div>
                        <div class="form-grid-modal">
                            <div class="form-group full-width">
                                <label>Production Location</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="Complete address of production facility" name="production_location">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Production Capacity</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="e.g. 1000 units per month" name="production_capacity">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Available Volume</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="Current stock available" name="available_volume">
                                    <i class="fas fa-cubes"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Pricing Information -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-tag"></i> 4. Pricing Information</div>
                        <div class="form-grid-modal">
                            <div class="form-group">
                                <label>Cost of Production (PHP)</label>
                                <div class="input-wrapper">
                                    <input type="number" placeholder="0.00" name="cost_production">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Suggested Retail Price (PHP)</label>
                                <div class="input-wrapper">
                                    <input type="number" placeholder="0.00" name="srp">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Wholesale Price (PHP)</label>
                                <div class="input-wrapper">
                                    <input type="number" placeholder="0.00" name="wholesale_price">
                                    <i class="fas fa-store"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Export Price ($)</label>
                                <div class="input-wrapper">
                                    <input type="number" placeholder="0.00" name="export_price">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Compliance & Safety Declaration -->
                    <div class="form-section">
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
                                <div class="input-wrapper">
                                    <input type="text" placeholder="Enter Permit Number if available" name="permit_number">
                                    <i class="fas fa-certificate"></i>
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label class="radio-label" style="font-weight: 600; color: var(--primary-color);">
                                    <input type="checkbox" required> I hereby certify that the product information provided is true and correct.
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Product Media Upload -->
                    <div class="form-section">
                        <div class="form-section-title"><i class="fas fa-images"></i> 6. Product Media</div>
                        <div class="form-grid-modal">
                            <div class="form-group full-width">
                                <label>Product Photos</label>
                                <div class="input-wrapper">
                                    <input type="file" name="product_images[]" multiple accept="image/*" class="form-control" required style="padding-left: 40px;">
                                    <i class="fas fa-upload"></i>
                                </div>
                                <small style="color: var(--text-muted); margin-top: 8px; display: block;">You can select multiple images. Best if size is under 2MB each.</small>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Optional (For Export) -->
                    <div class="form-section">
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
                                <div class="input-wrapper">
                                    <select name="export_exp">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                    <i class="fas fa-ship"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Target Export Country</label>
                                <div class="input-wrapper">
                                    <input type="text" placeholder="e.g. USA, Japan, UAE" name="target_country">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" form="productForm" id="submitBtn" class="submit-btn">Submit Registration</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../js/main.js"></script>
</body>
</html>

