<?php 
require_once '../../../includes/init.php'; 
if (!isLoggedIn()) { redirect('../../../login.php'); } 
if (!$_SESSION['profile_completed']) { redirect('../../../complete-profile.php'); } 

$userId = $_SESSION['user_id'];
$db = db();

// Fetch user documents
$documents = $db->fetchAll("SELECT * FROM business_documents WHERE user_id = :id", ['id' => $userId]);

// Helper to get doc by type
function getDocByType($docs, $type) {
    foreach ($docs as $doc) {
        if ($doc['document_type'] === $type) return $doc;
    }
    return null;
}

$requiredDocs = [
    ['type' => "Mayor's Permit", 'icon' => 'fa-file-invoice', 'desc' => 'LGU Business Authorization'],
    ['type' => 'DTI Registration', 'icon' => 'fa-file-contract', 'desc' => 'National Business Name Registry'],
    ['type' => 'BIR Certificate', 'icon' => 'fa-file-signature', 'desc' => 'Tax Compliance Certificate'],
    ['type' => 'Export Certification', 'icon' => 'fa-ship', 'desc' => 'Bureau of Customs / DTI Export Permit']
];

// Calculate Score
$verifiedCount = 0;
foreach($documents as $d) if($d['status'] === 'verified') $verifiedCount++;
$totalRequired = count($requiredDocs);
$score = $totalRequired > 0 ? round(($verifiedCount / $totalRequired) * 100) : 0;
$dashOffset = 440 - (440 * ($score / 100));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Status - LGU 3 MSME Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        .compliance-wrapper {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 24px;
        }

        .document-checklist {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .doc-item {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .doc-meta {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-pill-user {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .pill-valid {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .pill-expired {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .pill-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .health-meter {
            text-align: center;
            padding: 40px 24px;
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            position: sticky;
            top: 24px;
        }

        .circle-progress-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .circle-svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .circle-bg {
            fill: none;
            stroke: var(--border-color);
            stroke-width: 8;
        }

        .circle-bar {
            fill: none;
            stroke: var(--success-color);
            stroke-width: 10;
            stroke-dasharray: 440;
            stroke-dashoffset: 66;
            /* 85% */
            stroke-linecap: round;
            filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.3));
            transition: all 0.5s ease;
        }

        .percentage-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 32px;
            font-weight: 800;
            color: var(--success-color);
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        }

        body.dark-mode .percentage-text {
            color: #10b981;
            text-shadow: 0 0 25px rgba(16, 185, 129, 0.4);
        }

        body.dark-mode .circle-bar {
            stroke: #10b981;
            filter: drop-shadow(0 0 8px rgba(16, 185, 129, 0.5));
        }

        body.dark-mode .circle-bg {
            stroke: rgba(255, 255, 255, 0.05);
        }

        /* Modal Styles (Synced with Profile Page) */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
            animation: modalSlideUp 0.3s ease-out;
        }

        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-muted);
            cursor: pointer;
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
                    <li class="active"><a href="#"><i class="fas fa-check-double"></i> <span>Compliance
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
                    <h2>Compliance & Regulatory Status</h2>
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
                        <img src="https://ui-avatars.com/api/?name=Juana+Dela+Cruz&background=00205B&color=fff"
                            alt="User Avatar" class="avatar">
                    </div>
                    <button class="btn-secondary" onclick="window.print()"><i class="fas fa-download"></i> Download
                        Report</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="compliance-wrapper">
                    <div class="document-checklist">
                        <h3 style="margin-bottom: 8px;">Permit Checklist</h3>

                        <div id="compliance-list">
                            <?php foreach ($requiredDocs as $req): 
                                $existingDoc = getDocByType($documents, $req['type']);
                                $statusClass = 'pill-pending';
                                $statusText = 'Not Uploaded';
                                
                                if ($existingDoc) {
                                    if ($existingDoc['status'] === 'verified') {
                                        $statusClass = 'pill-valid';
                                        $statusText = 'Verified';
                                    } elseif ($existingDoc['status'] === 'rejected') {
                                        $statusClass = 'pill-expired';
                                        $statusText = 'Rejected';
                                    } else {
                                        $statusClass = 'pill-pending';
                                        $statusText = 'In Review';
                                    }
                                }
                            ?>
                                <div class="doc-item">
                                    <div class="doc-meta">
                                        <div class="icon-btn" style="background: rgba(59, 130, 246, 0.1);">
                                            <i class="fas <?php echo $req['icon']; ?>"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;"><?php echo $req['type']; ?></div>
                                            <div style="font-size: 11px; color: var(--text-muted);"><?php echo $req['desc']; ?></div>
                                            <?php if ($existingDoc && $existingDoc['status'] === 'rejected'): ?>
                                                <div style="font-size: 10px; color: var(--danger-color); margin-top: 4px;">
                                                    <i class="fas fa-info-circle"></i> Reason: <?php echo htmlspecialchars($existingDoc['remarks']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <span class="status-pill-user <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        <?php if (!$existingDoc): ?>
                                            <button class="btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="openUploadModal('<?php echo $req['type']; ?>')">Upload</button>
                                        <?php else: ?>
                                            <button class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" onclick="openUploadModal('<?php echo $req['type']; ?>')">
                                                <?php echo $existingDoc['status'] === 'rejected' ? 'Re-upload' : 'Renew / Replace'; ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="health-meter">
                        <h3>Compliance Score</h3>
                        <div class="circle-progress-wrapper">
                            <svg class="circle-svg" viewBox="0 0 160 160">
                                <circle class="circle-bg" cx="80" cy="80" r="70"></circle>
                                <circle class="circle-bar" cx="80" cy="80" r="70" style="stroke-dashoffset: <?php echo $dashOffset; ?>;"></circle>
                            </svg>
                            <div class="percentage-text"><?php echo $score; ?>%</div>
                        </div>
                        <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">
                            <?php if($score == 100): ?>
                                You are **fully compliant**! Your business is ready for all regional and national incentives.
                            <?php elseif($score >= 75): ?>
                                You are **highly compliant**! Complete the remaining documents to unlock export eligibility.
                            <?php else: ?>
                                Focus on uploading the **Required 4** documents to improve your compliance status.
                            <?php endif; ?>
                        </p>
                        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 24px 0;">
                        <h4 style="margin-bottom: 12px;">Requirements for Export</h4>
                        <ul style="text-align: left; font-size: 13px; color: var(--text-muted); list-style: none; padding: 0;">
                            <?php foreach($requiredDocs as $req): 
                                $isVerified = false;
                                foreach($documents as $d) if($d['document_type'] === $req['type'] && $d['status'] === 'verified') $isVerified = true;
                            ?>
                                <li style="margin-bottom: 8px;">
                                    <i class="fas <?php echo $isVerified ? 'fa-check-circle' : 'fa-times-circle'; ?>"
                                       style="color: var(--<?php echo $isVerified ? 'success' : 'danger'; ?>-color); margin-right: 8px;"></i> 
                                    <?php echo $req['type']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Upload Modal (Synced with Profile) -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Renew/Upload <span id="modal-doc-type">Document</span></h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="uploadForm">
                <input type="hidden" name="document_type" id="hidden-doc-type">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Select New File (PDF, JPG, PNG - Max 5MB)</label>
                    <input type="file" name="document_file" id="document_file" required accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="button" class="btn-secondary" style="flex: 1;" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary" style="flex: 1;">Proceed Submission</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openUploadModal(type) {
            document.getElementById('modal-doc-type').textContent = type;
            document.getElementById('hidden-doc-type').value = type;
            document.getElementById('uploadModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.remove('active');
            document.getElementById('uploadForm').reset();
        }

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'upload-document');

            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            fetch('../../../ajax/compliance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Submitted!',
                        text: 'Your document has been sent for verification.',
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Failed', data.message, 'error');
                    btn.innerHTML = 'Proceed Submission';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred.', 'error');
                btn.innerHTML = 'Proceed Submission';
                btn.disabled = false;
            });
        });
    </script>

    <script src="../../../js/main.js"></script>
</body>

</html>

