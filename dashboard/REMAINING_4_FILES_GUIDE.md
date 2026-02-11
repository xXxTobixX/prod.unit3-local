# QUICK REFACTORING SCRIPT FOR REMAINING 4 FILES

## Files Already Done ✅
1. administrator/index.php
2. user-profile-approval.php
3. reports-analytics.php

## Files Remaining (4):
4. compliance-monitoring.php
5. incentives-assistance.php
6. product-registry.php
7. program-training.php
8. user-management.php

---

## EXACT STEPS FOR EACH FILE

### Step 1: Find the Content Boundaries

Open the file and find these line numbers:

1. **PHP Logic End**: Find the line with `?>` before `<!DOCTYPE html>`
2. **Content Start**: Find `<div class="content-wrapper">` or first content after `</header>` in main
3. **Content End**: Find `</div>` before `</main>`
4. **Scripts Start**: Find first `<script>` tag after content

### Step 2: Extract What You Need

**KEEP:**
- Lines 1 to PHP closing `?>` (all PHP logic)
- Inline `<style>` tags (if any, between `<head>` and `</head>`)
- Content between content-wrapper div
- JavaScript code from `<script>` tags

**DELETE:**
- `<!DOCTYPE html>` through `<div class="content-wrapper">`
- Everything after content-wrapper's closing `</div>`

### Step 3: Build the New File

```php
<?php
// PASTE: All original PHP logic here (lines 1 to ?>)

// ADD: Page configuration
$pageTitle = "PAGE TITLE - LGU 3";
$pageHeading = "PAGE HEADING";
$activePage = "page-identifier";
$baseUrl = "";

include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/navbar.php';
?>

<!-- PASTE: Inline <style> tags here if any -->

<!-- PASTE: Main content here (from content-wrapper) -->

<?php 
// PASTE: JavaScript here
$additionalJS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
$additionalJS .= '<script>
// Your page JavaScript
</script>';

include '../layouts/footer.php'; 
?>
```

---

## SPECIFIC CONFIGURATIONS

### 4. compliance-monitoring.php
```php
$pageTitle = "Compliance Monitoring - LGU 3";
$pageHeading = "Compliance & Permit Monitoring";
$activePage = "compliance-monitoring";
$baseUrl = "";
```
**PHP Logic Ends:** Line 32
**Has Styles:** Yes (lines 43-150 approx)
**Has JavaScript:** Yes (SweetAlert for approve/reject)

---

### 5. incentives-assistance.php
```php
$pageTitle = "Incentives & Support - LGU 3";
$pageHeading = "Incentives & Support Programs";
$activePage = "incentives-assistance";
$baseUrl = "";
```
**Has Styles:** Likely yes
**Has JavaScript:** Check for modals/forms

---

### 6. product-registry.php
```php
$pageTitle = "Product & MSME Registry - LGU 3";
$pageHeading = "Product & MSME Registry";
$activePage = "product-registry";
$baseUrl = "";
```
**Has Styles:** Likely yes
**Has JavaScript:** Likely for product actions

---

### 7. program-training.php
```php
$pageTitle = "Program & Training - LGU 3";
$pageHeading = "Program & Training Management";
$activePage = "program-training";
$baseUrl = "";
```

---

### 8. user-management.php
```php
$pageTitle = "User Management - LGU 3";
$pageHeading = "User Management";
$activePage = "user-management";
$baseUrl = "";
```
**Note:** This is likely the LARGEST file (770 lines based on cursor position)
**Has Styles:** Definitely yes
**Has JavaScript:** Definitely yes (user actions, modals, etc.)

---

## FASTEST WORKFLOW

For each file:

1. **Open original file** in one tab
2. **Create new file** in another tab
3. **Copy PHP logic** (top section)
4. **Add page config** (4 lines)
5. **Add includes** (3 lines)
6. **Copy styles** (if any)
7. **Find and copy main content** (search for "compliance-grid" or similar unique class)
8. **Copy JavaScript** to `$additionalJS`
9. **Add footer include**
10. **Save and test**

**Time per file:** 15-20 minutes
**Total time:** 1-1.5 hours

---

## TESTING CHECKLIST

After refactoring each file:
- [ ] Page loads without errors
- [ ] Sidebar highlights correct item
- [ ] All content displays properly
- [ ] Styles are applied
- [ ] JavaScript functions work
- [ ] No console errors

---

## NEED HELP?

Reference files:
- `user-profile-approval.php` - Simple example
- `reports-analytics.php` - Complex with charts
- `administrator/index.php` - Dashboard example

All layout files are in:
- `dashboard/administrator/layouts/`

Documentation:
- `LAYOUTS_README.md` - Complete guide
- `REFACTORING_GUIDE.md` - Step-by-step
- `ADMIN_PAGES_REFACTOR_GUIDE.md` - Quick reference
