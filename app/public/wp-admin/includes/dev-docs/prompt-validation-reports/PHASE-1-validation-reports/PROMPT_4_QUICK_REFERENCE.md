# PROMPT 4 - QUICK REFERENCE GUIDE
## Enrollment Frontend JavaScript

**Date:** October 22, 2025  
**Status:** ✅ COMPLETE  
**Purpose:** Quick reference for frontend enrollment system

---

## 📁 FILE LOCATIONS

```
assets/
├── css/
│   └── frontend/
│       └── enrollment.css              # Frontend styles
└── js/
    └── frontend/
        └── enrollment.js                # Frontend JavaScript

includes/
└── class-aiddata-lms-frontend-assets.php  # Asset enqueue class

templates/
└── template-parts/
    └── enrollment-button.php           # Enrollment button template
```

---

## 🎯 JAVASCRIPT API

### EnrollmentManager Object

```javascript
// Available globally as part of enrollment.js
EnrollmentManager = {
    init()                                  // Initialize system
    bindEvents()                            // Bind event handlers
    handleEnroll(event)                     // Handle enroll click
    enrollUser(tutorialId, $button)         // Execute enrollment
    handleUnenroll(event)                   // Handle unenroll click
    unenrollUser(tutorialId, $button)       // Execute unenrollment
    checkEnrollmentStatus()                 // Check status on load
    updateEnrollmentUI(id, enrolled, data)  // Update UI
    updateProgressDisplay(id, progress)     // Update progress bar
    getStatusText(status, percent)          // Get status text
    getTutorialId()                         // Get tutorial ID
    showSuccess(message)                    // Show success notification
    showError(message)                      // Show error notification
    showNotification(message, type)         // Show notification
    handleContinue(event)                   // Handle continue click
}
```

### Localized JavaScript Object

```javascript
aiddataLMS = {
    ajaxUrl: '...',           // WordPress AJAX URL
    enrollmentNonce: '...',   // Enrollment action nonce
    progressNonce: '...',     // Progress action nonce
    tutorialId: 123,          // Current tutorial ID
    confirmUnenroll: '...'    // Unenrollment confirmation text
}
```

---

## 🎨 CSS CLASSES

### Button Classes
```css
.aiddata-lms-enroll-btn        /* Enrollment button */
.aiddata-lms-continue-btn      /* Continue/Start button */
.aiddata-lms-unenroll-btn      /* Unenrollment button */
.aiddata-lms-login-btn         /* Login button */
```

### Container Classes
```css
.aiddata-lms-enrollment-container  /* Main container */
.aiddata-lms-enrolled-state        /* Enrolled state container */
.aiddata-lms-login-prompt          /* Login prompt container */
```

### Progress Classes
```css
.aiddata-lms-progress          /* Progress container */
.progress                      /* Progress bar container */
.progress-bar                  /* Progress bar fill */
.status-text                   /* Status text */
```

### Notification Classes
```css
.aiddata-lms-notification              /* Base notification */
.aiddata-lms-notification-success      /* Success notification */
.aiddata-lms-notification-error        /* Error notification */
.aiddata-lms-notification-info         /* Info notification */
.show                                  /* Show state (visible) */
```

### Utility Classes
```css
.spinner-border                /* Loading spinner */
.spinner-border-sm             /* Small spinner */
.visible                       /* Visible state */
```

---

## 🔌 TEMPLATE USAGE

### Basic Usage
```php
<?php
// In your theme template
$tutorial_id = get_the_ID();
include AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php';
?>
```

### Custom Container
```php
<div class="my-custom-container">
    <?php
    $tutorial_id = 123;
    include AIDDATA_LMS_PATH . 'templates/template-parts/enrollment-button.php';
    ?>
</div>
```

### Manual Button (Advanced)
```php
<?php
$tutorial_id = get_the_ID();
$user_id = get_current_user_id();
$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
$is_enrolled = $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );
?>

<?php if ( is_user_logged_in() && ! $is_enrolled ) : ?>
    <button class="aiddata-lms-enroll-btn custom-class" 
            data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
        Custom Enroll Text
    </button>
<?php endif; ?>
```

---

## 📡 AJAX ENDPOINTS USED

### Check Enrollment Status
```javascript
action: 'aiddata_lms_check_enrollment_status'
method: GET
params: { tutorial_id }
response: { enrolled, user_logged_in, enrollment, progress }
```

### Enroll User
```javascript
action: 'aiddata_lms_enroll_tutorial'
method: POST
params: { tutorial_id, nonce }
response: { message, enrollment, redirect_url }
```

### Unenroll User
```javascript
action: 'aiddata_lms_unenroll_tutorial'
method: POST
params: { tutorial_id, confirm, nonce }
response: { message }
```

---

## 🎭 UI STATES

### 1. Not Logged In
```html
<div class="aiddata-lms-login-prompt">
    <p>Please log in to enroll...</p>
    <a href="..." class="aiddata-lms-login-btn">Log In</a>
</div>
```

### 2. Not Enrolled
```html
<button class="aiddata-lms-enroll-btn" data-tutorial-id="123">
    Enroll Now
</button>
```

### 3. Enrolled
```html
<div class="aiddata-lms-enrolled-state visible">
    <p><strong>You are enrolled...</strong></p>
    <div class="aiddata-lms-progress">
        <div class="progress">
            <div class="progress-bar" style="width: 66%">66%</div>
        </div>
        <p class="status-text">In Progress - 66%</p>
    </div>
    <a href="..." class="aiddata-lms-continue-btn visible">Continue Learning</a>
    <button class="aiddata-lms-unenroll-btn">Unenroll</button>
</div>
```

---

## 🔔 NOTIFICATION SYSTEM

### Show Success
```javascript
EnrollmentManager.showSuccess('Successfully enrolled!');
```

### Show Error
```javascript
EnrollmentManager.showError('Enrollment failed. Please try again.');
```

### Custom Notification
```javascript
EnrollmentManager.showNotification('Custom message', 'info');
// Types: success, error, info
```

### Notification Behavior
- Slides in from right
- Displays for 5 seconds
- Slides out and removes
- Multiple notifications stack

---

## 🎨 STYLING CUSTOMIZATION

### Override Button Colors
```css
.aiddata-lms-enroll-btn {
    background-color: #your-color;
}

.aiddata-lms-enroll-btn:hover {
    background-color: #your-hover-color;
}
```

### Override Progress Bar
```css
.aiddata-lms-progress .progress-bar {
    background-color: #your-color;
}
```

### Override Notifications
```css
.aiddata-lms-notification-success {
    border-left-color: #your-color;
}
```

---

## 🔧 JAVASCRIPT CUSTOMIZATION

### Listen for Events
```javascript
// Custom handler after enrollment
jQuery(document).on('click', '.aiddata-lms-enroll-btn', function() {
    console.log('User clicked enroll');
});

// Check AJAX success
jQuery(document).ajaxSuccess(function(event, xhr, settings) {
    if (settings.data.indexOf('aiddata_lms_enroll_tutorial') > -1) {
        console.log('Enrollment AJAX completed');
    }
});
```

### Modify Behavior
```javascript
// Add custom action before enrollment
jQuery(document).on('click', '.aiddata-lms-enroll-btn', function(e) {
    if (!confirm('Are you sure?')) {
        e.stopImmediatePropagation();
        return false;
    }
});
```

---

## 🛠️ DEBUGGING

### Check JavaScript Loaded
```javascript
console.log(typeof EnrollmentManager); // Should be "object"
console.log(aiddataLMS);               // Should show localized data
```

### Check Styles Loaded
```javascript
// In browser console
document.querySelector('.aiddata-lms-enroll-btn');  // Should find button
getComputedStyle(document.querySelector('.aiddata-lms-enroll-btn')).backgroundColor;
```

### Check AJAX Requests
```javascript
// In browser Network tab
// Look for admin-ajax.php requests
// Check request payload and response
```

### Common Issues

**Button not working:**
- Check JavaScript console for errors
- Verify jQuery is loaded
- Confirm enrollment.js is enqueued
- Check nonce is valid

**Styles not applied:**
- Verify enrollment.css is enqueued
- Check browser cache
- Inspect element for class names
- Check CSS specificity

**AJAX fails:**
- Check nonce is included
- Verify tutorial ID is correct
- Check user is logged in (for enrollment)
- Review Network tab for error details

---

## 📱 RESPONSIVE BREAKPOINTS

```css
/* Default: Desktop */
/* All styles apply */

/* Mobile: <= 768px */
@media (max-width: 768px) {
    /* Full-width buttons */
    /* Stacked layout */
    /* Adjusted notifications */
}
```

---

## ♿ ACCESSIBILITY FEATURES

### Keyboard Navigation
- Tab: Navigate between buttons
- Enter/Space: Activate buttons
- Escape: Close notifications (future)

### Screen Readers
- ARIA labels on progress bar
- Semantic HTML structure
- Focus management
- Status announcements

### Visual
- Focus outlines visible
- High contrast support
- Color not only indicator
- Sufficient button sizing

---

## 🔗 INTEGRATION POINTS

### With AJAX Handlers (Prompt 3)
- Calls AJAX endpoints
- Handles responses
- Displays errors

### With Managers (Prompts 1 & 2)
- Template uses enrollment manager
- Template uses progress manager
- Displays enrollment status
- Shows progress data

### With Plugin
- Assets enqueued conditionally
- Localization provides data
- Follows WordPress standards

---

## ✅ CHECKLIST FOR IMPLEMENTATION

### Files Created
- [ ] enrollment.js in assets/js/frontend/
- [ ] enrollment.css in assets/css/frontend/
- [ ] enrollment-button.php in templates/template-parts/
- [ ] class-aiddata-lms-frontend-assets.php in includes/

### Plugin Integration
- [ ] Frontend assets class initialized
- [ ] Scripts enqueue on tutorial pages
- [ ] Styles enqueue on tutorial pages
- [ ] Localization includes AJAX data

### Testing
- [ ] Enroll button works
- [ ] Unenroll button works
- [ ] Progress displays correctly
- [ ] Notifications appear
- [ ] Mobile responsive
- [ ] Keyboard accessible
- [ ] No JavaScript errors

---

**Quick Reference Date:** October 22, 2025  
**Status:** Complete and Ready for Use ✅

