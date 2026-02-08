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

    // Add hover effects for table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseover', () => {
            row.style.background = 'rgba(0, 32, 91, 0.02)';
        });
        row.addEventListener('mouseout', () => {
            row.style.background = 'transparent';
        });
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
});
