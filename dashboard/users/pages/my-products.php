<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 
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
                            <span class="user-name"><?php echo htmlspecialchars(                            <span class="user-name">Juana Dela Cruz</span>SESSION["user_name"]); ?></span>
                            <span class="user-role">Business Owner</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Juana+Dela+Cruz&background=00205B&color=fff"
                            alt="User Avatar" class="avatar">
                    </div>
                    <button class="btn-primary" onclick="alert('Opening Product Submission Form...')"><i
                            class="fas fa-plus"></i> Register New Product</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="product-grid-user">
                    <!-- Product 1 -->
                    <div class="product-card-user">
                        <div class="product-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1559056191-7237f00037a3?w=500"
                                class="product-img-user">
                            <span class="product-status-tag" style="color: var(--success-color);">Active</span>
                        </div>
                        <div class="product-body-user">
                            <span class="export-badge-user">Export Ready</span>
                            <h4 style="margin-top: 8px;">Premium Arabica Dark Roast</h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">100% Organic beans
                                sourced from Bukidnon highlands.</p>
                            <div class="product-meta-user">
                                <span style="font-weight: 700; color: var(--primary-color);">₱ 450.00</span>
                                <div style="display: flex; gap: 8px;">
                                    <button class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn" title="View Analytics"><i
                                            class="fas fa-chart-bar"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="product-card-user">
                        <div class="product-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1511222229239-2a9557458145?w=500"
                                class="product-img-user">
                            <span class="product-status-tag" style="color: var(--warning-color);">Under Review</span>
                        </div>
                        <div class="product-body-user">
                            <h4 style="margin-top: 8px;">Wild Civet Coffee (Luwak)</h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Rare collectible
                                coffee beans with smooth profile.</p>
                            <div class="product-meta-user">
                                <span style="font-weight: 700; color: var(--primary-color);">₱ 1,200.00</span>
                                <div style="display: flex; gap: 8px;">
                                    <button class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn" title="Details"><i class="fas fa-info-circle"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="product-card-user">
                        <div class="product-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1544903256-014a68393e50?w=500"
                                class="product-img-user">
                            <span class="product-status-tag" style="color: var(--success-color);">Active</span>
                        </div>
                        <div class="product-body-user">
                            <span class="export-badge-user">Regional Winner</span>
                            <h4 style="margin-top: 8px;">Arabica Medium Roast (250g)</h4>
                            <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Balanced acidity and
                                fruity notes. Award-winning batch.</p>
                            <div class="product-meta-user">
                                <span style="font-weight: 700; color: var(--primary-color);">₱ 380.00</span>
                                <div style="display: flex; gap: 8px;">
                                    <button class="icon-btn" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn" title="Analytics"><i class="fas fa-chart-bar"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../../js/main.js"></script>
</body>

</html>

