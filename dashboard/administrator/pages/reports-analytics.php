<?php
require_once '../../../includes/init.php';

// Check if user is logged in and has admin-level role
if (!isLoggedIn() || !in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
    redirect('../../../login.php');
}

$db = db();

// KPI Calculations
$totalMSMEs = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user'")['count'];
$totalProducts = $db->fetchOne("SELECT COUNT(*) as count FROM user_products")['count'];
$exportReady = $db->fetchOne("SELECT COUNT(*) as count FROM user_products WHERE intended_market = 'export' AND status = 'approved'")['count'];
$complianceRate = 0;

$totalDocs = $db->fetchOne("SELECT COUNT(*) as count FROM business_documents")['count'];
$verifiedDocs = $db->fetchOne("SELECT COUNT(*) as count FROM business_documents WHERE status = 'verified'")['count'];
if ($totalDocs > 0) {
    $complianceRate = round(($verifiedDocs / $totalDocs) * 100);
}

// Sector Distribution
$sectorStats = $db->fetchAll("
    SELECT sector, COUNT(*) as count 
    FROM business_profiles 
    GROUP BY sector 
    ORDER BY count DESC
");

// Product Category Distribution
$productStats = $db->fetchAll("
    SELECT category, COUNT(*) as count 
    FROM user_products 
    GROUP BY category 
    ORDER BY count DESC
");

// Registration Status
$regStats = $db->fetchAll("
    SELECT status, COUNT(*) as count 
    FROM users 
    WHERE role = 'user' 
    GROUP BY status
");

// Page configuration
$pageTitle = "Reports & Analytics - LGU 3";
$pageHeading = "Reports & Analytics Command Center";
$activePage = "reports-analytics";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<style>
    /* Analytics Specific Styles */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .report-section {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dotted var(--border-color);
    }

    .stat-row:last-child {
        border: none;
    }

    .chart-placeholder {
        height: 150px;
        background: var(--bg-color);
        border-radius: 12px;
        display: flex;
        align-items: flex-end;
        gap: 8px;
        padding: 16px;
        margin: 16px 0;
    }

    .bar {
        flex: 1;
        background: var(--primary-color);
        border-radius: 4px 4px 0 0;
        transition: height 0.3s;
    }

    .bar.secondary {
        background: var(--secondary-color);
    }

    .bar.accent {
        background: var(--accent-color);
    }

    .kpi-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 32px;
    }

    .kpi-card {
        background: var(--card-bg);
        padding: 24px;
        border-radius: 16px;
        border-bottom: 4px solid var(--primary-color);
        box-shadow: var(--shadow-sm);
    }

    .print-controls {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
    }

    @media print {
        .sidebar,
        .top-header,
        .print-controls {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .report-section {
            break-inside: avoid;
            border: 1px solid #ddd !important;
        }
    }
</style>

<div class="print-controls">
    <button class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Full Report</button>
    <button class="btn-secondary" onclick="alert('Exporting to Excel...')"><i class="fas fa-file-excel"></i> Export Excel</button>
    <button class="btn-secondary" onclick="alert('Downloading PDF Summary...')"><i class="fas fa-file-pdf"></i> Download PDF</button>
</div>

<!-- KPI Overview Section -->
<div class="kpi-container">
    <div class="kpi-card">
        <div style="font-size: 13px; color: var(--text-muted);">Total MSMEs</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);"><?php echo number_format($totalMSMEs); ?></div>
        <div style="font-size: 11px; color: var(--success-color); margin-top: 4px;"><i class="fas fa-arrow-up"></i> Live Count</div>
    </div>
    <div class="kpi-card" style="border-bottom-color: var(--secondary-color);">
        <div style="font-size: 13px; color: var(--text-muted);">Registered Products</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--secondary-color);"><?php echo number_format($totalProducts); ?></div>
        <div style="font-size: 11px; color: color: var(--text-muted); margin-top: 4px;">Verified Products</div>
    </div>
    <div class="kpi-card" style="border-bottom-color: var(--success-color);">
        <div style="font-size: 13px; color: var(--text-muted);">Compliant Enterprises</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--success-color);"><?php echo $complianceRate; ?>%</div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Effective Rate</div>
    </div>
    <div class="kpi-card" style="border-bottom-color: var(--accent-color);">
        <div style="font-size: 13px; color: var(--text-muted);">Export Ready</div>
        <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);"><?php echo number_format($exportReady); ?></div>
        <div style="font-size: 11px; color: var(--success-color); margin-top: 4px;"><i class="fas fa-check-circle"></i> Certified</div>
    </div>
</div>

<div class="reports-grid">
    <!-- 1. MSME & User Reports -->
    <div class="report-section">
        <div class="report-header">
            <h4><i class="fas fa-users-cog"></i> MSME Sector Distribution</h4>
        </div>
        <?php foreach (array_slice($sectorStats, 0, 3) as $stat): ?>
        <div class="stat-row"><span><?php echo htmlspecialchars($stat['sector'] ?: 'Uncategorized'); ?></span><strong><?php echo $stat['count']; ?></strong></div>
        <?php endforeach; ?>
        
        <div class="chart-placeholder">
            <?php foreach (array_slice($sectorStats, 0, 4) as $stat): 
                $h = $sectorStats[0]['count'] > 0 ? ($stat['count'] / $sectorStats[0]['count']) * 100 : 0;
            ?>
            <div class="bar" style="height: <?php echo max(10, $h); ?>%;" title="<?php echo htmlspecialchars($stat['sector']); ?>"></div>
            <?php endforeach; ?>
        </div>
        <p style="font-size: 11px; color: var(--text-muted);">* Distribution across top Sectors</p>
    </div>

    <!-- 2. Product Reports -->
    <div class="report-section">
        <div class="report-header">
            <h4><i class="fas fa-box-open"></i> Product Category Analysis</h4>
        </div>
        <?php foreach (array_slice($productStats, 0, 3) as $stat): ?>
        <div class="stat-row"><span><?php echo htmlspecialchars($stat['category'] ?: 'Uncategorized'); ?></span><strong><?php echo $stat['count']; ?></strong></div>
        <?php endforeach; ?>

        <div class="chart-placeholder">
            <?php foreach (array_slice($productStats, 0, 4) as $stat): 
                $h = $productStats[0]['count'] > 0 ? ($stat['count'] / $productStats[0]['count']) * 100 : 0;
            ?>
            <div class="bar secondary" style="height: <?php echo max(10, $h); ?>;"></div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. Compliance Reports -->
    <div class="report-section">
        <div class="report-header">
            <h4><i class="fas fa-file-signature"></i> Regulatory Compliance</h4>
        </div>
        <div class="stat-row"><span>Verified Documents</span><strong><?php echo $verifiedDocs; ?></strong></div>
        <div class="stat-row"><span>Total Submissions</span><strong><?php echo $totalDocs; ?></strong></div>
        <div class="stat-row"><span>Rejected / Pending</span><strong><?php echo $totalDocs - $verifiedDocs; ?></strong></div>
        <div style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                <span>Global Compliance Status</span>
                <strong><?php echo $complianceRate; ?>%</strong>
            </div>
            <div style="height: 8px; background: var(--border-color); border-radius: 10px; overflow:hidden;">
                <div style="width: <?php echo $complianceRate; ?>%; height:100%; background: var(--success-color);"></div>
            </div>
        </div>
    </div>

    <!-- 4. Programs & Training Reports -->
    <div class="report-section">
        <div class="report-header">
            <h4><i class="fas fa-graduation-cap"></i> Capacity Building Impact</h4>
        </div>
        <div class="stat-row"><span>Total Attendance</span><strong>1,420</strong></div>
        <div class="stat-row"><span>Prog. Participation</span><strong>72%</strong></div>
        <div class="stat-row"><span>Certificates Issued</span><strong>892</strong></div>
        <div class="chart-placeholder">
            <div class="bar accent" style="height: 30%;"></div>
            <div class="bar accent" style="height: 50%;"></div>
            <div class="bar accent" style="height: 85%;"></div>
            <div class="bar accent" style="height: 70%;"></div>
        </div>
    </div>

    <!-- 5. Market & Export Reports -->
    <div class="report-section">
        <div class="report-header">
            <h4><i class="fas fa-globe-americas"></i> Market Access Stats</h4>
        </div>
        <div class="stat-row"><span>Trade Fair Apps</span><strong>156</strong></div>
        <div class="stat-row"><span>Verified Buyer Matches</span><strong>42</strong></div>
        <div class="stat-row"><span>Export Vol (Monthly)</span><strong>5.2 Tons</strong></div>
        <div class="stat-row"><span>Regional Trade Value</span><strong>₱ 4.2M</strong></div>
    </div>

    <!-- 6. Monthly Growth Trend -->
    <div class="report-section" style="grid-column: span 1;">
        <div class="report-header">
            <h4><i class="fas fa-chart-line"></i> 2024 Year-to-Date Trend</h4>
        </div>
        <div style="height: 180px; display: flex; align-items: flex-end; gap: 4px; padding: 10px;">
            <div class="bar" style="height: 30%;"></div>
            <div class="bar" style="height: 45%;"></div>
            <div class="bar" style="height: 40%;"></div>
            <div class="bar" style="height: 65%;"></div>
            <div class="bar" style="height: 55%;"></div>
            <div class="bar" style="height: 85%;"></div>
            <div class="bar" style="height: 95%;"></div>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-muted); margin-top: 8px;">
            <span>JAN</span><span>MAR</span><span>MAY</span><span>JUL</span><span>SEP</span><span>NOV</span>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>
