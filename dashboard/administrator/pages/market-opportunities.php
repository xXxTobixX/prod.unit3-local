<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market & Trade Management - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        /* Market & Trade Specific Styles */
        .market-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .market-tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .tab-item {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted);
            position: relative;
        }

        .tab-item.active {
            color: var(--primary-color);
        }

        .tab-item.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
        }

        .opportunity-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .opportunity-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        .card-tag {
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: inline-block;
        }

        .tag-fair {
            background: rgba(139, 92, 246, 0.1);
            color: #7C3AED;
        }

        .tag-buyer {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .tag-program {
            background: rgba(250, 189, 46, 0.1);
            color: #B45309;
        }

        .buyer-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 12px 0;
            padding: 12px;
            background: var(--bg-color);
            border-radius: 10px;
        }

        .buyer-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .btn-access {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
        }

        /* Access Program Sidebar */
        .sidebar-card {
            background: #00205B;
            color: white;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .program-stat {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
                    <li><a href="product-registry.php"><i class="fas fa-building"></i> <span>Product & MSME
                                Registry</span></a></li>
                    <li><a href="compliance-monitoring.php"><i class="fas fa-clipboard-check"></i> <span>Compliance
                                Monitoring</span></a></li>
                    <li><a href="program-training.php"><i class="fas fa-graduation-cap"></i> <span>Program &
                                Training</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-handshake"></i> <span>Market & Trade
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

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>Market & Trade Opportunities</h2>
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
                        <span class="badge">3</span>
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
                <div class="market-tabs">
                    <div class="tab-item active" onclick="alert('Viewing Trade Fairs')">Trade Fairs</div>
                    <div class="tab-item" onclick="alert('Viewing Buyer Requests')">Buyer Requests</div>
                    <div class="tab-item" onclick="alert('Viewing Market Access Programs')">Market Access Programs</div>
                </div>

                <div class="market-grid">
                    <!-- Column 1: Active Listings -->
                    <div>
                        <h3 style="margin-bottom: 20px;">Active Opportunities</h3>

                        <!-- Trade Fair Listing -->
                        <div class="opportunity-card">
                            <span class="card-tag tag-fair">International Trade Fair</span>
                            <h4>Manila FAME 2024 Showcase</h4>
                            <p style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">Premium slot for LGU
                                3 top-tier MSMEs. Full subsidy for booth rentals and logistics support.</p>
                            <div class="buyer-info">
                                <i class="fas fa-calendar-alt" style="color: var(--primary-color);"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: 13px;">October 15-17, 2024</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">SMX Convention Center,
                                        Manila</div>
                                </div>
                            </div>
                            <button class="btn-access" onclick="alert('Opening Application Interface...')">Allocate
                                Slots</button>
                        </div>

                        <!-- Buyer Request Listing -->
                        <div class="opportunity-card">
                            <span class="card-tag tag-buyer">Bulk Buyer Request</span>
                            <h4>Institutional Order: High-End Packaging</h4>
                            <p style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">A luxury hotel chain
                                is looking for sustainable, handwoven bamboo packaging for amenities.</p>
                            <div class="buyer-info">
                                <img src="https://ui-avatars.com/api/?name=Aman+Resorts&background=random"
                                    class="buyer-avatar">
                                <div>
                                    <div style="font-weight: 600; font-size: 13px;">Enterprise Partner: Aman Resorts
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-muted);">Quantity: 5,000 sets /
                                        quarter</div>
                                </div>
                            </div>
                            <button class="btn-access" style="background: var(--success-color);"
                                onclick="alert('Matching MSMEs...')">Match with MSMEs</button>
                        </div>
                    </div>

                    <!-- Column 2: Programs & Stats -->
                    <div>
                        <h3 style="margin-bottom: 20px;">Market Access Programs</h3>

                        <div class="sidebar-card">
                            <span class="card-tag" style="background: rgba(255,255,255,0.2); color: white;">Ongoing
                                Program</span>
                            <h4 style="margin-top: 10px;">Export Mastery Path</h4>
                            <p style="font-size: 13px; opacity: 0.8; margin-top: 10px;">Accelerated program for MSMEs
                                aiming for the European and US markets.</p>
                            <div class="program-stat">
                                <span>Active Participants</span>
                                <strong>12 MSMEs</strong>
                            </div>
                            <div class="program-stat">
                                <span>Export Readiness</span>
                                <strong>78% Avg.</strong>
                            </div>
                            <button class="btn-access"
                                style="background: white; color: var(--primary-color); margin-top: 20px;">Manage
                                Program</button>
                        </div>

                        <div class="card" style="padding: 24px;">
                            <h4 style="margin-bottom: 16px;"><i class="fas fa-chart-line"></i> Market Trends</h4>
                            <div
                                style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                                    <span>Eco-Friendly Products</span>
                                    <span style="color: var(--success-color);"><i class="fas fa-arrow-up"></i>
                                        +24%</span>
                                </div>
                                <p style="font-size: 11px; color: var(--text-muted);">High demand in Western markets</p>
                            </div>
                            <div>
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600;">
                                    <span>Frozen Delicacies</span>
                                    <span style="color: var(--success-color);"><i class="fas fa-arrow-up"></i>
                                        +12%</span>
                                </div>
                                <p style="font-size: 11px; color: var(--text-muted);">Rising interest from Asian
                                    importers</p>
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
