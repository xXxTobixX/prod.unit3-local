        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo $logoPath ?? '../../images/logo.png'; ?>" alt="PH Logo" class="gov-logo">
                <div class="header-text">
                    <h1>LGU 3</h1>
                    <p>USERS Portal</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo ($activePage ?? '') == 'dashboard' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? '../'; ?>index.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'profile-management' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>profile-management.php"><i class="fas fa-id-card"></i> <span>My Profile</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'my-products' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>my-products.php"><i class="fas fa-box"></i> <span>My Products</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'compliance-status' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>compliance-status.php"><i class="fas fa-check-double"></i> <span>Compliance Status</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'my-training' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>my-training.php"><i class="fas fa-certificate"></i> <span>My Training</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'applied-incentives' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>applied-incentives.php"><i class="fas fa-hand-holding-usd"></i> <span>Applied Incentives</span></a>
                    </li>
                    <li class="<?php echo ($activePage ?? '') == 'market-insights' ? 'active' : ''; ?>">
                        <a href="<?php echo $baseUrl ?? ''; ?>market-insights.php"><i class="fas fa-chart-line"></i> <span>Market Insights</span></a>
                    </li>
                </ul>

                <div class="nav-divider"></div>

                <ul>
                    <li><a href="<?php echo $baseUrl ?? ''; ?>profile-management.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="<?php echo $baseUrl ?? ''; ?>help-center.php"><i class="fas fa-question-circle"></i> <span>Help Center</span></a></li>
                    <li class="logout"><a href="<?php echo $logoutPath ?? '../../ajax/auth.php?action=logout'; ?>"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>
