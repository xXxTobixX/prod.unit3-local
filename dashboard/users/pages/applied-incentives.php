<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 

$unreadNotifs = getUnreadNotifications();
$notifCount = count($unreadNotifs);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applied Incentives - LGU 3 USERS Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        .incentive-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, #003699 100%);
            color: white;
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-stats {
            display: flex;
            gap: 40px;
        }

        .h-stat-item {
            text-align: center;
        }

        .h-stat-val {
            font-size: 24px;
            font-weight: 800;
            display: block;
        }

        .h-stat-label {
            font-size: 11px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .application-timeline {
            position: relative;
            padding-left: 40px;
        }

        .application-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            height: 100%;
            width: 2px;
            background: var(--border-color);
        }

        .timeline-node {
            position: relative;
            margin-bottom: 32px;
        }

        .timeline-node::after {
            content: '';
            position: absolute;
            left: -33px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 4px solid var(--primary-color);
        }

        .timeline-node.completed::after {
            background: var(--success-color);
            border-color: var(--success-color);
        }

        .node-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
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
                    <p>USERS Portal</p>
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
                    <li class="active"><a href="#"><i class="fas fa-hand-holding-usd"></i> <span>Applied
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
                    <h2>Incentives & Financial Support</h2>
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
                    <button class="btn-primary" onclick="alert('Starting New Application...')"><i
                            class="fas fa-paper-plane"></i> Apply for Incentive</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="incentive-hero">
                    <div>
                        <h2 style="margin-bottom: 8px;">Support Summary</h2>
                        <p style="opacity: 0.9; font-size: 14px;">Total financial assistance and grants received to
                            date.</p>
                    </div>
                    <div class="hero-stats">
                        <div class="h-stat-item">
                            <span class="h-stat-val">₱ 75,000</span>
                            <span class="h-stat-label">Total Gained</span>
                        </div>
                        <div class="h-stat-item">
                            <span class="h-stat-val">1</span>
                            <span class="h-stat-label">Active Loans</span>
                        </div>
                        <div class="h-stat-item">
                            <span class="h-stat-val">2</span>
                            <span class="h-stat-label">Completed</span>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 400px; gap: 32px;">
                    <div>
                        <h3 style="margin-bottom: 24px;">Active Application Tracking</h3>

                        <div class="application-timeline">
                            <div class="timeline-node completed">
                                <div class="node-card">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                        <div>
                                            <h4 style="color: var(--primary-color);">#GR-7721: Startup Recovery Grant
                                            </h4>
                                            <small style="color: var(--text-muted);">Submitted on Feb 01, 2024</small>
                                        </div>
                                        <span class="status-pill-user pill-valid">Step 1: Done</span>
                                    </div>
                                    <p style="font-size: 14px; line-height: 1.5;">Your initial documents have been
                                        verified by the LGU screening committee. Everything is in order.</p>
                                </div>
                            </div>

                            <div class="timeline-node">
                                <div class="node-card" style="border-left: 4px solid var(--primary-color);">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                        <div>
                                            <h4 style="color: var(--primary-color);">Current Status: LGU Review</h4>
                                            <small style="color: var(--text-muted);">In progress since Feb 05,
                                                2024</small>
                                        </div>
                                        <span class="status-pill-user pill-pending">In Progress</span>
                                    </div>
                                    <p style="font-size: 14px; line-height: 1.5;">The specialized committee is currently
                                        assessing the impact potential of your modernization project.</p>
                                    <div
                                        style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); font-size: 13px; color: var(--text-muted);">
                                        <i class="fas fa-info-circle"></i> Estimated time: 3-5 working days
                                    </div>
                                </div>
                            </div>

                            <div class="timeline-node" style="opacity: 0.5;">
                                <div class="node-card">
                                    <h4>Step 3: Board Approval</h4>
                                    <p style="font-size: 13px; margin-top: 8px;">Final review by the LGU Governing
                                        Board.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="card" style="padding: 24px;">
                            <h3 style="margin-bottom: 20px;">History</h3>

                            <div style="margin-top: 16px;">
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                                    <strong>Equipment Rebate</strong>
                                    <span style="color: var(--success-color);">₱ 25,000</span>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">Date: Jan 12, 2024 • Ref:
                                    #RE-001</div>
                            </div>

                            <div
                                style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border-color);">
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                                    <strong>Logistics Subsidy</strong>
                                    <span style="color: var(--success-color);">₱ 50,000</span>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted);">Date: Dec 20, 2023 • Ref:
                                    #LS-992</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../js/main.js"></script>
</body>

</html>

