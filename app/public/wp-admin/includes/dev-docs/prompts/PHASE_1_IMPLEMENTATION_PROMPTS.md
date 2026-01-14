# PHASE 1 IMPLEMENTATION PROMPTS
## AidData LMS Tutorial Builder - Core Infrastructure (Weeks 3-5)

**Version:** 1.0  
**Date:** October 22, 2025  
**Purpose:** Detailed prompts for AI agent implementation of Phase 1  
**Context Documents:** Reference these for persistent context throughout implementation

---

## 📚 REQUIRED CONTEXT DOCUMENTS

**CRITICAL: Load these documents into context before starting each prompt:**

### Primary References
1. **TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md**
   - Section 3: Feature Specifications (enrollment, email, analytics)
   - Section 2.2: Database Schema (lines 258-654)
   - Section 2.4: Class Architecture (lines 732-856)

2. **IMPLEMENTATION_PATHWAY.md**
   - Phase 1: Core Infrastructure (lines 283-536)
   - Development Standards (lines 2046-2180)

3. **CODE_STANDARDS_AND_VALIDATION_GUIDE.md**
   - Section 1: Code Standards & Rules (lines 21-402)
   - Section 4: Phase 1 Validation

4. **INTEGRATION_VALIDATION_MATRIX.md**
   - Section 4: Frontend-Backend Integration (lines 225-344)
   - Section 5: Data Flow Validation (lines 345-445)

### Phase 0 Completion Documents
5. **PROMPT_9_VALIDATION_REPORT.md** - Phase 0 final validation
6. **class-aiddata-lms-install.php** - Database schema reference
7. **class-aiddata-lms-database.php** - Database helper methods
8. **class-aiddata-lms.php** - Core plugin class structure

---

## 🎯 PHASE 1 OVERVIEW

**Goal:** Build enrollment system, email system, and basic analytics foundation

**Duration:** 3 weeks (15 working days)

**Sprint Goal:** Create functional enrollment system with email notifications and basic analytics tracking

**Prerequisites from Phase 0:**
- ✅ Database schema complete (6 tables)
- ✅ Autoloader functional
- ✅ Core plugin class structure
- ✅ Post types and taxonomies registered
- ✅ Plugin activation verified

**Deliverables:**
- ✅ Complete enrollment system (backend + frontend)
- ✅ Email queue and notification system
- ✅ Basic analytics tracking infrastructure
- ✅ Dashboard widgets for statistics

---

## WEEK 3: ENROLLMENT SYSTEM

---

### PROMPT 1: ENROLLMENT MANAGER BACKEND

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 3 → Days 1-2 (lines 287-326)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.2 Enrollment System
- class-aiddata-lms-install.php → Enrollments table schema (lines 93-127)
- class-aiddata-lms-database.php → Table helper methods

**Objective:**
Implement the complete enrollment manager backend with user enrollment, unenrollment, status tracking, and prerequisite validation.

**Instructions:**

1. **Create Enrollment Manager Class** (`includes/tutorials/class-aiddata-lms-tutorial-enrollment.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Tutorial_Enrollment`
   - Public property: `$table_name` (using database helper)
   - Constructor: Initialize table name
   
   Core Methods:
   ```php
   public function enroll_user( 
       int $user_id, 
       int $tutorial_id, 
       string $source = 'web' 
   ): bool|WP_Error
   
   public function unenroll_user( 
       int $user_id, 
       int $tutorial_id 
   ): bool|WP_Error
   
   public function get_user_enrollments( 
       int $user_id, 
       string $status = 'active' 
   ): array
   
   public function get_tutorial_enrollments( 
       int $tutorial_id, 
       string $status = 'active' 
   ): array
   
   public function is_user_enrolled( 
       int $user_id, 
       int $tutorial_id 
   ): bool
   
   public function get_enrollment( 
       int $user_id, 
       int $tutorial_id 
   ): ?object
   
   public function update_enrollment_status( 
       int $enrollment_id, 
       string $status 
   ): bool|WP_Error
   
   public function mark_completed( 
       int $user_id, 
       int $tutorial_id, 
       ?DateTime $completed_at = null 
   ): bool|WP_Error
   
   public function get_enrollment_count( 
       int $tutorial_id, 
       ?string $status = null 
   ): int
   
   private function validate_enrollment( 
       int $user_id, 
       int $tutorial_id 
   ): array
   ```
   
   Reference:
   - IMPLEMENTATION_PATHWAY.md lines 296-307 for method signatures
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 28-73 for PHP standards
   
   File location: `/includes/tutorials/class-aiddata-lms-tutorial-enrollment.php`

2. **Implement Enrollment Validation**
   
   `validate_enrollment()` should check:
   - User exists and is logged in
   - Tutorial exists and is published
   - User not already enrolled (check for duplicate)
   - Enrollment limit not exceeded (if configured)
   - Deadline not passed (if configured)
   - Prerequisites met (if configured)
   - Access permissions (if restricted by role/capability)
   
   Return array format:
   ```php
   [
       'valid' => bool,
       'errors' => array,  // Array of error messages
       'warnings' => array // Array of warning messages
   ]
   ```

3. **Implement Enrollment Events System**
   
   Fire WordPress hooks at key points:
   ```php
   // Before enrollment validation
   do_action( 'aiddata_lms_before_enrollment', $user_id, $tutorial_id, $source );
   
   // After successful enrollment
   do_action( 'aiddata_lms_user_enrolled', $enrollment_id, $user_id, $tutorial_id, $source );
   
   // After unenrollment
   do_action( 'aiddata_lms_user_unenrolled', $user_id, $tutorial_id );
   
   // After marking complete
   do_action( 'aiddata_lms_enrollment_completed', $enrollment_id, $user_id, $tutorial_id );
   
   // On enrollment status change
   do_action( 'aiddata_lms_enrollment_status_changed', $enrollment_id, $old_status, $new_status );
   ```
   
   These hooks will be used for:
   - Email notifications (Week 4)
   - Analytics tracking (Week 5)
   - Future integrations

4. **Create Enrollment Meta Manager** (optional enhancement)
   
   If enrollment needs additional metadata:
   ```php
   public function add_enrollment_meta( 
       int $enrollment_id, 
       string $meta_key, 
       mixed $meta_value 
   ): bool
   
   public function get_enrollment_meta( 
       int $enrollment_id, 
       string $meta_key 
   ): mixed
   
   public function update_enrollment_meta( 
       int $enrollment_id, 
       string $meta_key, 
       mixed $meta_value 
   ): bool
   ```
   
   Store in WordPress `user_meta` table with key format: `aiddata_lms_enrollment_{enrollment_id}_{meta_key}`

5. **Error Handling & Return Values**
   
   Success Cases:
   - Return `true` for simple operations
   - Return enrollment ID for create operations
   - Return enrollment object or array for get operations
   
   Error Cases:
   - Return `WP_Error` with error code and message
   - Error codes:
     - `already_enrolled` - User already enrolled
     - `invalid_user` - User doesn't exist
     - `invalid_tutorial` - Tutorial doesn't exist
     - `prerequisites_not_met` - Prerequisites missing
     - `enrollment_limit_reached` - Enrollment limit exceeded
     - `enrollment_closed` - Enrollment deadline passed
     - `permission_denied` - User lacks required permissions
     - `database_error` - Database operation failed

6. **Database Operations**
   
   Use WordPress `$wpdb` for all database operations:
   - Use `$wpdb->prepare()` for all queries
   - Use transactions for related operations
   - Handle errors gracefully
   - Log database errors
   
   Example:
   ```php
   global $wpdb;
   $table_name = AidData_LMS_Database::get_table_name( 'enrollments' );
   
   $result = $wpdb->insert(
       $table_name,
       [
           'user_id' => $user_id,
           'tutorial_id' => $tutorial_id,
           'enrolled_at' => current_time( 'mysql' ),
           'status' => 'active',
           'source' => $source,
       ],
       [ '%d', '%d', '%s', '%s', '%s' ]
   );
   ```

**Validation Checklist:**
- [ ] All methods have complete docblocks
- [ ] Type hints on all parameters
- [ ] Return types declared
- [ ] Input validation on all methods
- [ ] SQL injection prevention (prepared statements)
- [ ] Error handling implemented
- [ ] WordPress hooks fired appropriately
- [ ] ABSPATH security check
- [ ] WP_Error used for errors
- [ ] Follows WordPress coding standards

**Expected Outcome:**
- Enrollment manager class created
- All enrollment operations functional
- Validation system working
- Hooks firing correctly
- Ready for frontend integration

---

### PROMPT 2: ENROLLMENT PROGRESS MANAGER

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 (continuation)
- class-aiddata-lms-install.php → Progress table schema (lines 129-168)
- class-aiddata-lms-tutorial-enrollment.php → From Prompt 1

**Objective:**
Implement tutorial progress tracking system with step completion, percentage calculation, and status management.

**Instructions:**

1. **Create Progress Manager Class** (`includes/tutorials/class-aiddata-lms-tutorial-progress.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Tutorial_Progress`
   - Integration with enrollment system
   
   Core Methods:
   ```php
   public function update_progress( 
       int $user_id, 
       int $tutorial_id, 
       int $step_index, 
       ?int $enrollment_id = null 
   ): bool|WP_Error
   
   public function get_progress( 
       int $user_id, 
       int $tutorial_id 
   ): ?object
   
   public function get_last_step( 
       int $user_id, 
       int $tutorial_id 
   ): int
   
   public function calculate_progress_percent( 
       int $user_id, 
       int $tutorial_id 
   ): float
   
   public function mark_tutorial_complete( 
       int $user_id, 
       int $tutorial_id 
   ): bool|WP_Error
   
   public function get_completed_steps( 
       int $user_id, 
       int $tutorial_id 
   ): array
   
   public function is_step_completed( 
       int $user_id, 
       int $tutorial_id, 
       int $step_index 
   ): bool
   
   public function update_time_spent( 
       int $user_id, 
       int $tutorial_id, 
       int $seconds 
   ): bool
   
   private function get_tutorial_step_count( 
       int $tutorial_id 
   ): int
   ```
   
   File location: `/includes/tutorials/class-aiddata-lms-tutorial-progress.php`

2. **Step Completion Logic**
   
   Implementation details:
   - Store completed steps as comma-separated string: "0,1,2,5"
   - Update `current_step` to most recent step
   - Calculate `progress_percent` = (completed_steps / total_steps) * 100
   - Update `last_accessed` timestamp
   - Update `status` based on progress:
     - `not_started` = 0%
     - `in_progress` = 1-99%
     - `completed` = 100%
   
   Example:
   ```php
   // Get current completed steps
   $completed = $this->get_completed_steps( $user_id, $tutorial_id );
   
   // Add new step if not already completed
   if ( ! in_array( $step_index, $completed, true ) ) {
       $completed[] = $step_index;
       sort( $completed );
   }
   
   // Save updated completion
   $completed_string = implode( ',', $completed );
   
   // Calculate percentage
   $total_steps = $this->get_tutorial_step_count( $tutorial_id );
   $progress_percent = ( count( $completed ) / $total_steps ) * 100;
   ```

3. **Tutorial Structure Integration**
   
   Get tutorial steps from post meta:
   ```php
   private function get_tutorial_step_count( int $tutorial_id ): int {
       $steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );
       
       if ( empty( $steps ) || ! is_array( $steps ) ) {
           return 0;
       }
       
       return count( $steps );
   }
   ```
   
   Note: Tutorial step structure will be defined in Phase 2, but prepare for array of step objects.

4. **Progress Events**
   
   Fire hooks for tracking:
   ```php
   do_action( 'aiddata_lms_step_completed', $user_id, $tutorial_id, $step_index );
   do_action( 'aiddata_lms_progress_updated', $user_id, $tutorial_id, $progress_percent );
   do_action( 'aiddata_lms_tutorial_completed', $user_id, $tutorial_id, $enrollment_id );
   ```

5. **Create Progress Initialization**
   
   When user enrolls, create initial progress record:
   ```php
   public function initialize_progress( 
       int $user_id, 
       int $tutorial_id, 
       int $enrollment_id 
   ): bool|WP_Error {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'progress' );
       
       $result = $wpdb->insert(
           $table_name,
           [
               'user_id' => $user_id,
               'tutorial_id' => $tutorial_id,
               'enrollment_id' => $enrollment_id,
               'current_step' => 0,
               'completed_steps' => '',
               'progress_percent' => 0.00,
               'status' => 'not_started',
               'quiz_passed' => 0,
               'quiz_attempts' => 0,
               'time_spent' => 0,
           ],
           [ '%d', '%d', '%d', '%d', '%s', '%f', '%s', '%d', '%d', '%d' ]
       );
       
       return $result !== false;
   }
   ```
   
   Hook this to enrollment:
   ```php
   add_action( 'aiddata_lms_user_enrolled', array( $this, 'on_user_enrolled' ), 10, 4 );
   
   public function on_user_enrolled( 
       int $enrollment_id, 
       int $user_id, 
       int $tutorial_id, 
       string $source 
   ): void {
       $this->initialize_progress( $user_id, $tutorial_id, $enrollment_id );
   }
   ```

6. **Time Tracking**
   
   Implement time spent tracking:
   - Frontend will send periodic updates (every 30 seconds)
   - Accumulate time in database
   - Format for display (hours, minutes)
   
   Helper method:
   ```php
   public function format_time_spent( int $seconds ): string {
       $hours = floor( $seconds / 3600 );
       $minutes = floor( ( $seconds % 3600 ) / 60 );
       
       if ( $hours > 0 ) {
           return sprintf( '%d hour(s), %d minute(s)', $hours, $minutes );
       }
       
       return sprintf( '%d minute(s)', $minutes );
   }
   ```

**Validation Checklist:**
- [ ] All methods have complete docblocks
- [ ] Type hints and return types
- [ ] Progress calculation accurate
- [ ] Step completion tracking works
- [ ] Integration with enrollment system
- [ ] Hooks fired appropriately
- [ ] Time tracking functional
- [ ] Status updates correctly
- [ ] Database operations safe

**Expected Outcome:**
- Progress manager class functional
- Step completion tracking works
- Progress percentage calculated correctly
- Ready for frontend integration
- Hooks prepared for analytics

---

### PROMPT 3: ENROLLMENT AJAX HANDLERS

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 3 → Days 3-5 (lines 327-371)
- class-aiddata-lms-tutorial-enrollment.php → From Prompt 1
- class-aiddata-lms-tutorial-progress.php → From Prompt 2

**Objective:**
Implement AJAX handlers for enrollment, unenrollment, and status checking with proper security and error handling.

**Instructions:**

1. **Create AJAX Handler Class** (`includes/tutorials/class-aiddata-lms-tutorial-ajax.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Tutorial_AJAX`
   - Constructor registers all AJAX actions
   - Separate methods for each AJAX endpoint
   
   Constructor:
   ```php
   public function __construct() {
       // Logged-in users
       add_action( 'wp_ajax_aiddata_lms_enroll_tutorial', array( $this, 'enroll_tutorial' ) );
       add_action( 'wp_ajax_aiddata_lms_unenroll_tutorial', array( $this, 'unenroll_tutorial' ) );
       add_action( 'wp_ajax_aiddata_lms_check_enrollment_status', array( $this, 'check_enrollment_status' ) );
       add_action( 'wp_ajax_aiddata_lms_update_step_progress', array( $this, 'update_step_progress' ) );
       add_action( 'wp_ajax_aiddata_lms_update_time_spent', array( $this, 'update_time_spent' ) );
       
       // Guest users (if allowing guest preview)
       add_action( 'wp_ajax_nopriv_aiddata_lms_check_enrollment_status', array( $this, 'check_enrollment_status' ) );
   }
   ```
   
   File location: `/includes/tutorials/class-aiddata-lms-tutorial-ajax.php`

2. **Implement Enrollment AJAX Handler**
   
   ```php
   public function enroll_tutorial(): void {
       // Verify nonce
       check_ajax_referer( 'aiddata-lms-enrollment', 'nonce' );
       
       // Check user login
       if ( ! is_user_logged_in() ) {
           wp_send_json_error( 
               array( 'message' => __( 'You must be logged in to enroll.', 'aiddata-lms' ) ),
               401
           );
       }
       
       // Get and validate parameters
       $tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
       
       if ( empty( $tutorial_id ) ) {
           wp_send_json_error( 
               array( 'message' => __( 'Invalid tutorial ID.', 'aiddata-lms' ) ),
               400
           );
       }
       
       // Perform enrollment
       $user_id = get_current_user_id();
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       
       $result = $enrollment_manager->enroll_user( $user_id, $tutorial_id, 'web' );
       
       if ( is_wp_error( $result ) ) {
           wp_send_json_error(
               array(
                   'message' => $result->get_error_message(),
                   'code' => $result->get_error_code(),
               ),
               400
           );
       }
       
       // Get enrollment data
       $enrollment = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );
       
       wp_send_json_success(
           array(
               'message' => __( 'Successfully enrolled in tutorial.', 'aiddata-lms' ),
               'enrollment' => array(
                   'id' => $enrollment->id,
                   'enrolled_at' => $enrollment->enrolled_at,
                   'status' => $enrollment->status,
               ),
               'redirect_url' => get_permalink( $tutorial_id ),
           )
       );
   }
   ```

3. **Implement Unenrollment AJAX Handler**
   
   ```php
   public function unenroll_tutorial(): void {
       check_ajax_referer( 'aiddata-lms-enrollment', 'nonce' );
       
       if ( ! is_user_logged_in() ) {
           wp_send_json_error( 
               array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
               401
           );
       }
       
       $tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
       
       if ( empty( $tutorial_id ) ) {
           wp_send_json_error( 
               array( 'message' => __( 'Invalid tutorial ID.', 'aiddata-lms' ) ),
               400
           );
       }
       
       $user_id = get_current_user_id();
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       
       // Confirm unenrollment
       $confirm = isset( $_POST['confirm'] ) && $_POST['confirm'] === 'yes';
       
       if ( ! $confirm ) {
           wp_send_json_error(
               array( 'message' => __( 'Please confirm unenrollment.', 'aiddata-lms' ) ),
               400
           );
       }
       
       $result = $enrollment_manager->unenroll_user( $user_id, $tutorial_id );
       
       if ( is_wp_error( $result ) ) {
           wp_send_json_error(
               array(
                   'message' => $result->get_error_message(),
                   'code' => $result->get_error_code(),
               ),
               400
           );
       }
       
       wp_send_json_success(
           array(
               'message' => __( 'Successfully unenrolled from tutorial.', 'aiddata-lms' ),
           )
       );
   }
   ```

4. **Implement Enrollment Status Check**
   
   ```php
   public function check_enrollment_status(): void {
       $tutorial_id = isset( $_GET['tutorial_id'] ) ? absint( $_GET['tutorial_id'] ) : 0;
       
       if ( empty( $tutorial_id ) ) {
           wp_send_json_error( 
               array( 'message' => __( 'Invalid tutorial ID.', 'aiddata-lms' ) ),
               400
           );
       }
       
       $user_id = get_current_user_id();
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       
       $is_enrolled = $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );
       
       $response = array(
           'enrolled' => $is_enrolled,
           'user_logged_in' => is_user_logged_in(),
       );
       
       if ( $is_enrolled && $user_id > 0 ) {
           $enrollment = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );
           $progress_manager = new AidData_LMS_Tutorial_Progress();
           $progress = $progress_manager->get_progress( $user_id, $tutorial_id );
           
           $response['enrollment'] = array(
               'id' => $enrollment->id,
               'status' => $enrollment->status,
               'enrolled_at' => $enrollment->enrolled_at,
           );
           
           if ( $progress ) {
               $response['progress'] = array(
                   'percent' => $progress->progress_percent,
                   'current_step' => $progress->current_step,
                   'status' => $progress->status,
               );
           }
       }
       
       wp_send_json_success( $response );
   }
   ```

5. **Implement Step Progress Update**
   
   ```php
   public function update_step_progress(): void {
       check_ajax_referer( 'aiddata-lms-progress', 'nonce' );
       
       if ( ! is_user_logged_in() ) {
           wp_send_json_error( 
               array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
               401
           );
       }
       
       $tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
       $step_index = isset( $_POST['step_index'] ) ? absint( $_POST['step_index'] ) : 0;
       
       if ( empty( $tutorial_id ) || $step_index < 0 ) {
           wp_send_json_error( 
               array( 'message' => __( 'Invalid parameters.', 'aiddata-lms' ) ),
               400
           );
       }
       
       $user_id = get_current_user_id();
       
       // Verify enrollment
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       if ( ! $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) ) {
           wp_send_json_error(
               array( 'message' => __( 'You are not enrolled in this tutorial.', 'aiddata-lms' ) ),
               403
           );
       }
       
       // Update progress
       $progress_manager = new AidData_LMS_Tutorial_Progress();
       $result = $progress_manager->update_progress( $user_id, $tutorial_id, $step_index );
       
       if ( is_wp_error( $result ) ) {
           wp_send_json_error(
               array(
                   'message' => $result->get_error_message(),
                   'code' => $result->get_error_code(),
               ),
               400
           );
       }
       
       // Get updated progress
       $progress = $progress_manager->get_progress( $user_id, $tutorial_id );
       
       wp_send_json_success(
           array(
               'message' => __( 'Progress updated successfully.', 'aiddata-lms' ),
               'progress' => array(
                   'percent' => $progress->progress_percent,
                   'current_step' => $progress->current_step,
                   'completed_steps' => $progress_manager->get_completed_steps( $user_id, $tutorial_id ),
                   'status' => $progress->status,
               ),
           )
       );
   }
   ```

6. **Implement Time Tracking Update**
   
   ```php
   public function update_time_spent(): void {
       check_ajax_referer( 'aiddata-lms-progress', 'nonce' );
       
       if ( ! is_user_logged_in() ) {
           wp_send_json_error( 
               array( 'message' => __( 'You must be logged in.', 'aiddata-lms' ) ),
               401
           );
       }
       
       $tutorial_id = isset( $_POST['tutorial_id'] ) ? absint( $_POST['tutorial_id'] ) : 0;
       $seconds = isset( $_POST['seconds'] ) ? absint( $_POST['seconds'] ) : 0;
       
       if ( empty( $tutorial_id ) || $seconds <= 0 ) {
           wp_send_json_error( 
               array( 'message' => __( 'Invalid parameters.', 'aiddata-lms' ) ),
               400
           );
       }
       
       $user_id = get_current_user_id();
       $progress_manager = new AidData_LMS_Tutorial_Progress();
       
       $result = $progress_manager->update_time_spent( $user_id, $tutorial_id, $seconds );
       
       if ( is_wp_error( $result ) ) {
           wp_send_json_error(
               array(
                   'message' => $result->get_error_message(),
               ),
               400
           );
       }
       
       wp_send_json_success(
           array(
               'message' => __( 'Time updated.', 'aiddata-lms' ),
           )
       );
   }
   ```

7. **Register AJAX Class**
   
   In `class-aiddata-lms.php`, add:
   ```php
   private function load_dependencies() {
       // ... existing code ...
       
       // Initialize AJAX handlers
       if ( wp_doing_ajax() ) {
           new AidData_LMS_Tutorial_AJAX();
       }
   }
   ```

**Validation Checklist:**
- [ ] All AJAX actions have nonce verification
- [ ] User authentication checked
- [ ] Input sanitization on all parameters
- [ ] Proper HTTP status codes
- [ ] Consistent JSON response format
- [ ] Error messages user-friendly
- [ ] Success messages clear
- [ ] Database operations safe
- [ ] Works with and without JavaScript

**Expected Outcome:**
- AJAX handlers functional
- Enrollment/unenrollment works
- Status checking accurate
- Progress updates working
- Time tracking operational
- Ready for frontend JavaScript

---

### PROMPT 4: ENROLLMENT FRONTEND JAVASCRIPT

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 3 → Days 3-5 (lines 350-371)
- class-aiddata-lms-tutorial-ajax.php → From Prompt 3
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 1.2 JavaScript Standards

**Objective:**
Implement frontend JavaScript for enrollment interactions with AJAX communication, error handling, and UI updates.

**Instructions:**

1. **Create Enrollment JavaScript** (`assets/js/frontend/enrollment.js`)
   
   Requirements:
   - Modern ES6+ JavaScript
   - jQuery for AJAX (WordPress standard)
   - Event delegation
   - Error handling
   - Loading states
   - Success notifications
   
   File location: `/assets/js/frontend/enrollment.js`

2. **Main Enrollment Handler**
   
   ```javascript
   (function($) {
       'use strict';
       
       /**
        * Enrollment Manager
        */
       const EnrollmentManager = {
           /**
            * Initialize enrollment handlers
            */
           init: function() {
               this.bindEvents();
               this.checkEnrollmentStatus();
           },
           
           /**
            * Bind click events
            */
           bindEvents: function() {
               $(document).on('click', '.aiddata-lms-enroll-btn', this.handleEnroll.bind(this));
               $(document).on('click', '.aiddata-lms-unenroll-btn', this.handleUnenroll.bind(this));
               $(document).on('click', '.aiddata-lms-continue-btn', this.handleContinue.bind(this));
           },
           
           /**
            * Handle enrollment click
            */
           handleEnroll: function(e) {
               e.preventDefault();
               
               const $button = $(e.currentTarget);
               const tutorialId = $button.data('tutorial-id');
               
               if (!tutorialId) {
                   this.showError('Invalid tutorial ID');
                   return;
               }
               
               this.enrollUser(tutorialId, $button);
           },
           
           /**
            * Enroll user in tutorial
            */
           enrollUser: function(tutorialId, $button) {
               // Set loading state
               const originalText = $button.html();
               $button.prop('disabled', true)
                      .html('<span class="spinner-border spinner-border-sm"></span> Enrolling...');
               
               $.ajax({
                   url: aiddataLMS.ajaxUrl,
                   type: 'POST',
                   data: {
                       action: 'aiddata_lms_enroll_tutorial',
                       tutorial_id: tutorialId,
                       nonce: aiddataLMS.enrollmentNonce
                   },
                   success: (response) => {
                       if (response.success) {
                           this.showSuccess(response.data.message);
                           
                           // Update UI
                           this.updateEnrollmentUI(tutorialId, true, response.data);
                           
                           // Redirect if URL provided
                           if (response.data.redirect_url) {
                               setTimeout(() => {
                                   window.location.href = response.data.redirect_url;
                               }, 1000);
                           }
                       } else {
                           this.showError(response.data.message || 'Enrollment failed');
                           $button.prop('disabled', false).html(originalText);
                       }
                   },
                   error: (xhr, status, error) => {
                       console.error('Enrollment error:', error);
                       this.showError('Network error. Please try again.');
                       $button.prop('disabled', false).html(originalText);
                   }
               });
           },
           
           /**
            * Handle unenrollment click
            */
           handleUnenroll: function(e) {
               e.preventDefault();
               
               const $button = $(e.currentTarget);
               const tutorialId = $button.data('tutorial-id');
               
               if (!tutorialId) {
                   this.showError('Invalid tutorial ID');
                   return;
               }
               
               // Confirm unenrollment
               if (confirm(aiddataLMS.confirmUnenroll || 'Are you sure you want to unenroll? Your progress will be saved.')) {
                   this.unenrollUser(tutorialId, $button);
               }
           },
           
           /**
            * Unenroll user from tutorial
            */
           unenrollUser: function(tutorialId, $button) {
               const originalText = $button.html();
               $button.prop('disabled', true)
                      .html('<span class="spinner-border spinner-border-sm"></span> Unenrolling...');
               
               $.ajax({
                   url: aiddataLMS.ajaxUrl,
                   type: 'POST',
                   data: {
                       action: 'aiddata_lms_unenroll_tutorial',
                       tutorial_id: tutorialId,
                       confirm: 'yes',
                       nonce: aiddataLMS.enrollmentNonce
                   },
                   success: (response) => {
                       if (response.success) {
                           this.showSuccess(response.data.message);
                           this.updateEnrollmentUI(tutorialId, false);
                       } else {
                           this.showError(response.data.message || 'Unenrollment failed');
                           $button.prop('disabled', false).html(originalText);
                       }
                   },
                   error: (xhr, status, error) => {
                       console.error('Unenrollment error:', error);
                       this.showError('Network error. Please try again.');
                       $button.prop('disabled', false).html(originalText);
                   }
               });
           },
           
           /**
            * Check current enrollment status
            */
           checkEnrollmentStatus: function() {
               const tutorialId = this.getTutorialId();
               
               if (!tutorialId) {
                   return;
               }
               
               $.ajax({
                   url: aiddataLMS.ajaxUrl,
                   type: 'GET',
                   data: {
                       action: 'aiddata_lms_check_enrollment_status',
                       tutorial_id: tutorialId
                   },
                   success: (response) => {
                       if (response.success) {
                           this.updateEnrollmentUI(tutorialId, response.data.enrolled, response.data);
                       }
                   }
               });
           },
           
           /**
            * Update UI based on enrollment status
            */
           updateEnrollmentUI: function(tutorialId, isEnrolled, data = {}) {
               const $container = $('.aiddata-lms-enrollment-container[data-tutorial-id="' + tutorialId + '"]');
               
               if (!$container.length) {
                   return;
               }
               
               if (isEnrolled) {
                   // User is enrolled
                   $container.find('.aiddata-lms-enroll-btn').hide();
                   $container.find('.aiddata-lms-enrolled-state').show();
                   $container.find('.aiddata-lms-continue-btn').show();
                   
                   // Update progress if available
                   if (data.progress) {
                       this.updateProgressDisplay(tutorialId, data.progress);
                   }
               } else {
                   // User is not enrolled
                   $container.find('.aiddata-lms-enroll-btn').show();
                   $container.find('.aiddata-lms-enrolled-state').hide();
                   $container.find('.aiddata-lms-continue-btn').hide();
               }
           },
           
           /**
            * Update progress display
            */
           updateProgressDisplay: function(tutorialId, progress) {
               const $progress = $('.aiddata-lms-progress[data-tutorial-id="' + tutorialId + '"]');
               
               if (!$progress.length) {
                   return;
               }
               
               // Update progress bar
               const percent = parseFloat(progress.percent) || 0;
               $progress.find('.progress-bar')
                       .css('width', percent + '%')
                       .attr('aria-valuenow', percent)
                       .text(Math.round(percent) + '%');
               
               // Update status text
               const statusText = this.getStatusText(progress.status, percent);
               $progress.find('.status-text').text(statusText);
           },
           
           /**
            * Get status text
            */
           getStatusText: function(status, percent) {
               if (status === 'completed') {
                   return 'Completed';
               } else if (status === 'in_progress') {
                   return 'In Progress - ' + Math.round(percent) + '%';
               } else {
                   return 'Not Started';
               }
           },
           
           /**
            * Get tutorial ID from page
            */
           getTutorialId: function() {
               // Try to get from enrollment container
               const $container = $('.aiddata-lms-enrollment-container');
               if ($container.length) {
                   return $container.data('tutorial-id');
               }
               
               // Try to get from body class
               const bodyClasses = $('body').attr('class');
               const match = bodyClasses && bodyClasses.match(/postid-(\d+)/);
               if (match && match[1]) {
                   return parseInt(match[1]);
               }
               
               return null;
           },
           
           /**
            * Show success message
            */
           showSuccess: function(message) {
               this.showNotification(message, 'success');
           },
           
           /**
            * Show error message
            */
           showError: function(message) {
               this.showNotification(message, 'error');
           },
           
           /**
            * Show notification
            */
           showNotification: function(message, type = 'info') {
               // Create notification element
               const $notification = $('<div>', {
                   'class': 'aiddata-lms-notification aiddata-lms-notification-' + type,
                   'text': message
               });
               
               // Append to body
               $('body').append($notification);
               
               // Fade in
               setTimeout(() => {
                   $notification.addClass('show');
               }, 100);
               
               // Auto-hide after 5 seconds
               setTimeout(() => {
                   $notification.removeClass('show');
                   setTimeout(() => {
                       $notification.remove();
                   }, 300);
               }, 5000);
           },
           
           /**
            * Handle continue button
            */
           handleContinue: function(e) {
               e.preventDefault();
               
               const $button = $(e.currentTarget);
               const continueUrl = $button.attr('href');
               
               if (continueUrl) {
                   window.location.href = continueUrl;
               }
           }
       };
       
       // Initialize on document ready
       $(document).ready(function() {
           EnrollmentManager.init();
       });
       
   })(jQuery);
   ```

3. **Enqueue Script**
   
   Create enqueue function in `includes/class-aiddata-lms.php` or separate asset loader:
   
   ```php
   public function enqueue_frontend_scripts(): void {
       if ( is_singular( 'aiddata_tutorial' ) ) {
           // Enqueue enrollment script
           wp_enqueue_script(
               'aiddata-lms-enrollment',
               AIDDATA_LMS_URL . 'assets/js/frontend/enrollment.js',
               array( 'jquery' ),
               AIDDATA_LMS_VERSION,
               true
           );
           
           // Localize script
           wp_localize_script(
               'aiddata-lms-enrollment',
               'aiddataLMS',
               array(
                   'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                   'enrollmentNonce' => wp_create_nonce( 'aiddata-lms-enrollment' ),
                   'progressNonce' => wp_create_nonce( 'aiddata-lms-progress' ),
                   'confirmUnenroll' => __( 'Are you sure you want to unenroll? Your progress will be saved but you will need to re-enroll to continue.', 'aiddata-lms' ),
                   'tutorialId' => get_the_ID(),
               )
           );
       }
   }
   
   add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
   ```

4. **Create Notification Styles** (`assets/css/frontend/enrollment.css`)
   
   ```css
   /* Enrollment Button Styles */
   .aiddata-lms-enroll-btn {
       display: inline-block;
       padding: 12px 24px;
       background-color: #0073aa;
       color: #fff;
       border: none;
       border-radius: 4px;
       font-size: 16px;
       font-weight: 600;
       cursor: pointer;
       transition: background-color 0.3s ease;
   }
   
   .aiddata-lms-enroll-btn:hover {
       background-color: #005177;
   }
   
   .aiddata-lms-enroll-btn:disabled {
       opacity: 0.6;
       cursor: not-allowed;
   }
   
   /* Enrolled State */
   .aiddata-lms-enrolled-state {
       display: none;
       padding: 16px;
       background-color: #f0f6fc;
       border-left: 4px solid #0073aa;
       margin-bottom: 20px;
   }
   
   .aiddata-lms-enrolled-state.visible {
       display: block;
   }
   
   /* Progress Display */
   .aiddata-lms-progress {
       margin: 20px 0;
   }
   
   .aiddata-lms-progress .progress {
       height: 24px;
       background-color: #e9ecef;
       border-radius: 4px;
       overflow: hidden;
   }
   
   .aiddata-lms-progress .progress-bar {
       height: 100%;
       background-color: #28a745;
       transition: width 0.6s ease;
       display: flex;
       align-items: center;
       justify-content: center;
       color: #fff;
       font-size: 14px;
       font-weight: 600;
   }
   
   .aiddata-lms-progress .status-text {
       margin-top: 8px;
       font-size: 14px;
       color: #6c757d;
   }
   
   /* Notification Styles */
   .aiddata-lms-notification {
       position: fixed;
       top: 20px;
       right: 20px;
       padding: 16px 24px;
       background-color: #fff;
       border-radius: 4px;
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
       z-index: 9999;
       opacity: 0;
       transform: translateX(100%);
       transition: all 0.3s ease;
       max-width: 400px;
   }
   
   .aiddata-lms-notification.show {
       opacity: 1;
       transform: translateX(0);
   }
   
   .aiddata-lms-notification-success {
       border-left: 4px solid #28a745;
   }
   
   .aiddata-lms-notification-error {
       border-left: 4px solid #dc3545;
   }
   
   .aiddata-lms-notification-info {
       border-left: 4px solid #17a2b8;
   }
   
   /* Continue Button */
   .aiddata-lms-continue-btn {
       display: none;
       padding: 10px 20px;
       background-color: #28a745;
       color: #fff;
       border: none;
       border-radius: 4px;
       text-decoration: none;
       font-weight: 600;
       transition: background-color 0.3s ease;
   }
   
   .aiddata-lms-continue-btn.visible {
       display: inline-block;
   }
   
   .aiddata-lms-continue-btn:hover {
       background-color: #218838;
       color: #fff;
   }
   
   /* Spinner */
   .spinner-border {
       display: inline-block;
       width: 16px;
       height: 16px;
       vertical-align: text-bottom;
       border: 2px solid currentColor;
       border-right-color: transparent;
       border-radius: 50%;
       animation: spinner-border 0.75s linear infinite;
   }
   
   @keyframes spinner-border {
       to { transform: rotate(360deg); }
   }
   
   .spinner-border-sm {
       width: 14px;
       height: 14px;
       border-width: 2px;
   }
   
   /* Responsive Design */
   @media (max-width: 768px) {
       .aiddata-lms-notification {
           right: 10px;
           left: 10px;
           max-width: none;
       }
       
       .aiddata-lms-enroll-btn {
           width: 100%;
           text-align: center;
       }
   }
   ```
   
   Enqueue CSS:
   ```php
   wp_enqueue_style(
       'aiddata-lms-enrollment',
       AIDDATA_LMS_URL . 'assets/css/frontend/enrollment.css',
       array(),
       AIDDATA_LMS_VERSION
   );
   ```

5. **Create Enrollment Button Template** (`templates/template-parts/enrollment-button.php`)
   
   ```php
   <?php
   /**
    * Enrollment Button Template
    *
    * @var int $tutorial_id
    * @var bool $is_enrolled
    * @var object|null $progress
    */
   
   if ( ! defined( 'ABSPATH' ) ) {
       exit;
   }
   
   $user_id = get_current_user_id();
   $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
   $progress_manager = new AidData_LMS_Tutorial_Progress();
   
   $is_enrolled = $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );
   $progress = $is_enrolled ? $progress_manager->get_progress( $user_id, $tutorial_id ) : null;
   ?>
   
   <div class="aiddata-lms-enrollment-container" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
       
       <?php if ( ! is_user_logged_in() ) : ?>
           <!-- Not logged in -->
           <div class="aiddata-lms-login-prompt">
               <p><?php esc_html_e( 'Please log in to enroll in this tutorial.', 'aiddata-lms' ); ?></p>
               <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="aiddata-lms-login-btn">
                   <?php esc_html_e( 'Log In', 'aiddata-lms' ); ?>
               </a>
           </div>
       
       <?php elseif ( ! $is_enrolled ) : ?>
           <!-- Not enrolled -->
           <button type="button" class="aiddata-lms-enroll-btn" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
               <?php esc_html_e( 'Enroll Now', 'aiddata-lms' ); ?>
           </button>
       
       <?php else : ?>
           <!-- Enrolled -->
           <div class="aiddata-lms-enrolled-state visible">
               <p><strong><?php esc_html_e( 'You are enrolled in this tutorial', 'aiddata-lms' ); ?></strong></p>
               
               <?php if ( $progress ) : ?>
                   <div class="aiddata-lms-progress" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
                       <div class="progress">
                           <div class="progress-bar" role="progressbar" 
                                aria-valuenow="<?php echo esc_attr( $progress->progress_percent ); ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100" 
                                style="width: <?php echo esc_attr( $progress->progress_percent ); ?>%">
                               <?php echo esc_html( round( $progress->progress_percent ) ); ?>%
                           </div>
                       </div>
                       <p class="status-text">
                           <?php
                           if ( $progress->status === 'completed' ) {
                               esc_html_e( 'Completed', 'aiddata-lms' );
                           } elseif ( $progress->status === 'in_progress' ) {
                               printf(
                                   /* translators: %s: progress percentage */
                                   esc_html__( 'In Progress - %s%%', 'aiddata-lms' ),
                                   esc_html( round( $progress->progress_percent ) )
                               );
                           } else {
                               esc_html_e( 'Not Started', 'aiddata-lms' );
                           }
                           ?>
                       </p>
                   </div>
                   
                   <a href="<?php echo esc_url( get_permalink( $tutorial_id ) . '?step=' . $progress->current_step ); ?>" 
                      class="aiddata-lms-continue-btn visible">
                       <?php
                       if ( $progress->progress_percent > 0 ) {
                           esc_html_e( 'Continue Learning', 'aiddata-lms' );
                       } else {
                           esc_html_e( 'Start Learning', 'aiddata-lms' );
                       }
                       ?>
                   </a>
               <?php endif; ?>
               
               <button type="button" class="aiddata-lms-unenroll-btn" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
                   <?php esc_html_e( 'Unenroll', 'aiddata-lms' ); ?>
               </button>
           </div>
       
       <?php endif; ?>
       
   </div>
   ```

**Validation Checklist:**
- [ ] JavaScript follows ES6+ standards
- [ ] All AJAX calls have error handling
- [ ] Loading states implemented
- [ ] Success/error notifications work
- [ ] UI updates dynamically
- [ ] Nonces included in requests
- [ ] Cross-browser compatible
- [ ] Mobile responsive
- [ ] Accessibility (keyboard navigation)
- [ ] No console errors

**Expected Outcome:**
- Enrollment button functional
- AJAX enrollment works
- UI updates dynamically
- Progress displayed correctly
- Notifications appear
- Mobile responsive
- Ready for production use

---

## WEEK 4: EMAIL SYSTEM

---

### PROMPT 5: EMAIL QUEUE MANAGER

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 4 → Days 1-3 (lines 373-431)
- class-aiddata-lms-install.php → Email queue table schema (lines 224-255)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.5 Email Notifications

**Objective:**
Implement the email queue management system with priority handling, scheduling, retry logic, and batch processing.

**Instructions:**

1. **Create Email Queue Manager Class** (`includes/email/class-aiddata-lms-email-queue.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Email_Queue`
   - Handles email queueing and processing
   - Supports priority and scheduling
   
   Core Methods:
   ```php
   public function add_to_queue( 
       string $recipient_email, 
       string $subject, 
       string $message, 
       string $email_type, 
       array $options = [] 
   ): int|WP_Error
   
   public function process_queue( int $batch_size = 10 ): array
   
   public function retry_failed( int $max_attempts = 3 ): int
   
   public function get_queue_stats(): array
   
   public function get_pending_emails( int $limit = 10 ): array
   
   public function mark_as_sent( int $email_id ): bool
   
   public function mark_as_failed( int $email_id, string $error_message ): bool
   
   public function delete_old_emails( int $days = 30 ): int
   
   private function send_email( object $email ): bool
   ```
   
   File location: `/includes/email/class-aiddata-lms-email-queue.php`

2. **Implement Add to Queue Method**
   
   ```php
   public function add_to_queue( 
       string $recipient_email, 
       string $subject, 
       string $message, 
       string $email_type, 
       array $options = [] 
   ): int|WP_Error {
       global $wpdb;
       
       // Validate email
       if ( ! is_email( $recipient_email ) ) {
           return new WP_Error( 'invalid_email', __( 'Invalid email address.', 'aiddata-lms' ) );
       }
       
       // Parse options
       $defaults = array(
           'recipient_name' => '',
           'user_id' => 0,
           'template_id' => null,
           'template_data' => array(),
           'priority' => 5, // 1-10, 1 = highest
           'scheduled_for' => null,
       );
       
       $options = wp_parse_args( $options, $defaults );
       
       // Prepare data
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       $data = array(
           'recipient_email' => sanitize_email( $recipient_email ),
           'recipient_name' => sanitize_text_field( $options['recipient_name'] ),
           'user_id' => absint( $options['user_id'] ),
           'subject' => sanitize_text_field( $subject ),
           'message' => wp_kses_post( $message ),
           'email_type' => sanitize_key( $email_type ),
           'template_id' => $options['template_id'] ? sanitize_key( $options['template_id'] ) : null,
           'template_data' => ! empty( $options['template_data'] ) ? wp_json_encode( $options['template_data'] ) : null,
           'priority' => min( max( absint( $options['priority'] ), 1 ), 10 ),
           'status' => 'pending',
           'attempts' => 0,
           'scheduled_for' => $options['scheduled_for'],
       );
       
       $format = array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s' );
       
       // Insert into queue
       $result = $wpdb->insert( $table_name, $data, $format );
       
       if ( $result === false ) {
           return new WP_Error( 'db_error', __( 'Failed to queue email.', 'aiddata-lms' ), $wpdb->last_error );
       }
       
       $email_id = $wpdb->insert_id;
       
       // Fire action
       do_action( 'aiddata_lms_email_queued', $email_id, $email_type, $recipient_email );
       
       return $email_id;
   }
   ```

3. **Implement Queue Processing**
   
   ```php
   public function process_queue( int $batch_size = 10 ): array {
       global $wpdb;
       
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       // Get pending emails ordered by priority and scheduled time
       $emails = $wpdb->get_results(
           $wpdb->prepare(
               "SELECT * FROM $table_name 
                WHERE status = 'pending' 
                AND (scheduled_for IS NULL OR scheduled_for <= NOW())
                ORDER BY priority ASC, created_at ASC
                LIMIT %d",
               $batch_size
           )
       );
       
       $results = array(
           'sent' => 0,
           'failed' => 0,
           'skipped' => 0,
       );
       
       foreach ( $emails as $email ) {
           // Update status to processing
           $wpdb->update(
               $table_name,
               array( 'status' => 'processing' ),
               array( 'id' => $email->id ),
               array( '%s' ),
               array( '%d' )
           );
           
           // Attempt to send
           $sent = $this->send_email( $email );
           
           if ( $sent ) {
               $this->mark_as_sent( $email->id );
               $results['sent']++;
           } else {
               // Check if we should retry
               $attempts = $email->attempts + 1;
               
               if ( $attempts < 3 ) {
                   // Retry later
                   $wpdb->update(
                       $table_name,
                       array(
                           'status' => 'pending',
                           'attempts' => $attempts,
                           'last_attempt' => current_time( 'mysql' ),
                       ),
                       array( 'id' => $email->id ),
                       array( '%s', '%d', '%s' ),
                       array( '%d' )
                   );
                   $results['skipped']++;
               } else {
                   // Max attempts reached
                   $this->mark_as_failed( $email->id, 'Maximum retry attempts reached' );
                   $results['failed']++;
               }
           }
       }
       
       do_action( 'aiddata_lms_queue_processed', $results );
       
       return $results;
   }
   ```

4. **Implement Email Sending**
   
   ```php
   private function send_email( object $email ): bool {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       // Apply filters for customization
       $to = apply_filters( 'aiddata_lms_email_to', $email->recipient_email, $email );
       $subject = apply_filters( 'aiddata_lms_email_subject', $email->subject, $email );
       $message = apply_filters( 'aiddata_lms_email_message', $email->message, $email );
       
       // Set headers
       $headers = array(
           'Content-Type: text/html; charset=UTF-8',
       );
       
       if ( ! empty( $email->recipient_name ) ) {
           $to = sprintf( '%s <%s>', $email->recipient_name, $email->recipient_email );
       }
       
       $headers = apply_filters( 'aiddata_lms_email_headers', $headers, $email );
       
       // Send email
       $sent = wp_mail( $to, $subject, $message, $headers );
       
       if ( ! $sent ) {
           // Log error
           error_log( sprintf( 'Failed to send email ID %d to %s', $email->id, $email->recipient_email ) );
           
           // Update attempt
           $wpdb->update(
               $table_name,
               array(
                   'attempts' => $email->attempts + 1,
                   'last_attempt' => current_time( 'mysql' ),
                   'error_message' => 'wp_mail() returned false',
               ),
               array( 'id' => $email->id ),
               array( '%d', '%s', '%s' ),
               array( '%d' )
           );
       }
       
       return $sent;
   }
   ```

5. **Implement Helper Methods**
   
   ```php
   public function mark_as_sent( int $email_id ): bool {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       $result = $wpdb->update(
           $table_name,
           array(
               'status' => 'sent',
               'sent_at' => current_time( 'mysql' ),
           ),
           array( 'id' => $email_id ),
           array( '%s', '%s' ),
           array( '%d' )
       );
       
       do_action( 'aiddata_lms_email_sent', $email_id );
       
       return $result !== false;
   }
   
   public function mark_as_failed( int $email_id, string $error_message ): bool {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       $result = $wpdb->update(
           $table_name,
           array(
               'status' => 'failed',
               'error_message' => sanitize_textarea_field( $error_message ),
               'last_attempt' => current_time( 'mysql' ),
           ),
           array( 'id' => $email_id ),
           array( '%s', '%s', '%s' ),
           array( '%d' )
       );
       
       do_action( 'aiddata_lms_email_failed', $email_id, $error_message );
       
       return $result !== false;
   }
   
   public function get_queue_stats(): array {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       $stats = $wpdb->get_row(
           "SELECT 
               COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
               COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing,
               COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent,
               COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
               COUNT(*) as total
           FROM $table_name",
           ARRAY_A
       );
       
       return $stats ?: array();
   }
   
   public function delete_old_emails( int $days = 30 ): int {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'email' );
       
       $deleted = $wpdb->query(
           $wpdb->prepare(
               "DELETE FROM $table_name 
                WHERE status = 'sent' 
                AND sent_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
               $days
           )
       );
       
       return $deleted ?: 0;
   }
   ```

6. **Setup WP-Cron Integration**
   
   ```php
   // In constructor or init method
   public function __construct() {
       // Schedule cron if not already scheduled
       if ( ! wp_next_scheduled( 'aiddata_lms_process_email_queue' ) ) {
           wp_schedule_event( time(), 'aiddata_lms_five_minutes', 'aiddata_lms_process_email_queue' );
       }
       
       // Hook to cron event
       add_action( 'aiddata_lms_process_email_queue', array( $this, 'process_queue' ) );
       
       // Add custom cron schedule
       add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );
   }
   
   public function add_cron_schedule( array $schedules ): array {
       $schedules['aiddata_lms_five_minutes'] = array(
           'interval' => 5 * MINUTE_IN_SECONDS,
           'display'  => __( 'Every 5 Minutes', 'aiddata-lms' ),
       );
       
       return $schedules;
   }
   ```

**Validation Checklist:**
- [ ] Email validation working
- [ ] Queue insertion successful
- [ ] Priority ordering correct
- [ ] Scheduling functional
- [ ] Batch processing works
- [ ] Retry logic functional
- [ ] WP-Cron scheduled
- [ ] Error handling robust
- [ ] Hooks firing correctly
- [ ] Old emails cleanup works

**Expected Outcome:**
- Email queue system functional
- Emails queuing correctly
- Processing working
- WP-Cron scheduled
- Ready for template system

---

### PROMPT 6: EMAIL TEMPLATE SYSTEM

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 4 → Days 1-3 (lines 391-431)
- class-aiddata-lms-email-queue.php → From Prompt 5
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Email template specifications

**Objective:**
Implement email template system with variable replacement, HTML formatting, and template management.

**Instructions:**

1. **Create Email Template Manager** (`includes/email/class-aiddata-lms-email-templates.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Email_Templates`
   - Template loading and rendering
   - Variable replacement
   
   Core Methods:
   ```php
   public function render_template( 
       string $template_id, 
       array $variables = [] 
   ): string
   
   public function get_template_content( string $template_id ): string
   
   public function replace_variables( string $content, array $variables ): string
   
   public function get_available_variables(): array
   
   public function get_available_templates(): array
   
   public function validate_template( string $content ): bool
   
   private function load_template_file( string $template_id ): string
   
   private function get_default_variables(): array
   ```
   
   File location: `/includes/email/class-aiddata-lms-email-templates.php`

2. **Define Template Variables**
   
   ```php
   private function get_default_variables(): array {
       return array(
           '{user_name}' => '',
           '{user_email}' => '',
           '{user_first_name}' => '',
           '{user_last_name}' => '',
           '{tutorial_title}' => '',
           '{tutorial_url}' => '',
           '{tutorial_description}' => '',
           '{progress_percent}' => '0',
           '{completion_date}' => '',
           '{enrolled_date}' => '',
           '{certificate_url}' => '',
           '{certificate_id}' => '',
           '{quiz_score}' => '',
           '{quiz_attempts}' => '',
           '{quiz_passing_score}' => '',
           '{site_name}' => get_bloginfo( 'name' ),
           '{site_url}' => home_url(),
           '{site_admin_email}' => get_option( 'admin_email' ),
           '{current_date}' => wp_date( get_option( 'date_format' ) ),
           '{current_year}' => wp_date( 'Y' ),
       );
   }
   
   public function replace_variables( string $content, array $variables ): string {
       $defaults = $this->get_default_variables();
       $variables = array_merge( $defaults, $variables );
       
       foreach ( $variables as $key => $value ) {
           // Ensure key has curly braces
           if ( strpos( $key, '{' ) !== 0 ) {
               $key = '{' . $key . '}';
           }
           
           $content = str_replace( $key, $value, $content );
       }
       
       return $content;
   }
   ```

3. **Create Template Files**
   
   Create HTML email templates in `/assets/templates/email/`:
   
   **Enrollment Confirmation** (`enrollment-confirmation.html`):
   ```html
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Welcome to {tutorial_title}</title>
   </head>
   <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
       
       <div style="background-color: #0073aa; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
           <h1 style="margin: 0; font-size: 24px;">Welcome to Your Tutorial!</h1>
       </div>
       
       <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
           <p>Hi {user_first_name},</p>
           
           <p>Congratulations! You've successfully enrolled in <strong>{tutorial_title}</strong>.</p>
           
           <p>You can start learning right away by clicking the button below:</p>
           
           <div style="text-align: center; margin: 30px 0;">
               <a href="{tutorial_url}" style="display: inline-block; background-color: #0073aa; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                   Start Learning
               </a>
           </div>
           
           <p>If the button doesn't work, copy and paste this link into your browser:</p>
           <p style="word-break: break-all; color: #0073aa;">{tutorial_url}</p>
           
           <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
           
           <p style="font-size: 14px; color: #666;">
               Questions? Contact us at <a href="mailto:{site_admin_email}">{site_admin_email}</a>
           </p>
           
           <p style="font-size: 12px; color: #999; text-align: center; margin-top: 30px;">
               &copy; {current_year} {site_name}. All rights reserved.<br>
               <a href="{site_url}" style="color: #0073aa; text-decoration: none;">{site_url}</a>
           </p>
       </div>
       
   </body>
   </html>
   ```
   
   **Progress Reminder** (`progress-reminder.html`):
   ```html
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Continue Your Learning Journey</title>
   </head>
   <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
       
       <div style="background-color: #28a745; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
           <h1 style="margin: 0; font-size: 24px;">You're Making Great Progress!</h1>
       </div>
       
       <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
           <p>Hi {user_first_name},</p>
           
           <p>You're <strong>{progress_percent}%</strong> of the way through <strong>{tutorial_title}</strong>. Keep going!</p>
           
           <div style="background-color: #e9ecef; border-radius: 8px; padding: 3px; margin: 20px 0;">
               <div style="background-color: #28a745; height: 20px; border-radius: 6px; width: {progress_percent}%; transition: width 0.6s ease;"></div>
           </div>
           
           <p>Continue where you left off:</p>
           
           <div style="text-align: center; margin: 30px 0;">
               <a href="{tutorial_url}" style="display: inline-block; background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                   Continue Learning
               </a>
           </div>
           
           <p style="font-size: 14px; color: #666; margin-top: 30px;">
               <strong>Tip:</strong> Consistent learning leads to better retention. Try to complete at least one step today!
           </p>
           
           <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
           
           <p style="font-size: 12px; color: #999; text-align: center;">
               &copy; {current_year} {site_name}. All rights reserved.
           </p>
       </div>
       
   </body>
   </html>
   ```
   
   **Completion Congratulations** (`completion-congratulations.html`):
   ```html
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Congratulations on Completing {tutorial_title}</title>
   </head>
   <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
       
       <div style="background-color: #ffc107; color: #333; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
           <h1 style="margin: 0; font-size: 28px;">🎉 Congratulations! 🎉</h1>
       </div>
       
       <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
           <p>Hi {user_first_name},</p>
           
           <p>Amazing work! You've successfully completed <strong>{tutorial_title}</strong>.</p>
           
           <p>Completion Date: <strong>{completion_date}</strong></p>
           
           <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
               <p style="margin: 0;"><strong>Your certificate is ready!</strong></p>
           </div>
           
           <div style="text-align: center; margin: 30px 0;">
               <a href="{certificate_url}" style="display: inline-block; background-color: #ffc107; color: #333; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                   Download Certificate
               </a>
           </div>
           
           <p>Share your achievement:</p>
           <ul style="list-style: none; padding: 0;">
               <li style="margin: 10px 0;">
                   <a href="https://www.linkedin.com/sharing/share-offsite/?url={certificate_url}" style="color: #0073aa; text-decoration: none;">
                       📱 Share on LinkedIn
                   </a>
               </li>
               <li style="margin: 10px 0;">
                   <a href="https://twitter.com/intent/tweet?text=I%20just%20completed%20{tutorial_title}%21&url={certificate_url}" style="color: #0073aa; text-decoration: none;">
                       🐦 Share on Twitter
                   </a>
               </li>
           </ul>
           
           <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
           
           <p style="font-size: 14px; color: #666;">
               Continue your learning journey! Check out more tutorials at <a href="{site_url}" style="color: #0073aa;">{site_name}</a>
           </p>
           
           <p style="font-size: 12px; color: #999; text-align: center; margin-top: 30px;">
               &copy; {current_year} {site_name}. All rights reserved.
           </p>
       </div>
       
   </body>
   </html>
   ```

4. **Implement Template Loading**
   
   ```php
   private function load_template_file( string $template_id ): string {
       $template_path = AIDDATA_LMS_PATH . 'assets/templates/email/' . $template_id . '.html';
       
       // Allow themes to override
       $theme_template = get_stylesheet_directory() . '/aiddata-lms/email/' . $template_id . '.html';
       
       if ( file_exists( $theme_template ) ) {
           $template_path = $theme_template;
       }
       
       if ( ! file_exists( $template_path ) ) {
           return '';
       }
       
       return file_get_contents( $template_path );
   }
   
   public function render_template( string $template_id, array $variables = [] ): string {
       $content = $this->load_template_file( $template_id );
       
       if ( empty( $content ) ) {
           return '';
       }
       
       // Apply filters for customization
       $content = apply_filters( 'aiddata_lms_email_template_content', $content, $template_id );
       $variables = apply_filters( 'aiddata_lms_email_template_variables', $variables, $template_id );
       
       // Replace variables
       $content = $this->replace_variables( $content, $variables );
       
       return $content;
   }
   ```

5. **Create Email Notification Triggers** (`includes/email/class-aiddata-lms-email-notifications.php`)
   
   ```php
   class AidData_LMS_Email_Notifications {
       
       private $template_manager;
       private $queue_manager;
       
       public function __construct() {
           $this->template_manager = new AidData_LMS_Email_Templates();
           $this->queue_manager = new AidData_LMS_Email_Queue();
           
           $this->register_hooks();
       }
       
       private function register_hooks(): void {
           // Enrollment events
           add_action( 'aiddata_lms_user_enrolled', array( $this, 'on_user_enrolled' ), 10, 4 );
           
           // Progress events
           add_action( 'aiddata_lms_progress_updated', array( $this, 'on_progress_updated' ), 10, 3 );
           
           // Completion events
           add_action( 'aiddata_lms_tutorial_completed', array( $this, 'on_tutorial_completed' ), 10, 3 );
           
           // Certificate events
           add_action( 'aiddata_lms_certificate_generated', array( $this, 'on_certificate_generated' ), 10, 3 );
       }
       
       public function on_user_enrolled( 
           int $enrollment_id, 
           int $user_id, 
           int $tutorial_id, 
           string $source 
       ): void {
           $user = get_userdata( $user_id );
           $tutorial = get_post( $tutorial_id );
           
           if ( ! $user || ! $tutorial ) {
               return;
           }
           
           // Prepare variables
           $variables = array(
               '{user_name}' => $user->display_name,
               '{user_email}' => $user->user_email,
               '{user_first_name}' => $user->first_name ?: $user->display_name,
               '{user_last_name}' => $user->last_name,
               '{tutorial_title}' => $tutorial->post_title,
               '{tutorial_url}' => get_permalink( $tutorial_id ),
               '{tutorial_description}' => wp_trim_words( $tutorial->post_excerpt, 30 ),
               '{enrolled_date}' => wp_date( get_option( 'date_format' ) ),
           );
           
           // Render template
           $message = $this->template_manager->render_template( 'enrollment-confirmation', $variables );
           
           if ( empty( $message ) ) {
               return;
           }
           
           // Queue email
           $this->queue_manager->add_to_queue(
               $user->user_email,
               sprintf( __( 'Welcome to %s', 'aiddata-lms' ), $tutorial->post_title ),
               $message,
               'enrollment_confirmation',
               array(
                   'recipient_name' => $user->display_name,
                   'user_id' => $user_id,
                   'priority' => 3, // High priority
               )
           );
       }
       
       public function on_progress_updated( 
           int $user_id, 
           int $tutorial_id, 
           float $progress_percent 
       ): void {
           // Send reminder at 25%, 50%, 75%
           $milestones = array( 25, 50, 75 );
           $rounded_progress = round( $progress_percent );
           
           if ( ! in_array( $rounded_progress, $milestones, true ) ) {
               return;
           }
           
           // Check if already sent for this milestone
           $sent_meta_key = '_aiddata_lms_progress_email_' . $rounded_progress;
           $already_sent = get_user_meta( $user_id, $sent_meta_key, true );
           
           if ( $already_sent ) {
               return;
           }
           
           $user = get_userdata( $user_id );
           $tutorial = get_post( $tutorial_id );
           
           if ( ! $user || ! $tutorial ) {
               return;
           }
           
           $variables = array(
               '{user_first_name}' => $user->first_name ?: $user->display_name,
               '{tutorial_title}' => $tutorial->post_title,
               '{tutorial_url}' => get_permalink( $tutorial_id ),
               '{progress_percent}' => $rounded_progress,
           );
           
           $message = $this->template_manager->render_template( 'progress-reminder', $variables );
           
           if ( ! empty( $message ) ) {
               $this->queue_manager->add_to_queue(
                   $user->user_email,
                   sprintf( __( 'You\'re %d%% through %s!', 'aiddata-lms' ), $rounded_progress, $tutorial->post_title ),
                   $message,
                   'progress_reminder',
                   array(
                       'recipient_name' => $user->display_name,
                       'user_id' => $user_id,
                       'priority' => 5, // Normal priority
                   )
               );
               
               // Mark as sent
               update_user_meta( $user_id, $sent_meta_key, time() );
           }
       }
       
       public function on_tutorial_completed( 
           int $user_id, 
           int $tutorial_id, 
           int $enrollment_id 
       ): void {
           $user = get_userdata( $user_id );
           $tutorial = get_post( $tutorial_id );
           
           if ( ! $user || ! $tutorial ) {
               return;
           }
           
           $variables = array(
               '{user_first_name}' => $user->first_name ?: $user->display_name,
               '{tutorial_title}' => $tutorial->post_title,
               '{completion_date}' => wp_date( get_option( 'date_format' ) ),
               '{certificate_url}' => home_url( '/certificates/' . $enrollment_id ), // Placeholder
           );
           
           $message = $this->template_manager->render_template( 'completion-congratulations', $variables );
           
           if ( ! empty( $message ) ) {
               $this->queue_manager->add_to_queue(
                   $user->user_email,
                   sprintf( __( 'Congratulations on completing %s!', 'aiddata-lms' ), $tutorial->post_title ),
                   $message,
                   'completion_congratulations',
                   array(
                       'recipient_name' => $user->display_name,
                       'user_id' => $user_id,
                       'priority' => 2, // High priority
                   )
               );
           }
       }
   }
   ```

6. **Initialize Email Notifications**
   
   In `class-aiddata-lms.php`:
   ```php
   private function load_dependencies() {
       // ... existing code ...
       
       // Initialize email system
       new AidData_LMS_Email_Notifications();
   }
   ```

**Validation Checklist:**
- [ ] Templates load correctly
- [ ] Variable replacement works
- [ ] HTML emails render properly
- [ ] Hooks fire on appropriate events
- [ ] Emails queue successfully
- [ ] Theme overrides work
- [ ] No broken links in emails
- [ ] Milestone emails sent once
- [ ] All templates created

**Expected Outcome:**
- Email templates functional
- Variable replacement working
- Notifications triggered automatically
- Emails queued on events
- Professional HTML emails
- Ready for testing

---

## WEEK 5: BASIC ANALYTICS FOUNDATION

---

### PROMPT 7: ANALYTICS TRACKING SYSTEM

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 5 → Days 1-3 (lines 459-503)
- class-aiddata-lms-install.php → Analytics table schema (lines 200-222)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Analytics requirements

**Objective:**
Implement analytics tracking system for events, sessions, and user activity with privacy controls.

**Instructions:**

1. **Create Analytics Manager Class** (`includes/analytics/class-aiddata-lms-analytics.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Analytics`
   - Event tracking
   - Session management
   - Privacy compliance
   
   Core Methods:
   ```php
   public function track_event( 
       int $tutorial_id, 
       string $event_type, 
       array $event_data = [], 
       ?int $user_id = null 
   ): int|WP_Error
   
   public function get_tutorial_analytics( 
       int $tutorial_id, 
       array $date_range = [] 
   ): array
   
   public function get_user_analytics( 
       int $user_id, 
       array $date_range = [] 
   ): array
   
   public function get_platform_analytics( array $date_range = [] ): array
   
   public function get_event_count( 
       string $event_type, 
       ?int $tutorial_id = null, 
       array $date_range = [] 
   ): int
   
   public function get_unique_users( 
       ?int $tutorial_id = null, 
       array $date_range = [] 
   ): int
   
   private function get_session_id(): string
   
   private function hash_ip_address( string $ip ): string
   ```
   
   File location: `/includes/analytics/class-aiddata-lms-analytics.php`

2. **Implement Event Tracking**
   
   ```php
   public function track_event( 
       int $tutorial_id, 
       string $event_type, 
       array $event_data = [], 
       ?int $user_id = null 
   ): int|WP_Error {
       global $wpdb;
       
       // Validate tutorial exists
       if ( ! get_post( $tutorial_id ) ) {
           return new WP_Error( 'invalid_tutorial', __( 'Tutorial not found.', 'aiddata-lms' ) );
       }
       
       // Get user ID if not provided
       if ( is_null( $user_id ) ) {
           $user_id = get_current_user_id();
           $user_id = $user_id > 0 ? $user_id : null;
       }
       
       // Get session ID
       $session_id = $this->get_session_id();
       
       // Get IP address (hashed for privacy)
       $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
       $ip_hash = $this->hash_ip_address( $ip_address );
       
       // Get user agent
       $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
       
       // Get referrer
       $referrer = $_SERVER['HTTP_REFERER'] ?? '';
       
       // Prepare data
       $table_name = AidData_LMS_Database::get_table_name( 'analytics' );
       
       $data = array(
           'tutorial_id' => $tutorial_id,
           'user_id' => $user_id,
           'event_type' => sanitize_key( $event_type ),
           'event_data' => ! empty( $event_data ) ? wp_json_encode( $event_data ) : null,
           'session_id' => $session_id,
           'ip_address' => $ip_hash,
           'user_agent' => substr( sanitize_text_field( $user_agent ), 0, 500 ),
           'referrer' => substr( esc_url_raw( $referrer ), 0, 500 ),
       );
       
       $format = array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' );
       
       // Insert event
       $result = $wpdb->insert( $table_name, $data, $format );
       
       if ( $result === false ) {
           return new WP_Error( 'db_error', __( 'Failed to track event.', 'aiddata-lms' ), $wpdb->last_error );
       }
       
       $event_id = $wpdb->insert_id;
       
       // Fire action for extensions
       do_action( 'aiddata_lms_event_tracked', $event_id, $event_type, $tutorial_id, $user_id );
       
       return $event_id;
   }
   ```

3. **Implement Session Management**
   
   ```php
   private function get_session_id(): string {
       // Check for existing session
       if ( ! session_id() ) {
           session_start();
       }
       
       // Get or create session ID
       if ( ! isset( $_SESSION['aiddata_lms_session_id'] ) ) {
           $_SESSION['aiddata_lms_session_id'] = wp_generate_uuid4();
       }
       
       return $_SESSION['aiddata_lms_session_id'];
   }
   
   private function hash_ip_address( string $ip ): string {
       // Hash IP for privacy (GDPR compliance)
       $salt = get_option( 'aiddata_lms_analytics_salt', wp_generate_password( 64, true, true ) );
       
       // Save salt if it doesn't exist
       if ( ! get_option( 'aiddata_lms_analytics_salt' ) ) {
           update_option( 'aiddata_lms_analytics_salt', $salt );
       }
       
       return hash( 'sha256', $ip . $salt );
   }
   ```

4. **Implement Analytics Queries**
   
   ```php
   public function get_tutorial_analytics( 
       int $tutorial_id, 
       array $date_range = [] 
   ): array {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'analytics' );
       
       // Parse date range
       $where_date = $this->build_date_where_clause( $date_range );
       
       // Get event counts by type
       $event_counts = $wpdb->get_results(
           $wpdb->prepare(
               "SELECT event_type, COUNT(*) as count
                FROM $table_name
                WHERE tutorial_id = %d
                $where_date
                GROUP BY event_type
                ORDER BY count DESC",
               $tutorial_id
           ),
           ARRAY_A
       );
       
       // Get unique users
       $unique_users = $wpdb->get_var(
           $wpdb->prepare(
               "SELECT COUNT(DISTINCT user_id)
                FROM $table_name
                WHERE tutorial_id = %d
                AND user_id IS NOT NULL
                $where_date",
               $tutorial_id
           )
       );
       
       // Get unique sessions
       $unique_sessions = $wpdb->get_var(
           $wpdb->prepare(
               "SELECT COUNT(DISTINCT session_id)
                FROM $table_name
                WHERE tutorial_id = %d
                $where_date",
               $tutorial_id
           )
       );
       
       return array(
           'tutorial_id' => $tutorial_id,
           'event_counts' => $event_counts,
           'unique_users' => (int) $unique_users,
           'unique_sessions' => (int) $unique_sessions,
           'date_range' => $date_range,
       );
   }
   
   private function build_date_where_clause( array $date_range ): string {
       global $wpdb;
       
       if ( empty( $date_range ) ) {
           return '';
       }
       
       $where = '';
       
       if ( ! empty( $date_range['start'] ) ) {
           $where .= $wpdb->prepare( ' AND created_at >= %s', $date_range['start'] );
       }
       
       if ( ! empty( $date_range['end'] ) ) {
           $where .= $wpdb->prepare( ' AND created_at <= %s', $date_range['end'] );
       }
       
       return $where;
   }
   
   public function get_platform_analytics( array $date_range = [] ): array {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'analytics' );
       
       $where_date = $this->build_date_where_clause( $date_range );
       
       // Total events
       $total_events = $wpdb->get_var(
           "SELECT COUNT(*) FROM $table_name WHERE 1=1 $where_date"
       );
       
       // Unique users
       $unique_users = $wpdb->get_var(
           "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE user_id IS NOT NULL $where_date"
       );
       
       // Unique tutorials accessed
       $unique_tutorials = $wpdb->get_var(
           "SELECT COUNT(DISTINCT tutorial_id) FROM $table_name WHERE 1=1 $where_date"
       );
       
       // Top events
       $top_events = $wpdb->get_results(
           "SELECT event_type, COUNT(*) as count
            FROM $table_name
            WHERE 1=1 $where_date
            GROUP BY event_type
            ORDER BY count DESC
            LIMIT 10",
           ARRAY_A
       );
       
       // Top tutorials
       $top_tutorials = $wpdb->get_results(
           "SELECT tutorial_id, COUNT(*) as event_count, COUNT(DISTINCT user_id) as user_count
            FROM $table_name
            WHERE 1=1 $where_date
            GROUP BY tutorial_id
            ORDER BY event_count DESC
            LIMIT 10",
           ARRAY_A
       );
       
       return array(
           'total_events' => (int) $total_events,
           'unique_users' => (int) $unique_users,
           'unique_tutorials' => (int) $unique_tutorials,
           'top_events' => $top_events,
           'top_tutorials' => $top_tutorials,
           'date_range' => $date_range,
       );
   }
   ```

5. **Hook Analytics to Events**
   
   Create hooks to track events:
   ```php
   public function __construct() {
       // Track enrollment events
       add_action( 'aiddata_lms_user_enrolled', array( $this, 'track_enrollment' ), 10, 4 );
       
       // Track progress events
       add_action( 'aiddata_lms_step_completed', array( $this, 'track_step_completion' ), 10, 3 );
       
       // Track tutorial start
       add_action( 'aiddata_lms_tutorial_started', array( $this, 'track_tutorial_start' ), 10, 2 );
       
       // Track tutorial view
       add_action( 'template_redirect', array( $this, 'track_tutorial_view' ) );
   }
   
   public function track_enrollment( int $enrollment_id, int $user_id, int $tutorial_id, string $source ): void {
       $this->track_event(
           $tutorial_id,
           'tutorial_enroll',
           array(
               'enrollment_id' => $enrollment_id,
               'source' => $source,
           ),
           $user_id
       );
   }
   
   public function track_step_completion( int $user_id, int $tutorial_id, int $step_index ): void {
       $this->track_event(
           $tutorial_id,
           'step_complete',
           array(
               'step_index' => $step_index,
           ),
           $user_id
       );
   }
   
   public function track_tutorial_view(): void {
       if ( ! is_singular( 'aiddata_tutorial' ) ) {
           return;
       }
       
       $tutorial_id = get_the_ID();
       
       if ( ! $tutorial_id ) {
           return;
       }
       
       $this->track_event(
           $tutorial_id,
           'tutorial_view',
           array(
               'page' => 'overview',
           )
       );
   }
   ```

6. **Initialize Analytics**
   
   In `class-aiddata-lms.php`:
   ```php
   private function load_dependencies() {
       // ... existing code ...
       
       // Initialize analytics
       new AidData_LMS_Analytics();
   }
   ```

**Validation Checklist:**
- [ ] Events tracked correctly
- [ ] Session IDs generated
- [ ] IP addresses hashed
- [ ] Privacy compliance (GDPR)
- [ ] Database queries optimized
- [ ] Hooks fire on events
- [ ] Guest tracking works
- [ ] User tracking works
- [ ] No performance impact

**Expected Outcome:**
- Analytics tracking functional
- Events logged to database
- Privacy-compliant tracking
- Session management working
- Ready for reporting dashboard

---

### PROMPT 8: DASHBOARD WIDGETS & BASIC REPORTS

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 1 → Week 5 → Days 4-5 (lines 504-529)
- class-aiddata-lms-analytics.php → From Prompt 7
- WordPress Dashboard Widgets API

**Objective:**
Create WordPress dashboard widgets displaying enrollment statistics, popular tutorials, and basic reports with export functionality.

**Instructions:**

1. **Create Dashboard Class** (`includes/admin/class-aiddata-lms-admin-dashboard.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Admin_Dashboard`
   - Register dashboard widgets
   - Display statistics
   
   Core Methods:
   ```php
   public function __construct()
   
   public function register_widgets(): void
   
   public function render_enrollments_widget(): void
   
   public function render_popular_tutorials_widget(): void
   
   public function render_completion_stats_widget(): void
   
   public function render_recent_activity_widget(): void
   
   private function get_enrollment_stats(): array
   
   private function get_popular_tutorials( int $limit = 5 ): array
   ```
   
   File location: `/includes/admin/class-aiddata-lms-admin-dashboard.php`

2. **Register Dashboard Widgets**
   
   ```php
   public function __construct() {
       add_action( 'wp_dashboard_setup', array( $this, 'register_widgets' ) );
   }
   
   public function register_widgets(): void {
       // Only show to users with capability
       if ( ! current_user_can( 'manage_aiddata_lms' ) ) {
           return;
       }
       
       // Enrollments widget
       wp_add_dashboard_widget(
           'aiddata_lms_enrollments',
           __( 'Tutorial Enrollments', 'aiddata-lms' ),
           array( $this, 'render_enrollments_widget' )
       );
       
       // Popular tutorials widget
       wp_add_dashboard_widget(
           'aiddata_lms_popular_tutorials',
           __( 'Popular Tutorials', 'aiddata-lms' ),
           array( $this, 'render_popular_tutorials_widget' )
       );
       
       // Completion stats widget
       wp_add_dashboard_widget(
           'aiddata_lms_completion_stats',
           __( 'Completion Statistics', 'aiddata-lms' ),
           array( $this, 'render_completion_stats_widget' )
       );
       
       // Recent activity widget
       wp_add_dashboard_widget(
           'aiddata_lms_recent_activity',
           __( 'Recent Learning Activity', 'aiddata-lms' ),
           array( $this, 'render_recent_activity_widget' )
       );
   }
   ```

3. **Implement Enrollments Widget**
   
   ```php
   public function render_enrollments_widget(): void {
       $stats = $this->get_enrollment_stats();
       ?>
       <div class="aiddata-lms-dashboard-widget">
           <div class="aiddata-lms-stats-grid">
               <div class="stat-box">
                   <div class="stat-value"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></div>
                   <div class="stat-label"><?php esc_html_e( 'Total Enrollments', 'aiddata-lms' ); ?></div>
               </div>
               
               <div class="stat-box">
                   <div class="stat-value stat-positive">
                       +<?php echo esc_html( number_format_i18n( $stats['today'] ) ); ?>
                   </div>
                   <div class="stat-label"><?php esc_html_e( 'Today', 'aiddata-lms' ); ?></div>
               </div>
               
               <div class="stat-box">
                   <div class="stat-value"><?php echo esc_html( number_format_i18n( $stats['active'] ) ); ?></div>
                   <div class="stat-label"><?php esc_html_e( 'Active Learners', 'aiddata-lms' ); ?></div>
               </div>
               
               <div class="stat-box">
                   <div class="stat-value stat-success">
                       <?php echo esc_html( number_format_i18n( $stats['completed'] ) ); ?>
                   </div>
                   <div class="stat-label"><?php esc_html_e( 'Completed', 'aiddata-lms' ); ?></div>
               </div>
           </div>
           
           <div class="widget-footer">
               <a href="<?php echo esc_url( admin_url( 'admin.php?page=aiddata-lms-reports' ) ); ?>">
                   <?php esc_html_e( 'View Full Report', 'aiddata-lms' ); ?> →
               </a>
           </div>
       </div>
       
       <style>
           .aiddata-lms-stats-grid {
               display: grid;
               grid-template-columns: repeat(2, 1fr);
               gap: 15px;
               margin-bottom: 15px;
           }
           
           .stat-box {
               background: #f9f9f9;
               padding: 15px;
               border-radius: 4px;
               text-align: center;
           }
           
           .stat-value {
               font-size: 32px;
               font-weight: bold;
               color: #333;
               line-height: 1;
               margin-bottom: 8px;
           }
           
           .stat-positive {
               color: #28a745;
           }
           
           .stat-success {
               color: #0073aa;
           }
           
           .stat-label {
               font-size: 13px;
               color: #666;
           }
           
           .widget-footer {
               padding-top: 10px;
               border-top: 1px solid #ddd;
               text-align: right;
           }
       </style>
       <?php
   }
   
   private function get_enrollment_stats(): array {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'enrollments' );
       
       // Total enrollments
       $total = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
       
       // Today's enrollments
       $today = $wpdb->get_var(
           "SELECT COUNT(*) FROM $table_name 
            WHERE DATE(enrolled_at) = CURDATE()"
       );
       
       // Active learners (enrolled but not completed)
       $active = $wpdb->get_var(
           "SELECT COUNT(*) FROM $table_name 
            WHERE status = 'active' AND completed_at IS NULL"
       );
       
       // Completed
       $completed = $wpdb->get_var(
           "SELECT COUNT(*) FROM $table_name 
            WHERE completed_at IS NOT NULL"
       );
       
       return array(
           'total' => (int) $total,
           'today' => (int) $today,
           'active' => (int) $active,
           'completed' => (int) $completed,
       );
   }
   ```

4. **Implement Popular Tutorials Widget**
   
   ```php
   public function render_popular_tutorials_widget(): void {
       $tutorials = $this->get_popular_tutorials( 5 );
       
       if ( empty( $tutorials ) ) {
           echo '<p>' . esc_html__( 'No tutorial data available yet.', 'aiddata-lms' ) . '</p>';
           return;
       }
       ?>
       <div class="aiddata-lms-popular-tutorials">
           <table class="widefat">
               <thead>
                   <tr>
                       <th><?php esc_html_e( 'Tutorial', 'aiddata-lms' ); ?></th>
                       <th><?php esc_html_e( 'Enrollments', 'aiddata-lms' ); ?></th>
                       <th><?php esc_html_e( 'Completion Rate', 'aiddata-lms' ); ?></th>
                   </tr>
               </thead>
               <tbody>
                   <?php foreach ( $tutorials as $tutorial ) : ?>
                       <tr>
                           <td>
                               <a href="<?php echo esc_url( get_edit_post_link( $tutorial['tutorial_id'] ) ); ?>">
                                   <?php echo esc_html( get_the_title( $tutorial['tutorial_id'] ) ); ?>
                               </a>
                           </td>
                           <td><?php echo esc_html( number_format_i18n( $tutorial['enrollment_count'] ) ); ?></td>
                           <td>
                               <span class="completion-rate" style="color: <?php echo $tutorial['completion_rate'] >= 50 ? '#28a745' : '#ffc107'; ?>">
                                   <?php echo esc_html( round( $tutorial['completion_rate'], 1 ) ); ?>%
                               </span>
                           </td>
                       </tr>
                   <?php endforeach; ?>
               </tbody>
           </table>
       </div>
       <?php
   }
   
   private function get_popular_tutorials( int $limit = 5 ): array {
       global $wpdb;
       $table_name = AidData_LMS_Database::get_table_name( 'enrollments' );
       
       $results = $wpdb->get_results(
           $wpdb->prepare(
               "SELECT 
                   tutorial_id,
                   COUNT(*) as enrollment_count,
                   SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed_count,
                   (SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*)) * 100 as completion_rate
                FROM $table_name
                GROUP BY tutorial_id
                ORDER BY enrollment_count DESC
                LIMIT %d",
               $limit
           ),
           ARRAY_A
       );
       
       return $results ?: array();
   }
   ```

5. **Create Basic Reports Page** (`includes/admin/class-aiddata-lms-admin-reports.php`)
   
   ```php
   class AidData_LMS_Admin_Reports {
       
       public function __construct() {
           add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
       }
       
       public function add_menu_page(): void {
           add_submenu_page(
               'edit.php?post_type=aiddata_tutorial',
               __( 'Reports', 'aiddata-lms' ),
               __( 'Reports', 'aiddata-lms' ),
               'view_tutorial_analytics',
               'aiddata-lms-reports',
               array( $this, 'render_page' )
           );
       }
       
       public function render_page(): void {
           // Get date range from query params
           $date_range = array(
               'start' => $_GET['start_date'] ?? date( 'Y-m-d', strtotime( '-30 days' ) ),
               'end' => $_GET['end_date'] ?? date( 'Y-m-d' ),
           );
           
           $analytics = new AidData_LMS_Analytics();
           $stats = $analytics->get_platform_analytics( $date_range );
           
           ?>
           <div class="wrap">
               <h1><?php esc_html_e( 'Tutorial Analytics & Reports', 'aiddata-lms' ); ?></h1>
               
               <div class="aiddata-lms-reports-filters">
                   <form method="get">
                       <input type="hidden" name="post_type" value="aiddata_tutorial">
                       <input type="hidden" name="page" value="aiddata-lms-reports">
                       
                       <label for="start_date"><?php esc_html_e( 'Start Date:', 'aiddata-lms' ); ?></label>
                       <input type="date" name="start_date" value="<?php echo esc_attr( $date_range['start'] ); ?>">
                       
                       <label for="end_date"><?php esc_html_e( 'End Date:', 'aiddata-lms' ); ?></label>
                       <input type="date" name="end_date" value="<?php echo esc_attr( $date_range['end'] ); ?>">
                       
                       <button type="submit" class="button"><?php esc_html_e( 'Apply', 'aiddata-lms' ); ?></button>
                       
                       <a href="<?php echo esc_url( add_query_arg( 'action', 'export_csv' ) ); ?>" class="button button-primary">
                           <?php esc_html_e( 'Export CSV', 'aiddata-lms' ); ?>
                       </a>
                   </form>
               </div>
               
               <div class="aiddata-lms-stats-cards">
                   <div class="stats-card">
                       <h3><?php esc_html_e( 'Total Events', 'aiddata-lms' ); ?></h3>
                       <div class="stat-number"><?php echo esc_html( number_format_i18n( $stats['total_events'] ) ); ?></div>
                   </div>
                   
                   <div class="stats-card">
                       <h3><?php esc_html_e( 'Unique Users', 'aiddata-lms' ); ?></h3>
                       <div class="stat-number"><?php echo esc_html( number_format_i18n( $stats['unique_users'] ) ); ?></div>
                   </div>
                   
                   <div class="stats-card">
                       <h3><?php esc_html_e( 'Active Tutorials', 'aiddata-lms' ); ?></h3>
                       <div class="stat-number"><?php echo esc_html( number_format_i18n( $stats['unique_tutorials'] ) ); ?></div>
                   </div>
               </div>
               
               <!-- More report sections... -->
               
           </div>
           <?php
       }
   }
   ```

6. **Initialize Dashboard and Reports**
   
   In `class-aiddata-lms.php`:
   ```php
   private function define_admin_hooks(): void {
       // ... existing code ...
       
       // Initialize dashboard widgets
       new AidData_LMS_Admin_Dashboard();
       
       // Initialize reports page
       new AidData_LMS_Admin_Reports();
   }
   ```

**Validation Checklist:**
- [ ] Dashboard widgets display correctly
- [ ] Statistics accurate
- [ ] Date range filtering works
- [ ] Export CSV functional
- [ ] Responsive design
- [ ] Performance acceptable
- [ ] Permissions checked
- [ ] No SQL errors
- [ ] Charts render correctly

**Expected Outcome:**
- Dashboard widgets functional
- Statistics displayed
- Reports page accessible
- Data export working
- Phase 1 complete!

---

## 📋 PHASE 1 COMPLETION CHECKLIST

### Week 3 Deliverables:
- [ ] Enrollment manager backend (`class-aiddata-lms-tutorial-enrollment.php`)
- [ ] Progress manager backend (`class-aiddata-lms-tutorial-progress.php`)
- [ ] AJAX handlers (`class-aiddata-lms-tutorial-ajax.php`)
- [ ] Frontend JavaScript (`enrollment.js`)
- [ ] Enrollment templates
- [ ] CSS styling

### Week 4 Deliverables:
- [ ] Email queue manager (`class-aiddata-lms-email-queue.php`)
- [ ] Email template system
- [ ] Email notification triggers
- [ ] WP-Cron integration
- [ ] Admin email settings

### Week 5 Deliverables:
- [ ] Analytics tracker (`class-aiddata-lms-analytics.php`)
- [ ] Event tracking
- [ ] Dashboard widgets
- [ ] Basic reports
- [ ] Data export

### Integration Tests:
- [ ] User can enroll in tutorial
- [ ] Progress tracks correctly
- [ ] Emails send on enrollment
- [ ] Analytics logs events
- [ ] Dashboard displays stats

---

## ✅ PHASE 1 SUCCESS CRITERIA

Phase 1 is considered successful when:

1. ✅ **Enrollment System**: Users can enroll/unenroll with validation
2. ✅ **Progress Tracking**: Step completion and percentages tracked
3. ✅ **Email System**: Emails queue and send reliably
4. ✅ **Analytics**: Events logged and displayed
5. ✅ **Integration**: All systems work together
6. ✅ **Performance**: < 500ms response for enrollment
7. ✅ **Testing**: All validation tests pass
8. ✅ **Security**: No vulnerabilities detected

---

**Document End**

For questions or clarifications during Phase 1 implementation, refer to:
- IMPLEMENTATION_PATHWAY.md (lines 283-536)
- This prompts document
- Phase 0 validation reports for patterns and standards

