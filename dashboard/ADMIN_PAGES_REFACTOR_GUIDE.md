# Quick Refactoring Templates for Administrator Pages

## Files to Refactor (6 remaining):
1. ✅ user-profile-approval.php (DONE)
2. ⏳ reports-analytics.php
3. ⏳ compliance-monitoring.php
4. ⏳ incentives-assistance.php
5. ⏳ product-registry.php
6. ⏳ program-training.php
7. ⏳ user-management.php

---

## TEMPLATE STRUCTURE

Every file follows this pattern:

```php
<?php
// 1. KEEP: All PHP logic at top (require, auth checks, database queries)
require_once '../../../includes/init.php';
// ... all your logic ...

// 2. ADD: Page configuration
$pageTitle = "PAGE TITLE - LGU 3";
$pageHeading = "PAGE HEADING";
$activePage = "page-identifier";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<!-- 3. KEEP: Inline styles if any -->
<style>
/* Your page-specific styles */
</style>

<!-- 4. KEEP: Main content (everything between <div class="content-wrapper"> and </div> before </main>) -->
<!-- Your page content here -->

<?php 
// 5. ADD: Page-specific JavaScript
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
// Your JavaScript here
</script>';

include '../layouts/footer.php'; 
?>
```

---

## SPECIFIC CONFIGURATIONS FOR EACH FILE

### 2. reports-analytics.php
```php
$pageTitle = "Reports & Analytics - LGU 3";
$pageHeading = "Reports & Analytics Command Center";
$activePage = "reports-analytics";
$baseUrl = "";
```
**Content starts at:** Line 231 (`<div class="content-wrapper">`)
**Content ends at:** Before `</main>` tag
**Has inline styles:** Yes (lines 51-170 approximately)
**Has JavaScript:** Minimal

---

### 3. compliance-monitoring.php
```php
$pageTitle = "Compliance Monitoring - LGU 3";
$pageHeading = "Compliance Monitoring";
$activePage = "compliance-monitoring";
$baseUrl = "";
```
**Content starts at:** Line with `<div class="content-wrapper">`
**Has inline styles:** Likely yes
**Has JavaScript:** Check for SweetAlert or custom functions

---

### 4. incentives-assistance.php
```php
$pageTitle = "Incentives & Support - LGU 3";
$pageHeading = "Incentives & Support Programs";
$activePage = "incentives-assistance";
$baseUrl = "";
```

---

### 5. product-registry.php
```php
$pageTitle = "Product & MSME Registry - LGU 3";
$pageHeading = "Product & MSME Registry";
$activePage = "product-registry";
$baseUrl = "";
```

---

### 6. program-training.php
```php
$pageTitle = "Program & Training - LGU 3";
$pageHeading = "Program & Training Management";
$activePage = "program-training";
$baseUrl = "";
```

---

### 7. user-management.php
```php
$pageTitle = "User Management - LGU 3";
$pageHeading = "User Management";
$activePage = "user-management";
$baseUrl = "";
```

---

## STEP-BY-STEP PROCESS FOR EACH FILE

1. **Open the file**
2. **Find line with `<!DOCTYPE html>`** - This is where you'll start cutting
3. **Find line with `<div class="content-wrapper">`** - Content starts here
4. **Find closing `</div>` before `</main>`** - Content ends here
5. **Find `<script>` tags** - These go into `$additionalJS`
6. **Find `</body>` and `</html>`** - Delete these

### What to DELETE:
- Lines from `<!DOCTYPE html>` to just before `<div class="content-wrapper">`
- Everything after content-wrapper's closing `</div>` (the `</main>`, `</div>`, scripts, `</body>`, `</html>`)

### What to KEEP:
- All PHP at the top
- Inline `<style>` tags (move them AFTER the layout includes)
- Content between `<div class="content-wrapper">` and its closing `</div>`

### What to ADD:
- Page configuration variables
- Layout includes (header, sidebar, navbar)
- `$additionalJS` variable with scripts
- Footer include

---

## QUICK CHECKLIST

For each file:
- [ ] Backup original file
- [ ] Keep PHP logic (lines 1 until `?>` before `<!DOCTYPE`)
- [ ] Add page configuration (4 variables)
- [ ] Add 3 layout includes
- [ ] Move `<style>` tags after includes (if any)
- [ ] Keep ONLY content-wrapper content
- [ ] Move JavaScript to `$additionalJS`
- [ ] Add footer include
- [ ] Test the page
- [ ] Verify sidebar highlighting

---

## COMMON MISTAKES TO AVOID

1. ❌ Forgetting to set `$baseUrl = ""` (should be empty for pages/ folder)
2. ❌ Not moving inline styles after layout includes
3. ❌ Forgetting to escape quotes in JavaScript (use `\"` or single quotes)
4. ❌ Deleting too much content (keep everything in content-wrapper)
5. ❌ Not testing after refactoring

---

## ESTIMATED TIME PER FILE

- Simple pages: 10-15 minutes
- Complex pages with lots of JS: 20-25 minutes
- **Total for 6 files: 1.5-2 hours**

---

## NEED HELP?

If you encounter issues:
1. Check the LAYOUTS_README.md for detailed examples
2. Look at user-profile-approval.php (already refactored) as reference
3. Make sure all paths are correct (../ for layouts, ../../../ for other resources)
