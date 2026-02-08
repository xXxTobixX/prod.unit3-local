<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        /* Analytics Specific Styles */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .report-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dotted var(--border-color);
        }

        .stat-row:last-child {
            border: none;
        }

        .chart-placeholder {
            height: 150px;
            background: var(--bg-color);
            border-radius: 12px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding: 16px;
            margin: 16px 0;
        }

        .bar {
            flex: 1;
            background: var(--primary-color);
            border-radius: 4px 4px 0 0;
            transition: height 0.3s;
        }

        .bar.secondary {
            background: var(--secondary-color);
        }

        .bar.accent {
            background: var(--accent-color);
        }

        .kpi-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .kpi-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            border-bottom: 4px solid var(--primary-color);
            box-shadow: var(--shadow-sm);
        }

        .print-controls {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        @media print {

            .sidebar,
            .top-header,
            .print-controls {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .report-section {
                break-inside: avoid;
                border: 1px solid #ddd !important;
            }
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
                    <li><a href="market-opportunities.php"><i class="fas fa-handshake"></i> <span>Market & Trade
                                Management</span></a></li>
                    <li><a href="incentives-assistance.php"><i class="fas fa-gift"></i> <span>Incentives &
                                Support</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-chart-bar"></i> <span>Reports & Analytics</span></a>
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
                    <h2>Reports & Analytics Command Center</h2>
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
                <div class="print-controls">
                    <button class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Full
                        Report</button>
                    <button class="btn-secondary" onclick="alert('Exporting to Excel...')"><i
                            class="fas fa-file-excel"></i> Export Excel</button>
                    <button class="btn-secondary" onclick="alert('Downloading PDF Summary...')"><i
                            class="fas fa-file-pdf"></i> Download PDF</button>
                </div>

                <!-- KPI Overview Section -->
                <div class="kpi-container">
                    <div class="kpi-card">
                        <div style="font-size: 13px; color: var(--text-muted);">Total MSMEs</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">1,284</div>
                        <div style="font-size: 11px; color: var(--success-color); margin-top: 4px;"><i
                                class="fas fa-arrow-up"></i> 12% vs last year</div>
                    </div>
                    <div class="kpi-card" style="border-bottom-color: var(--secondary-color);">
                        <div style="font-size: 13px; color: var(--text-muted);">Registered Products</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--secondary-color);">4,512</div>
                        <div style="font-size: 11px; color: var(--success-color); margin-top: 4px;"><i
                                class="fas fa-arrow-up"></i> 8% monthly</div>
                    </div>
                    <div class="kpi-card" style="border-bottom-color: var(--success-color);">
                        <div style="font-size: 13px; color: var(--text-muted);">Compliant Enterprises</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--success-color);">942</div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">73% Total Rate</div>
                    </div>
                    <div class="kpi-card" style="border-bottom-color: var(--accent-color);">
                        <div style="font-size: 13px; color: var(--text-muted);">Export Revenue (Est.)</div>
                        <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">₱ 14.2M</div>
                        <div style="font-size: 11px; color: var(--success-color); margin-top: 4px;"><i
                                class="fas fa-arrow-up"></i> 22% growth</div>
                    </div>
                </div>

                <div class="reports-grid">
                    <!-- 1. MSME & User Reports -->
                    <div class="report-section">
                        <div class="report-header">
                            <h4><i class="fas fa-users-cog"></i> MSME & User Distribution</h4>
                        </div>
                        <div class="stat-row"><span>Total Active Users</span><strong>842</strong></div>
                        <div class="stat-row"><span>Inactive / Pending</span><strong>124</strong></div>
                        <div class="stat-row"><span>Top Barangay</span><strong>Poblacion (342)</strong></div>
                        <div class="chart-placeholder">
                            <div class="bar" style="height: 80%;"></div>
                            <div class="bar" style="height: 60%;"></div>
                            <div class="bar" style="height: 40%;"></div>
                            <div class="bar" style="height: 90%;"></div>
                        </div>
                        <p style="font-size: 11px; color: var(--text-muted);">* Distribution across top 4 Sectors</p>
                    </div>

                    <!-- 2. Product Reports -->
                    <div class="report-section">
                        <div class="report-header">
                            <h4><i class="fas fa-box-open"></i> Product Inventory Analysis</h4>
                        </div>
                        <div class="stat-row"><span>Local Commodities</span><strong>3,842</strong></div>
                        <div class="stat-row"><span>Export Ready (Cert)</span><strong>670</strong></div>
                        <div class="stat-row"><span>Category Champion</span><strong>Agriculture (45%)</strong></div>
                        <div class="chart-placeholder">
                            <div class="bar secondary" style="height: 90%;"></div>
                            <div class="bar secondary" style="height: 40%;"></div>
                            <div class="bar secondary" style="height: 55%;"></div>
                            <div class="bar secondary" style="height: 30%;"></div>
                        </div>
                    </div>

                    <!-- 3. Compliance Reports -->
                    <div class="report-section">
                        <div class="report-header">
                            <h4><i class="fas fa-file-signature"></i> Regulatory Compliance</h4>
                        </div>
                        <div class="stat-row"><span>Valid Permits</span><strong>942</strong></div>
                        <div class="stat-row"><span>Expired Permits</span><strong>142</strong></div>
                        <div class="stat-row"><span>Pending Renewal</span><strong>85</strong></div>
                        <div style="margin-top: 20px;">
                            <div
                                style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                <span>Global Compliance Status</span>
                                <strong>82%</strong>
                            </div>
                            <div
                                style="height: 8px; background: var(--border-color); border-radius: 10px; overflow:hidden;">
                                <div style="width: 82%; height:100%; background: var(--success-color);"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Programs & Training Reports -->
                    <div class="report-section">
                        <div class="report-header">
                            <h4><i class="fas fa-graduation-cap"></i> Capacity Building Impact</h4>
                        </div>
                        <div class="stat-row"><span>Total Attendance</span><strong>1,420</strong></div>
                        <div class="stat-row"><span>Prog. Participation</span><strong>72%</strong></div>
                        <div class="stat-row"><span>Certificates Issued</span><strong>892</strong></div>
                        <div class="chart-placeholder">
                            <div class="bar accent" style="height: 30%;"></div>
                            <div class="bar accent" style="height: 50%;"></div>
                            <div class="bar accent" style="height: 85%;"></div>
                            <div class="bar accent" style="height: 70%;"></div>
                        </div>
                    </div>

                    <!-- 5. Market & Export Reports -->
                    <div class="report-section">
                        <div class="report-header">
                            <h4><i class="fas fa-globe-americas"></i> Market Access Stats</h4>
                        </div>
                        <div class="stat-row"><span>Trade Fair Apps</span><strong>156</strong></div>
                        <div class="stat-row"><span>Verified Buyer Matches</span><strong>42</strong></div>
                        <div class="stat-row"><span>Export Vol (Monthly)</span><strong>5.2 Tons</strong></div>
                        <div class="stat-row"><span>Regional Trade Value</span><strong>₱ 4.2M</strong></div>
                    </div>

                    <!-- 6. Monthly Growth Trend -->
                    <div class="report-section" style="grid-column: span 1;">
                        <div class="report-header">
                            <h4><i class="fas fa-chart-line"></i> 2024 Year-to-Date Trend</h4>
                        </div>
                        <div style="height: 180px; display: flex; align-items: flex-end; gap: 4px; padding: 10px;">
                            <div class="bar" style="height: 30%;"></div>
                            <div class="bar" style="height: 45%;"></div>
                            <div class="bar" style="height: 40%;"></div>
                            <div class="bar" style="height: 65%;"></div>
                            <div class="bar" style="height: 55%;"></div>
                            <div class="bar" style="height: 85%;"></div>
                            <div class="bar" style="height: 95%;"></div>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-muted); margin-top: 8px;">
                            <span>JAN</span><span>MAR</span><span>MAY</span><span>JUL</span><span>SEP</span><span>NOV</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../../js/main.js"></script>
</body>

</html>
