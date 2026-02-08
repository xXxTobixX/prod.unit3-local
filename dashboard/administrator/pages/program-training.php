<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs & Training - LGU 3 Administrative Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css">
    <style>
        /* Custom Programs Styles */
        .programs-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .program-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.3s;
            display: flex;
            gap: 20px;
        }

        .program-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .program-img {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
        }

        .program-info {
            flex: 1;
        }

        .program-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
            display: inline-block;
        }

        .badge-training {
            background: rgba(59, 130, 246, 0.1);
            color: #2563EB;
        }

        .badge-fair {
            background: rgba(139, 92, 246, 0.1);
            color: #7C3AED;
        }

        .badge-market {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .enroll-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .enroll-btn:hover {
            background: var(--primary-light);
        }

        .opportunity-item {
            padding: 16px;
            background: var(--bg-color);
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 4px solid var(--accent-color);
        }

        .opportunity-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .opportunity-tag {
            font-size: 11px;
            padding: 2px 8px;
            background: rgba(250, 189, 46, 0.1);
            color: #B45309;
            border-radius: 4px;
            font-weight: 700;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 32px;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            color: var(--text-main);
        }

        .attendance-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 16px;
        }

        .attendance-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .progress-pill {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 700;
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
                    <li class="active"><a href="#"><i class="fas fa-graduation-cap"></i> <span>Program &
                                Training</span></a></li>
                    <li><a href="market-opportunities.php"><i class="fas fa-handshake"></i> <span>Market & Trade
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
                    <h2>Programs & Capacity Building</h2>
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
                    <button class="btn-primary" onclick="openProgramModal()"><i class="fas fa-plus"></i> Create New
                        New Program</button>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="programs-container">
                    <!-- Training & Trade Fairs -->
                    <div>
                        <div class="section-title">
                            <h3>Available Training Programs</h3>
                        </div>

                        <div class="program-card">
                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=300"
                                class="program-img">
                            <div class="program-info">
                                <span class="program-badge badge-training">Skills Development</span>
                                <h4>Digital Marketing for MSMEs</h4>
                                <p style="font-size: 14px; color: var(--text-muted); margin: 8px 0;">Learn how to
                                    leverage social media and e-commerce platforms to scale your reach.</p>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                                    <div style="font-size: 13px;"><i class="fas fa-calendar"></i> Starts: Feb 20, 2024
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-secondary" style="padding: 8px 12px; font-size: 13px;"
                                            onclick="openTrackingModal('Digital Marketing')"><i
                                                class="fas fa-chart-line"></i> Track Progress</button>
                                        <button class="enroll-btn" onclick="openEnrollModal('Digital Marketing')">Enroll
                                            Enterprise</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="program-card">
                            <img src="https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=300"
                                class="program-img">
                            <div class="program-info">
                                <span class="program-badge badge-training">Compliance</span>
                                <h4>Export Quality Standards & Certification</h4>
                                <p style="font-size: 14px; color: var(--text-muted); margin: 8px 0;">Advanced workshop
                                    on meeting international food safety and packaging standards.</p>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                                    <div style="font-size: 13px;"><i class="fas fa-calendar"></i> Starts: Mar 05, 2024
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-secondary" style="padding: 8px 12px; font-size: 13px;"
                                            onclick="openTrackingModal('Export Quality')">Track Progress</button>
                                        <button class="enroll-btn" onclick="openEnrollModal('Export Quality')">Enroll
                                            Enterprise</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title" style="margin-top: 40px;">
                            <h3>Upcoming Trade Fairs</h3>
                        </div>

                        <div class="program-card">
                            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300"
                                class="program-img">
                            <div class="program-info">
                                <span class="program-badge badge-fair">Regional Fair</span>
                                <h4>LGU 3 Summer Expo 2024</h4>
                                <p style="font-size: 14px; color: var(--text-muted); margin: 8px 0;">The biggest annual
                                    showcase for local products in the province. 50+ slots available.</p>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                                    <div style="font-size: 13px;"><i class="fas fa-map-marker-alt"></i> Provincial
                                        Capitol</div>
                                    <button class="enroll-btn" style="background: var(--secondary-color);"
                                        onclick="alert('Redirecting to Application Form...')">Apply for Slot</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Market Opportunities -->
                    <div>
                        <div class="section-title">
                            <h3>Market Opportunities</h3>
                        </div>
                        <div class="card" style="padding: 24px;">
                            <div class="opportunity-item">
                                <div class="opportunity-header">
                                    <span class="opportunity-tag">Export Lead</span>
                                    <small>2 days ago</small>
                                </div>
                                <h5 style="margin-bottom: 4px;">Coffee Importer - Singapore</h5>
                                <p style="font-size: 12px; color: var(--text-muted);">Looking for 500kg monthly supply
                                    of organic Arabica beans.</p>
                                <button class="btn-action" style="margin-top: 10px; width:100%;">View Proposal</button>
                            </div>

                            <div class="opportunity-item" style="border-left-color: var(--primary-color);">
                                <div class="opportunity-header">
                                    <span class="opportunity-tag"
                                        style="background: rgba(0,32,91,0.1); color: var(--primary-color);">Retail
                                        Partner</span>
                                    <small>5 days ago</small>
                                </div>
                                <h5 style="margin-bottom: 4px;">National Supermarket Chain</h5>
                                <p style="font-size: 12px; color: var(--text-muted);">Inviting snacks MSMEs for
                                    nationwide distribution listing.</p>
                                <button class="btn-action" style="margin-top: 10px; width:100%;">Contact Buyer</button>
                            </div>

                            <div class="opportunity-item" style="border-left-color: var(--success-color);">
                                <div class="opportunity-header">
                                    <span class="opportunity-tag"
                                        style="background: rgba(16,185,129,0.1); color: var(--success-color);">Gov't
                                        Grant</span>
                                    <small>1 week ago</small>
                                </div>
                                <h5 style="margin-bottom: 4px;">MSME Digitalization Fund</h5>
                                <p style="font-size: 12px; color: var(--text-muted);">Apply for up to ₱50,000 subsidy
                                    for e-commerce equipment.</p>
                                <button class="btn-action" style="margin-top: 10px; width:100%;">Apply Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Enrollment Modal -->
    <div id="enrollModal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="modalTitle" style="margin-bottom: 24px;">Enroll Enterprise</h3>
            <form onsubmit="event.preventDefault(); alert('Enterprise successfully enrolled!'); closeEnrollModal();">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; font-size: 14px; font-weight:600; margin-bottom:8px;">Select
                        MSME</label>
                    <select style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color);">
                        <option>Bukidnon Farms Co.</option>
                        <option>Luzon Crafts Hub</option>
                        <option>Visayas Delicacies</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; font-size: 14px; font-weight:600; margin-bottom:8px;">Participant
                        Name</label>
                    <input type="text" placeholder="Enter representative name"
                        style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color);">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-secondary" onclick="closeEnrollModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Confirm Enrollment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Program Modal -->
    <div id="createProgramModal" class="modal-overlay">
        <div class="modal-content">
            <h3>Create New Program</h3>
            <form
                onsubmit="event.preventDefault(); alert('New mission-critical program created!'); closeCreateProgramModal();"
                style="margin-top: 20px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">Program
                        Title</label>
                    <input type="text" placeholder="e.g. Export Excellence 2024"
                        style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color);">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label
                            style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">Category</label>
                        <select
                            style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color);">
                            <option>Training</option>
                            <option>Export Support</option>
                            <option>Marketing</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;">Start
                            Date</label>
                        <input type="date"
                            style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color);">
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-secondary" onclick="closeCreateProgramModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Create Program</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance & Progress Modal -->
    <div id="trackingModal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="trackingTitle">Attendance & Progress Tracking</h3>
            <div class="attendance-list" id="trackingList">
                <div class="attendance-row">
                    <div>
                        <div style="font-weight: 600;">Bukidnon Farms Co.</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Rep: Juana Dela Cruz</div>
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <span class="progress-pill"
                            style="background: rgba(16,185,129,0.1); color: var(--success-color);">80% Progress</span>
                        <input type="checkbox" checked title="Attendance">
                    </div>
                </div>
                <div class="attendance-row">
                    <div>
                        <div style="font-weight: 600;">Luzon Crafts Hub</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Rep: Mario Santos</div>
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <span class="progress-pill"
                            style="background: rgba(245,158,11,0.1); color: var(--warning-color);">45% Progress</span>
                        <input type="checkbox" checked title="Attendance">
                    </div>
                </div>
                <div class="attendance-row">
                    <div>
                        <div style="font-weight: 600;">Visayas Delicacies</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Rep: Elena Reyes</div>
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <span class="progress-pill"
                            style="background: rgba(16,185,129,0.1); color: var(--success-color);">95% Progress</span>
                        <input type="checkbox" title="Attendance">
                    </div>
                </div>
            </div>
            <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                <button class="btn-primary" onclick="closeTrackingModal()">Save Updates</button>
            </div>
        </div>
    </div>

    <script src="../../../js/main.js"></script>
    <script>
        const enrollModal = document.getElementById('enrollModal');
        const createModal = document.getElementById('createProgramModal');
        const trackModal = document.getElementById('trackingModal');

        function openEnrollModal(program) {
            document.getElementById('modalTitle').innerText = 'Enroll for: ' + program;
            enrollModal.style.display = 'flex';
        }
        function closeEnrollModal() { enrollModal.style.display = 'none'; }

        function openCreateProgramModal() { createModal.style.display = 'flex'; }
        function closeCreateProgramModal() { createModal.style.display = 'none'; }

        function openTrackingModal(program) {
            document.getElementById('trackingTitle').innerText = program + ' - Attendance & Progress';
            trackModal.style.display = 'flex';
        }
        function closeTrackingModal() { trackModal.style.display = 'none'; }

        window.onclick = function (e) {
            if (e.target == enrollModal) closeEnrollModal();
            if (e.target == createModal) closeCreateProgramModal();
            if (e.target == trackModal) closeTrackingModal();
        }
    </script>
</body>

</html>
