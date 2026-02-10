<?php 
require_once '../../includes/init.php'; 

if (!isLoggedIn()) {
    redirect('../../login.php');
}
if (!isset($_SESSION['profile_completed']) || !$_SESSION['profile_completed']) {
    redirect('../../complete-profile.php');
}

$userId = $_SESSION['user_id'];
$db = db();

// 1. My Products Count
$productCount = $db->fetchOne("SELECT COUNT(*) as total FROM user_products WHERE user_id = ?", [$userId])['total'] ?? 0;

// 2. Compliance Rating
$requiredDocTypes = ["Mayor's Permit", "DTI Registration", "BIR Certificate", "Export Certification"];
$userDocs = $db->fetchAll("SELECT * FROM business_documents WHERE user_id = ?", [$userId]);
$verifiedCount = 0;
foreach($userDocs as $d) if($d['status'] === 'verified') $verifiedCount++;
$totalRequired = count($requiredDocTypes);
$complianceScore = $totalRequired > 0 ? round(($verifiedCount / $totalRequired) * 100) : 0;

// 3. Recent Activity (Combining Notifications and Documents)
$activities = $db->fetchAll("
    SELECT title, message as details, type as category, created_at, 'notification' as source
    FROM notifications 
    WHERE user_id = ? OR (role = 'user' AND user_id IS NULL)
    ORDER BY created_at DESC LIMIT 4
", [$userId]);

// If no notifications, use documents as fall back for activity
if (empty($activities)) {
    foreach($userDocs as $doc) {
        $activities[] = [
            'title' => 'Document ' . ucfirst($doc['status']),
            'details' => $doc['document_type'],
            'category' => $doc['status'] === 'verified' ? 'success' : ($doc['status'] === 'rejected' ? 'danger' : 'warning'),
            'created_at' => $doc['uploaded_at'],
            'source' => 'document'
        ];
    }
    usort($activities, function($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
    $activities = array_slice($activities, 0, 4);
}

// Helpers for Checklist
function getStatusForChecklist($docs, $type) {
    foreach($docs as $d) {
        if($d['document_type'] === $type) return $d['status'];
    }
    return 'not_uploaded';
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
                            <p class="stat-number"><?php echo $productCount; ?></p>
                            <span class="stat-trend positive">Active in Registry</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Compliance Rating</h3>
                            <p class="stat-number"><?php echo $complianceScore; ?>%</p>
                            <span class="stat-trend <?php echo $complianceScore >= 75 ? 'positive' : ''; ?>">
                                <i class="fas <?php echo $complianceScore >= 75 ? 'fa-check-circle' : 'fa-info-circle'; ?>"></i> 
                                <?php echo $complianceScore >= 100 ? 'Fully Compliant' : ($complianceScore >= 75 ? 'Good Standing' : 'Needs Attention'); ?>
                            </span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon yellow">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Ongoing Trainings</h3>
                            <p class="stat-number">0</p>
                            <span class="stat-trend"><i class="fas fa-clock"></i> No active sessions</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Active Incentives</h3>
                            <p class="stat-number">0</p>
                            <span class="stat-trend"><i class="fas fa-times-circle"></i> None applied</span>
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
                                    <?php if(empty($activities)): ?>
                                        <tr><td colspan="6" style="text-align:center; padding: 20px;">No recent activity found</td></tr>
                                    <?php else: ?>
                                        <?php foreach($activities as $act): 
                                            $statusLabel = 'Info';
                                            $statusClass = 'status-pending';
                                            if(strpos(strtolower($act['category']), 'success') !== false) { $statusLabel = 'Success'; $statusClass = 'status-approved'; }
                                            if(strpos(strtolower($act['category']), 'error') !== false || strpos(strtolower($act['category']), 'danger') !== false) { $statusLabel = 'Failed'; $statusClass = 'status-rejected'; }
                                        ?>
                                            <tr>
                                                <td class="ref">#<?php echo strtoupper(substr($act['source'], 0, 1)); ?>-<?php echo date('Ymd', strtotime($act['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($act['title']); ?></td>
                                                <td><?php echo ucfirst($act['source']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($act['created_at'])); ?></td>
                                                <td><span class="status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                                                <td><button class="btn-action" onclick="showActivityDetails('<?php echo addslashes($act['details']); ?>')">View</button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="project-tracker card">
                        <div class="card-header">
                            <h3>My Compliance Checklist</h3>
                        </div>
                        <div class="project-list">
                            <?php foreach($requiredDocTypes as $type): 
                                $status = getStatusForChecklist($userDocs, $type);
                                $pWidth = 0; $pClass = 'orange'; $pText = 'Not Uploaded';
                                if($status === 'verified') { $pWidth = 100; $pClass = 'green'; $pText = 'Verified'; }
                                elseif($status === 'pending') { $pWidth = 50; $pClass = 'blue'; $pText = 'In Review'; }
                                elseif($status === 'rejected') { $pWidth = 30; $pClass = 'red'; $pText = 'Rejected'; }
                            ?>
                                <div class="project-item">
                                    <div class="project-info">
                                        <span><?php echo $type; ?></span>
                                        <span><?php echo $pText; ?></span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress <?php echo $pClass; ?>" style="width: <?php echo $pWidth; ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/main.js"></script>
    <script>
        function showActivityDetails(details) {
            Swal.fire({
                title: 'Activity Details',
                text: details,
                icon: 'info',
                confirmButtonColor: '#00205B'
            });
        }
    </script>
</body>

</html>
