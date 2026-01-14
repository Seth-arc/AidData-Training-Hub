# How to Run Phase 2 Validation Tests

This guide explains how to properly run the Phase 2 validation tests for the AidData LMS Plugin.

## Overview

The AidData LMS Plugin includes a comprehensive validation system that can verify Phase 2 implementation through automated tests. The validation system includes:

- **40 automated tests** across 9 categories
- **28 file existence checks**
- **Security validation** (nonces, capabilities, sanitization)
- **Performance benchmarks** (asset sizes, query optimization)
- **Accessibility checks** (ARIA labels, keyboard navigation)
- **Integration tests** (Phase 0 & 1 compatibility)

## Validation Methods

### Method 1: Quick File Check (No WordPress Required)

This method checks if all Phase 2 files exist without requiring WordPress or database access.

```bash
php wp-content/plugins/aiddata-training/includes/admin/validate-phase-2-files.php
```

**Output Example:**
```
================================================================================
        PHASE 2 FILE VALIDATION
================================================================================
Date: 2025-10-23 16:27:33
Total Files Checked:    28
Files Found:            26
Files Missing:          2
Completion Rate:        92.86%

✓ EXCELLENT! All critical Phase 2 files are present.
================================================================================
```

**Pros:**
- Fast execution (< 1 second)
- No WordPress or database required
- Works even if site is down
- Color-coded output
- Perfect for CI/CD pipelines

**Cons:**
- Only checks file existence
- Doesn't verify functionality
- Doesn't test AJAX handlers or hooks

### Method 2: Full Validation via WordPress Admin (Recommended)

This method runs all 40 automated tests and provides a comprehensive HTML report.

**Steps:**
1. Ensure your Local by Flywheel site is running
2. Log in to WordPress as an administrator
3. Navigate to **Tutorials → Phase 2 Validation**
4. Click **Run Phase 2 Validation Tests**
5. Review the detailed HTML report

**Test Categories:**
- Tutorial Builder (5 tests)
- Admin List Interface (5 tests)
- Frontend Display (5 tests)
- Active Tutorial (5 tests)
- Progress Persistence (4 tests)
- Integration (5 tests)
- Security (4 tests)
- Performance (3 tests)
- Accessibility (4 tests)

**Pros:**
- Comprehensive testing
- Beautiful HTML report
- Tests actual functionality
- Checks AJAX handlers
- Verifies database tables
- Can export results

**Cons:**
- Requires WordPress running
- Requires database connection
- Slightly slower (2-5 seconds)

### Method 3: CLI with WordPress (WP-CLI)

If you have WP-CLI installed and the site is running:

```bash
wp eval-file wp-content/plugins/aiddata-training/includes/admin/run-phase-2-validation.php
```

**Pros:**
- Command-line friendly
- Colored terminal output
- Full functional testing
- Good for automation

**Cons:**
- Requires WP-CLI installed
- Requires site running
- Requires database connection

### Method 4: Direct PHP Script (Requires WordPress)

From within the WordPress directory:

```bash
php wp-content/plugins/aiddata-training/includes/admin/run-phase-2-validation.php
```

**Note:** This requires the WordPress site to be running and database accessible.

## Interpreting Results

### Pass Rates

- **90%+** ✓ Excellent! Phase 2 is ready for Phase 3 advancement
- **75-89%** ⚠ Good progress. Address failing tests before Phase 3
- **Below 75%** ✗ Action required. Several critical features missing

### Common Issues

#### "Database Error" when running CLI validation

**Problem:** The Local by Flywheel site is not running.

**Solution:**
1. Start your Local site
2. Verify the site is accessible in browser
3. Run the validation again

OR use Method 1 (file check) which doesn't require database.

#### "Files Missing" in file validation

**Problem:** Phase 2 implementation is incomplete.

**Solution:**
1. Review `PHASE_2_IMPLEMENTATION_PROMPTS.md`
2. Implement missing prompts
3. Run validation again

#### Tests failing in admin validation

**Problem:** Code issues or missing functionality.

**Solution:**
1. Review the detailed error messages in the report
2. Check PHP error logs
3. Verify file permissions
4. Clear WordPress cache
5. Deactivate and reactivate the plugin

## What Gets Tested

### File Existence Tests (28 files)

✓ **Tutorial Builder (Prompt 1)**
- Meta boxes class
- Step builder views
- JavaScript and CSS

✓ **Admin List Interface (Prompt 2)**
- List table handler
- JavaScript and CSS

✓ **Frontend Display (Prompt 3)**
- Archive and single templates
- Tutorial card template
- Enrollment button
- Frontend CSS

✓ **Progress Persistence (Prompt 4)**
- Progress tracking class
- Milestones class

✓ **Active Tutorial Navigation (Prompt 5)**
- Active tutorial template
- Step renderer class
- AJAX handler class
- Navigation JavaScript and CSS

✓ **Supporting Classes**
- Enrollment manager
- Post types and taxonomies
- Frontend assets loader

✓ **Validation System**
- Validation classes
- Admin pages
- CLI runners

### Functional Tests (40 tests)

The full validation system tests:
- WordPress hooks registration
- AJAX handlers
- Database tables
- Security implementations
- Output escaping
- Input sanitization
- Performance metrics
- Accessibility features
- Phase 0 & 1 integration

## Recommended Testing Workflow

### Step 1: Quick File Check
```bash
php wp-content/plugins/aiddata-training/includes/admin/validate-phase-2-files.php
```

If this shows 100% completion, proceed to Step 2.

### Step 2: Full Validation
1. Start your Local site
2. Go to WordPress Admin → Tutorials → Phase 2 Validation
3. Click "Run Phase 2 Validation Tests"
4. Review the report

### Step 3: Manual Testing

Even with 100% automated test pass rate, perform manual testing:

- [ ] Create a tutorial with multiple steps
- [ ] Enroll in a tutorial
- [ ] Navigate through tutorial steps
- [ ] Mark steps complete
- [ ] Test milestone notifications
- [ ] Verify progress saves
- [ ] Test on mobile devices
- [ ] Test with keyboard only
- [ ] Test with screen reader
- [ ] Check cross-browser compatibility

### Step 4: Update Documentation

After validation passes:

1. Update `PHASE-2-BASELINE-VALIDATION-REPORT.md` with current results
2. Document any issues encountered and resolved
3. Update implementation status in relevant prompt validation reports

## Continuous Integration

### GitHub Actions Example

```yaml
name: Phase 2 Validation

on: [push, pull_request]

jobs:
  validate:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run File Validation
        run: php includes/admin/validate-phase-2-files.php
```

### Local Git Hook

Create `.git/hooks/pre-push`:

```bash
#!/bin/bash
echo "Running Phase 2 file validation..."
php wp-content/plugins/aiddata-training/includes/admin/validate-phase-2-files.php
if [ $? -ne 0 ]; then
    echo "Validation failed. Push aborted."
    exit 1
fi
```

Make it executable:
```bash
chmod +x .git/hooks/pre-push
```

## Troubleshooting

### PowerShell vs Bash

**PowerShell (Windows):**
```powershell
php "C:\path\to\wp-content\plugins\aiddata-training\includes\admin\validate-phase-2-files.php"
```

**Bash (Mac/Linux):**
```bash
php wp-content/plugins/aiddata-training/includes/admin/validate-phase-2-files.php
```

### Permission Issues

```bash
# Fix file permissions (Linux/Mac)
chmod +x includes/admin/*.php
```

### WordPress Not Found

If you get "Could not find WordPress installation":

1. Check you're in the WordPress root directory
2. Verify `wp-load.php` exists
3. Use absolute path to the script

## Next Steps

After validation passes:

1. **Manual Testing:** Test all user workflows
2. **Cross-Browser Testing:** Chrome, Firefox, Safari, Edge
3. **Mobile Testing:** iOS and Android devices
4. **Accessibility Testing:** Screen readers (NVDA, JAWS, VoiceOver)
5. **Performance Testing:** Use Query Monitor plugin
6. **Documentation:** Update all validation reports
7. **Phase 3 Planning:** Review Phase 3 requirements

## Support

For issues or questions:

1. Review validation report messages
2. Check PHP error logs
3. Review implementation documentation in `dev-docs/`
4. Check `PHASE_2_IMPLEMENTATION_PROMPTS.md`
5. Review validation reports in `dev-docs/prompt-validation-reports/PHASE-2-validation-reports/`

---

**Last Updated:** October 23, 2025  
**Plugin Version:** 2.0.0  
**Validation System Version:** 1.0.0

