# PROMPT 7 - QUICK REFERENCE
## Analytics Tracking System

**Date:** October 22, 2025  
**Status:** ✅ VALIDATED  
**Purpose:** Quick reference for Analytics Tracking integration

---

## 📋 QUICK OVERVIEW

### Component
**Analytics Manager** - Complete event tracking and analytics system with privacy compliance

### Files
- **Core:** `includes/analytics/class-aiddata-lms-analytics.php`
- **Tests:** `includes/analytics/class-aiddata-lms-analytics-test.php`
- **Runner:** `includes/analytics/run-analytics-tests.php`

---

## 🚀 QUICK START

### Initialize Analytics
```php
$analytics = new AidData_LMS_Analytics();
```

### Track Event
```php
$event_id = $analytics->track_event(
    $tutorial_id,
    'custom_event',
    array( 'key' => 'value' ),
    $user_id // optional
);
```

### Get Analytics
```php
$data = $analytics->get_tutorial_analytics( $tutorial_id );
$user_data = $analytics->get_user_analytics( $user_id );
$platform_data = $analytics->get_platform_analytics();
```

---

## 📊 CORE METHODS

### Event Tracking
```php
// Track any event
track_event( int $tutorial_id, string $event_type, array $event_data = [], ?int $user_id = null ): int|WP_Error

// Specific tracking methods
track_enrollment( int $enrollment_id, int $user_id, int $tutorial_id, string $source ): void
track_step_completion( int $user_id, int $tutorial_id, int $step_index ): void
track_tutorial_view(): void
track_tutorial_completion( int $user_id, int $tutorial_id, int $enrollment_id ): void
```

### Analytics Queries
```php
// Tutorial analytics
get_tutorial_analytics( int $tutorial_id, array $date_range = [] ): array

// User analytics
get_user_analytics( int $user_id, array $date_range = [] ): array

// Platform analytics
get_platform_analytics( array $date_range = [] ): array

// Event counting
get_event_count( string $event_type, ?int $tutorial_id = null, array $date_range = [] ): int

// Unique users
get_unique_users( ?int $tutorial_id = null, array $date_range = [] ): int
```

### Data Management
```php
// Delete old records
delete_old_records( int $days = 365 ): int
```

---

## 🎯 EVENT TYPES

### Standard Events
- `tutorial_view` - Page view
- `tutorial_enroll` - User enrollment
- `step_complete` - Step completion
- `tutorial_complete` - Tutorial completion

### Custom Events
You can track any custom event:
```php
$analytics->track_event(
    $tutorial_id,
    'quiz_attempt',
    array(
        'quiz_id' => 123,
        'score' => 85,
        'passed' => true
    ),
    $user_id
);
```

---

## 🔄 DATA FLOW

```
User Action
    ↓
WordPress Hook Fires
    ↓
Analytics::track_event()
    ↓
Database Insert (with privacy controls)
    ↓
Event ID Returned
    ↓
aiddata_lms_event_tracked Hook Fires
```

---

## 🪝 HOOK INTEGRATION

### Hooks Fired
```php
// After event tracked
do_action( 'aiddata_lms_event_tracked', $event_id, $event_type, $tutorial_id, $user_id );
```

### Hooks Listened To
```php
// Automatically tracks these events
add_action( 'aiddata_lms_user_enrolled', ... );
add_action( 'aiddata_lms_step_completed', ... );
add_action( 'aiddata_lms_tutorial_completed', ... );
add_action( 'template_redirect', ... );
```

---

## 📊 ANALYTICS DATA STRUCTURE

### Tutorial Analytics
```php
array(
    'tutorial_id' => int,
    'event_counts' => array(
        array( 'event_type' => 'view', 'count' => 150 ),
        array( 'event_type' => 'enroll', 'count' => 50 ),
    ),
    'unique_users' => int,
    'unique_sessions' => int,
    'date_range' => array( 'start' => ..., 'end' => ... )
)
```

### User Analytics
```php
array(
    'user_id' => int,
    'total_events' => int,
    'unique_tutorials' => int,
    'event_counts' => array( ... ),
    'tutorial_activity' => array( ... ),
    'date_range' => array( ... )
)
```

### Platform Analytics
```php
array(
    'total_events' => int,
    'unique_users' => int,
    'unique_tutorials' => int,
    'top_events' => array( ... ),
    'top_tutorials' => array( ... ),
    'date_range' => array( ... )
)
```

---

## 🔒 PRIVACY FEATURES

### IP Hashing
- All IP addresses hashed with SHA256
- Unique salt per installation
- GDPR compliant

### Guest Users
- NULL user_id for guest tracking
- Session-based identification
- No cookies required

### Data Retention
```php
// Clean up old data
$deleted = $analytics->delete_old_records( 365 ); // Keep 1 year
```

---

## 📅 DATE RANGE FILTERING

### Format
```php
$date_range = array(
    'start' => '2025-01-01 00:00:00',
    'end'   => '2025-12-31 23:59:59'
);
```

### Usage
```php
$data = $analytics->get_tutorial_analytics( $tutorial_id, $date_range );
$data = $analytics->get_user_analytics( $user_id, $date_range );
$data = $analytics->get_platform_analytics( $date_range );
$count = $analytics->get_event_count( 'tutorial_view', null, $date_range );
```

---

## 🧪 COMMON USE CASES

### Use Case 1: Track Custom Event
```php
$analytics = new AidData_LMS_Analytics();

$result = $analytics->track_event(
    $tutorial_id,
    'download',
    array( 'file' => 'resource.pdf' ),
    $user_id
);

if ( is_wp_error( $result ) ) {
    error_log( $result->get_error_message() );
}
```

### Use Case 2: Get Tutorial Performance
```php
$analytics = new AidData_LMS_Analytics();

$views = $analytics->get_event_count( 'tutorial_view', $tutorial_id );
$enrollments = $analytics->get_event_count( 'tutorial_enroll', $tutorial_id );
$unique_users = $analytics->get_unique_users( $tutorial_id );

$conversion_rate = ( $enrollments / $views ) * 100;
```

### Use Case 3: User Activity Dashboard
```php
$user_data = $analytics->get_user_analytics( $user_id );

echo "Total Activity: " . $user_data['total_events'];
echo "Tutorials Accessed: " . $user_data['unique_tutorials'];

foreach ( $user_data['tutorial_activity'] as $activity ) {
    $tutorial = get_post( $activity['tutorial_id'] );
    echo $tutorial->post_title . ": Last active " . $activity['last_activity'];
}
```

### Use Case 4: Monthly Report
```php
$date_range = array(
    'start' => date( 'Y-m-01 00:00:00' ),
    'end'   => date( 'Y-m-t 23:59:59' )
);

$data = $analytics->get_platform_analytics( $date_range );

echo "This Month:";
echo "Total Events: " . $data['total_events'];
echo "Active Users: " . $data['unique_users'];
echo "Active Tutorials: " . $data['unique_tutorials'];
```

### Use Case 5: Top Tutorials Report
```php
$data = $analytics->get_platform_analytics();

echo "Top Tutorials:";
foreach ( $data['top_tutorials'] as $tutorial ) {
    $post = get_post( $tutorial['tutorial_id'] );
    echo $post->post_title;
    echo "Events: " . $tutorial['event_count'];
    echo "Users: " . $tutorial['user_count'];
}
```

---

## 🔍 DEBUGGING

### Check if Event Tracked
```php
$event_id = $analytics->track_event( ... );

if ( is_wp_error( $event_id ) ) {
    error_log( 'Tracking failed: ' . $event_id->get_error_message() );
} else {
    error_log( 'Event tracked with ID: ' . $event_id );
}
```

### Verify Analytics Data
```php
$data = $analytics->get_tutorial_analytics( $tutorial_id );
error_log( 'Tutorial analytics: ' . print_r( $data, true ) );
```

### Check Event Count
```php
$count = $analytics->get_event_count( 'tutorial_view', $tutorial_id );
error_log( "Views for tutorial $tutorial_id: $count" );
```

---

## 🎓 INTEGRATION STATUS

### With Previous Prompts
- ✅ Prompt 1 (Enrollment) - Tracks enrollments
- ✅ Prompt 2 (Progress) - Tracks steps and completion
- ✅ Prompt 3 (AJAX) - Works with AJAX operations
- ✅ Prompt 4 (Frontend) - Tracks frontend interactions
- ✅ Prompt 5 (Email Queue) - Ready for email tracking
- ✅ Prompt 6 (Email Templates) - Ready for template tracking

### With Next Prompts
- 🔜 Prompt 8 (Dashboard) - Query methods ready
- 🔜 Prompt 8 (Reports) - Aggregation methods ready

---

## 📝 ERROR CODES

| Code | Meaning |
|------|---------|
| `invalid_tutorial` | Tutorial doesn't exist |
| `db_error` | Database operation failed |

---

## ✅ VALIDATION STATUS

### Requirements Met
- ✅ All 8 core methods implemented
- ✅ Event tracking functional
- ✅ Privacy compliance (GDPR)
- ✅ Session management working
- ✅ Date range filtering works
- ✅ Hook integration complete
- ✅ 20 tests passing

### Ready For
- ✅ Dashboard widgets (Prompt 8)
- ✅ Basic reports (Prompt 8)
- ✅ Production use

---

**Reference Date:** October 22, 2025  
**Status:** APPROVED ✅

