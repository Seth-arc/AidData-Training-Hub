# PROMPT 3 QUICK REFERENCE
## AJAX Handlers - Essential Information

**Date:** October 22, 2025  
**Status:** ✅ COMPLETE

---

## 📦 FILES CREATED

```
includes/tutorials/
├── class-aiddata-lms-tutorial-ajax.php (365 lines)
├── class-aiddata-lms-tutorial-ajax-test.php (700+ lines)
└── run-ajax-tests.php (75 lines)
```

---

## 🔌 AJAX ENDPOINTS

### 1. Enroll Tutorial
```javascript
action: 'aiddata_lms_enroll_tutorial'
method: POST
nonce: 'aiddata-lms-enrollment'
params: { tutorial_id, nonce }
returns: { enrollment, redirect_url }
```

### 2. Unenroll Tutorial
```javascript
action: 'aiddata_lms_unenroll_tutorial'
method: POST
nonce: 'aiddata-lms-enrollment'
params: { tutorial_id, confirm: 'yes', nonce }
returns: { message }
```

### 3. Check Enrollment Status
```javascript
action: 'aiddata_lms_check_enrollment_status'
method: GET
nonce: none
params: { tutorial_id }
returns: { enrolled, user_logged_in, enrollment?, progress? }
```

### 4. Update Step Progress
```javascript
action: 'aiddata_lms_update_step_progress'
method: POST
nonce: 'aiddata-lms-progress'
params: { tutorial_id, step_index, nonce }
returns: { progress: { percent, current_step, completed_steps, status } }
```

### 5. Update Time Spent
```javascript
action: 'aiddata_lms_update_time_spent'
method: POST
nonce: 'aiddata-lms-progress'
params: { tutorial_id, seconds, nonce }
returns: { message }
```

---

## 🔒 SECURITY

### Nonces Required
- **Enrollment/Unenrollment:** `aiddata-lms-enrollment`
- **Progress/Time Updates:** `aiddata-lms-progress`

### Authentication
- All POST requests require logged-in user
- GET status check works for guests
- 401 error for unauthenticated requests

### Validation
- Tutorial ID: `absint()` validation
- Step index: >= 0
- Seconds: > 0
- Enrollment verification before progress updates

---

## 📊 HTTP STATUS CODES

- **200** - Success
- **400** - Bad Request (invalid parameters)
- **401** - Unauthorized (not logged in)
- **403** - Forbidden (not enrolled)

---

## 🧪 TESTING

### Run Tests
```
WordPress Admin → AidData LMS → AJAX Tests
```

### Test Coverage
- ✅ 15 comprehensive tests
- ✅ Security validation
- ✅ Input validation
- ✅ Integration testing
- ✅ Error handling

---

## 🔗 INTEGRATION

### With Enrollment Manager (Prompt 1)
```php
$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
$result = $enrollment_manager->enroll_user( $user_id, $tutorial_id, 'web' );
```

### With Progress Manager (Prompt 2)
```php
$progress_manager = new AidData_LMS_Tutorial_Progress();
$result = $progress_manager->update_progress( $user_id, $tutorial_id, $step_index );
```

### Loaded in Main Plugin
```php
// includes/class-aiddata-lms.php
if ( wp_doing_ajax() ) {
    new AidData_LMS_Tutorial_AJAX();
}
```

---

## 💻 FRONTEND USAGE (PROMPT 4)

### Required Script Localization
```php
wp_localize_script( 'aiddata-lms-enrollment', 'aiddataLMS', array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'enrollmentNonce' => wp_create_nonce( 'aiddata-lms-enrollment' ),
    'progressNonce' => wp_create_nonce( 'aiddata-lms-progress' ),
    'tutorialId' => get_the_ID(),
) );
```

### Example AJAX Call
```javascript
jQuery.ajax({
    url: aiddataLMS.ajaxUrl,
    type: 'POST',
    data: {
        action: 'aiddata_lms_enroll_tutorial',
        tutorial_id: aiddataLMS.tutorialId,
        nonce: aiddataLMS.enrollmentNonce
    },
    success: function(response) {
        if (response.success) {
            // Handle success
        } else {
            // Handle error
        }
    }
});
```

---

## 📋 JSON RESPONSE FORMAT

### Success Response
```json
{
    "success": true,
    "data": {
        "message": "Success message",
        // Additional data...
    }
}
```

### Error Response
```json
{
    "success": false,
    "data": {
        "message": "Error message",
        "code": "error_code"
    }
}
```

---

## ✅ VALIDATION CHECKLIST

- [x] All 5 endpoints implemented
- [x] Nonce verification on POST requests
- [x] User authentication checks
- [x] Input sanitization
- [x] HTTP status codes
- [x] JSON response format
- [x] Integration with managers
- [x] Error handling
- [x] Test suite (15 tests)
- [x] Documentation complete

---

## 🚀 NEXT: PROMPT 4

Ready for Frontend JavaScript implementation:
- Create `enrollment.js`
- Create `enrollment.css`
- Create enrollment button template
- Implement UI interactions
- Handle loading states
- Display notifications

---

**Status:** COMPLETE ✅  
**Next Step:** Prompt 4 - Frontend JavaScript

