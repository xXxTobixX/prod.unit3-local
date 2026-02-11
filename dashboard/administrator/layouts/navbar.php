        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="toggle-sidebar" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <h2><?php echo $pageHeading ?? 'Dashboard'; ?></h2>
                </div>
                <div class="header-right">
                    <button id="theme-toggle" class="icon-btn"><i class="fas fa-moon"></i></button>
                    <button class="icon-btn"><i class="fas fa-bell"></i></button>
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                            <span class="user-role"><?php echo ucfirst($_SESSION['user_role'] ?? 'Administrator'); ?></span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'Admin'); ?>&background=00205B&color=fff" alt="User Avatar" class="avatar">
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
