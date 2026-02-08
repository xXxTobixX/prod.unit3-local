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
    <title>Market Insights - LGU 3 MSME Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        .trend-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }

        .trend-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        .market-opportunity-user {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 32px;
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 1fr 200px;
            gap: 40px;
            align-items: center;
        }

        .match-badge {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .lead-meta {
            display: flex;
            gap: 20px;
            margin-top: 16px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .price-trend-graph {
            height: 100px;
            display: flex;
            align-items: flex-end;
            gap: 4px;
            margin-top: 16px;
        }

        .bar-mini {
            flex: 1;
            background: var(--primary-light);
            border-radius: 2px 2px 0 0;
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
                    <li><a href="my-products.php"><i class="fas fa-box"></i> <span>My Products</span></a></li>
                    <li><a href="compliance-status.php"><i class="fas fa-check-double"></i> <span>Compliance
                                Status</span></a></li>
                    <li><a href="my-training.php"><i class="fas fa-certificate"></i> <span>My Training</span></a></li>
                    <li><a href="applied-incentives.php"><i class="fas fa-hand-holding-usd"></i> <span>Applied
                                Incentives</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-chart-line"></i> <span>Market Insights</span></a>
                    </li>
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
                    <h2>Market Intelligence & Leads</h2>
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
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search markets...">
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="trend-grid">
                    <div class="trend-card">
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Arabica
                            Coffee Price</div>
                        <div style="font-size: 24px; font-weight: 800; color: var(--success-color); margin: 8px 0;">₱
                            420.00 / kg <i class="fas fa-arrow-up" style="font-size: 16px;"></i></div>
                        <div style="font-size: 11px; color: var(--text-muted);">+15% vs last quarter</div>
                        <div class="price-trend-graph">
                            <div class="bar-mini" style="height: 40%;"></div>
                            <div class="bar-mini" style="height: 55%;"></div>
                            <div class="bar-mini" style="height: 45%;"></div>
                            <div class="bar-mini" style="height: 70%;"></div>
                            <div class="bar-mini" style="height: 60%;"></div>
                            <div class="bar-mini" style="height: 85%;"></div>
                            <div class="bar-mini" style="height: 95%; background: var(--success-color);"></div>
                        </div>
                    </div>

                    <div class="trend-card">
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Highest
                            Demand Sector</div>
                        <div style="font-size: 24px; font-weight: 800; color: var(--primary-color); margin: 8px 0;">
                            Processed Food</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Trending in: Singapore, Malaysia</div>
                        <div style="margin-top: 24px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-info-circle" style="color: var(--primary-light);"></i>
                            <span style="font-size: 12px;">High export potential this season</span>
                        </div>
                    </div>

                    <div class="trend-card" style="background: var(--primary-color); color: white;">
                        <div style="font-size: 12px; opacity: 0.8; text-transform: uppercase;">Global Readiness Score
                        </div>
                        <div style="font-size: 32px; font-weight: 800; margin: 12px 0;">78 / 100</div>
                        <p style="font-size: 12px; opacity: 0.8;">You are in the <strong>Top 15%</strong> of MSMEs in
                            LGU 3!</p>
                        <button class="btn-action"
                            style="margin-top: 20px; width: 100%; border-color: white; color: white;">Get Detailed
                            Report</button>
                    </div>
                </div>

                <h3 style="margin-bottom: 24px;">Recommended Leads for You</h3>

                <!-- Lead 1 -->
                <div class="market-opportunity-user">
                    <div>
                        <span class="match-badge"><i class="fas fa-star"></i> 95% Match</span>
                        <h3 style="margin-top: 12px;">Premium Coffee Importer - Dubai, UAE</h3>
                        <p style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">Buyer looking for
                            high-altitude single origin coffee beans with certifications. Initial order: 1,000kg
                            monthly.</p>
                        <div class="lead-meta">
                            <span><i class="fas fa-calendar"></i> Posted: 2 days ago</span>
                            <span><i class="fas fa-tag"></i> Coffee / Agriculture</span>
                            <span><i class="fas fa-map-marker-alt"></i> Middle East Market</span>
                        </div>
                    </div>
                    <div class="lead-actions">
                        <button class="btn-primary" style="width: 100%; margin-bottom: 12px;">Express Interest</button>
                        <button class="btn-secondary" style="width: 100%;">View Requirements</button>
                    </div>
                </div>

                <!-- Lead 2 -->
                <div class="market-opportunity-user">
                    <div>
                        <span class="match-badge"
                            style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color);"><i
                                class="fas fa-store"></i> Retail Lead</span>
                        <h3 style="margin-top: 12px;">National organic grocery chain expansion</h3>
                        <p style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">Opening 10 new stores in
                            Metro Manila. Inviting local suppliers for organic condiments and coffee.</p>
                        <div class="lead-meta">
                            <span><i class="fas fa-calendar"></i> Deadline: Feb 28, 2024</span>
                            <span><i class="fas fa-tag"></i> Retail / Distribution</span>
                            <span><i class="fas fa-map-marker-alt"></i> Domestic Market</span>
                        </div>
                    </div>
                    <div class="lead-actions">
                        <button class="btn-primary" style="width: 100%; margin-bottom: 12px;">Apply for Slot</button>
                        <button class="btn-secondary" style="width: 100%;">Contact Agent</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../../js/main.js"></script>
</body>

</html>

