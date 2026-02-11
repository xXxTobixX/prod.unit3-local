        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo $logoPath ?? '../../images/logo.png'; ?>" alt="PH Logo" class="gov-logo">
                <div class="header-text">
                    <h1>LGU 3</h1>
                    <p>Administrative Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo ($activePage ?? '') == 'dashboard' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? '../'; ?>index.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'user-management' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>user-management.php"><i class="fas fa-user-shield"></i> <span>User Management</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'product-registry' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>product-registry.php"><i class="fas fa-building"></i> <span>Product & MSME Registry</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'profile-verifications' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>user-profile-approval.php"><i class="fas fa-user-check"></i> <span>Profile Verifications</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'compliance-monitoring' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>compliance-monitoring.php"><i class="fas fa-clipboard-check"></i> <span>Compliance Monitoring</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'program-training' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>program-training.php"><i class="fas fa-graduation-cap"></i> <span>Program & Training</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'incentives-assistance' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>incentives-assistance.php"><i class="fas fa-gift"></i> <span>Incentives & Support</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'reports-analytics' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>reports-analytics.php"><i class="fas fa-chart-bar"></i> <span>Reports & Analytics</span></a>
                    </li>
                </ul>

                <div class="nav-divider"></div>

                <ul>
                    <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="#"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
                    <li class="logout"><a href="<?php echo $logoutPath ?? '../../ajax/auth.php?action=logout'; ?>"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>
