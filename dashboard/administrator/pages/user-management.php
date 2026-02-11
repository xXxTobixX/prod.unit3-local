<?php 
require_once '../../../includes/init.php'; 

// Check if user is logged in and has admin-level role
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../../login.php');
}

$db = db();

// Fetch only MSME/Vendor users
$users = $db->fetchAll("SELECT id, firstname, lastname, email, role, status, business_name FROM users ORDER BY id DESC");

// Page configuration
$pageTitle = "User Management - LGU 3";
$pageHeading = "User Management";
$activePage = "user-management";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<style>
    /* Custom styles for User Management */
    .page-actions {
        display: flex;
        gap: 12px;
    }

    .role-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(0, 32, 91, 0.1);
        color: var(--primary-color);
        text-transform: uppercase;
    }

    .status-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-inactive, .status-rejected {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 14px;
    }

    .btn-action.view {
        color: var(--primary-color);
        background: rgba(0, 32, 91, 0.05);
    }

    .btn-action.view:hover {
        background: var(--primary-color);
        color: white;
    }

    .btn-action.edit {
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.05);
    }

    .btn-action.edit:hover {
        background: #f59e0b;
        color: white;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--card-bg);
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-muted);
    }

    .form-group {
        margin-bottom: 20px;
        padding: 0 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        background: var(--bg-color);
        color: var(--text-main);
    }

    .modal-footer {
        padding: 24px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    /* Toast Notification */
    .toast-container {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 10000;
    }

    .toast {
        background: var(--card-bg);
        border-left: 5px solid var(--primary-color);
        color: var(--text-main);
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: var(--shadow-md);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease-out;
    }

    .toast.success {
        border-left-color: var(--success-color);
    }

    .toast.error {
        border-left-color: var(--danger-color);
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast.fade-out {
        animation: fadeOut 0.4s ease-out forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(400px);
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <h3>System Users Registry</h3>
        <div class="page-actions">
            <button class="btn-primary" onclick="openModal('add')">
                <i class="fas fa-plus"></i> Create New User
            </button>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>User Details</th>
                    <th>Business Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        No registered MSMEs found.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>U-<?php echo $u['id']; ?></td>
                        <td>
                            <div class="table-user">
                                <div class="name"><?php echo htmlspecialchars(html_entity_decode($u['firstname'] . ' ' . $u['lastname'])); ?></div>
                                <div class="email"><?php echo htmlspecialchars(html_entity_decode($u['email'])); ?></div>
                            </div>
                        </td>
                        <td><span class="access-level"><?php echo htmlspecialchars(html_entity_decode($u['business_name'])); ?></span></td>
                        <td><span class="role-badge"><?php echo htmlspecialchars(html_entity_decode(ucfirst($u['role']))); ?></span></td>
                        <td>
                            <span class="status-pill status-<?php echo strtolower($u['status']); ?>">
                                <?php echo ucfirst($u['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action view" title="View Details" onclick="viewUser(<?php echo $u['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action edit" title="Edit User" onclick="editUser(<?php echo $u['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Create New User</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <form id="userForm">
            <input type="hidden" id="userId">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" placeholder="Enter user's full name" required>
            </div>
            <div class="form-group">
                <label for="businessName">Business Name</label>
                <input type="text" id="businessName" placeholder="Enter business name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="Enter official email" required>
            </div>
            <div class="form-group">
                <label for="role">User Role</label>
                <select id="role" required>
                    <option value="user">Vendor/Producer</option>
                    <option value="MSME Coordinator">MSME Coordinator</option>
                    <option value="Market Analyst">Market Analyst</option>
                </select>
            </div>
            <div id="statusGroup" class="form-group" style="display: none;">
                <label for="status">Account Status</label>
                <select id="status">
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary" id="submitBtn">Save User</button>
            </div>
        </form>
    </div>
</div>

<?php 
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
    document.addEventListener("DOMContentLoaded", function() {
        function openModal(mode, userId = null) {
            const modal = document.getElementById("userModal");
            const modalTitle = document.getElementById("modalTitle");
            const statusGroup = document.getElementById("statusGroup");
            
            if (mode === "add") {
                modalTitle.textContent = "Create New User";
                document.getElementById("userForm").reset();
                document.getElementById("userId").value = "";
                statusGroup.style.display = "none";
            } else if (mode === "edit" && userId) {
                modalTitle.textContent = "Edit User Details";
                statusGroup.style.display = "block";
                // Load user data via AJAX
                fetch(`../../../ajax/auth.php?action=get-user&id=${userId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById("userId").value = data.user.id;
                            document.getElementById("fullName").value = data.user.firstname + " " + data.user.lastname;
                            document.getElementById("businessName").value = data.user.business_name;
                            document.getElementById("email").value = data.user.email;
                            document.getElementById("role").value = data.user.role;
                            document.getElementById("status").value = data.user.status;
                        }
                    });
            }
            
            modal.classList.add("active");
        }

        function closeModal() {
            document.getElementById("userModal").classList.remove("active");
        }

        function viewUser(userId) {
            Swal.fire({
                title: "Loading User Details...",
                text: "Please wait",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`../../../ajax/auth.php?action=get-user&id=${userId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const u = data.user;
                        Swal.fire({
                            title: u.firstname + " " + u.lastname,
                            html: `
                                <div style="text-align: left;">
                                    <p><strong>Email:</strong> ${u.email}</p>
                                    <p><strong>Business:</strong> ${u.business_name || "N/A"}</p>
                                    <p><strong>Role:</strong> ${u.role}</p>
                                    <p><strong>Status:</strong> ${u.status}</p>
                                </div>
                            `,
                            icon: "info"
                        });
                    }
                });
        }

        function editUser(userId) {
            openModal("edit", userId);
        }

        // Form submission
        document.getElementById("userForm").onsubmit = function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById("submitBtn");
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = "Saving...";

            const formData = new FormData();
            formData.append("userId", document.getElementById("userId").value);
            formData.append("fullName", document.getElementById("fullName").value);
            formData.append("businessName", document.getElementById("businessName").value);
            formData.append("email", document.getElementById("email").value);
            formData.append("role", document.getElementById("role").value);
            formData.append("status", document.getElementById("status").value);

            fetch("../../../ajax/auth.php?action=save-user", {
                method: "POST",
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showNotification("Success", data.message, "success");
                    setTimeout(() => {
                        closeModal();
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification("Error", data.message, "error");
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            })
            .catch(error => {
                showNotification("Network Error", error.message, "error");
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            });
        };

        function showNotification(title, message, type = "success") {
            let container = document.querySelector(".toast-container");
            if (!container) {
                container = document.createElement("div");
                container.className = "toast-container";
                document.body.appendChild(container);
            }

            const toast = document.createElement("div");
            toast.className = `toast ${type}`;
            const icon = type === "success" ? "fa-check-circle" : "fa-exclamation-circle";

            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <div class="toast-content">
                    <h4>${title}</h4>
                    <p>${message}</p>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add("fade-out");
                setTimeout(() => {
                    toast.remove();
                    if (container.childNodes.length === 0) container.remove();
                }, 400);
            }, 4000);
        }

        // Make functions global
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.viewUser = viewUser;
        window.editUser = editUser;
    });
</script>';

include '../layouts/footer.php'; 
?>
