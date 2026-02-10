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


    /**
     * Animated Notification System (Toast)
     */
    function showNotification(title, message, type = 'info') {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <div class="toast-content">
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
                if (container.childNodes.length === 0) container.remove();
            }, 400);
        }, 5000);
    }

    /**
     * Real-time Notification Fetching
     */
    function checkNotifications() {
        const isPage = window.location.pathname.includes('/pages/');
        const ajaxPath = isPage ? '../../../ajax/notifications.php' : '../../ajax/notifications.php';

        fetch(`${ajaxPath}?action=get-unread`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const bellContainer = document.querySelector('.notifications');
                    const badge = document.querySelector('.notifications .badge');

                    if (badge) {
                        const currentCount = parseInt(badge.textContent) || 0;

                        if (data.count > currentCount) {
                            // New notification arrived!
                            badge.textContent = data.count;
                            badge.style.opacity = '1';

                            // 1. Shake the bell
                            if (bellContainer) {
                                bellContainer.classList.add('shake');
                                setTimeout(() => bellContainer.classList.remove('shake'), 1000);
                            }

                            // 2. Pulse the badge
                            badge.style.animation = 'none';
                            badge.offsetHeight; // trigger reflow
                            badge.style.animation = 'pulse 1s cubic-bezier(0.4, 0, 0.2, 1) 2';

                            // 3. Show premium toast for the most recent one
                            if (data.notifications && data.notifications.length > 0) {
                                const latest = data.notifications[0];

                                // Internal Toast first
                                showNotification(latest.title, latest.message, latest.type);

                                // If SweetAlert2 is loaded, we can use it for a more "premium" feel as requested
                                if (typeof Swal !== 'undefined') {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 4000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener('mouseenter', Swal.stopTimer)
                                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                                        }
                                    });
                                    Toast.fire({
                                        icon: latest.type || 'info',
                                        title: latest.title,
                                        text: latest.message
                                    });
                                }
                            }
                        } else if (data.count === 0) {
                            badge.textContent = '0';
                            badge.style.opacity = '0';
                        } else {
                            badge.textContent = data.count;
                            badge.style.opacity = '1';
                        }
                    }
                }
            })
            .catch(error => console.error('Notification Error:', error));
    }

    // Initialize notification check
    if (document.querySelector('.notifications')) {
        checkNotifications();
        setInterval(checkNotifications, 7000); // Check every 7 seconds for a more "real-time" feel

        // Add click listener for bell icon
        document.querySelector('.notifications').addEventListener('click', (e) => {
            e.stopPropagation();
            openNotifModal();
        });
    }

    /**
     * Notification Modal Logic
     */
    function createNotifModal() {
        if (document.getElementById('notif-modal-overlay')) return;

        const overlay = document.createElement('div');
        overlay.className = 'notif-modal-overlay';
        overlay.id = 'notif-modal-overlay';
        overlay.innerHTML = `
            <div class="notif-modal">
                <div class="notif-modal-header">
                    <h3>Notifications</h3>
                    <button class="btn-close-notif"><i class="fas fa-times"></i></button>
                </div>
                <div class="notif-modal-body" id="notif-modal-body">
                    <div class="loading-notif" style="text-align:center; padding:30px;">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
                <div class="notif-modal-footer">
                    <a href="#" class="btn-mark-all" id="mark-all-btn">Mark all as read</a>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        // Close listeners
        overlay.querySelector('.btn-close-notif').addEventListener('click', () => {
            overlay.classList.remove('active');
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.classList.remove('active');
        });

        // Mark all as read
        overlay.querySelector('#mark-all-btn').addEventListener('click', (e) => {
            e.preventDefault();
            const isPage = window.location.pathname.includes('/pages/');
            const ajaxPath = isPage ? '../../../ajax/notifications.php' : '../../ajax/notifications.php';

            fetch(`${ajaxPath}?action=mark-all-read`, { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const unreadItems = overlay.querySelectorAll('.notif-item.unread');
                        unreadItems.forEach(item => item.classList.remove('unread'));
                        checkNotifications(); // Refresh badge
                    }
                });
        });
    }

    function openNotifModal() {
        createNotifModal();
        const overlay = document.getElementById('notif-modal-overlay');
        const body = document.getElementById('notif-modal-body');
        overlay.classList.add('active');

        const isPage = window.location.pathname.includes('/pages/');
        const ajaxPath = isPage ? '../../../ajax/notifications.php' : '../../ajax/notifications.php';

        fetch(`${ajaxPath}?action=get-all`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.notifications && data.notifications.length > 0) {
                        let html = '';
                        data.notifications.forEach(n => {
                            const unreadClass = n.is_read == 0 ? 'unread' : '';
                            const typeClass = n.type || 'info';
                            const icon = typeClass === 'success' ? 'fa-check' : (typeClass === 'error' ? 'fa-exclamation' : 'fa-info');
                            const time = new Date(n.created_at).toLocaleString();

                            html += `
                                <div class="notif-item ${unreadClass} ${typeClass}" data-id="${n.id}">
                                    <div class="notif-item-icon">
                                        <i class="fas ${icon}"></i>
                                    </div>
                                    <div class="notif-item-content">
                                        <h4>${n.title}</h4>
                                        <p>${n.message}</p>
                                        <span class="notif-item-time">${time}</span>
                                    </div>
                                </div>
                            `;
                        });
                        body.innerHTML = html;

                        // Add mark-as-read listener to items
                        body.querySelectorAll('.notif-item.unread').forEach(item => {
                            item.addEventListener('click', function () {
                                const id = this.getAttribute('data-id');
                                fetch(`${ajaxPath}?action=mark-read`, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `id=${id}`
                                }).then(r => r.json()).then(res => {
                                    if (res.success) {
                                        this.classList.remove('unread');
                                        checkNotifications();
                                    }
                                });
                            });
                        });

                    } else {
                        body.innerHTML = `
                            <div class="empty-notif">
                                <i class="fas fa-bell-slash"></i>
                                <p>No notifications yet</p>
                            </div>
                        `;
                    }
                }
            });
    }



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
