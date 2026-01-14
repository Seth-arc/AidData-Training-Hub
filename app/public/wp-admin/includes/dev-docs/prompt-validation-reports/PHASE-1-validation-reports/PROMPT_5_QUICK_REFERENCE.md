# PROMPT 5 - QUICK REFERENCE
## Email Queue Manager

**Date:** October 22, 2025  
**Status:** ✅ COMPLETE  
**File:** `includes/email/class-aiddata-lms-email-queue.php`

---

## 📋 CLASS OVERVIEW

```php
class AidData_LMS_Email_Queue {
    public $table_name;
    private $max_attempts = 3;
}
```

---

## 🔧 PUBLIC METHODS

### 1. Add Email to Queue
```php
add_to_queue( 
    string $recipient_email, 
    string $subject, 
    string $message, 
    string $email_type, 
    array $options = [] 
): int|WP_Error
```

**Options:**
- `recipient_name` - Display name for recipient
- `user_id` - WordPress user ID
- `template_id` - Email template identifier
- `template_data` - Array of template variables
- `priority` - 1-10 (1 = highest)
- `scheduled_for` - Datetime string for future sending

**Returns:** Email ID on success, WP_Error on failure

**Example:**
```php
$queue = new AidData_LMS_Email_Queue();
$email_id = $queue->add_to_queue(
    'user@example.com',
    'Welcome!',
    '<p>Welcome message</p>',
    'welcome',
    array(
        'recipient_name' => 'John Doe',
        'priority' => 3,
    )
);
```

---

### 2. Process Queue
```php
process_queue( int $batch_size = 10 ): array
```

**Returns:** Array with `sent`, `failed`, `skipped` counts

**Example:**
```php
$results = $queue->process_queue( 20 );
echo "Sent: {$results['sent']}";
```

---

### 3. Retry Failed Emails
```php
retry_failed( int $max_attempts = 3 ): int
```

**Returns:** Count of emails reset for retry

**Example:**
```php
$count = $queue->retry_failed();
echo "Reset {$count} emails";
```

---

### 4. Get Queue Statistics
```php
get_queue_stats(): array
```

**Returns:** Array with status counts

**Keys:**
- `pending` - Emails waiting to send
- `processing` - Emails currently being sent
- `sent` - Successfully sent emails
- `failed` - Failed emails
- `total` - Total email count

**Example:**
```php
$stats = $queue->get_queue_stats();
echo "Pending: {$stats['pending']}";
```

---

### 5. Get Pending Emails
```php
get_pending_emails( int $limit = 10 ): array
```

**Returns:** Array of email objects

**Example:**
```php
$pending = $queue->get_pending_emails( 50 );
foreach ( $pending as $email ) {
    echo $email->subject;
}
```

---

### 6. Mark as Sent
```php
mark_as_sent( int $email_id ): bool
```

**Returns:** True on success, false on failure

**Example:**
```php
$queue->mark_as_sent( 123 );
```

---

### 7. Mark as Failed
```php
mark_as_failed( int $email_id, string $error_message ): bool
```

**Returns:** True on success, false on failure

**Example:**
```php
$queue->mark_as_failed( 123, 'SMTP error' );
```

---

### 8. Delete Old Emails
```php
delete_old_emails( int $days = 30 ): int
```

**Returns:** Count of deleted emails

**Example:**
```php
$deleted = $queue->delete_old_emails( 60 );
echo "Deleted {$deleted} old emails";
```

---

## 🪝 ACTION HOOKS

### aiddata_lms_email_queued
**Fires:** After email added to queue  
**Parameters:** `$email_id, $email_type, $recipient_email`

```php
add_action( 'aiddata_lms_email_queued', function( $id, $type, $email ) {
    error_log( "Email {$id} queued for {$email}" );
}, 10, 3 );
```

---

### aiddata_lms_queue_processed
**Fires:** After batch processing  
**Parameters:** `$results` (array)

```php
add_action( 'aiddata_lms_queue_processed', function( $results ) {
    error_log( "Sent: {$results['sent']}" );
}, 10, 1 );
```

---

### aiddata_lms_email_sent
**Fires:** After email marked as sent  
**Parameters:** `$email_id`

```php
add_action( 'aiddata_lms_email_sent', function( $email_id ) {
    // Track successful delivery
}, 10, 1 );
```

---

### aiddata_lms_email_failed
**Fires:** After email marked as failed  
**Parameters:** `$email_id, $error_message`

```php
add_action( 'aiddata_lms_email_failed', function( $id, $error ) {
    error_log( "Email {$id} failed: {$error}" );
}, 10, 2 );
```

---

## 🔀 FILTER HOOKS

### aiddata_lms_email_to
**Filters:** Recipient email address  
**Parameters:** `$recipient_email, $email` (object)

```php
add_filter( 'aiddata_lms_email_to', function( $to, $email ) {
    // Modify recipient
    return $to;
}, 10, 2 );
```

---

### aiddata_lms_email_subject
**Filters:** Email subject  
**Parameters:** `$subject, $email` (object)

```php
add_filter( 'aiddata_lms_email_subject', function( $subject, $email ) {
    return '[AidData] ' . $subject;
}, 10, 2 );
```

---

### aiddata_lms_email_message
**Filters:** Email message body  
**Parameters:** `$message, $email` (object)

```php
add_filter( 'aiddata_lms_email_message', function( $message, $email ) {
    // Modify message
    return $message;
}, 10, 2 );
```

---

### aiddata_lms_email_headers
**Filters:** Email headers array  
**Parameters:** `$headers, $email` (object)

```php
add_filter( 'aiddata_lms_email_headers', function( $headers, $email ) {
    $headers[] = 'Reply-To: support@example.com';
    return $headers;
}, 10, 2 );
```

---

## ⏰ WP-CRON

### Cron Event
**Event Name:** `aiddata_lms_process_email_queue`  
**Schedule:** Every 5 minutes  
**Hook:** Connected to `process_queue()` method

### Manual Processing
```php
// Force immediate processing
do_action( 'aiddata_lms_process_email_queue' );

// Or call directly
$queue = new AidData_LMS_Email_Queue();
$queue->process_queue( 50 );
```

### Check Next Scheduled
```php
$timestamp = wp_next_scheduled( 'aiddata_lms_process_email_queue' );
if ( $timestamp ) {
    echo 'Next run: ' . wp_date( 'Y-m-d H:i:s', $timestamp );
}
```

---

## 📊 DATABASE FIELDS

### Email Queue Table

| Field | Type | Description |
|-------|------|-------------|
| `id` | BIGINT | Primary key |
| `recipient_email` | VARCHAR | Email address |
| `recipient_name` | VARCHAR | Display name |
| `user_id` | BIGINT | WordPress user ID |
| `subject` | VARCHAR | Email subject |
| `message` | TEXT | Email body (HTML) |
| `email_type` | VARCHAR | Type identifier |
| `template_id` | VARCHAR | Template name |
| `template_data` | TEXT | JSON data |
| `priority` | TINYINT | 1-10 priority |
| `status` | VARCHAR | pending/processing/sent/failed |
| `attempts` | INT | Send attempts |
| `scheduled_for` | DATETIME | Send time |
| `sent_at` | DATETIME | Success timestamp |
| `last_attempt` | DATETIME | Last try timestamp |
| `error_message` | TEXT | Error details |
| `created_at` | DATETIME | Queue timestamp |

---

## 🎯 COMMON USE CASES

### Queue Simple Email
```php
$queue = new AidData_LMS_Email_Queue();
$queue->add_to_queue(
    'user@example.com',
    'Test Email',
    '<p>Hello World!</p>',
    'test'
);
```

---

### Queue with Priority
```php
$queue->add_to_queue(
    'user@example.com',
    'Urgent: Action Required',
    '<p>Please respond immediately</p>',
    'urgent',
    array( 'priority' => 1 ) // Highest priority
);
```

---

### Schedule Future Email
```php
$send_time = gmdate( 'Y-m-d H:i:s', strtotime( '+24 hours' ) );

$queue->add_to_queue(
    'user@example.com',
    'Reminder',
    '<p>Don\'t forget!</p>',
    'reminder',
    array( 'scheduled_for' => $send_time )
);
```

---

### Queue with Template Data
```php
$queue->add_to_queue(
    'user@example.com',
    'Welcome',
    '', // Will be filled by template
    'welcome',
    array(
        'template_id' => 'welcome-email',
        'template_data' => array(
            'user_name' => 'John',
            'tutorial_title' => 'GIS Basics',
        ),
    )
);
```

---

### Monitor Queue Status
```php
$stats = $queue->get_queue_stats();

echo "Queue Status:\n";
echo "- Pending: {$stats['pending']}\n";
echo "- Sent: {$stats['sent']}\n";
echo "- Failed: {$stats['failed']}\n";
echo "- Total: {$stats['total']}\n";
```

---

### Cleanup Old Emails
```php
// Delete emails older than 60 days
$deleted = $queue->delete_old_emails( 60 );
echo "Cleaned up {$deleted} old emails";
```

---

## ⚠️ ERROR HANDLING

### Check for Errors
```php
$result = $queue->add_to_queue( 'invalid-email', 'Test', 'Message', 'test' );

if ( is_wp_error( $result ) ) {
    echo 'Error: ' . $result->get_error_message();
    echo 'Code: ' . $result->get_error_code();
} else {
    echo 'Success! Email ID: ' . $result;
}
```

---

### Error Codes
- `invalid_email` - Email address format invalid
- `db_error` - Database operation failed

---

## 🔒 SECURITY NOTES

1. **Email Validation**
   - Always validates with `is_email()`
   - Rejects invalid formats

2. **Input Sanitization**
   - All inputs sanitized before database
   - HTML content allowed but sanitized

3. **SQL Injection Prevention**
   - All queries use prepared statements
   - No raw SQL with user input

4. **Priority Clamping**
   - Values clamped between 1-10
   - Prevents invalid priorities

---

## 📈 PERFORMANCE TIPS

1. **Batch Size**
   - Default: 10 emails per batch
   - Increase for faster processing
   - Decrease if memory limited

2. **Cleanup Schedule**
   - Run `delete_old_emails()` regularly
   - Prevents table bloat
   - Recommended: 30-90 days retention

3. **Cron Frequency**
   - Default: 5 minutes
   - Modify via filter if needed
   - Balance between speed and load

---

## 🧪 TESTING

### Run Tests
```
Navigate to: WP Admin → AidData LMS → Email Queue Tests
Click: "Run Email Queue Tests"
```

### Test Coverage
- 16 comprehensive tests
- Queue operations
- Priority handling
- Retry logic
- Statistics
- WP-Cron integration

---

## ✅ INTEGRATION CHECKLIST

- [x] Class instantiated in main plugin
- [x] WP-Cron scheduled automatically
- [x] Database table exists
- [ ] Test with real email address
- [ ] Verify cron execution
- [ ] Monitor queue processing
- [ ] Set up email templates (Prompt 6)

---

**Status:** READY FOR PRODUCTION  
**Next:** Prompt 6 - Email Template System

---

**Document Date:** October 22, 2025  
**Reference:** PROMPT_5_VALIDATION_REPORT.md

