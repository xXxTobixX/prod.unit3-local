<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 

$unreadNotifs = getUnreadNotifications();
$notifCount = count($unreadNotifs);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - LGU 3 MSME Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        .help-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
            animation: fadeIn 0.8s ease-out;
        }

        .help-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .help-header h2 {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 16px;
        }

        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .help-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .help-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        .help-icon {
            width: 60px;
            height: 60px;
            background: rgba(0, 32, 91, 0.1);
            color: var(--primary-color);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        body.dark-mode .help-icon {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .help-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
            color: var(--text-main);
        }

        .help-card p {
            color: var(--text-muted);
            line-height: 1.6;
            font-size: 14px;
        }

        .contact-support {
            margin-top: 60px;
            background: var(--primary-color);
            color: white;
            padding: 40px;
            border-radius: 24px;
            text-align: center;
        }

        .contact-support h3 {
            color: white;
            margin-bottom: 15px;
        }

        .support-btns {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 25px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                    <li><a href="profile-management.php"><i class="fas fa-id-card"></i> <span>My Profile</span></a></li>
                    <li><a href="my-products.php"><i class="fas fa-box"></i> <span>My Products</span></a></li>
                    <li><a href="compliance-status.php"><i class="fas fa-check-double"></i> <span>Compliance Status</span></a></li>
                    <li><a href="my-training.php"><i class="fas fa-certificate"></i> <span>My Training</span></a></li>
                    <li><a href="applied-incentives.php"><i class="fas fa-hand-holding-usd"></i> <span>Applied Incentives</span></a></li>
                    <li><a href="market-insights.php"><i class="fas fa-chart-line"></i> <span>Market Insights</span></a></li>
                </ul>

                <div class="nav-divider"></div>

                <ul>
                    <li><a href="profile-management.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li class="active"><a href="#"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
                    <li class="logout"><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>Help Center</h2>
                </div>
                <div class="header-right">
                    <div class="theme-toggle">
                        <div class="theme-switch">
                            <div class="theme-switch-handle">
                                <i class="fas fa-sun"></i>
                                <i class="fas fa-moon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="help-container">
                <div class="help-header">
                    <h2>How can we help you?</h2>
                    <p>Search our guides or contact our support team for assistance.</p>
                </div>

                <div class="help-grid">
                    <div class="help-card">
                        <div class="help-icon"><i class="fas fa-user-check"></i></div>
                        <h3>Getting Verified</h3>
                        <p>Learn how to upload your business permits (Mayor's, DTI, BIR) and get verified to access LGU incentives.</p>
                    </div>
                    <div class="help-card">
                        <div class="help-icon"><i class="fas fa-box-open"></i></div>
                        <h3>Product Portfolio</h3>
                        <p>A step-by-step guide on registering your products, adding variants, and managing your inventory effectively.</p>
                    </div>
                    <div class="help-card">
                        <div class="help-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Account Security</h3>
                        <p>Keep your account safe by enabling Two-Factor Authentication (OTP) and learning how to update your password.</p>
                    </div>
                    <div class="help-card">
                        <div class="help-icon"><i class="fas fa-chart-pie"></i></div>
                        <h3>Incentives & Reports</h3>
                        <p>Understand how incentives are computed and how to download your compliance and training reports.</p>
                    </div>
                </div>

                <div class="contact-support">
                    <h3>Still need help?</h3>
                    <p>Our support team is available Monday to Friday, 8:00 AM - 5:00 PM.</p>
                    <div class="support-btns">
                        <button class="btn-secondary" style="background: white; color: var(--primary-color);">
                            <i class="fas fa-envelope"></i> Email Support
                        </button>
                        <button class="btn-secondary" style="background: white; color: var(--primary-color);">
                            <i class="fas fa-phone"></i> Call Hotline
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../js/main.js"></script>
</body>
</html>
