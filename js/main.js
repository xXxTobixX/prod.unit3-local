document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const mainContent = document.querySelector('.main-content');
    const topHeader = document.querySelector('.top-header');

    // Toggle Sidebar Functionality
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth > 768) {
                // Desktop toggle: Collapse sidebar
                document.body.classList.toggle('collapsed-sidebar');
            } else {
                // Mobile toggle: Show/Hide sidebar
                document.body.classList.toggle('show-sidebar');
            }
        });
    }

    // Close sidebar when clicking overlay on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 &&
            document.body.classList.contains('show-sidebar') &&
            !sidebar.contains(e.target) &&
            !toggleBtn.contains(e.target)) {
            document.body.classList.remove('show-sidebar');
        }
    });


    // Simple Activity Notification
    // Simulating a new notification after 5 seconds
    setTimeout(() => {
        const badge = document.querySelector('.badge');
        if (badge) {
            badge.textContent = '4';
            badge.style.transform = 'scale(1.2)';
            setTimeout(() => badge.style.transform = 'scale(1)', 300);
        }
    }, 5000);

    // Simplified initialization - CSS Media Queries handle the rest
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            document.body.classList.remove('show-sidebar');
        }
    });

    // Theme Toggle Logic
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.body.classList.toggle('dark-mode', savedTheme === 'dark');
    };

    const toggleTheme = () => {
        const isDark = document.body.classList.toggle('dark-mode');
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
    };

    /**
     * Logout Confirmation System
     */
    const createLogoutModal = () => {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.id = 'logout-modal-overlay';

        // Create modal content
        overlay.innerHTML = `
            <div class="logout-modal">
                <div class="logout-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h2>Confirm Logout</h2>
                <p>Are you sure you want to end your session? You will need to log in again to access your dashboard.</p>
                <div class="logout-modal-buttons">
                    <button class="btn-logout-cancel" id="btn-logout-cancel">Cancel</button>
                    <button class="btn-logout-confirm" id="btn-logout-confirm">Logout</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Event Listeners
        const btnCancel = overlay.querySelector('#btn-logout-cancel');
        const btnConfirm = overlay.querySelector('#btn-logout-confirm');

        const closeLogoutModal = () => {
            overlay.classList.remove('active');
            setTimeout(() => overlay.remove(), 300);
        };

        btnCancel.addEventListener('click', closeLogoutModal);

        btnConfirm.addEventListener('click', () => {
            // Redirect to a real logout.php or home
            // For now, let's fade out the dashboard and redirect
            overlay.innerHTML = `
                <div class="logout-modal">
                    <div class="logout-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Logging out...</h2>
                    <p>Cleaning up your session securely.</p>
                </div>
            `;

            setTimeout(() => {
                window.location.href = '../../login.php';
            }, 1000);
        });

        // Trigger animation
        setTimeout(() => overlay.classList.add('active'), 10);
    };

    // Initialize Theme
    initTheme();

    // Global Event Delegation
    document.addEventListener('click', (e) => {
        // Theme Toggle
        if (e.target.closest('.theme-toggle')) {
            toggleTheme();
        }

        // Logout Triggers
        const logoutTrigger = e.target.closest('.logout a') || e.target.closest('#logout-trigger');
        if (logoutTrigger) {
            e.preventDefault();
            createLogoutModal();
        }
    });
});
