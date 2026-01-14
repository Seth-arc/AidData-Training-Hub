# CODE STANDARDS AND VALIDATION GUIDE
## AidData LMS Tutorial Builder - Quality Assurance Framework

**Version:** 1.0  
**Date:** October 22, 2025  
**Purpose:** Ensure consistency, prevent conflicts, and maintain quality across all implementation phases

---

## TABLE OF CONTENTS

1. [Code Standards & Rules](#1-code-standards--rules)
2. [Architecture Validation](#2-architecture-validation)
3. [Phase-by-Phase Validation Checklist](#3-phase-by-phase-validation-checklist)
4. [Integration Checkpoints](#4-integration-checkpoints)
5. [Common Pitfalls & Prevention](#5-common-pitfalls--prevention)
6. [Pre-Commit Validation](#6-pre-commit-validation)
7. [Code Review Checklist](#7-code-review-checklist)

---

## 1. CODE STANDARDS & RULES

### 1.1 PHP Coding Standards

#### WordPress PHP Standards (MANDATORY)

```php
// ✅ CORRECT
class AidData_LMS_Tutorial_Enrollment {
    private $db;
    
    /**
     * Enroll user in tutorial
     *
     * @param int $user_id User ID
     * @param int $tutorial_id Tutorial ID
     * @return bool|WP_Error Success status or error
     */
    public function enroll_user( int $user_id, int $tutorial_id ) {
        // Validate input
        if ( ! $user_id || ! $tutorial_id ) {
            return new WP_Error( 'invalid_input', __( 'Invalid user or tutorial ID', 'aiddata-lms' ) );
        }
        
        // Check if already enrolled
        if ( $this->is_enrolled( $user_id, $tutorial_id ) ) {
            return new WP_Error( 'already_enrolled', __( 'User is already enrolled', 'aiddata-lms' ) );
        }
        
        // Database operation
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'aiddata_lms_tutorial_enrollments',
            array(
                'user_id'     => $user_id,
                'tutorial_id' => $tutorial_id,
                'enrolled_at' => current_time( 'mysql' ),
                'status'      => 'active',
            ),
            array( '%d', '%d', '%s', '%s' )
        );
        
        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Failed to enroll user', 'aiddata-lms' ) );
        }
        
        // Fire action hook
        do_action( 'aiddata_lms_user_enrolled', $user_id, $tutorial_id );
        
        return true;
    }
}

// ❌ WRONG - Violates multiple standards
class aiddataLMSEnrollment {  // Wrong naming convention
    function enrollUser($userId, $tutorialId) {  // No type hints, wrong naming
        global $wpdb;
        $wpdb->query("INSERT INTO enrollments VALUES ($userId, $tutorialId)");  // SQL injection!
        return true;  // No error handling
    }
}
```

**PHP Rules:**

| Rule | Requirement | Validation |
|------|-------------|------------|
| **Naming** | Classes: `Class_Name_With_Underscores`<br>Methods: `method_name_with_underscores`<br>Variables: `$variable_name` | Linter check |
| **Type Hints** | All function parameters MUST have type hints (PHP 8.1+) | Code review |
| **Return Types** | All functions MUST declare return type | Code review |
| **Error Handling** | Return `WP_Error` on failure, never throw exceptions | Code review |
| **Docblocks** | ALL public methods MUST have docblocks | PHPDoc checker |
| **Spacing** | 1 space after control structures, around operators | PHP_CodeSniffer |
| **Braces** | Opening brace on same line for functions/classes | PHP_CodeSniffer |
| **Indentation** | TABS (not spaces) for indentation | EditorConfig |
| **Line Length** | Max 100 characters (soft limit), 120 (hard limit) | PHP_CodeSniffer |
| **Yoda Conditions** | Use for comparisons: `if ( 'value' === $var )` | PHP_CodeSniffer |

#### Security Rules (CRITICAL)

```php
// ✅ CORRECT - Secure database queries
global $wpdb;
$results = $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}aiddata_lms_enrollments WHERE user_id = %d AND tutorial_id = %d",
    $user_id,
    $tutorial_id
) );

// ✅ CORRECT - Nonce verification
if ( ! wp_verify_nonce( $_POST['nonce'], 'enroll_tutorial_' . $tutorial_id ) ) {
    wp_die( esc_html__( 'Security check failed', 'aiddata-lms' ) );
}

// ✅ CORRECT - Capability check
if ( ! current_user_can( 'edit_posts' ) ) {
    wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'aiddata-lms' ) ) );
}

// ✅ CORRECT - Input sanitization
$tutorial_title = sanitize_text_field( $_POST['tutorial_title'] );
$tutorial_content = wp_kses_post( $_POST['tutorial_content'] );
$tutorial_id = absint( $_POST['tutorial_id'] );

// ✅ CORRECT - Output escaping
echo '<h1>' . esc_html( $tutorial_title ) . '</h1>';
echo '<a href="' . esc_url( $tutorial_url ) . '">' . esc_html( $link_text ) . '</a>';
echo '<div class="' . esc_attr( $css_class ) . '">';

// ❌ WRONG - Multiple security issues
$results = $wpdb->query( "SELECT * FROM enrollments WHERE user_id = {$_POST['user_id']}" );  // SQL injection!
echo "<h1>{$_POST['title']}</h1>";  // XSS vulnerability!
if ( $_POST['action'] === 'delete' ) {  // No nonce check!
    $wpdb->delete( 'enrollments', array( 'id' => $_POST['id'] ) );  // CSRF vulnerability!
}
```

**Security Validation Checklist:**

- [ ] All database queries use `$wpdb->prepare()`
- [ ] All AJAX actions verify nonce
- [ ] All admin actions check capabilities
- [ ] All user input is sanitized
- [ ] All output is escaped
- [ ] No direct `$_POST`, `$_GET`, `$_REQUEST` usage without sanitization
- [ ] File uploads validate file type and size
- [ ] No `eval()` or similar dangerous functions

---

### 1.2 JavaScript Coding Standards

#### Modern JavaScript (ES6+)

```javascript
// ✅ CORRECT - Modern, clean, secure
class VideoTracker {
    constructor( element, options = {} ) {
        this.element = element;
        this.options = {
            updateInterval: 10000,
            completionThreshold: 0.9,
            ...options
        };
        this.currentPosition = 0;
        this.updateTimer = null;
    }
    
    /**
     * Start tracking video progress
     */
    startTracking() {
        if ( this.updateTimer ) {
            return;
        }
        
        this.updateTimer = setInterval( () => {
            this.updateProgress();
        }, this.options.updateInterval );
    }
    
    /**
     * Update progress via AJAX
     */
    async updateProgress() {
        try {
            const response = await fetch( ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'aiddata_lms_update_video_progress',
                    nonce: aiddataLMS.nonce,
                    tutorial_id: this.options.tutorialId,
                    step_index: this.options.stepIndex,
                    position: this.currentPosition,
                    duration: this.getDuration()
                })
            });
            
            if ( ! response.ok ) {
                throw new Error( 'Network response was not ok' );
            }
            
            const data = await response.json();
            
            if ( data.success ) {
                this.onProgressUpdated( data.data );
            } else {
                this.onProgressError( data.data );
            }
        } catch ( error ) {
            console.error( 'Failed to update progress:', error );
            this.handleError( error );
        }
    }
    
    /**
     * Clean up when destroying
     */
    destroy() {
        if ( this.updateTimer ) {
            clearInterval( this.updateTimer );
            this.updateTimer = null;
        }
    }
}

// ❌ WRONG - Old style, insecure, messy
function trackVideo() {  // No structure
    var pos = 0;  // Use const/let
    setInterval(function() {  // Use arrow function or method
        $.post(ajaxurl, {  // Use fetch API
            action: 'update_progress',  // No nonce!
            position: pos  // No validation
        }, function(response) {  // No error handling
            console.log(response);
        });
    }, 10000);
}
```

**JavaScript Rules:**

| Rule | Requirement | Validation |
|------|-------------|------------|
| **Modern Syntax** | Use ES6+: arrow functions, classes, template literals | ESLint |
| **Variable Declaration** | Use `const` by default, `let` when needed, NEVER `var` | ESLint |
| **Naming** | camelCase for variables/functions, PascalCase for classes | ESLint |
| **Async Operations** | Use async/await or Promises, not callbacks | Code review |
| **Error Handling** | Always use try-catch for async operations | ESLint |
| **No Global Pollution** | Wrap in IIFE or use modules | Code review |
| **Comments** | JSDoc for all public methods | ESLint |
| **Nonce Verification** | All AJAX requests MUST include nonce | Code review |
| **No console.log** | Remove all console statements in production | ESLint (no-console) |

---

### 1.3 Database Standards

#### Schema Consistency Rules

```sql
-- ✅ CORRECT - Follows conventions
CREATE TABLE wp_aiddata_lms_tutorial_enrollments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    tutorial_id BIGINT UNSIGNED NOT NULL,
    enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    completion_date DATETIME NULL,
    UNIQUE KEY user_tutorial (user_id, tutorial_id),
    KEY tutorial_id (tutorial_id),
    KEY status (status),
    KEY enrolled_at (enrolled_at),
    CONSTRAINT fk_enrollment_user FOREIGN KEY (user_id) 
        REFERENCES wp_users(ID) ON DELETE CASCADE,
    CONSTRAINT fk_enrollment_tutorial FOREIGN KEY (tutorial_id) 
        REFERENCES wp_posts(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ❌ WRONG - Multiple issues
CREATE TABLE enrollments (  -- Missing prefix
    id INT PRIMARY KEY,  -- Should be BIGINT UNSIGNED
    user INT,  -- No UNSIGNED, no NOT NULL
    tutorial INT,  -- No constraints
    date DATETIME  -- Ambiguous name
);  -- No ENGINE, CHARSET, COLLATE
```

**Database Rules:**

| Rule | Requirement | Validation |
|------|-------------|------------|
| **Table Prefix** | ALWAYS use `wp_aiddata_lms_` prefix | Schema review |
| **Primary Keys** | `BIGINT UNSIGNED AUTO_INCREMENT` | Schema review |
| **Foreign Keys** | Use `BIGINT UNSIGNED` to match WordPress | Schema review |
| **Timestamps** | Use `DATETIME`, not `TIMESTAMP` | Schema review |
| **Indexes** | Add indexes for all foreign keys and commonly queried columns | Schema review |
| **Constraints** | Add foreign key constraints with ON DELETE CASCADE | Schema review |
| **Engine** | Always InnoDB | Schema review |
| **Charset** | utf8mb4 with utf8mb4_unicode_ci collation | Schema review |
| **Column Names** | snake_case, descriptive, no abbreviations | Schema review |

#### Query Performance Rules

```php
// ✅ CORRECT - Optimized query
global $wpdb;
$results = $wpdb->get_results( $wpdb->prepare(
    "SELECT 
        e.id,
        e.user_id,
        e.tutorial_id,
        e.enrolled_at,
        p.post_title as tutorial_title
    FROM {$wpdb->prefix}aiddata_lms_tutorial_enrollments e
    INNER JOIN {$wpdb->posts} p ON e.tutorial_id = p.ID
    WHERE e.user_id = %d 
    AND e.status = %s
    AND p.post_status = 'publish'
    ORDER BY e.enrolled_at DESC
    LIMIT 10",
    $user_id,
    'active'
) );

// ❌ WRONG - N+1 query problem
$enrollments = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}aiddata_lms_tutorial_enrollments" );
foreach ( $enrollments as $enrollment ) {
    $tutorial = $wpdb->get_row( "SELECT * FROM {$wpdb->posts} WHERE ID = {$enrollment->tutorial_id}" );  // Executed in loop!
}
```

**Query Performance Validation:**

- [ ] No SELECT * (specify columns)
- [ ] Use JOINs instead of multiple queries
- [ ] Add LIMIT to all queries that don't need all results
- [ ] Use indexes (check with EXPLAIN)
- [ ] No queries in loops (N+1 problem)
- [ ] Cache results when appropriate

---

### 1.4 CSS Standards

```css
/* ✅ CORRECT - BEM methodology, organized */
.aiddata-lms-tutorial {
    margin-bottom: 2rem;
}

.aiddata-lms-tutorial__header {
    padding: 1.5rem;
    background-color: #f5f5f5;
}

.aiddata-lms-tutorial__title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 0.5rem;
}

.aiddata-lms-tutorial__meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #666;
}

.aiddata-lms-tutorial--featured {
    border: 2px solid #0073aa;
}

.aiddata-lms-tutorial--completed {
    opacity: 0.7;
}

/* ❌ WRONG - No structure, inconsistent */
.tutorial { margin: 20px; }  /* Not prefixed */
#tutorial-title { font-size: 24px !important; }  /* ID selector, !important */
.TutorialMeta { color: gray; }  /* Wrong naming */
div.tutorial > div { padding: 10px; }  /* Overly specific */
```

**CSS Rules:**

| Rule | Requirement | Validation |
|------|-------------|------------|
| **Naming** | BEM methodology: `.block__element--modifier` | Stylelint |
| **Prefix** | All classes start with `aiddata-lms-` | Stylelint |
| **No IDs** | Never use ID selectors for styling | Stylelint |
| **No !important** | Avoid !important (only for overrides with comment) | Stylelint |
| **Units** | Use rem for sizing, px for borders | Stylelint |
| **Colors** | Use variables, not hardcoded colors | Code review |
| **Responsive** | Mobile-first approach | Code review |

---

## 2. ARCHITECTURE VALIDATION

### 2.1 File Structure Validation

**Verify each phase maintains correct structure:**

```
wp-content/plugins/aiddata-lms/
├── aiddata-lms.php                 ✓ Main plugin file
├── includes/                        ✓ PHP classes
│   ├── class-aiddata-lms.php       ✓ Core class
│   ├── tutorials/                   ✓ Feature grouping
│   ├── video/                       ✓ Feature grouping
│   ├── quiz/                        ✓ Feature grouping
│   └── ...
├── assets/                          ✓ Frontend assets
│   ├── js/
│   │   ├── admin/                   ✓ Admin scripts
│   │   ├── frontend/                ✓ Frontend scripts
│   │   └── blocks/                  ✓ Gutenberg blocks
│   ├── css/
│   │   ├── admin/                   ✓ Admin styles
│   │   ├── frontend/                ✓ Frontend styles
│   │   └── blocks/                  ✓ Block styles
│   └── templates/                   ✓ Email/cert templates
└── templates/                       ✓ PHP templates
    └── template-parts/              ✓ Reusable parts
```

**Validation Checklist:**

- [ ] No files in root except main plugin file and readme
- [ ] All PHP classes in `includes/` subdirectories
- [ ] Assets organized by admin/frontend/blocks
- [ ] No mixing of concerns (e.g., quiz logic in video folder)
- [ ] Template files separate from logic files

### 2.2 Class Dependency Validation

**Dependency Rules:**

```php
// ✅ CORRECT - Dependency injection
class AidData_LMS_Tutorial_Enrollment {
    private $db_handler;
    private $email_system;
    private $analytics;
    
    public function __construct( 
        AidData_LMS_Database $db_handler,
        AidData_LMS_Email_System $email_system,
        AidData_LMS_Analytics $analytics
    ) {
        $this->db_handler = $db_handler;
        $this->email_system = $email_system;
        $this->analytics = $analytics;
    }
}

// ❌ WRONG - Tight coupling
class AidData_LMS_Tutorial_Enrollment {
    public function enroll_user( $user_id, $tutorial_id ) {
        // Direct instantiation - bad!
        $email = new AidData_LMS_Email_System();
        $analytics = new AidData_LMS_Analytics();
    }
}
```

**Dependency Validation:**

- [ ] No direct class instantiation (use dependency injection)
- [ ] No circular dependencies (A depends on B, B depends on A)
- [ ] Core classes don't depend on feature classes
- [ ] Feature classes can depend on core classes
- [ ] Use interfaces for loose coupling

---

## 3. PHASE-BY-PHASE VALIDATION CHECKLIST

### PHASE 0: Foundation & Setup

#### Week 1: Environment & Database

**Pre-Development Validation:**
- [ ] All team members can access repository
- [ ] Local WordPress installs functional
- [ ] MySQL 8.0+ confirmed
- [ ] PHP 8.1+ confirmed
- [ ] Node.js 18+ confirmed

**Database Schema Validation:**
- [ ] All table names use `wp_aiddata_lms_` prefix
- [ ] All primary keys are `BIGINT UNSIGNED AUTO_INCREMENT`
- [ ] All foreign keys have constraints with CASCADE
- [ ] All tables use InnoDB engine
- [ ] All tables use utf8mb4 charset
- [ ] All indexes created for foreign keys
- [ ] No duplicate table names
- [ ] Migration script creates tables in correct order (dependencies first)

**Integration Checks:**
- [ ] New tables don't conflict with existing WordPress tables
- [ ] Foreign keys reference correct WordPress tables
- [ ] Column names follow WordPress conventions

#### Week 2: Plugin Structure

**File Structure Validation:**
- [ ] Main plugin file has correct header
- [ ] Autoloader file created and functional
- [ ] Core class follows singleton pattern
- [ ] No files outside approved structure

**Custom Post Type Validation:**
- [ ] `aiddata_tutorial` post type registered
- [ ] Supports Gutenberg
- [ ] Has correct capabilities
- [ ] Rewrite rules flushed
- [ ] REST API enabled
- [ ] No conflicts with existing post types

**Taxonomy Validation:**
- [ ] Tutorial categories hierarchical
- [ ] Tutorial tags non-hierarchical
- [ ] REST API enabled for both
- [ ] No conflicts with existing taxonomies

**Phase 0 Exit Criteria:**
- [ ] Plugin activates without errors
- [ ] Database tables created successfully
- [ ] Custom post types appear in admin
- [ ] No PHP errors or warnings
- [ ] All team members confirmed setup working

---

### PHASE 1: Core Infrastructure

#### Week 3: Enrollment System

**Backend Validation:**
- [ ] `AidData_LMS_Tutorial_Enrollment` class exists
- [ ] All methods have docblocks
- [ ] All methods have type hints
- [ ] Error handling returns `WP_Error`
- [ ] WordPress hooks fired on enroll/unenroll
- [ ] Database operations use `$wpdb->prepare()`

**Frontend Validation:**
- [ ] Enrollment button displays correct states
- [ ] AJAX nonce included in all requests
- [ ] Error messages are user-friendly
- [ ] Success messages displayed
- [ ] Loading states prevent double-submission

**Integration Checks:**
- [ ] Enrollment doesn't conflict with other plugins
- [ ] User must be logged in (or guest logic implemented)
- [ ] Enrollment creates analytics event
- [ ] Enrollment can trigger email (Week 4 dependency)

**Unit Test Coverage:**
- [ ] Test successful enrollment
- [ ] Test duplicate enrollment prevention
- [ ] Test unenrollment
- [ ] Test enrollment with prerequisites
- [ ] Test enrollment limits

#### Week 4: Email System

**Email Queue Validation:**
- [ ] Queue table created correctly
- [ ] Queue processes in batches
- [ ] Failed emails retry with backoff
- [ ] Old queue items cleaned up
- [ ] No email loops or infinite retries

**Template Validation:**
- [ ] All templates use WordPress email wrapper
- [ ] All variables properly replaced
- [ ] HTML email valid (W3C validator)
- [ ] Plain text version included
- [ ] Unsubscribe link present

**Integration Checks:**
- [ ] Enrollment triggers welcome email
- [ ] Email system doesn't block user actions (async)
- [ ] WP-Cron configured correctly
- [ ] Email preferences save per user

**Conflict Prevention:**
- [ ] Check for other email plugins (e.g., WP Mail SMTP)
- [ ] Don't override wp_mail function
- [ ] Use WordPress hooks, not direct mail()

#### Week 5: Basic Analytics

**Analytics Tracking Validation:**
- [ ] Events logged to correct table
- [ ] No duplicate events
- [ ] Personal data hashed/anonymized
- [ ] Session tracking implemented
- [ ] No performance impact on frontend

**Dashboard Widget Validation:**
- [ ] Widgets display correct data
- [ ] Queries optimized (no slow queries)
- [ ] Cache used for expensive queries
- [ ] Permissions checked before display

**Integration Checks:**
- [ ] Analytics doesn't conflict with Google Analytics
- [ ] Events fired from all user actions
- [ ] Admin-only visibility

**Phase 1 Exit Criteria:**
- [ ] User can enroll in tutorial
- [ ] Confirmation email sent
- [ ] Analytics tracking enrollment
- [ ] No database errors
- [ ] Page load < 2s

---

### PHASE 2: Tutorial Builder & Management

#### Week 6: Tutorial Builder UI

**Wizard Validation:**
- [ ] All steps accessible
- [ ] Navigation doesn't lose data
- [ ] Auto-save working every 30s
- [ ] Draft saved on exit
- [ ] Validation prevents advancement without required fields

**Form Validation:**
- [ ] Client-side validation immediate
- [ ] Server-side validation always runs
- [ ] Error messages specific and helpful
- [ ] Required fields marked clearly

**Integration Checks:**
- [ ] Builder doesn't conflict with Gutenberg
- [ ] Media uploader uses WordPress Media Library
- [ ] No JavaScript errors in console

#### Week 7: Tutorial Management

**Admin Interface Validation:**
- [ ] Custom columns display correct data
- [ ] Bulk actions work on multiple items
- [ ] Filters function correctly
- [ ] Quick edit saves properly
- [ ] No admin UI breaks

**Integration Checks:**
- [ ] Standard WordPress list table features work
- [ ] Works with admin themes
- [ ] Mobile admin responsive

#### Week 8: Tutorial Navigation

**Navigation Validation:**
- [ ] Step navigation doesn't skip steps
- [ ] Progress persists on page reload
- [ ] Locked steps prevent access
- [ ] Sidebar collapsible on mobile

**Progress Tracking Validation:**
- [ ] Progress percentage accurate
- [ ] Step completion status correct
- [ ] Resume feature loads correct step
- [ ] No lost progress

**Integration Checks:**
- [ ] Works with caching plugins
- [ ] AJAX requests don't queue up
- [ ] Browser back button works correctly

**Phase 2 Exit Criteria:**
- [ ] Admin can create tutorial
- [ ] Tutorial displays on frontend
- [ ] User can navigate steps
- [ ] Progress saves correctly
- [ ] Mobile responsive

---

### PHASE 3: Video Tracking System

#### Week 9: Video Platform Integrations

**Platform Integration Validation:**
- [ ] Panopto API initialized correctly
- [ ] YouTube API key configured
- [ ] Vimeo SDK loaded
- [ ] HTML5 video fallback works
- [ ] Each platform detects correctly

**Universal Wrapper Validation:**
- [ ] Consistent API across platforms
- [ ] Event normalization working
- [ ] No memory leaks
- [ ] Player destruction cleans up

**Integration Checks:**
- [ ] Only one platform active per step
- [ ] Platform switch doesn't break page
- [ ] API keys secure (not in client code)

#### Week 10: Video Progress Tracking

**Backend Tracking Validation:**
- [ ] Progress updates throttled (every 10s)
- [ ] No duplicate database writes
- [ ] Resume position accurate
- [ ] Completion threshold (90%) respected

**Frontend Tracking Validation:**
- [ ] Progress bar updates smoothly
- [ ] Tracking continues after resume
- [ ] Offline queue works
- [ ] No AJAX errors

**Integration Checks:**
- [ ] Video tracking doesn't affect tutorial navigation
- [ ] Works with all video platforms
- [ ] Progress contributes to overall tutorial progress

**Phase 3 Exit Criteria:**
- [ ] All video platforms working
- [ ] Progress tracking accurate
- [ ] Resume feature functional
- [ ] No platform-specific bugs
- [ ] Performance acceptable

---

### PHASE 4: Quiz & Certificate System

#### Week 11: Quiz Builder

**Quiz Builder Validation:**
- [ ] All 8 question types supported
- [ ] Questions can be reordered
- [ ] Quiz settings save correctly
- [ ] Questions can be duplicated
- [ ] Import/export works

**Question Type Validation:**
- [ ] Each type renders correctly
- [ ] Each type has correct answer format
- [ ] Points calculated correctly
- [ ] Required fields enforced

**Integration Checks:**
- [ ] Quiz links to correct tutorial
- [ ] Media uploads work in questions
- [ ] Rich text editor functional

#### Week 12: Quiz Frontend & Grading

**Quiz Interface Validation:**
- [ ] Timer accurate to the second
- [ ] Navigation doesn't lose answers
- [ ] Submit requires confirmation
- [ ] Can't submit twice

**Grading Engine Validation:**
- [ ] Each question type grades correctly
- [ ] Partial credit calculated correctly
- [ ] Pass/fail threshold accurate
- [ ] Attempt limits enforced

**Integration Checks:**
- [ ] Quiz completion affects tutorial progress
- [ ] Failed quiz allows retry
- [ ] Results stored correctly

#### Week 13: Certificate System

**Certificate Generation Validation:**
- [ ] PDF generates without errors
- [ ] All template variables replaced
- [ ] QR code generates correctly
- [ ] File size reasonable (<1MB)

**Certificate Management Validation:**
- [ ] Certificates stored securely
- [ ] Download link works
- [ ] Verification page functional
- [ ] Revocation works

**Integration Checks:**
- [ ] Certificate only generated after passing quiz
- [ ] Certificate triggers completion email
- [ ] Verification doesn't require login

**Phase 4 Exit Criteria:**
- [ ] Quiz can be created
- [ ] Quiz can be taken
- [ ] Grading accurate
- [ ] Certificate generates
- [ ] Verification works

---

### PHASE 5: REST API & Analytics

#### Week 14: REST API

**API Infrastructure Validation:**
- [ ] All endpoints registered
- [ ] Authentication working
- [ ] Rate limiting functional
- [ ] CORS configured correctly

**Endpoint Validation:**
- [ ] All endpoints return consistent format
- [ ] Error responses standardized
- [ ] Permissions checked on all endpoints
- [ ] Input validation on all endpoints

**Integration Checks:**
- [ ] API doesn't break frontend
- [ ] JWT tokens expire correctly
- [ ] API documentation accurate

#### Week 15: Analytics Dashboard

**Dashboard Validation:**
- [ ] All widgets load data
- [ ] Charts render correctly
- [ ] Filters work
- [ ] Export functional

**Performance Validation:**
- [ ] Dashboard loads < 3s
- [ ] Queries optimized
- [ ] Caching implemented
- [ ] No N+1 queries

**Integration Checks:**
- [ ] Dashboard doesn't conflict with other admin pages
- [ ] Works with multisite
- [ ] Data accurate

**Phase 5 Exit Criteria:**
- [ ] API fully functional
- [ ] API documentation complete
- [ ] Analytics dashboard working
- [ ] Performance targets met

---

### PHASE 6: Testing & Optimization

#### Week 16: Testing Sprint

**Unit Test Validation:**
- [ ] Coverage > 80%
- [ ] All critical paths tested
- [ ] Tests run in isolation
- [ ] Tests are repeatable

**Integration Test Validation:**
- [ ] All user flows tested
- [ ] All AJAX actions tested
- [ ] All API endpoints tested

**Manual Testing Validation:**
- [ ] All browsers tested
- [ ] All devices tested
- [ ] Accessibility audit passed
- [ ] No console errors

#### Week 17: Performance Optimization

**Frontend Performance:**
- [ ] Assets minified
- [ ] Images optimized
- [ ] Lazy loading implemented
- [ ] Page load < 2s

**Backend Performance:**
- [ ] Queries optimized
- [ ] Caching implemented
- [ ] No slow queries (> 1s)
- [ ] Memory usage acceptable

**Phase 6 Exit Criteria:**
- [ ] All tests passing
- [ ] No critical bugs
- [ ] Performance targets met
- [ ] Accessibility compliant
- [ ] Security scan clean

---

### PHASE 7: Deployment & Launch

#### Week 18: Pre-Deployment

**Documentation Validation:**
- [ ] User docs complete
- [ ] Developer docs complete
- [ ] API docs complete
- [ ] All sections accurate

**Staging Validation:**
- [ ] Staging matches production
- [ ] All features work on staging
- [ ] UAT completed
- [ ] Sign-offs received

#### Week 19: Production Deployment

**Deployment Validation:**
- [ ] Backup verified
- [ ] Deployment checklist complete
- [ ] Rollback plan ready
- [ ] Team briefed

**Post-Deployment Validation:**
- [ ] All critical paths tested
- [ ] No errors in logs
- [ ] Performance acceptable
- [ ] Monitoring active

#### Week 20: Launch & Stabilization

**Launch Validation:**
- [ ] Announcement sent
- [ ] Support team ready
- [ ] Monitoring dashboards active
- [ ] Feedback system working

**Phase 7 Exit Criteria:**
- [ ] Production deployed successfully
- [ ] No critical issues
- [ ] Users can access system
- [ ] Support tickets manageable
- [ ] Project closed

---

## 4. INTEGRATION CHECKPOINTS

### 4.1 Cross-Feature Integration Validation

**Enrollment → Progress → Quiz → Certificate Flow:**

```
User Enrolls (Phase 1)
    ↓
    ├─ Creates enrollment record ✓
    ├─ Triggers welcome email ✓
    ├─ Logs analytics event ✓
    └─ Initializes progress (0%) ✓
    
User Watches Videos (Phase 3)
    ↓
    ├─ Updates video progress table ✓
    ├─ Updates tutorial progress ✓
    ├─ Logs analytics events ✓
    └─ Checks completion threshold ✓
    
Videos Complete → Quiz Available (Phase 4)
    ↓
    ├─ Quiz step unlocked ✓
    ├─ User can start quiz ✓
    ├─ Quiz progress tracked ✓
    └─ Results stored ✓
    
Quiz Passed → Certificate (Phase 4)
    ↓
    ├─ Certificate generated ✓
    ├─ Completion email sent ✓
    ├─ Tutorial marked complete ✓
    ├─ Analytics updated ✓
    └─ User can download certificate ✓
```

**Integration Validation Checklist:**

- [ ] Enrollment enables all subsequent features
- [ ] Progress tracking feeds into analytics
- [ ] Video completion unlocks quiz
- [ ] Quiz pass generates certificate
- [ ] Certificate marks tutorial complete
- [ ] All steps trigger appropriate emails
- [ ] Analytics captures all events
- [ ] API exposes all data consistently

### 4.2 Database Consistency Validation

**Cross-Table Consistency:**

```sql
-- ✓ CHECK: Every enrollment has corresponding progress
SELECT COUNT(*) FROM wp_aiddata_lms_tutorial_enrollments e
LEFT JOIN wp_aiddata_lms_tutorial_progress p 
    ON e.user_id = p.user_id AND e.tutorial_id = p.tutorial_id
WHERE p.id IS NULL;
-- Should return 0

-- ✓ CHECK: Completed tutorials have certificates
SELECT COUNT(*) FROM wp_aiddata_lms_tutorial_progress p
LEFT JOIN wp_aiddata_lms_certificates c
    ON p.user_id = c.user_id AND p.tutorial_id = c.tutorial_id
WHERE p.status = 'completed' AND p.quiz_passed = 1 AND c.id IS NULL;
-- Should return 0

-- ✓ CHECK: All video progress has parent enrollment
SELECT COUNT(*) FROM wp_aiddata_lms_video_progress v
LEFT JOIN wp_aiddata_lms_tutorial_enrollments e
    ON v.user_id = e.user_id AND v.tutorial_id = e.tutorial_id
WHERE e.id IS NULL;
-- Should return 0
```

**Validation Rules:**

- [ ] No orphaned records (all foreign keys valid)
- [ ] Enrollment exists before progress
- [ ] Progress exists before video progress
- [ ] Certificate only exists if quiz passed
- [ ] All timestamps in logical order (enroll < complete)

### 4.3 API Consistency Validation

**Endpoint Response Format Consistency:**

All endpoints MUST return:
```json
{
    "success": true|false,
    "data": {
        // Actual response data
    },
    "message": "Human readable message",
    "code": "machine_readable_code"
}
```

**Validation:**

- [ ] All success responses have `success: true`
- [ ] All error responses have `success: false`
- [ ] All responses include `message`
- [ ] All error responses include `code`
- [ ] HTTP status codes match success/error
- [ ] Date format consistent (ISO 8601)
- [ ] ID fields always integers
- [ ] Boolean fields always true/false (not 0/1)

---

## 5. COMMON PITFALLS & PREVENTION

### 5.1 Race Conditions

**Problem:** Multiple AJAX requests updating same data

```javascript
// ❌ WRONG - Can create race condition
function updateProgress() {
    $.post(ajaxurl, data, function(response) {
        // What if another request completes first?
        updateUI(response);
    });
}

// ✅ CORRECT - Use locks or sequential processing
class ProgressUpdater {
    constructor() {
        this.updating = false;
        this.pendingUpdate = null;
    }
    
    async updateProgress(data) {
        if (this.updating) {
            this.pendingUpdate = data;
            return;
        }
        
        this.updating = true;
        try {
            await this.sendUpdate(data);
            if (this.pendingUpdate) {
                const pending = this.pendingUpdate;
                this.pendingUpdate = null;
                await this.updateProgress(pending);
            }
        } finally {
            this.updating = false;
        }
    }
}
```

**Prevention Checklist:**

- [ ] Sequential AJAX requests for state updates
- [ ] Disable buttons during submission
- [ ] Use optimistic locking in database
- [ ] Check modified timestamps before updates

### 5.2 Memory Leaks

**Problem:** Event listeners not removed

```javascript
// ❌ WRONG - Memory leak
class VideoPlayer {
    init() {
        $(window).on('resize', this.handleResize);
        setInterval(this.update, 1000);
    }
}

// ✅ CORRECT - Proper cleanup
class VideoPlayer {
    init() {
        this.resizeHandler = this.handleResize.bind(this);
        $(window).on('resize', this.resizeHandler);
        this.updateTimer = setInterval(() => this.update(), 1000);
    }
    
    destroy() {
        $(window).off('resize', this.resizeHandler);
        if (this.updateTimer) {
            clearInterval(this.updateTimer);
        }
    }
}
```

**Prevention Checklist:**

- [ ] All event listeners have corresponding removal
- [ ] All intervals/timeouts are cleared
- [ ] destroy() method implemented for all classes
- [ ] No circular references in closures

### 5.3 N+1 Query Problem

**Problem:** Queries inside loops

```php
// ❌ WRONG - N+1 queries
$enrollments = $wpdb->get_results( "SELECT * FROM enrollments" );
foreach ( $enrollments as $enrollment ) {
    $user = get_user_by( 'id', $enrollment->user_id );  // Query in loop!
    $tutorial = get_post( $enrollment->tutorial_id );  // Query in loop!
}

// ✅ CORRECT - Single query with JOINs
$results = $wpdb->get_results( "
    SELECT 
        e.*,
        u.display_name,
        p.post_title
    FROM {$wpdb->prefix}aiddata_lms_tutorial_enrollments e
    INNER JOIN {$wpdb->users} u ON e.user_id = u.ID
    INNER JOIN {$wpdb->posts} p ON e.tutorial_id = p.ID
" );
```

**Prevention Checklist:**

- [ ] No queries inside loops
- [ ] Use JOINs instead of multiple queries
- [ ] Batch get operations (get_posts with IDs array)
- [ ] Profile queries with Query Monitor plugin

### 5.4 Caching Issues

**Problem:** Stale cached data

```php
// ❌ WRONG - Cache never invalidates
function get_tutorial_progress( $user_id, $tutorial_id ) {
    $cache_key = "progress_{$user_id}_{$tutorial_id}";
    $progress = wp_cache_get( $cache_key );
    if ( false === $progress ) {
        $progress = calculate_progress( $user_id, $tutorial_id );
        wp_cache_set( $cache_key, $progress );  // Cache forever!
    }
    return $progress;
}

// ✅ CORRECT - Cache with expiration and invalidation
function get_tutorial_progress( $user_id, $tutorial_id ) {
    $cache_key = "progress_{$user_id}_{$tutorial_id}";
    $progress = wp_cache_get( $cache_key, 'aiddata_lms_progress' );
    if ( false === $progress ) {
        $progress = calculate_progress( $user_id, $tutorial_id );
        wp_cache_set( $cache_key, $progress, 'aiddata_lms_progress', HOUR_IN_SECONDS );
    }
    return $progress;
}

// Invalidate when progress updates
function update_progress( $user_id, $tutorial_id, $data ) {
    // Update database
    $result = save_progress( $user_id, $tutorial_id, $data );
    
    // Invalidate cache
    $cache_key = "progress_{$user_id}_{$tutorial_id}";
    wp_cache_delete( $cache_key, 'aiddata_lms_progress' );
    
    return $result;
}
```

**Prevention Checklist:**

- [ ] All caches have expiration time
- [ ] Cache keys include all relevant variables
- [ ] Cache invalidated on data update
- [ ] Use cache groups for batch invalidation

### 5.5 XSS Vulnerabilities

**Problem:** Unescaped output

```php
// ❌ WRONG - XSS vulnerability
echo '<h1>' . $tutorial_title . '</h1>';
echo '<div>' . $_POST['comment'] . '</div>';

// ✅ CORRECT - Always escape output
echo '<h1>' . esc_html( $tutorial_title ) . '</h1>';
echo '<div>' . wp_kses_post( get_comment_text() ) . '</div>';
```

**Prevention Checklist:**

- [ ] ALL output escaped with appropriate function
- [ ] Use `esc_html()` for plain text
- [ ] Use `esc_attr()` for attributes
- [ ] Use `esc_url()` for URLs
- [ ] Use `wp_kses_post()` for HTML content
- [ ] Never trust user input, even from database

---

## 6. PRE-COMMIT VALIDATION

### 6.1 Automated Checks (Required Before Commit)

**Setup Git Hooks:**

```bash
# .git/hooks/pre-commit
#!/bin/bash

echo "Running pre-commit checks..."

# 1. PHP Lint
echo "Checking PHP syntax..."
find . -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
if [ $? -eq 0 ]; then
    echo "❌ PHP syntax errors found!"
    exit 1
fi

# 2. PHP CodeSniffer
echo "Running PHP CodeSniffer..."
phpcs --standard=WordPress includes/
if [ $? -ne 0 ]; then
    echo "❌ Coding standards violations found!"
    exit 1
fi

# 3. ESLint
echo "Running ESLint..."
eslint assets/js/
if [ $? -ne 0 ]; then
    echo "❌ JavaScript linting errors found!"
    exit 1
fi

# 4. Check for debug code
echo "Checking for debug statements..."
if git diff --cached | grep -E "(console\.(log|debug)|var_dump|print_r|error_log\()"; then
    echo "❌ Debug statements found! Remove before committing."
    exit 1
fi

# 5. Check for TODO/FIXME
echo "Checking for TODO/FIXME..."
if git diff --cached | grep -E "(TODO|FIXME)" | grep -v "^-"; then
    echo "⚠️  Warning: TODO/FIXME found. Document in issue tracker."
fi

echo "✅ All pre-commit checks passed!"
exit 0
```

**Pre-Commit Checklist:**

- [ ] PHP syntax valid
- [ ] PHP coding standards pass
- [ ] JavaScript linting pass
- [ ] No console.log or debug statements
- [ ] No TODO/FIXME (or documented in issues)
- [ ] All tests pass
- [ ] No merge conflicts

### 6.2 Manual Pre-Commit Checks

**Before Every Commit:**

- [ ] Code formatted correctly
- [ ] Docblocks complete
- [ ] No commented-out code
- [ ] No unused variables/functions
- [ ] Commit message follows convention
- [ ] Changes tested locally
- [ ] No secrets/API keys in code

---

## 7. CODE REVIEW CHECKLIST

### 7.1 Functionality Review

**Reviewer Must Verify:**

- [ ] Code does what PR claims
- [ ] All acceptance criteria met
- [ ] Edge cases handled
- [ ] Error handling appropriate
- [ ] User experience smooth

### 7.2 Security Review

**Critical Security Checks:**

- [ ] All database queries use `prepare()`
- [ ] All AJAX actions verify nonce
- [ ] All admin actions check capabilities
- [ ] All input sanitized
- [ ] All output escaped
- [ ] No SQL injection vectors
- [ ] No XSS vectors
- [ ] No CSRF vectors
- [ ] File uploads validated
- [ ] No sensitive data in logs

### 7.3 Performance Review

**Performance Checks:**

- [ ] No N+1 queries
- [ ] Queries optimized (use EXPLAIN)
- [ ] Caching used appropriately
- [ ] No queries in loops
- [ ] Assets minified
- [ ] Images optimized
- [ ] Lazy loading used
- [ ] No memory leaks

### 7.4 Code Quality Review

**Code Quality Checks:**

- [ ] Follows coding standards
- [ ] DRY (Don't Repeat Yourself)
- [ ] SOLID principles followed
- [ ] Clear variable/function names
- [ ] No overly complex functions (< 50 lines)
- [ ] No overly nested code (< 4 levels)
- [ ] Comments explain "why", not "what"
- [ ] No magic numbers (use constants)

### 7.5 Testing Review

**Testing Checks:**

- [ ] Unit tests exist
- [ ] Tests cover critical paths
- [ ] Tests pass locally
- [ ] Integration tests exist (if needed)
- [ ] Manual testing performed
- [ ] Edge cases tested

### 7.6 Documentation Review

**Documentation Checks:**

- [ ] Docblocks complete and accurate
- [ ] README updated (if needed)
- [ ] API docs updated (if API changed)
- [ ] Changelog updated
- [ ] Complex logic commented

---

## 8. CONTINUOUS VALIDATION SCHEDULE

### 8.1 Daily Validation

**Every Morning Standup:**
- [ ] Review yesterday's commits
- [ ] Check CI/CD pipeline status
- [ ] Review open issues
- [ ] Plan today's integration points

### 8.2 Weekly Validation

**Every Sprint Review:**
- [ ] Run full test suite
- [ ] Check code coverage
- [ ] Review pending PRs
- [ ] Update documentation
- [ ] Check for technical debt

### 8.3 Phase-End Validation

**At End of Each Phase:**
- [ ] Complete phase checklist (Section 3)
- [ ] Run integration tests
- [ ] Performance benchmark
- [ ] Security scan
- [ ] Accessibility audit
- [ ] Code quality report
- [ ] Update progress tracking

### 8.4 Pre-Production Validation

**Before Production Deployment:**
- [ ] ALL items in this document verified
- [ ] All tests passing
- [ ] Performance targets met
- [ ] Security audit clean
- [ ] Accessibility compliant
- [ ] Documentation complete
- [ ] UAT sign-off received
- [ ] Rollback plan ready

---

## APPENDIX A: Validation Tools

### Required Tools

| Tool | Purpose | Installation |
|------|---------|--------------|
| PHP_CodeSniffer | PHP coding standards | `composer require --dev squizlabs/php_codesniffer` |
| PHPStan | Static analysis | `composer require --dev phpstan/phpstan` |
| PHPUnit | Unit testing | `composer require --dev phpunit/phpunit` |
| ESLint | JavaScript linting | `npm install --save-dev eslint` |
| Stylelint | CSS linting | `npm install --save-dev stylelint` |
| Jest | JavaScript testing | `npm install --save-dev jest` |
| Query Monitor | WordPress query analysis | WordPress plugin |
| Debug Bar | WordPress debugging | WordPress plugin |

### Configuration Files

**.phpcs.xml**
```xml
<?xml version="1.0"?>
<ruleset name="AidData LMS">
    <description>Coding standards for AidData LMS</description>
    <rule ref="WordPress"/>
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/node_modules/*</exclude-pattern>
</ruleset>
```

**.eslintrc.json**
```json
{
    "extends": ["eslint:recommended"],
    "env": {
        "browser": true,
        "es6": true,
        "jquery": true
    },
    "rules": {
        "no-console": "error",
        "no-var": "error",
        "prefer-const": "error"
    }
}
```

---

## APPENDIX B: Quick Reference

### Critical Rules Summary

**ALWAYS:**
- ✅ Use `$wpdb->prepare()` for queries
- ✅ Verify nonce on AJAX actions
- ✅ Check capabilities on admin actions
- ✅ Sanitize ALL input
- ✅ Escape ALL output
- ✅ Use type hints
- ✅ Write docblocks
- ✅ Handle errors with WP_Error
- ✅ Write tests for critical paths

**NEVER:**
- ❌ Direct SQL queries without prepare()
- ❌ Output without escaping
- ❌ Use $_POST/$_GET without sanitization
- ❌ Ignore nonce verification
- ❌ Skip capability checks
- ❌ Use var in JavaScript
- ❌ Use console.log in production
- ❌ Query inside loops
- ❌ Use !important in CSS
- ❌ Commit debug code

---

**This validation guide ensures quality, consistency, and security across the entire AidData LMS Tutorial Builder implementation. Follow it religiously!**

