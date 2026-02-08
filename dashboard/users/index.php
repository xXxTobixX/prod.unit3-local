<?php 
require_once '../../includes/init.php'; 
if (!isLoggedIn()) {
    redirect('../../login.php');
}
if (!$_SESSION['profile_completed']) {
    redirect('../../complete-profile.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../../images/logo.png" alt="PH Logo" class="gov-logo">
                <div class="header-text">
                    <h1>LGU 3</h1>
                    <p>USERS Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="#"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
                    <li><a href="pages/profile-management.php"><i class="fas fa-id-card"></i> <span>My
                                Profile</span></a></li>
                    <li><a href="pages/my-products.php"><i class="fas fa-box"></i> <span>My Products</span></a></li>
                    <li><a href="pages/compliance-status.php"><i class="fas fa-check-double"></i> <span>Compliance
                                Status</span></a></li>
                    <li><a href="pages/my-training.php"><i class="fas fa-certificate"></i> <span>My Training</span></a>
                    </li>
                    <li><a href="pages/applied-incentives.php"><i class="fas fa-hand-holding-usd"></i> <span>Applied
                                Incentives</span></a></li>
                    <li><a href="pages/market-insights.php"><i class="fas fa-chart-line"></i> <span>Market
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2>Welcome, <?php echo htmlspecialchars(html_entity_decode($_SESSION['business_name'] ?? $_SESSION['user_name'], ENT_QUOTES, 'UTF-8')); ?>!</h2>
                </div>

                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search my data...">
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
                        <span class="badge">2</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars(html_entity_decode($_SESSION['user_name'], ENT_QUOTES, 'UTF-8')); ?></span>
                            <span class="user-role">Business Owner</span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(html_entity_decode($_SESSION['user_name'], ENT_QUOTES, 'UTF-8')); ?>&background=00205B&color=fff"
                            alt="User Avatar" class="avatar">
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-details">
                            <h3>My Products</h3>
                            <p class="stat-number">12</p>
                            <span class="stat-trend positive"><i class="fas fa-plus"></i> 2 new this month</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Compliance Rating</h3>
                            <p class="stat-number">85%</p>
                            <span class="stat-trend positive"><i class="fas fa-arrow-up"></i> Good Standing</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon yellow">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Ongoing Trainings</h3>
                            <p class="stat-number">2</p>
                            <span class="stat-trend"><i class="fas fa-clock"></i> Next session tomorrow</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Active Incentives</h3>
                            <p class="stat-number">1</p>
                            <span class="stat-trend positive"><i class="fas fa-check-circle"></i> Startup Grant</span>
                        </div>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="main-grid">
                    <div class="recent-requests card">
                        <div class="card-header">
                            <h3>Recent Activity</h3>
                            <a href="#" class="view-all">See All History</a>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ref No.</th>
                                        <th>Activity Name</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ref">#BF-2024-05</td>
                                        <td>Product Inspection (Arabica Coffee)</td>
                                        <td>Inspection</td>
                                        <td>Feb 07, 2024</td>
                                        <td><span class="status status-approved">Passed</span></td>
                                        <td><button class="btn-action">View Report</button></td>
                                    </tr>
                                    <tr>
                                        <td class="ref">#BF-2024-04</td>
                                        <td>Mayor's Permit Renewal</td>
                                        <td>Compliance</td>
                                        <td>Feb 05, 2024</td>
                                        <td><span class="status status-approved">Valid</span></td>
                                        <td><button class="btn-action">View Permit</button></td>
                                    </tr>
                                    <tr>
                                        <td class="ref">#BF-2024-03</td>
                                        <td>Digital Marketing Workshop</td>
                                        <td>Training</td>
                                        <td>Feb 01, 2024</td>
                                        <td><span class="status status-pending">Ongoing</span></td>
                                        <td><button class="btn-action">Go to Room</button></td>
                                    </tr>
                                    <tr>
                                        <td class="ref">#BF-2024-02</td>
                                        <td>Logistics Subsidy Claim</td>
                                        <td>Incentive</td>
                                        <td>Jan 28, 2024</td>
                                        <td><span class="status status-approved">Disbursed</span></td>
                                        <td><button class="btn-action">Reciept</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="project-tracker card">
                        <div class="card-header">
                            <h3>My Compliance Checklist</h3>
                        </div>
                        <div class="project-list">
                            <div class="project-item">
                                <div class="project-info">
                                    <span>Business Permit</span>
                                    <span>Valid</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress green" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="project-item">
                                <div class="project-info">
                                    <span>DTI Registration</span>
                                    <span>Valid</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress green" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="project-item">
                                <div class="project-info">
                                    <span>FDA Product Certification</span>
                                    <span>Active (2 of 3 products)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress orange" style="width: 66%;"></div>
                                </div>
                            </div>
                            <div class="project-item">
                                <div class="project-info">
                                    <span>Export Readiness Rating</span>
                                    <span>Improving</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress blue" style="width: 75%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="system-announcement">
                            <h4><i class="fas fa-lightbulb"></i> Pro-tip</h4>
                            <p>Completing the "Export Quality" training can boost your rating to 90% and unlock more
                                grants!</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../js/main.js"></script>
</body>

</html>
