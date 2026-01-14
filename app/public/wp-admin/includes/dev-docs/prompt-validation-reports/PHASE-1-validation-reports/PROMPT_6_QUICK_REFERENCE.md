# PROMPT 6 QUICK REFERENCE
## Email Template System

**Date:** October 22, 2025  
**Status:** ✅ COMPLETE  
**Purpose:** Quick reference for Email Template System usage

---

## 📦 FILES

### Core Classes
- `includes/email/class-aiddata-lms-email-templates.php` - Template manager
- `includes/email/class-aiddata-lms-email-notifications.php` - Notification triggers

### Templates
- `assets/templates/email/enrollment-confirmation.html`
- `assets/templates/email/progress-reminder.html`
- `assets/templates/email/completion-congratulations.html`

### Tests
- `includes/email/class-aiddata-lms-email-templates-test.php` - Test suite
- `includes/email/run-email-template-tests.php` - Test runner

---

## 🚀 QUICK START

### Render a Template
```php
$template_manager = new AidData_LMS_Email_Templates();

$variables = array(
    '{user_first_name}' => 'John',
    '{tutorial_title}' => 'GIS Basics',
    '{tutorial_url}' => 'https://example.com/tutorial',
);

$html = $template_manager->render_template( 'enrollment-confirmation', $variables );
```

### Get Available Variables
```php
$template_manager = new AidData_LMS_Email_Templates();
$variables = $template_manager->get_available_variables();
```

### Get Available Templates
```php
$template_manager = new AidData_LMS_Email_Templates();
$templates = $template_manager->get_available_templates();
```

---

## 📧 AVAILABLE TEMPLATES

### 1. Enrollment Confirmation
**Template ID:** `enrollment-confirmation`  
**Trigger:** When user enrolls in tutorial  
**Priority:** High (3)  
**Variables Used:**
- `{user_first_name}`
- `{tutorial_title}`
- `{tutorial_url}`
- `{tutorial_description}`
- `{enrolled_date}`

### 2. Progress Reminder
**Template ID:** `progress-reminder`  
**Trigger:** At 25%, 50%, 75% milestones  
**Priority:** Normal (5)  
**Variables Used:**
- `{user_first_name}`
- `{tutorial_title}`
- `{tutorial_url}`
- `{progress_percent}`

### 3. Completion Congratulations
**Template ID:** `completion-congratulations`  
**Trigger:** When tutorial is completed  
**Priority:** High (2)  
**Variables Used:**
- `{user_first_name}`
- `{tutorial_title}`
- `{completion_date}`
- `{certificate_url}`

---

## 🔤 TEMPLATE VARIABLES (20 Total)

### User Variables
| Variable | Description |
|----------|-------------|
| `{user_name}` | User display name |
| `{user_email}` | User email address |
| `{user_first_name}` | User first name |
| `{user_last_name}` | User last name |

### Tutorial Variables
| Variable | Description |
|----------|-------------|
| `{tutorial_title}` | Tutorial title |
| `{tutorial_url}` | Tutorial permalink |
| `{tutorial_description}` | Tutorial excerpt |

### Progress Variables
| Variable | Description |
|----------|-------------|
| `{progress_percent}` | Progress percentage |
| `{completion_date}` | Completion date |
| `{enrolled_date}` | Enrollment date |

### Certificate Variables
| Variable | Description |
|----------|-------------|
| `{certificate_url}` | Certificate URL |
| `{certificate_id}` | Certificate ID |

### Quiz Variables
| Variable | Description |
|----------|-------------|
| `{quiz_score}` | Quiz score |
| `{quiz_attempts}` | Number of attempts |
| `{quiz_passing_score}` | Passing score |

### Site Variables
| Variable | Description |
|----------|-------------|
| `{site_name}` | Site name |
| `{site_url}` | Site URL |
| `{site_admin_email}` | Admin email |
| `{current_date}` | Current date |
| `{current_year}` | Current year |

---

## 🎨 CUSTOMIZATION

### Override Template in Theme

1. Create directory:
```
wp-content/themes/your-theme/aiddata-lms/email/
```

2. Copy template:
```
cp assets/templates/email/enrollment-confirmation.html \
   wp-content/themes/your-theme/aiddata-lms/email/
```

3. Modify as needed

4. System automatically uses theme version

### Add Custom Variables

```php
add_filter( 'aiddata_lms_email_template_variables', function( $variables, $template_id ) {
    $variables['{custom_var}'] = 'Custom Value';
    return $variables;
}, 10, 2 );
```

### Modify Template Content

```php
add_filter( 'aiddata_lms_email_template_content', function( $content, $template_id ) {
    // Modify content
    return $content;
}, 10, 2 );
```

### Modify Enrollment Email Variables

```php
add_filter( 'aiddata_lms_enrollment_email_variables', function( $variables, $user_id, $tutorial_id ) {
    // Add or modify variables
    $variables['{custom_field}'] = get_user_meta( $user_id, 'custom', true );
    return $variables;
}, 10, 3 );
```

---

## 🪝 WORDPRESS HOOKS

### Action Hooks (Listening)

#### Enrollment
```php
do_action( 'aiddata_lms_user_enrolled', $enrollment_id, $user_id, $tutorial_id, $source );
// Triggers: Enrollment confirmation email
```

#### Progress
```php
do_action( 'aiddata_lms_progress_updated', $user_id, $tutorial_id, $progress_percent );
// Triggers: Milestone email (at 25%, 50%, 75%)
```

#### Completion
```php
do_action( 'aiddata_lms_tutorial_completed', $user_id, $tutorial_id, $enrollment_id );
// Triggers: Completion congratulations email
```

### Filter Hooks (Providing)

```php
// Modify template content
apply_filters( 'aiddata_lms_email_template_content', $content, $template_id );

// Modify variables
apply_filters( 'aiddata_lms_email_template_variables', $variables, $template_id );

// Modify available templates
apply_filters( 'aiddata_lms_available_templates', $templates );

// Modify enrollment email variables
apply_filters( 'aiddata_lms_enrollment_email_variables', $variables, $user_id, $tutorial_id );

// Modify progress email variables
apply_filters( 'aiddata_lms_progress_email_variables', $variables, $user_id, $tutorial_id );

// Modify completion email variables
apply_filters( 'aiddata_lms_completion_email_variables', $variables, $user_id, $tutorial_id );
```

---

## 🎯 MILESTONE TRACKING

### How It Works

1. Progress milestone reached (25%, 50%, or 75%)
2. System checks user meta: `_aiddata_lms_progress_email_{percent}_{tutorial_id}`
3. If not sent, email is queued
4. Timestamp stored in user meta
5. Duplicate emails prevented

### Check Milestone Status

```php
$milestone = 25; // or 50, 75
$tutorial_id = 123;
$user_id = 456;

$meta_key = "_aiddata_lms_progress_email_{$milestone}_{$tutorial_id}";
$sent = get_user_meta( $user_id, $meta_key, true );

if ( $sent ) {
    echo "Milestone email sent at: " . $sent;
}
```

### Clear Milestone (for testing)

```php
$milestone = 25;
$tutorial_id = 123;
$user_id = 456;

$meta_key = "_aiddata_lms_progress_email_{$milestone}_{$tutorial_id}";
delete_user_meta( $user_id, $meta_key );
```

---

## 🧪 TESTING

### Run Tests

1. Navigate to: `Admin > Tools > Email Template Tests`
2. Click "Run Tests"
3. View results

Or access directly:
```
/wp-admin/admin.php?page=aiddata-lms-email-template-tests
```

### Manual Testing

```php
// Create test notification
$notifications = new AidData_LMS_Email_Notifications();

// Test enrollment email
$notifications->on_user_enrolled( 1, 123, 456, 'test' );

// Test progress email
$notifications->on_progress_updated( 123, 456, 50.0 );

// Test completion email
$notifications->on_tutorial_completed( 123, 456, 1 );

// Check email queue
global $wpdb;
$table = AidData_LMS_Database::get_table_name( 'email' );
$emails = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC LIMIT 10" );
print_r( $emails );
```

---

## 🔍 DEBUGGING

### Check if Email Was Queued

```php
global $wpdb;
$table_name = AidData_LMS_Database::get_table_name( 'email' );

$email = $wpdb->get_row( $wpdb->prepare(
    "SELECT * FROM {$table_name} 
    WHERE user_id = %d 
    AND email_type = %s 
    ORDER BY id DESC LIMIT 1",
    $user_id,
    'enrollment_confirmation'
) );

if ( $email ) {
    echo "Email queued: ID {$email->id}, Status: {$email->status}";
}
```

### Check Template Rendering

```php
$template_manager = new AidData_LMS_Email_Templates();

// Test template exists
$content = $template_manager->get_template_content( 'enrollment-confirmation' );
if ( empty( $content ) ) {
    echo "Template not found!";
}

// Test variable replacement
$variables = array( '{user_name}' => 'Test' );
$result = $template_manager->replace_variables( 'Hello {user_name}', $variables );
echo $result; // Should output: Hello Test
```

### Enable Error Logging

```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

// Check logs in: wp-content/debug.log
```

---

## 📊 COMMON TASKS

### Queue Email Manually

```php
$queue = new AidData_LMS_Email_Queue();
$template_manager = new AidData_LMS_Email_Templates();

// Prepare variables
$variables = array(
    '{user_first_name}' => 'John',
    '{tutorial_title}' => 'GIS Tutorial',
    '{tutorial_url}' => 'https://example.com/tutorial',
);

// Render template
$message = $template_manager->render_template( 'enrollment-confirmation', $variables );

// Queue email
$email_id = $queue->add_to_queue(
    'user@example.com',
    'Welcome to GIS Tutorial',
    $message,
    'enrollment_confirmation',
    array(
        'recipient_name' => 'John Doe',
        'user_id' => 123,
        'priority' => 3,
    )
);
```

### Create Custom Template

1. Create HTML file:
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Custom Template</title>
</head>
<body>
    <h1>Hello {user_name}!</h1>
    <p>Your custom content here.</p>
</body>
</html>
```

2. Save to:
```
assets/templates/email/custom-template.html
```

3. Use in code:
```php
$html = $template_manager->render_template( 'custom-template', $variables );
```

### Add to Available Templates

```php
add_filter( 'aiddata_lms_available_templates', function( $templates ) {
    $templates['custom-template'] = __( 'Custom Template', 'aiddata-lms' );
    return $templates;
} );
```

---

## 💡 BEST PRACTICES

### 1. Always Provide Fallback Values
```php
$variables = array(
    '{user_first_name}' => $user->first_name ?: $user->display_name,
);
```

### 2. Use Theme Overrides for Branding
- Copy templates to theme
- Modify styles to match brand
- No code changes needed

### 3. Test Before Production
- Use test runner
- Send test emails
- Verify all variables

### 4. Monitor Email Queue
- Check queue stats regularly
- Process failed emails
- Clean up old emails

### 5. Respect User Preferences
- Check if notifications enabled
- Provide unsubscribe options
- Follow email best practices

---

## 📚 ADDITIONAL RESOURCES

### Template Locations
- Plugin: `assets/templates/email/`
- Theme override: `your-theme/aiddata-lms/email/`

### Class Files
- Template Manager: `includes/email/class-aiddata-lms-email-templates.php`
- Notifications: `includes/email/class-aiddata-lms-email-notifications.php`

### Related Systems
- Email Queue (Prompt 5): `includes/email/class-aiddata-lms-email-queue.php`
- Enrollment (Prompt 1): `includes/tutorials/class-aiddata-lms-tutorial-enrollment.php`
- Progress (Prompt 2): `includes/tutorials/class-aiddata-lms-tutorial-progress.php`

---

**Reference Date:** October 22, 2025  
**Status:** VALIDATED ✅  
**Version:** 1.0

