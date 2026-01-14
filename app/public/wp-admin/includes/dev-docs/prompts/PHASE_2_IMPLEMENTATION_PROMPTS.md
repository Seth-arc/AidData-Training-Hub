# PHASE 2 IMPLEMENTATION PROMPTS
## AidData LMS Tutorial Builder - Tutorial Builder & Management (Weeks 6-8)

**Version:** 1.0  
**Date:** October 22, 2025  
**Purpose:** Detailed prompts for AI agent implementation of Phase 2  
**Context Documents:** Reference these for persistent context throughout implementation

---

## 📚 REQUIRED CONTEXT DOCUMENTS

**CRITICAL: Load ALL these documents into context before starting each prompt:**

### Primary References (MUST READ BEFORE EVERY PROMPT)
1. **IMPLEMENTATION_PATHWAY.md**
   - Phase 2: Tutorial Builder & Management (lines 539-788)
   - Phase 0 Summary (lines 97-281) - Database schema reference
   - Phase 1 Summary (lines 283-536) - Enrollment/Progress integration
   - Development Standards (lines 2046-2180)

2. **TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md**
   - Section 3.1: Tutorial Structure (tutorial steps, content blocks, metadata)
   - Section 3.3: Admin Interface (tutorial builder UI specifications)
   - Section 3.4: Frontend Display (tutorial viewing experience)
   - Section 2.2: Database Schema (tutorial metadata storage)

3. **CODE_STANDARDS_AND_VALIDATION_GUIDE.md**
   - Section 1: Code Standards & Rules (lines 21-402)
   - Section 2: JavaScript & Frontend Standards
   - Section 5: Phase 2 Validation Criteria

4. **INTEGRATION_VALIDATION_MATRIX.md**
   - Section 4: Frontend-Backend Integration (lines 225-344)
   - Section 6: JavaScript-PHP Integration
   - Section 7: Conflict Prevention Rules (lines 586-698)

### Phase 0 & 1 Completion Documents
5. **texts.md** - Quick reference for Phase 0 and 1 integration
6. **PHASE_0_IMPLEMENTATION_PROMPTS.md** - Database and core structure reference
7. **PHASE_1_IMPLEMENTATION_PROMPTS.md** - Enrollment and progress integration
8. **PHASE_1_FINAL_SUMMARY.md** - Phase 1 completion status and integration points

### Existing Code References (From Phase 0 & 1)
9. **class-aiddata-lms-database.php** - Database helper methods
10. **class-aiddata-lms-post-types.php** - Tutorial post type configuration
11. **class-aiddata-lms-tutorial-enrollment.php** - Enrollment system integration
12. **class-aiddata-lms-tutorial-progress.php** - Progress tracking integration
13. **class-aiddata-lms.php** - Core plugin class and initialization

---

## 🎯 PHASE 2 OVERVIEW

**Goal:** Complete admin tutorial builder interface and frontend tutorial display with navigation

**Duration:** 3 weeks (15 working days)

**Sprint Goal:** Create a fully functional tutorial builder with multi-step wizard, content management, and interactive frontend tutorial experience

**Prerequisites from Phase 0 & 1:**
- ✅ Database schema complete (6 tables)
- ✅ Tutorial post type registered
- ✅ Taxonomies functional (categories, tags, difficulty)
- ✅ Enrollment system operational
- ✅ Progress tracking functional
- ✅ AJAX handlers established
- ✅ Email system ready
- ✅ Analytics tracking active

**Deliverables:**
- ✅ Multi-step tutorial builder wizard (admin)
- ✅ Step builder with drag-drop interface
- ✅ Tutorial management dashboard
- ✅ Frontend tutorial archive and single display
- ✅ Active tutorial navigation interface
- ✅ Progress persistence and resume functionality
- ✅ Integration with enrollment and progress systems

---

## WEEK 6: TUTORIAL BUILDER UI

---

### PROMPT 1: MULTI-STEP WIZARD - BASIC INFORMATION & SETTINGS

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 2 → Week 6 → Days 1-3 (lines 545-585)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.1 Tutorial Structure
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.3 Admin Interface
- class-aiddata-lms-post-types.php → Tutorial post type meta capabilities

**Objective:**
Implement the first part of the tutorial builder wizard interface with basic information collection (Step 1) and settings configuration (Step 2).

**Instructions:**

1. **Create Tutorial Builder Meta Box Class** (`includes/admin/class-aiddata-lms-tutorial-meta-boxes.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Tutorial_Meta_Boxes`
   - Register meta boxes for tutorial post type
   - Handle save operations with nonce verification
   - Sanitize and validate all inputs
   
   Core Methods:
   ```php
   public function __construct()
   public function register_meta_boxes(): void
   public function render_basic_info_meta_box( WP_Post $post ): void
   public function render_settings_meta_box( WP_Post $post ): void
   public function save_tutorial_meta( int $post_id ): void
   private function sanitize_tutorial_meta( array $meta_data ): array
   private function validate_tutorial_meta( array $meta_data ): array|WP_Error
   ```
   
   Reference:
   - IMPLEMENTATION_PATHWAY.md lines 548-579
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 24-149
   
   File location: `/includes/admin/class-aiddata-lms-tutorial-meta-boxes.php`

2. **Implement Basic Information Meta Box (Step 1)**
   
   Fields to include:
   - **Short Description** (textarea, 250 char limit)
     - Plain text summary for archive display
     - Character counter with JavaScript
   
   - **Full Description** (wp_editor)
     - Rich text for single tutorial page
     - Support for basic formatting
   
   - **Duration** (number input)
     - Estimated completion time in minutes
     - Validation: positive integer only
   
   - **Prerequisites** (post selector)
     - Multi-select from existing tutorials
     - AJAX search for large lists
     - Display as sortable list
   
   - **Learning Outcomes** (repeater field)
     - Add/remove outcome items
     - Text input for each outcome
     - Drag-drop reordering
   
   Meta key mapping:
   ```php
   '_tutorial_short_description' => sanitize_textarea_field()
   '_tutorial_full_description' => wp_kses_post()
   '_tutorial_duration' => absint()
   '_tutorial_prerequisites' => array of post IDs (serialized)
   '_tutorial_outcomes' => array of strings (serialized)
   ```
   
   HTML Structure:
   ```php
   <div class="aiddata-tutorial-meta-box">
       <div class="form-field">
           <label for="tutorial_short_description">
               <?php esc_html_e( 'Short Description', 'aiddata-lms' ); ?>
               <span class="required">*</span>
           </label>
           <textarea 
               id="tutorial_short_description" 
               name="tutorial_short_description"
               maxlength="250"
               rows="3"
               required
           ><?php echo esc_textarea( $short_description ); ?></textarea>
           <p class="description">
               <span id="char-count">0</span>/250 characters
           </p>
       </div>
       <!-- Additional fields... -->
   </div>
   ```

3. **Implement Settings Meta Box (Step 2)**
   
   Settings to include:
   - **Tutorial Type** (select)
     - Options: guided, self-paced, workshop
     - Default: self-paced
   
   - **Access Control** (select)
     - Options: public, members-only, restricted
     - Default: public
   
   - **Enrollment Options** (checkboxes)
     - Allow enrollment
     - Require approval
     - Send notification
     - Auto-enroll new users
   
   - **Enrollment Limit** (number input)
     - Maximum enrollments (0 = unlimited)
     - Validation: non-negative integer
   
   - **Enrollment Deadline** (date picker)
     - Optional cutoff date
     - Uses WordPress date format
   
   - **Completion Criteria** (checkboxes)
     - All steps required
     - Quiz passing required
     - Certificate generation
     - Minimum time spent
   
   - **Visibility Settings** (checkboxes)
     - Show in catalog
     - Feature on homepage
     - Allow search engines
   
   Meta key mapping:
   ```php
   '_tutorial_type' => 'guided|self-paced|workshop'
   '_tutorial_access' => 'public|members-only|restricted'
   '_tutorial_allow_enrollment' => bool
   '_tutorial_require_approval' => bool
   '_tutorial_enrollment_limit' => int
   '_tutorial_enrollment_deadline' => 'Y-m-d' format
   '_tutorial_completion_requires_all_steps' => bool
   '_tutorial_completion_requires_quiz' => bool
   '_tutorial_generate_certificate' => bool
   '_tutorial_show_in_catalog' => bool
   ```

4. **Implement Form Validation**
   
   Client-side validation (JavaScript):
   - Required field indicators (*)
   - Real-time character counting
   - Numeric range validation
   - Date format validation
   - Show error messages inline
   
   Server-side validation (PHP):
   ```php
   private function validate_tutorial_meta( array $meta_data ): array|WP_Error {
       $errors = array();
       
       // Short description required
       if ( empty( $meta_data['short_description'] ) ) {
           $errors[] = __( 'Short description is required.', 'aiddata-lms' );
       }
       
       // Duration must be positive
       if ( isset( $meta_data['duration'] ) && $meta_data['duration'] < 0 ) {
           $errors[] = __( 'Duration must be a positive number.', 'aiddata-lms' );
       }
       
       // Validate enrollment deadline date format
       if ( ! empty( $meta_data['enrollment_deadline'] ) ) {
           $date = DateTime::createFromFormat( 'Y-m-d', $meta_data['enrollment_deadline'] );
           if ( ! $date ) {
               $errors[] = __( 'Invalid enrollment deadline date.', 'aiddata-lms' );
           }
       }
       
       if ( ! empty( $errors ) ) {
           return new WP_Error( 'validation_failed', implode( ' ', $errors ), $errors );
       }
       
       return $meta_data;
   }
   ```

5. **Create Admin JavaScript for Meta Boxes** (`assets/js/admin/tutorial-meta-boxes.js`)
   
   Features:
   - Character counter for short description
   - Date picker for enrollment deadline
   - Prerequisites AJAX search
   - Learning outcomes repeater field
   - Drag-drop reordering for outcomes
   - Form validation feedback
   
   Implementation:
   ```javascript
   (function($) {
       'use strict';
       
       $(document).ready(function() {
           // Character counter
           const $shortDesc = $('#tutorial_short_description');
           const $charCount = $('#char-count');
           
           $shortDesc.on('input', function() {
               const length = $(this).val().length;
               $charCount.text(length);
               
               if (length > 250) {
                   $charCount.addClass('over-limit');
               } else {
                   $charCount.removeClass('over-limit');
               }
           });
           
           // Initialize date picker
           $('.aiddata-date-picker').datepicker({
               dateFormat: 'yy-mm-dd',
               minDate: 0
           });
           
           // Learning outcomes repeater
           initOutcomesRepeater();
           
           // Prerequisites AJAX search
           initPrerequisitesSearch();
       });
       
       // Additional helper functions...
   })(jQuery);
   ```
   
   File location: `/assets/js/admin/tutorial-meta-boxes.js`

6. **Create Admin CSS** (`assets/css/admin/tutorial-meta-boxes.css`)
   
   Styling requirements:
   - Clean, professional interface
   - Consistent spacing and typography
   - Clear visual hierarchy
   - Accessible form elements
   - Responsive design for smaller screens
   - Loading states for AJAX operations
   - Error/success message styling
   
   File location: `/assets/css/admin/tutorial-meta-boxes.css`

7. **Register Assets with WordPress**
   
   In core plugin class or admin assets class:
   ```php
   public function enqueue_admin_assets( string $hook ): void {
       // Only load on tutorial edit screen
       if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
           return;
       }
       
       $screen = get_current_screen();
       if ( 'aiddata_tutorial' !== $screen->post_type ) {
           return;
       }
       
       // Enqueue JavaScript
       wp_enqueue_script(
           'aiddata-lms-tutorial-meta-boxes',
           AIDDATA_LMS_URL . 'assets/js/admin/tutorial-meta-boxes.js',
           array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ),
           AIDDATA_LMS_VERSION,
           true
       );
       
       // Enqueue CSS
       wp_enqueue_style(
           'aiddata-lms-tutorial-meta-boxes',
           AIDDATA_LMS_URL . 'assets/css/admin/tutorial-meta-boxes.css',
           array( 'wp-jquery-ui-dialog' ),
           AIDDATA_LMS_VERSION
       );
       
       // Localize script with AJAX data
       wp_localize_script(
           'aiddata-lms-tutorial-meta-boxes',
           'aiddataTutorialMeta',
           array(
               'ajaxUrl' => admin_url( 'admin-ajax.php' ),
               'nonce' => wp_create_nonce( 'aiddata-tutorial-meta' ),
               'strings' => array(
                   'addOutcome' => __( 'Add Learning Outcome', 'aiddata-lms' ),
                   'removeOutcome' => __( 'Remove', 'aiddata-lms' ),
                   'searchPrerequisites' => __( 'Search tutorials...', 'aiddata-lms' ),
               ),
           )
       );
   }
   ```

8. **Implement Meta Save Handler with Security**
   
   ```php
   public function save_tutorial_meta( int $post_id ): void {
       // Verify nonce
       if ( ! isset( $_POST['aiddata_tutorial_meta_nonce'] ) ) {
           return;
       }
       
       if ( ! wp_verify_nonce( $_POST['aiddata_tutorial_meta_nonce'], 'aiddata_save_tutorial_meta' ) ) {
           return;
       }
       
       // Check autosave
       if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
           return;
       }
       
       // Check permissions
       if ( ! current_user_can( 'edit_post', $post_id ) ) {
           return;
       }
       
       // Check post type
       if ( 'aiddata_tutorial' !== get_post_type( $post_id ) ) {
           return;
       }
       
       // Collect meta data
       $meta_data = $this->collect_meta_from_post( $_POST );
       
       // Validate
       $validated = $this->validate_tutorial_meta( $meta_data );
       if ( is_wp_error( $validated ) ) {
           // Store errors in transient for display
           set_transient( 'aiddata_tutorial_meta_errors_' . $post_id, $validated->get_error_messages(), 45 );
           return;
       }
       
       // Sanitize
       $sanitized = $this->sanitize_tutorial_meta( $validated );
       
       // Save to post meta
       foreach ( $sanitized as $key => $value ) {
           update_post_meta( $post_id, '_tutorial_' . $key, $value );
       }
       
       // Fire action hook
       do_action( 'aiddata_lms_tutorial_meta_saved', $post_id, $sanitized );
   }
   ```

**Validation Checklist:**
- [ ] Meta boxes registered for tutorial post type only
- [ ] All form fields have proper labels and descriptions
- [ ] Required fields marked with asterisk (*)
- [ ] Client-side validation provides immediate feedback
- [ ] Server-side validation prevents invalid data
- [ ] All inputs properly sanitized
- [ ] Nonce verification on save
- [ ] Capability checks on save
- [ ] Error messages user-friendly and translatable
- [ ] Success feedback provided after save
- [ ] JavaScript uses jQuery (WordPress standard)
- [ ] CSS follows WordPress admin styles
- [ ] Assets enqueued only on tutorial edit screen
- [ ] AJAX endpoints properly secured
- [ ] All strings internationalized

**Expected Outcome:**
- Tutorial edit screen has two new meta boxes
- Basic Information meta box functional with all fields
- Settings meta box functional with all options
- Form validation working (client and server)
- Auto-save compatible
- Data persists correctly
- User-friendly interface
- Ready for step builder integration (Prompt 2)

---

### PROMPT 2: STEP BUILDER INTERFACE

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 2 → Week 6 → Days 4-5 (lines 587-625)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.1 Tutorial Steps
- class-aiddata-lms-tutorial-meta-boxes.php → From Prompt 1
- class-aiddata-lms-tutorial-progress.php → Step counting integration

**Objective:**
Implement the dynamic step builder interface with drag-drop reordering, multiple step types, and content editing capabilities.

**Instructions:**

1. **Create Step Builder Meta Box**
   
   Add to `class-aiddata-lms-tutorial-meta-boxes.php`:
   ```php
   public function render_steps_meta_box( WP_Post $post ): void {
       // Get existing steps
       $steps = get_post_meta( $post->ID, '_tutorial_steps', true );
       if ( ! is_array( $steps ) ) {
           $steps = array();
       }
       
       // Nonce field
       wp_nonce_field( 'aiddata_save_tutorial_steps', 'aiddata_tutorial_steps_nonce' );
       
       include AIDDATA_LMS_PATH . 'includes/admin/views/tutorial-step-builder.php';
   }
   ```
   
   Register meta box:
   ```php
   add_meta_box(
       'aiddata_tutorial_steps',
       __( 'Tutorial Steps', 'aiddata-lms' ),
       array( $this, 'render_steps_meta_box' ),
       'aiddata_tutorial',
       'normal',
       'high'
   );
   ```

2. **Define Step Structure**
   
   Each step is an array with this structure:
   ```php
   array(
       'id' => uniqid(),                // Unique step identifier
       'type' => 'video|text|interactive|resource|quiz',
       'title' => 'Step Title',
       'description' => 'Optional description',
       'content' => array(
           // Type-specific content
       ),
       'required' => true,              // Must complete to progress
       'estimated_time' => 10,          // Minutes
       'order' => 0,                    // Display order
   )
   ```

3. **Create Step Builder View Template** (`includes/admin/views/tutorial-step-builder.php`)
   
   ```php
   <?php
   /**
    * Tutorial Step Builder View
    */
   if ( ! defined( 'ABSPATH' ) ) exit;
   
   $steps = isset( $steps ) ? $steps : array();
   ?>
   
   <div class="aiddata-step-builder">
       <div class="step-builder-header">
           <button type="button" class="button button-primary" id="add-step">
               <span class="dashicons dashicons-plus"></span>
               <?php esc_html_e( 'Add Step', 'aiddata-lms' ); ?>
           </button>
           
           <div class="step-templates">
               <label><?php esc_html_e( 'Quick Add:', 'aiddata-lms' ); ?></label>
               <button type="button" class="button add-step-template" data-type="video">
                   <span class="dashicons dashicons-video-alt3"></span>
                   <?php esc_html_e( 'Video', 'aiddata-lms' ); ?>
               </button>
               <button type="button" class="button add-step-template" data-type="text">
                   <span class="dashicons dashicons-text-page"></span>
                   <?php esc_html_e( 'Text', 'aiddata-lms' ); ?>
               </button>
               <button type="button" class="button add-step-template" data-type="interactive">
                   <span class="dashicons dashicons-welcome-widgets-menus"></span>
                   <?php esc_html_e( 'Interactive', 'aiddata-lms' ); ?>
               </button>
               <button type="button" class="button add-step-template" data-type="resource">
                   <span class="dashicons dashicons-download"></span>
                   <?php esc_html_e( 'Resource', 'aiddata-lms' ); ?>
               </button>
               <button type="button" class="button add-step-template" data-type="quiz">
                   <span class="dashicons dashicons-list-view"></span>
                   <?php esc_html_e( 'Quiz', 'aiddata-lms' ); ?>
               </button>
           </div>
       </div>
       
       <div class="step-builder-container">
           <?php if ( empty( $steps ) ) : ?>
               <div class="no-steps-message">
                   <p><?php esc_html_e( 'No steps added yet. Click "Add Step" to begin building your tutorial.', 'aiddata-lms' ); ?></p>
               </div>
           <?php else : ?>
               <div class="steps-list" id="sortable-steps">
                   <?php foreach ( $steps as $index => $step ) : ?>
                       <?php include AIDDATA_LMS_PATH . 'includes/admin/views/step-item.php'; ?>
                   <?php endforeach; ?>
               </div>
           <?php endif; ?>
       </div>
       
       <input type="hidden" name="tutorial_steps" id="tutorial-steps-data" value="<?php echo esc_attr( wp_json_encode( $steps ) ); ?>">
   </div>
   
   <!-- Step Editor Modal -->
   <div id="step-editor-modal" class="aiddata-modal" style="display:none;">
       <div class="modal-content">
           <div class="modal-header">
               <h2 id="step-editor-title"><?php esc_html_e( 'Edit Step', 'aiddata-lms' ); ?></h2>
               <button type="button" class="modal-close">&times;</button>
           </div>
           <div class="modal-body" id="step-editor-body">
               <!-- Step editor form will be loaded here -->
           </div>
           <div class="modal-footer">
               <button type="button" class="button" id="cancel-step-edit">
                   <?php esc_html_e( 'Cancel', 'aiddata-lms' ); ?>
               </button>
               <button type="button" class="button button-primary" id="save-step-edit">
                   <?php esc_html_e( 'Save Step', 'aiddata-lms' ); ?>
               </button>
           </div>
       </div>
   </div>
   ```
   
   File location: `/includes/admin/views/tutorial-step-builder.php`

4. **Create Step Item Template** (`includes/admin/views/step-item.php`)
   
   ```php
   <?php
   /**
    * Single Step Item Template
    */
   if ( ! defined( 'ABSPATH' ) ) exit;
   
   $step_id = isset( $step['id'] ) ? $step['id'] : uniqid();
   $step_type = isset( $step['type'] ) ? $step['type'] : 'text';
   $step_title = isset( $step['title'] ) ? $step['title'] : __( 'Untitled Step', 'aiddata-lms' );
   $step_required = isset( $step['required'] ) && $step['required'];
   $step_time = isset( $step['estimated_time'] ) ? $step['estimated_time'] : 0;
   
   $type_icons = array(
       'video' => 'video-alt3',
       'text' => 'text-page',
       'interactive' => 'welcome-widgets-menus',
       'resource' => 'download',
       'quiz' => 'list-view',
   );
   
   $icon = isset( $type_icons[ $step_type ] ) ? $type_icons[ $step_type ] : 'admin-page';
   ?>
   
   <div class="step-item" data-step-id="<?php echo esc_attr( $step_id ); ?>" data-step-type="<?php echo esc_attr( $step_type ); ?>">
       <div class="step-handle">
           <span class="dashicons dashicons-menu"></span>
       </div>
       
       <div class="step-icon">
           <span class="dashicons dashicons-<?php echo esc_attr( $icon ); ?>"></span>
       </div>
       
       <div class="step-content">
           <div class="step-title">
               <?php echo esc_html( $step_title ); ?>
               <?php if ( $step_required ) : ?>
                   <span class="step-required-badge"><?php esc_html_e( 'Required', 'aiddata-lms' ); ?></span>
               <?php endif; ?>
           </div>
           <div class="step-meta">
               <span class="step-type"><?php echo esc_html( ucfirst( $step_type ) ); ?></span>
               <?php if ( $step_time > 0 ) : ?>
                   <span class="step-time">
                       <span class="dashicons dashicons-clock"></span>
                       <?php printf( esc_html__( '%d min', 'aiddata-lms' ), $step_time ); ?>
                   </span>
               <?php endif; ?>
           </div>
       </div>
       
       <div class="step-actions">
           <button type="button" class="button button-small edit-step" title="<?php esc_attr_e( 'Edit Step', 'aiddata-lms' ); ?>">
               <span class="dashicons dashicons-edit"></span>
           </button>
           <button type="button" class="button button-small duplicate-step" title="<?php esc_attr_e( 'Duplicate Step', 'aiddata-lms' ); ?>">
               <span class="dashicons dashicons-admin-page"></span>
           </button>
           <button type="button" class="button button-small delete-step" title="<?php esc_attr_e( 'Delete Step', 'aiddata-lms' ); ?>">
               <span class="dashicons dashicons-trash"></span>
           </button>
       </div>
   </div>
   ```
   
   File location: `/includes/admin/views/step-item.php`

5. **Implement Video Step Configuration**
   
   Video step content structure:
   ```php
   array(
       'platform' => 'panopto|youtube|vimeo|html5',
       'video_url' => 'https://...',
       'video_id' => 'extracted_id',
       'thumbnail_url' => 'https://...',
       'duration' => 300,              // Seconds
       'autoplay' => false,
       'completion_threshold' => 90,   // Percentage
       'description' => 'Video description',
       'transcript' => 'Optional transcript',
   )
   ```
   
   Platform detection:
   ```php
   private function detect_video_platform( string $url ): string {
       if ( strpos( $url, 'panopto' ) !== false ) {
           return 'panopto';
       } elseif ( strpos( $url, 'youtube.com' ) !== false || strpos( $url, 'youtu.be' ) !== false ) {
           return 'youtube';
       } elseif ( strpos( $url, 'vimeo.com' ) !== false ) {
           return 'vimeo';
       } else {
           return 'html5';
       }
   }
   
   private function extract_video_id( string $url, string $platform ): ?string {
       switch ( $platform ) {
           case 'youtube':
               preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches );
               return isset( $matches[1] ) ? $matches[1] : null;
           
           case 'vimeo':
               preg_match( '/vimeo\.com\/(\d+)/', $url, $matches );
               return isset( $matches[1] ) ? $matches[1] : null;
           
           case 'panopto':
               // Extract Panopto session ID
               preg_match( '/Viewer\.aspx\?id=([a-f0-9-]+)/', $url, $matches );
               return isset( $matches[1] ) ? $matches[1] : null;
           
           default:
               return null;
       }
   }
   ```

6. **Implement Text Step Configuration**
   
   Text step content structure:
   ```php
   array(
       'content' => 'HTML content',    // wp_kses_post()
       'format' => 'html|markdown',
       'attachments' => array(),       // Media IDs
       'allow_comments' => false,
   )
   ```
   
   Use WordPress editor (wp_editor):
   ```php
   wp_editor(
       $content,
       'step_text_content',
       array(
           'textarea_name' => 'step_content[text]',
           'media_buttons' => true,
           'teeny' => false,
           'textarea_rows' => 10,
       )
   );
   ```

7. **Implement Interactive Step Configuration**
   
   Interactive step content structure:
   ```php
   array(
       'interaction_type' => 'iframe|embed|simulation',
       'embed_code' => '<iframe>...',
       'url' => 'https://...',
       'height' => 600,
       'instructions' => 'How to complete this step',
       'completion_trigger' => 'manual|auto',
   )
   ```

8. **Implement Resource Download Step**
   
   Resource step content structure:
   ```php
   array(
       'resources' => array(
           array(
               'file_id' => 123,        // WordPress attachment ID
               'title' => 'Resource Name',
               'description' => 'File description',
               'file_type' => 'pdf|doc|zip',
               'file_size' => 1024,     // Bytes
               'download_url' => 'https://...',
           ),
       ),
       'instructions' => 'Instructions for using resources',
       'required_downloads' => array( 123, 456 ), // Which files must be downloaded
   )
   ```

9. **Create Step Builder JavaScript** (`assets/js/admin/tutorial-step-builder.js`)
   
   Core functionality:
   ```javascript
   (function($) {
       'use strict';
       
       const StepBuilder = {
           steps: [],
           currentStep: null,
           
           init: function() {
               this.loadSteps();
               this.bindEvents();
               this.initSortable();
           },
           
           loadSteps: function() {
               const stepsData = $('#tutorial-steps-data').val();
               if (stepsData) {
                   this.steps = JSON.parse(stepsData);
               }
           },
           
           bindEvents: function() {
               $('#add-step').on('click', () => this.addStep());
               $('.add-step-template').on('click', (e) => this.addStepFromTemplate(e));
               $(document).on('click', '.edit-step', (e) => this.editStep(e));
               $(document).on('click', '.duplicate-step', (e) => this.duplicateStep(e));
               $(document).on('click', '.delete-step', (e) => this.deleteStep(e));
               $('#save-step-edit').on('click', () => this.saveStepEdit());
               $('#cancel-step-edit').on('click', () => this.closeModal());
               $('.modal-close').on('click', () => this.closeModal());
           },
           
           initSortable: function() {
               $('#sortable-steps').sortable({
                   handle: '.step-handle',
                   placeholder: 'step-placeholder',
                   update: () => this.updateStepOrder()
               });
           },
           
           addStep: function(type = 'text') {
               const step = {
                   id: this.generateId(),
                   type: type,
                   title: 'New Step',
                   description: '',
                   content: {},
                   required: true,
                   estimated_time: 0,
                   order: this.steps.length
               };
               
               this.steps.push(step);
               this.renderStep(step);
               this.updateHiddenField();
               this.editStep(null, step.id);
           },
           
           addStepFromTemplate: function(e) {
               const type = $(e.currentTarget).data('type');
               this.addStep(type);
           },
           
           editStep: function(e, stepId = null) {
               if (e) {
                   stepId = $(e.currentTarget).closest('.step-item').data('step-id');
               }
               
               const step = this.steps.find(s => s.id === stepId);
               if (!step) return;
               
               this.currentStep = step;
               this.loadStepEditor(step);
               this.showModal();
           },
           
           loadStepEditor: function(step) {
               // Load type-specific editor
               const editorHtml = this.getEditorTemplate(step);
               $('#step-editor-body').html(editorHtml);
               
               // Initialize editor components
               this.initEditorComponents(step);
           },
           
           getEditorTemplate: function(step) {
               // Return HTML for step editor based on type
               const templates = {
                   video: this.getVideoEditorTemplate(step),
                   text: this.getTextEditorTemplate(step),
                   interactive: this.getInteractiveEditorTemplate(step),
                   resource: this.getResourceEditorTemplate(step),
                   quiz: this.getQuizEditorTemplate(step)
               };
               
               return templates[step.type] || templates.text;
           },
           
           saveStepEdit: function() {
               // Collect form data
               const formData = this.collectStepData();
               
               // Update current step
               Object.assign(this.currentStep, formData);
               
               // Re-render step in list
               this.renderStep(this.currentStep, true);
               
               // Update hidden field
               this.updateHiddenField();
               
               // Close modal
               this.closeModal();
           },
           
           duplicateStep: function(e) {
               const stepId = $(e.currentTarget).closest('.step-item').data('step-id');
               const step = this.steps.find(s => s.id === stepId);
               
               if (step) {
                   const duplicate = JSON.parse(JSON.stringify(step));
                   duplicate.id = this.generateId();
                   duplicate.title += ' (Copy)';
                   
                   this.steps.push(duplicate);
                   this.renderStep(duplicate);
                   this.updateHiddenField();
               }
           },
           
           deleteStep: function(e) {
               if (!confirm(aiddataStepBuilder.strings.confirmDelete)) {
                   return;
               }
               
               const $stepItem = $(e.currentTarget).closest('.step-item');
               const stepId = $stepItem.data('step-id');
               
               this.steps = this.steps.filter(s => s.id !== stepId);
               $stepItem.remove();
               this.updateHiddenField();
               
               if (this.steps.length === 0) {
                   this.showNoStepsMessage();
               }
           },
           
           updateStepOrder: function() {
               $('.step-item').each((index, element) => {
                   const stepId = $(element).data('step-id');
                   const step = this.steps.find(s => s.id === stepId);
                   if (step) {
                       step.order = index;
                   }
               });
               
               this.steps.sort((a, b) => a.order - b.order);
               this.updateHiddenField();
           },
           
           updateHiddenField: function() {
               $('#tutorial-steps-data').val(JSON.stringify(this.steps));
           },
           
           renderStep: function(step, replace = false) {
               // Render step HTML
               const $stepHtml = this.buildStepHtml(step);
               
               if (replace) {
                   $(`.step-item[data-step-id="${step.id}"]`).replaceWith($stepHtml);
               } else {
                   $('.no-steps-message').remove();
                   if ($('#sortable-steps').length === 0) {
                       $('.step-builder-container').html('<div class="steps-list" id="sortable-steps"></div>');
                       this.initSortable();
                   }
                   $('#sortable-steps').append($stepHtml);
               }
           },
           
           buildStepHtml: function(step) {
               // Build step item HTML (matches step-item.php structure)
               // ... implementation
           },
           
           showModal: function() {
               $('#step-editor-modal').fadeIn(200);
           },
           
           closeModal: function() {
               $('#step-editor-modal').fadeOut(200);
               this.currentStep = null;
           },
           
           generateId: function() {
               return 'step_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
           }
       };
       
       $(document).ready(function() {
           StepBuilder.init();
       });
       
   })(jQuery);
   ```
   
   File location: `/assets/js/admin/tutorial-step-builder.js`

10. **Implement Step Save Handler**
    
    Add to `class-aiddata-lms-tutorial-meta-boxes.php`:
    ```php
    private function save_tutorial_steps( int $post_id ): void {
        if ( ! isset( $_POST['tutorial_steps'] ) ) {
            return;
        }
        
        // Decode JSON
        $steps = json_decode( wp_unslash( $_POST['tutorial_steps'] ), true );
        
        if ( ! is_array( $steps ) ) {
            return;
        }
        
        // Sanitize each step
        $sanitized_steps = array_map( array( $this, 'sanitize_step' ), $steps );
        
        // Save to post meta
        update_post_meta( $post_id, '_tutorial_steps', $sanitized_steps );
        
        // Update step count for quick reference
        update_post_meta( $post_id, '_tutorial_step_count', count( $sanitized_steps ) );
        
        // Fire action
        do_action( 'aiddata_lms_tutorial_steps_saved', $post_id, $sanitized_steps );
    }
    
    private function sanitize_step( array $step ): array {
        return array(
            'id' => sanitize_text_field( $step['id'] ?? '' ),
            'type' => sanitize_text_field( $step['type'] ?? 'text' ),
            'title' => sanitize_text_field( $step['title'] ?? '' ),
            'description' => sanitize_textarea_field( $step['description'] ?? '' ),
            'content' => $this->sanitize_step_content( $step['content'] ?? array(), $step['type'] ?? 'text' ),
            'required' => (bool) ( $step['required'] ?? true ),
            'estimated_time' => absint( $step['estimated_time'] ?? 0 ),
            'order' => absint( $step['order'] ?? 0 ),
        );
    }
    
    private function sanitize_step_content( $content, string $type ): array {
        if ( ! is_array( $content ) ) {
            $content = array();
        }
        
        switch ( $type ) {
            case 'video':
                return array(
                    'platform' => sanitize_text_field( $content['platform'] ?? '' ),
                    'video_url' => esc_url_raw( $content['video_url'] ?? '' ),
                    'video_id' => sanitize_text_field( $content['video_id'] ?? '' ),
                    'thumbnail_url' => esc_url_raw( $content['thumbnail_url'] ?? '' ),
                    'duration' => absint( $content['duration'] ?? 0 ),
                    'autoplay' => (bool) ( $content['autoplay'] ?? false ),
                    'completion_threshold' => absint( $content['completion_threshold'] ?? 90 ),
                    'description' => wp_kses_post( $content['description'] ?? '' ),
                    'transcript' => wp_kses_post( $content['transcript'] ?? '' ),
                );
            
            case 'text':
                return array(
                    'content' => wp_kses_post( $content['content'] ?? '' ),
                    'format' => sanitize_text_field( $content['format'] ?? 'html' ),
                    'attachments' => array_map( 'absint', $content['attachments'] ?? array() ),
                    'allow_comments' => (bool) ( $content['allow_comments'] ?? false ),
                );
            
            // Additional cases for other step types...
            
            default:
                return $content;
        }
    }
    ```

**Validation Checklist:**
- [ ] Step builder meta box registered
- [ ] Drag-drop reordering works smoothly
- [ ] All step types can be added
- [ ] Step editing modal functional
- [ ] Video URL detection works for all platforms
- [ ] Text editor (wp_editor) functional
- [ ] File uploads work for resources
- [ ] Step duplication works correctly
- [ ] Step deletion with confirmation
- [ ] Step order persists on save
- [ ] All step data sanitized
- [ ] JavaScript errors handled gracefully
- [ ] UI responsive and accessible
- [ ] Loading states shown during operations
- [ ] Compatible with WordPress autosave
- [ ] Works with Gutenberg editor active

**Expected Outcome:**
- Functional step builder interface
- Multiple step types supported
- Drag-drop reordering works
- Step data persists correctly
- User-friendly modal editor
- Ready for tutorial management features (Week 7)
- Integration with progress tracking system

---

## WEEK 7: TUTORIAL MANAGEMENT

---

### PROMPT 3: ADMIN LIST INTERFACE & BULK ACTIONS

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 2 → Week 7 → Days 1-2 (lines 627-663)
- class-aiddata-lms-post-types.php → Tutorial post type configuration
- class-aiddata-lms-tutorial-enrollment.php → Enrollment counts
- class-aiddata-lms-tutorial-progress.php → Completion statistics

**Objective:**
Enhance the admin tutorials list with custom columns, filters, bulk actions, and quick edit functionality for efficient tutorial management.

**Instructions:**

1. **Extend Post Types Class for Custom Columns**
   
   Add to `class-aiddata-lms-post-types.php`:
   ```php
   public function __construct() {
       add_action( 'init', array( $this, 'register_post_types' ) );
       
       // Admin columns
       add_filter( 'manage_aiddata_tutorial_posts_columns', array( $this, 'add_tutorial_columns' ) );
       add_action( 'manage_aiddata_tutorial_posts_custom_column', array( $this, 'render_tutorial_column' ), 10, 2 );
       add_filter( 'manage_edit-aiddata_tutorial_sortable_columns', array( $this, 'sortable_tutorial_columns' ) );
       
       // Bulk actions
       add_filter( 'bulk_actions-edit-aiddata_tutorial', array( $this, 'add_bulk_actions' ) );
       add_filter( 'handle_bulk_actions-edit-aiddata_tutorial', array( $this, 'handle_bulk_actions' ), 10, 3 );
       
       // Quick edit
       add_action( 'quick_edit_custom_box', array( $this, 'add_quick_edit_fields' ), 10, 2 );
       add_action( 'save_post_aiddata_tutorial', array( $this, 'save_quick_edit_data' ) );
       
       // Filters
       add_action( 'restrict_manage_posts', array( $this, 'add_admin_filters' ) );
       add_filter( 'parse_query', array( $this, 'filter_tutorials_query' ) );
   }
   ```

2. **Implement Custom Admin Columns**
   
   ```php
   public function add_tutorial_columns( array $columns ): array {
       // Remove default date column
       $date = $columns['date'];
       unset( $columns['date'] );
       
       // Add custom columns
       $columns['thumbnail'] = __( 'Thumbnail', 'aiddata-lms' );
       $columns['steps'] = __( 'Steps', 'aiddata-lms' );
       $columns['enrollments'] = __( 'Enrollments', 'aiddata-lms' );
       $columns['active'] = __( 'Active', 'aiddata-lms' );
       $columns['completion_rate'] = __( 'Completion', 'aiddata-lms' );
       $columns['difficulty'] = __( 'Difficulty', 'aiddata-lms' );
       $columns['date'] = $date; // Re-add date at end
       
       return $columns;
   }
   
   public function render_tutorial_column( string $column, int $post_id ): void {
       switch ( $column ) {
           case 'thumbnail':
               if ( has_post_thumbnail( $post_id ) ) {
                   echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
               } else {
                   echo '<span class="dashicons dashicons-format-image"></span>';
               }
               break;
           
           case 'steps':
               $step_count = get_post_meta( $post_id, '_tutorial_step_count', true );
               if ( $step_count ) {
                   printf(
                       '<span class="step-count">%d</span>',
                       absint( $step_count )
                   );
               } else {
                   echo '<span class="dashicons dashicons-minus"></span>';
               }
               break;
           
           case 'enrollments':
               $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
               $total = $enrollment_manager->get_enrollment_count( $post_id );
               printf(
                   '<a href="%s">%d</a>',
                   esc_url( admin_url( 'admin.php?page=aiddata-lms-enrollments&tutorial_id=' . $post_id ) ),
                   $total
               );
               break;
           
           case 'active':
               $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
               $active = $enrollment_manager->get_enrollment_count( $post_id, 'active' );
               printf( '<span class="active-count">%d</span>', $active );
               break;
           
           case 'completion_rate':
               $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
               $total = $enrollment_manager->get_enrollment_count( $post_id );
               $completed = $enrollment_manager->get_enrollment_count( $post_id, 'completed' );
               
               if ( $total > 0 ) {
                   $rate = round( ( $completed / $total ) * 100 );
                   $color = $this->get_completion_color( $rate );
                   printf(
                       '<span class="completion-rate" style="color: %s;">%d%%</span>',
                       esc_attr( $color ),
                       $rate
                   );
               } else {
                   echo '<span class="dashicons dashicons-minus"></span>';
               }
               break;
           
           case 'difficulty':
               $terms = get_the_terms( $post_id, 'aiddata_tutorial_difficulty' );
               if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                   $term = array_shift( $terms );
                   printf(
                       '<a href="%s">%s</a>',
                       esc_url( add_query_arg( array( 'aiddata_tutorial_difficulty' => $term->slug ), 'edit.php?post_type=aiddata_tutorial' ) ),
                       esc_html( $term->name )
                   );
               } else {
                   echo '—';
               }
               break;
       }
   }
   
   private function get_completion_color( float $rate ): string {
       if ( $rate >= 75 ) {
           return '#46b450'; // Green
       } elseif ( $rate >= 50 ) {
           return '#ffb900'; // Yellow
       } else {
           return '#dc3232'; // Red
       }
   }
   ```

3. **Make Columns Sortable**
   
   ```php
   public function sortable_tutorial_columns( array $columns ): array {
       $columns['enrollments'] = 'enrollments';
       $columns['completion_rate'] = 'completion_rate';
       $columns['steps'] = 'steps';
       
       return $columns;
   }
   ```
   
   Handle sorting in query modification (see step 5).

4. **Add Bulk Actions**
   
   ```php
   public function add_bulk_actions( array $actions ): array {
       $actions['duplicate'] = __( 'Duplicate', 'aiddata-lms' );
       $actions['export_data'] = __( 'Export Data', 'aiddata-lms' );
       $actions['toggle_enrollment'] = __( 'Toggle Enrollment', 'aiddata-lms' );
       
       return $actions;
   }
   
   public function handle_bulk_actions( string $redirect_to, string $action, array $post_ids ): string {
       if ( empty( $post_ids ) ) {
           return $redirect_to;
       }
       
       switch ( $action ) {
           case 'duplicate':
               $count = $this->duplicate_tutorials( $post_ids );
               $redirect_to = add_query_arg( 'duplicated', $count, $redirect_to );
               break;
           
           case 'export_data':
               $this->export_tutorials_data( $post_ids );
               // Don't redirect for download
               return $redirect_to;
           
           case 'toggle_enrollment':
               $count = $this->toggle_enrollment_status( $post_ids );
               $redirect_to = add_query_arg( 'enrollment_toggled', $count, $redirect_to );
               break;
       }
       
       return $redirect_to;
   }
   
   private function duplicate_tutorials( array $post_ids ): int {
       $count = 0;
       
       foreach ( $post_ids as $post_id ) {
           $post = get_post( $post_id );
           if ( ! $post || $post->post_type !== 'aiddata_tutorial' ) {
               continue;
           }
           
           // Create duplicate
           $new_post = array(
               'post_title' => $post->post_title . ' (Copy)',
               'post_content' => $post->post_content,
               'post_status' => 'draft',
               'post_type' => 'aiddata_tutorial',
               'post_author' => get_current_user_id(),
           );
           
           $new_id = wp_insert_post( $new_post );
           
           if ( ! is_wp_error( $new_id ) ) {
               // Duplicate meta data
               $this->duplicate_post_meta( $post_id, $new_id );
               
               // Duplicate taxonomies
               $this->duplicate_post_taxonomies( $post_id, $new_id );
               
               $count++;
           }
       }
       
       return $count;
   }
   
   private function duplicate_post_meta( int $source_id, int $target_id ): void {
       $meta_keys = array(
           '_tutorial_short_description',
           '_tutorial_full_description',
           '_tutorial_duration',
           '_tutorial_steps',
           '_tutorial_type',
           '_tutorial_access',
           // ... all tutorial meta keys
       );
       
       foreach ( $meta_keys as $key ) {
           $value = get_post_meta( $source_id, $key, true );
           if ( $value ) {
               update_post_meta( $target_id, $key, $value );
           }
       }
   }
   
   private function duplicate_post_taxonomies( int $source_id, int $target_id ): void {
       $taxonomies = array( 'aiddata_tutorial_cat', 'aiddata_tutorial_tag', 'aiddata_tutorial_difficulty' );
       
       foreach ( $taxonomies as $taxonomy ) {
           $terms = wp_get_post_terms( $source_id, $taxonomy, array( 'fields' => 'ids' ) );
           if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
               wp_set_post_terms( $target_id, $terms, $taxonomy );
           }
       }
   }
   
   private function export_tutorials_data( array $post_ids ): void {
       // Generate CSV export
       $filename = 'tutorials-export-' . gmdate( 'Y-m-d-His' ) . '.csv';
       
       header( 'Content-Type: text/csv; charset=utf-8' );
       header( 'Content-Disposition: attachment; filename=' . $filename );
       
       $output = fopen( 'php://output', 'w' );
       
       // CSV header
       fputcsv( $output, array(
           'ID',
           'Title',
           'Status',
           'Steps',
           'Duration',
           'Enrollments',
           'Active',
           'Completed',
           'Completion Rate',
           'Created',
       ) );
       
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       
       foreach ( $post_ids as $post_id ) {
           $post = get_post( $post_id );
           if ( ! $post ) {
               continue;
           }
           
           $total = $enrollment_manager->get_enrollment_count( $post_id );
           $active = $enrollment_manager->get_enrollment_count( $post_id, 'active' );
           $completed = $enrollment_manager->get_enrollment_count( $post_id, 'completed' );
           $rate = $total > 0 ? round( ( $completed / $total ) * 100, 2 ) : 0;
           
           fputcsv( $output, array(
               $post->ID,
               $post->post_title,
               $post->post_status,
               get_post_meta( $post->ID, '_tutorial_step_count', true ),
               get_post_meta( $post->ID, '_tutorial_duration', true ),
               $total,
               $active,
               $completed,
               $rate . '%',
               $post->post_date,
           ) );
       }
       
       fclose( $output );
       exit;
   }
   
   private function toggle_enrollment_status( array $post_ids ): int {
       $count = 0;
       
       foreach ( $post_ids as $post_id ) {
           $current = get_post_meta( $post_id, '_tutorial_allow_enrollment', true );
           $new_value = ! $current;
           update_post_meta( $post_id, '_tutorial_allow_enrollment', $new_value );
           $count++;
       }
       
       return $count;
   }
   ```

5. **Add Admin Filters**
   
   ```php
   public function add_admin_filters(): void {
       global $typenow;
       
       if ( 'aiddata_tutorial' !== $typenow ) {
           return;
       }
       
       // Difficulty filter
       wp_dropdown_categories( array(
           'show_option_all' => __( 'All Difficulties', 'aiddata-lms' ),
           'taxonomy' => 'aiddata_tutorial_difficulty',
           'name' => 'aiddata_tutorial_difficulty',
           'selected' => isset( $_GET['aiddata_tutorial_difficulty'] ) ? $_GET['aiddata_tutorial_difficulty'] : '',
           'hierarchical' => true,
           'hide_empty' => false,
           'value_field' => 'slug',
       ) );
       
       // Enrollment status filter
       $enrollment_status = isset( $_GET['enrollment_status'] ) ? $_GET['enrollment_status'] : '';
       ?>
       <select name="enrollment_status">
           <option value=""><?php esc_html_e( 'All Enrollment Status', 'aiddata-lms' ); ?></option>
           <option value="open" <?php selected( $enrollment_status, 'open' ); ?>><?php esc_html_e( 'Open', 'aiddata-lms' ); ?></option>
           <option value="closed" <?php selected( $enrollment_status, 'closed' ); ?>><?php esc_html_e( 'Closed', 'aiddata-lms' ); ?></option>
       </select>
       <?php
       
       // Steps count filter
       $steps_filter = isset( $_GET['steps_filter'] ) ? $_GET['steps_filter'] : '';
       ?>
       <select name="steps_filter">
           <option value=""><?php esc_html_e( 'All Step Counts', 'aiddata-lms' ); ?></option>
           <option value="empty" <?php selected( $steps_filter, 'empty' ); ?>><?php esc_html_e( 'No Steps', 'aiddata-lms' ); ?></option>
           <option value="1-5" <?php selected( $steps_filter, '1-5' ); ?>><?php esc_html_e( '1-5 Steps', 'aiddata-lms' ); ?></option>
           <option value="6-10" <?php selected( $steps_filter, '6-10' ); ?>><?php esc_html_e( '6-10 Steps', 'aiddata-lms' ); ?></option>
           <option value="11+" <?php selected( $steps_filter, '11+' ); ?>><?php esc_html_e( '11+ Steps', 'aiddata-lms' ); ?></option>
       </select>
       <?php
   }
   
   public function filter_tutorials_query( WP_Query $query ): void {
       if ( ! is_admin() || ! $query->is_main_query() ) {
           return;
       }
       
       if ( 'aiddata_tutorial' !== $query->get( 'post_type' ) ) {
           return;
       }
       
       $meta_query = array();
       
       // Enrollment status filter
       if ( ! empty( $_GET['enrollment_status'] ) ) {
           $status = $_GET['enrollment_status'];
           $meta_query[] = array(
               'key' => '_tutorial_allow_enrollment',
               'value' => ( 'open' === $status ) ? '1' : '0',
               'compare' => '=',
           );
       }
       
       // Steps filter
       if ( ! empty( $_GET['steps_filter'] ) ) {
           $steps = $_GET['steps_filter'];
           
           if ( 'empty' === $steps ) {
               $meta_query[] = array(
                   'key' => '_tutorial_step_count',
                   'compare' => 'NOT EXISTS',
               );
           } elseif ( '1-5' === $steps ) {
               $meta_query[] = array(
                   'key' => '_tutorial_step_count',
                   'value' => array( 1, 5 ),
                   'compare' => 'BETWEEN',
                   'type' => 'NUMERIC',
               );
           } elseif ( '6-10' === $steps ) {
               $meta_query[] = array(
                   'key' => '_tutorial_step_count',
                   'value' => array( 6, 10 ),
                   'compare' => 'BETWEEN',
                   'type' => 'NUMERIC',
               );
           } elseif ( '11+' === $steps ) {
               $meta_query[] = array(
                   'key' => '_tutorial_step_count',
                   'value' => 10,
                   'compare' => '>',
                   'type' => 'NUMERIC',
               );
           }
       }
       
       if ( ! empty( $meta_query ) ) {
           $query->set( 'meta_query', $meta_query );
       }
   }
   ```

6. **Implement Quick Edit Fields**
   
   ```php
   public function add_quick_edit_fields( string $column_name, string $post_type ): void {
       if ( 'aiddata_tutorial' !== $post_type ) {
           return;
       }
       
       if ( 'title' === $column_name ) {
           ?>
           <fieldset class="inline-edit-col-right">
               <div class="inline-edit-col">
                   <label>
                       <span class="title"><?php esc_html_e( 'Duration (minutes)', 'aiddata-lms' ); ?></span>
                       <input type="number" name="tutorial_duration" min="0">
                   </label>
                   
                   <label>
                       <span class="title"><?php esc_html_e( 'Enrollment Limit', 'aiddata-lms' ); ?></span>
                       <input type="number" name="tutorial_enrollment_limit" min="0">
                   </label>
                   
                   <label>
                       <input type="checkbox" name="tutorial_allow_enrollment" value="1">
                       <span class="checkbox-title"><?php esc_html_e( 'Allow Enrollment', 'aiddata-lms' ); ?></span>
                   </label>
                   
                   <label>
                       <input type="checkbox" name="tutorial_show_in_catalog" value="1">
                       <span class="checkbox-title"><?php esc_html_e( 'Show in Catalog', 'aiddata-lms' ); ?></span>
                   </label>
               </div>
           </fieldset>
           <?php
           wp_nonce_field( 'aiddata_quick_edit', 'aiddata_quick_edit_nonce' );
       }
   }
   
   public function save_quick_edit_data( int $post_id ): void {
       // Verify nonce
       if ( ! isset( $_POST['aiddata_quick_edit_nonce'] ) ) {
           return;
       }
       
       if ( ! wp_verify_nonce( $_POST['aiddata_quick_edit_nonce'], 'aiddata_quick_edit' ) ) {
           return;
       }
       
       // Check autosave
       if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
           return;
       }
       
       // Check permissions
       if ( ! current_user_can( 'edit_post', $post_id ) ) {
           return;
       }
       
       // Save duration
       if ( isset( $_POST['tutorial_duration'] ) ) {
           update_post_meta( $post_id, '_tutorial_duration', absint( $_POST['tutorial_duration'] ) );
       }
       
       // Save enrollment limit
       if ( isset( $_POST['tutorial_enrollment_limit'] ) ) {
           update_post_meta( $post_id, '_tutorial_enrollment_limit', absint( $_POST['tutorial_enrollment_limit'] ) );
       }
       
       // Save checkboxes
       update_post_meta( $post_id, '_tutorial_allow_enrollment', isset( $_POST['tutorial_allow_enrollment'] ) ? 1 : 0 );
       update_post_meta( $post_id, '_tutorial_show_in_catalog', isset( $_POST['tutorial_show_in_catalog'] ) ? 1 : 0 );
   }
   ```

7. **Add Admin Notices for Bulk Actions**
   
   ```php
   public function __construct() {
       // ... existing hooks
       add_action( 'admin_notices', array( $this, 'bulk_action_notices' ) );
   }
   
   public function bulk_action_notices(): void {
       global $pagenow, $typenow;
       
       if ( 'edit.php' !== $pagenow || 'aiddata_tutorial' !== $typenow ) {
           return;
       }
       
       // Duplication notice
       if ( ! empty( $_GET['duplicated'] ) ) {
           $count = absint( $_GET['duplicated'] );
           printf(
               '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
               sprintf(
                   /* translators: %d: number of tutorials */
                   esc_html( _n( '%d tutorial duplicated.', '%d tutorials duplicated.', $count, 'aiddata-lms' ) ),
                   $count
               )
           );
       }
       
       // Enrollment toggle notice
       if ( ! empty( $_GET['enrollment_toggled'] ) ) {
           $count = absint( $_GET['enrollment_toggled'] );
           printf(
               '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
               sprintf(
                   /* translators: %d: number of tutorials */
                   esc_html( _n( 'Enrollment status toggled for %d tutorial.', 'Enrollment status toggled for %d tutorials.', $count, 'aiddata-lms' ) ),
                   $count
               )
           );
       }
   }
   ```

8. **Add CSS for Admin List** (`assets/css/admin/tutorial-list.css`)
   
   ```css
   /* Thumbnail column */
   .column-thumbnail img {
       border-radius: 4px;
       box-shadow: 0 1px 3px rgba(0,0,0,0.1);
   }
   
   /* Step count badge */
   .column-steps .step-count {
       display: inline-block;
       background: #2271b1;
       color: #fff;
       padding: 2px 8px;
       border-radius: 10px;
       font-size: 12px;
       font-weight: 500;
   }
   
   /* Enrollment counts */
   .column-enrollments a,
   .column-active .active-count {
       font-weight: 600;
       font-size: 14px;
   }
   
   /* Completion rate colors */
   .column-completion_rate .completion-rate {
       font-weight: 600;
       font-size: 14px;
   }
   
   /* Empty indicators */
   .column-steps .dashicons-minus,
   .column-completion_rate .dashicons-minus {
       color: #ddd;
   }
   
   /* Quick edit custom fields */
   fieldset.inline-edit-col-right .inline-edit-col {
       padding: 0 12px;
   }
   
   fieldset.inline-edit-col-right label {
       display: block;
       margin: 8px 0;
   }
   
   fieldset.inline-edit-col-right .title {
       display: block;
       margin-bottom: 4px;
       font-weight: 600;
   }
   
   fieldset.inline-edit-col-right input[type="number"] {
       width: 100%;
   }
   ```
   
   File location: `/assets/css/admin/tutorial-list.css`

**Validation Checklist:**
- [ ] Custom columns display correctly
- [ ] Enrollment counts accurate
- [ ] Completion rate calculated correctly
- [ ] Columns sortable (where applicable)
- [ ] Bulk duplication works
- [ ] Duplicate includes all meta and taxonomies
- [ ] CSV export functional
- [ ] Export includes all relevant data
- [ ] Enrollment toggle works
- [ ] Filters show correct results
- [ ] Quick edit saves data
- [ ] Admin notices display correctly
- [ ] No JavaScript errors
- [ ] Responsive on smaller screens
- [ ] Accessible to screen readers

**Expected Outcome:**
- Enhanced admin tutorials list
- Multiple custom columns functional
- Bulk actions operational
- Quick edit working
- Filters functional
- Export capability
- Professional, user-friendly interface
- Ready for frontend display (Prompt 4)

---

### PROMPT 4: FRONTEND TUTORIAL ARCHIVE & SINGLE DISPLAY

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 2 → Week 7 → Days 3-5 (lines 665-706)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.4 Frontend Display
- class-aiddata-lms-tutorial-enrollment.php → Enrollment button integration
- assets/js/frontend/enrollment.js → From Phase 1, Prompt 4

**Objective:**
Create beautiful, responsive frontend templates for tutorial archive (listing) and single tutorial pages with enrollment integration.

**Instructions:**

1. **Create Tutorial Archive Template** (`templates/archive-aiddata_tutorial.php`)
   
   Template structure:
   ```php
   <?php
   /**
    * Tutorial Archive Template
    */
   get_header();
   ?>
   
   <div class="aiddata-tutorials-archive">
       <div class="archive-header">
           <h1 class="archive-title">
               <?php
               if ( is_tax() ) {
                   single_term_title();
               } else {
                   esc_html_e( 'All Tutorials', 'aiddata-lms' );
               }
               ?>
           </h1>
           
           <?php if ( term_description() ) : ?>
               <div class="archive-description">
                   <?php echo term_description(); ?>
               </div>
           <?php endif; ?>
       </div>
       
       <div class="archive-filters">
           <?php echo do_shortcode( '[aiddata_tutorial_filters]' ); ?>
       </div>
       
       <div class="archive-content">
           <?php if ( have_posts() ) : ?>
               <div class="tutorials-grid">
                   <?php
                   while ( have_posts() ) :
                       the_post();
                       get_template_part( 'template-parts/content', 'tutorial-card' );
                   endwhile;
                   ?>
               </div>
               
               <?php the_posts_pagination(); ?>
               
           <?php else : ?>
               <div class="no-tutorials">
                   <p><?php esc_html_e( 'No tutorials found.', 'aiddata-lms' ); ?></p>
               </div>
           <?php endif; ?>
       </div>
   </div>
   
   <?php
   get_footer();
   ```
   
   File location: `/templates/archive-aiddata_tutorial.php`

2. **Create Tutorial Card Template** (`templates/template-parts/content-tutorial-card.php`)
   
   ```php
   <?php
   /**
    * Tutorial Card Template Part
    */
   if ( ! defined( 'ABSPATH' ) ) exit;
   
   $tutorial_id = get_the_ID();
   $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
   $is_enrolled = $enrollment_manager->is_user_enrolled( get_current_user_id(), $tutorial_id );
   
   // Get tutorial metadata
   $duration = get_post_meta( $tutorial_id, '_tutorial_duration', true );
   $step_count = get_post_meta( $tutorial_id, '_tutorial_step_count', true );
   $short_desc = get_post_meta( $tutorial_id, '_tutorial_short_description', true );
   
   // Get difficulty
   $difficulty_terms = get_the_terms( $tutorial_id, 'aiddata_tutorial_difficulty' );
   $difficulty = ! empty( $difficulty_terms ) ? $difficulty_terms[0]->name : '';
   
   // Get enrollment count
   $enrollment_count = $enrollment_manager->get_enrollment_count( $tutorial_id, 'active' );
   ?>
   
   <article id="tutorial-<?php the_ID(); ?>" <?php post_class( 'tutorial-card' ); ?>>
       <div class="tutorial-card-inner">
           <?php if ( has_post_thumbnail() ) : ?>
               <div class="tutorial-thumbnail">
                   <a href="<?php the_permalink(); ?>">
                       <?php the_post_thumbnail( 'medium_large' ); ?>
                   </a>
                   <?php if ( $is_enrolled ) : ?>
                       <span class="enrolled-badge"><?php esc_html_e( 'Enrolled', 'aiddata-lms' ); ?></span>
                   <?php endif; ?>
               </div>
           <?php endif; ?>
           
           <div class="tutorial-content">
               <div class="tutorial-meta">
                   <?php if ( $difficulty ) : ?>
                       <span class="difficulty difficulty-<?php echo esc_attr( sanitize_title( $difficulty ) ); ?>">
                           <?php echo esc_html( $difficulty ); ?>
                       </span>
                   <?php endif; ?>
                   
                   <?php
                   $categories = get_the_terms( $tutorial_id, 'aiddata_tutorial_cat' );
                   if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                       ?>
                       <span class="category">
                           <?php echo esc_html( $categories[0]->name ); ?>
                       </span>
                   <?php endif; ?>
               </div>
               
               <h2 class="tutorial-title">
                   <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
               </h2>
               
               <?php if ( $short_desc ) : ?>
                   <p class="tutorial-excerpt"><?php echo esc_html( $short_desc ); ?></p>
               <?php endif; ?>
               
               <div class="tutorial-stats">
                   <?php if ( $step_count ) : ?>
                       <span class="stat steps">
                           <span class="dashicons dashicons-list-view"></span>
                           <?php printf( esc_html__( '%d Steps', 'aiddata-lms' ), $step_count ); ?>
                       </span>
                   <?php endif; ?>
                   
                   <?php if ( $duration ) : ?>
                       <span class="stat duration">
                           <span class="dashicons dashicons-clock"></span>
                           <?php printf( esc_html__( '%d min', 'aiddata-lms' ), $duration ); ?>
                       </span>
                   <?php endif; ?>
                   
                   <span class="stat enrollments">
                       <span class="dashicons dashicons-groups"></span>
                       <?php printf( esc_html__( '%d enrolled', 'aiddata-lms' ), $enrollment_count ); ?>
                   </span>
               </div>
               
               <div class="tutorial-actions">
                   <?php if ( $is_enrolled ) : ?>
                       <a href="<?php echo esc_url( get_permalink() . '?action=continue' ); ?>" class="button button-primary">
                           <?php esc_html_e( 'Continue Learning', 'aiddata-lms' ); ?>
                       </a>
                   <?php else : ?>
                       <a href="<?php the_permalink(); ?>" class="button button-secondary">
                           <?php esc_html_e( 'Learn More', 'aiddata-lms' ); ?>
                       </a>
                   <?php endif; ?>
               </div>
           </div>
       </div>
   </article>
   ```
   
   File location: `/templates/template-parts/content-tutorial-card.php`

3. **Create Single Tutorial Template** (`templates/single-aiddata_tutorial.php`)
   
   ```php
   <?php
   /**
    * Single Tutorial Template
    */
   get_header();
   
   while ( have_posts() ) :
       the_post();
       
       $tutorial_id = get_the_ID();
       $user_id = get_current_user_id();
       
       // Get managers
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       $progress_manager = new AidData_LMS_Tutorial_Progress();
       
       // Check enrollment
       $is_enrolled = $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id );
       $enrollment = $enrollment_manager->get_enrollment( $user_id, $tutorial_id );
       
       // Get progress if enrolled
       $progress = $is_enrolled ? $progress_manager->get_progress( $user_id, $tutorial_id ) : null;
       
       // Get metadata
       $short_desc = get_post_meta( $tutorial_id, '_tutorial_short_description', true );
       $full_desc = get_post_meta( $tutorial_id, '_tutorial_full_description', true );
       $duration = get_post_meta( $tutorial_id, '_tutorial_duration', true );
       $outcomes = get_post_meta( $tutorial_id, '_tutorial_outcomes', true );
       $prerequisites_ids = get_post_meta( $tutorial_id, '_tutorial_prerequisites', true );
       $steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );
       
       // Check if user should continue or start
       $action = isset( $_GET['action'] ) ? $_GET['action'] : '';
       if ( $is_enrolled && 'continue' === $action ) {
           // Load active tutorial interface
           include AIDDATA_LMS_PATH . 'templates/template-parts/active-tutorial.php';
           get_footer();
           return;
       }
       ?>
       
       <article id="tutorial-<?php the_ID(); ?>" <?php post_class( 'single-tutorial' ); ?>>
           
           <!-- Hero Section -->
           <div class="tutorial-hero">
               <div class="hero-content">
                   <div class="hero-breadcrumbs">
                       <?php
                       $categories = get_the_terms( $tutorial_id, 'aiddata_tutorial_cat' );
                       if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                           foreach ( $categories as $category ) {
                               printf(
                                   '<a href="%s">%s</a>',
                                   esc_url( get_term_link( $category ) ),
                                   esc_html( $category->name )
                               );
                           }
                       }
                       ?>
                   </div>
                   
                   <h1 class="tutorial-title"><?php the_title(); ?></h1>
                   
                   <?php if ( $short_desc ) : ?>
                       <p class="tutorial-tagline"><?php echo esc_html( $short_desc ); ?></p>
                   <?php endif; ?>
                   
                   <div class="tutorial-meta-bar">
                       <?php
                       $difficulty_terms = get_the_terms( $tutorial_id, 'aiddata_tutorial_difficulty' );
                       if ( ! empty( $difficulty_terms ) && ! is_wp_error( $difficulty_terms ) ) :
                           ?>
                           <span class="meta-item difficulty">
                               <strong><?php esc_html_e( 'Level:', 'aiddata-lms' ); ?></strong>
                               <?php echo esc_html( $difficulty_terms[0]->name ); ?>
                           </span>
                       <?php endif; ?>
                       
                       <?php if ( $duration ) : ?>
                           <span class="meta-item duration">
                               <span class="dashicons dashicons-clock"></span>
                               <?php printf( esc_html__( '%d minutes', 'aiddata-lms' ), $duration ); ?>
                           </span>
                       <?php endif; ?>
                       
                       <?php if ( is_array( $steps ) ) : ?>
                           <span class="meta-item steps">
                               <span class="dashicons dashicons-list-view"></span>
                               <?php printf( esc_html__( '%d steps', 'aiddata-lms' ), count( $steps ) ); ?>
                           </span>
                       <?php endif; ?>
                       
                       <span class="meta-item enrollments">
                           <span class="dashicons dashicons-groups"></span>
                           <?php
                           $enrollment_count = $enrollment_manager->get_enrollment_count( $tutorial_id, 'active' );
                           printf( esc_html__( '%d enrolled', 'aiddata-lms' ), $enrollment_count );
                           ?>
                       </span>
                   </div>
                   
                   <?php if ( $is_enrolled && $progress ) : ?>
                       <div class="tutorial-progress-banner">
                           <div class="progress-bar-container">
                               <div class="progress-bar" style="width: <?php echo esc_attr( $progress->progress_percent ); ?>%;"></div>
                           </div>
                           <p class="progress-text">
                               <?php printf( esc_html__( '%d%% Complete', 'aiddata-lms' ), round( $progress->progress_percent ) ); ?>
                           </p>
                           <a href="<?php echo esc_url( get_permalink() . '?action=continue' ); ?>" class="button button-large button-primary">
                               <?php esc_html_e( 'Continue Learning', 'aiddata-lms' ); ?>
                           </a>
                       </div>
                   <?php else : ?>
                       <?php get_template_part( 'template-parts/enrollment-button' ); ?>
                   <?php endif; ?>
               </div>
               
               <?php if ( has_post_thumbnail() ) : ?>
                   <div class="hero-image">
                       <?php the_post_thumbnail( 'large' ); ?>
                   </div>
               <?php endif; ?>
           </div>
           
           <!-- Main Content -->
           <div class="tutorial-content-wrapper">
               <div class="tutorial-main">
                   
                   <!-- What You'll Learn -->
                   <?php if ( ! empty( $outcomes ) && is_array( $outcomes ) ) : ?>
                       <section class="tutorial-section outcomes-section">
                           <h2><?php esc_html_e( 'What You\'ll Learn', 'aiddata-lms' ); ?></h2>
                           <ul class="outcomes-list">
                               <?php foreach ( $outcomes as $outcome ) : ?>
                                   <li>
                                       <span class="dashicons dashicons-yes"></span>
                                       <?php echo esc_html( $outcome ); ?>
                                   </li>
                               <?php endforeach; ?>
                           </ul>
                       </section>
                   <?php endif; ?>
                   
                   <!-- Description -->
                   <?php if ( $full_desc || get_the_content() ) : ?>
                       <section class="tutorial-section description-section">
                           <h2><?php esc_html_e( 'About This Tutorial', 'aiddata-lms' ); ?></h2>
                           <div class="tutorial-description">
                               <?php echo wp_kses_post( $full_desc ?: get_the_content() ); ?>
                           </div>
                       </section>
                   <?php endif; ?>
                   
                   <!-- Steps Overview -->
                   <?php if ( ! empty( $steps ) && is_array( $steps ) ) : ?>
                       <section class="tutorial-section steps-section">
                           <h2><?php esc_html_e( 'Tutorial Content', 'aiddata-lms' ); ?></h2>
                           <div class="steps-accordion">
                               <?php
                               $completed_steps = $is_enrolled && $progress ? explode( ',', $progress->completed_steps ) : array();
                               
                               foreach ( $steps as $index => $step ) :
                                   $is_completed = in_array( (string) $index, $completed_steps, true );
                                   $step_number = $index + 1;
                                   ?>
                                   <div class="step-accordion-item <?php echo $is_completed ? 'completed' : ''; ?>">
                                       <div class="step-header">
                                           <span class="step-number"><?php echo esc_html( $step_number ); ?></span>
                                           <h3 class="step-title"><?php echo esc_html( $step['title'] ); ?></h3>
                                           <?php if ( ! empty( $step['estimated_time'] ) ) : ?>
                                               <span class="step-duration"><?php printf( esc_html__( '%d min', 'aiddata-lms' ), $step['estimated_time'] ); ?></span>
                                           <?php endif; ?>
                                           <?php if ( $is_completed ) : ?>
                                               <span class="step-check"><span class="dashicons dashicons-yes"></span></span>
                                           <?php elseif ( $is_enrolled ) : ?>
                                               <span class="step-lock"><span class="dashicons dashicons-lock"></span></span>
                                           <?php endif; ?>
                                       </div>
                                       <?php if ( ! empty( $step['description'] ) ) : ?>
                                           <div class="step-description">
                                               <?php echo esc_html( $step['description'] ); ?>
                                           </div>
                                       <?php endif; ?>
                                   </div>
                               <?php endforeach; ?>
                           </div>
                       </section>
                   <?php endif; ?>
                   
                   <!-- Prerequisites -->
                   <?php if ( ! empty( $prerequisites_ids ) && is_array( $prerequisites_ids ) ) : ?>
                       <section class="tutorial-section prerequisites-section">
                           <h2><?php esc_html_e( 'Prerequisites', 'aiddata-lms' ); ?></h2>
                           <p><?php esc_html_e( 'Before starting this tutorial, you should complete:', 'aiddata-lms' ); ?></p>
                           <ul class="prerequisites-list">
                               <?php
                               foreach ( $prerequisites_ids as $prereq_id ) :
                                   $prereq = get_post( $prereq_id );
                                   if ( ! $prereq ) {
                                       continue;
                                   }
                                   
                                   $prereq_is_completed = $enrollment_manager->get_enrollment( $user_id, $prereq_id );
                                   $prereq_is_completed = $prereq_is_completed && 'completed' === $prereq_is_completed->status;
                                   ?>
                                   <li class="<?php echo $prereq_is_completed ? 'completed' : ''; ?>">
                                       <?php if ( $prereq_is_completed ) : ?>
                                           <span class="dashicons dashicons-yes"></span>
                                       <?php else : ?>
                                           <span class="dashicons dashicons-minus"></span>
                                       <?php endif; ?>
                                       <a href="<?php echo esc_url( get_permalink( $prereq_id ) ); ?>">
                                           <?php echo esc_html( $prereq->post_title ); ?>
                                       </a>
                                   </li>
                               <?php endforeach; ?>
                           </ul>
                       </section>
                   <?php endif; ?>
                   
               </div>
               
               <!-- Sidebar -->
               <aside class="tutorial-sidebar">
                   <?php if ( ! $is_enrolled ) : ?>
                       <div class="sidebar-widget enrollment-widget">
                           <?php get_template_part( 'template-parts/enrollment-button' ); ?>
                       </div>
                   <?php endif; ?>
                   
                   <div class="sidebar-widget share-widget">
                       <h3><?php esc_html_e( 'Share This Tutorial', 'aiddata-lms' ); ?></h3>
                       <!-- Social sharing buttons -->
                   </div>
               </aside>
           </div>
           
       </article>
       
   <?php
   endwhile;
   
   get_footer();
   ```
   
   File location: `/templates/single-aiddata_tutorial.php`

4. **Create Frontend CSS** (`assets/css/frontend/tutorial-display.css`)
   
   Implement responsive, accessible styling:
   - Mobile-first approach
   - Flexbox/Grid layouts
   - Smooth transitions
   - Accessible color contrast
   - Print styles
   
   File location: `/assets/css/frontend/tutorial-display.css`

5. **Create Tutorial Filter Shortcode**
   
   Add to frontend class:
   ```php
   public function register_shortcodes(): void {
       add_shortcode( 'aiddata_tutorial_filters', array( $this, 'render_tutorial_filters' ) );
   }
   
   public function render_tutorial_filters( $atts ): string {
       $atts = shortcode_atts( array(
           'show_search' => true,
           'show_category' => true,
           'show_difficulty' => true,
           'show_sort' => true,
       ), $atts );
       
       ob_start();
       ?>
       <form class="tutorial-filters" method="get">
           <?php if ( $atts['show_search'] ) : ?>
               <div class="filter-item search-filter">
                   <input 
                       type="search" 
                       name="s" 
                       placeholder="<?php esc_attr_e( 'Search tutorials...', 'aiddata-lms' ); ?>"
                       value="<?php echo esc_attr( get_search_query() ); ?>"
                   >
               </div>
           <?php endif; ?>
           
           <?php if ( $atts['show_category'] ) : ?>
               <div class="filter-item category-filter">
                   <?php
                   wp_dropdown_categories( array(
                       'taxonomy' => 'aiddata_tutorial_cat',
                       'show_option_all' => __( 'All Categories', 'aiddata-lms' ),
                       'name' => 'tutorial_category',
                       'selected' => get_query_var( 'tutorial_category' ),
                       'hierarchical' => true,
                       'hide_empty' => true,
                       'value_field' => 'slug',
                   ) );
                   ?>
               </div>
           <?php endif; ?>
           
           <?php if ( $atts['show_difficulty'] ) : ?>
               <div class="filter-item difficulty-filter">
                   <?php
                   wp_dropdown_categories( array(
                       'taxonomy' => 'aiddata_tutorial_difficulty',
                       'show_option_all' => __( 'All Levels', 'aiddata-lms' ),
                       'name' => 'tutorial_difficulty',
                       'selected' => get_query_var( 'tutorial_difficulty' ),
                       'hide_empty' => true,
                       'value_field' => 'slug',
                   ) );
                   ?>
               </div>
           <?php endif; ?>
           
           <?php if ( $atts['show_sort'] ) : ?>
               <div class="filter-item sort-filter">
                   <select name="orderby">
                       <option value=""><?php esc_html_e( 'Sort by...', 'aiddata-lms' ); ?></option>
                       <option value="date" <?php selected( get_query_var( 'orderby' ), 'date' ); ?>><?php esc_html_e( 'Newest First', 'aiddata-lms' ); ?></option>
                       <option value="title" <?php selected( get_query_var( 'orderby' ), 'title' ); ?>><?php esc_html_e( 'Title', 'aiddata-lms' ); ?></option>
                       <option value="popular" <?php selected( get_query_var( 'orderby' ), 'popular' ); ?>><?php esc_html_e( 'Most Popular', 'aiddata-lms' ); ?></option>
                   </select>
               </div>
           <?php endif; ?>
           
           <button type="submit" class="filter-submit">
               <?php esc_html_e( 'Filter', 'aiddata-lms' ); ?>
           </button>
       </form>
       <?php
       return ob_get_clean();
   }
   ```

6. **Template Hierarchy Integration**
   
   Ensure WordPress template hierarchy works:
   ```php
   public function template_include( string $template ): string {
       if ( is_singular( 'aiddata_tutorial' ) ) {
           $plugin_template = AIDDATA_LMS_PATH . 'templates/single-aiddata_tutorial.php';
           if ( file_exists( $plugin_template ) ) {
               return $plugin_template;
           }
       }
       
       if ( is_post_type_archive( 'aiddata_tutorial' ) || is_tax( array( 'aiddata_tutorial_cat', 'aiddata_tutorial_tag', 'aiddata_tutorial_difficulty' ) ) ) {
           $plugin_template = AIDDATA_LMS_PATH . 'templates/archive-aiddata_tutorial.php';
           if ( file_exists( $plugin_template ) ) {
               return $plugin_template;
           }
       }
       
       return $template;
   }
   ```

**Validation Checklist:**
- [ ] Archive page displays tutorial cards correctly
- [ ] Filters functional
- [ ] Pagination works
- [ ] Single tutorial page displays all sections
- [ ] Enrollment button integration works
- [ ] Progress bar shows for enrolled users
- [ ] Prerequisites display correctly
- [ ] Steps accordion functional
- [ ] Responsive on all devices
- [ ] Images load properly
- [ ] Links work correctly
- [ ] Accessibility standards met (WCAG 2.1 AA)
- [ ] Print styles appropriate
- [ ] SEO meta tags present

**Expected Outcome:**
- Beautiful, professional tutorial archive
- Detailed single tutorial pages
- Seamless enrollment integration
- Responsive across devices
- Accessible to all users
- Ready for active tutorial interface (Prompt 5)

---

## WEEK 8: TUTORIAL NAVIGATION & PROGRESS

---

### PROMPT 5: ACTIVE TUTORIAL NAVIGATION INTERFACE

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 2 → Week 8 → Days 1-3 (lines 709-746)
- class-aiddata-lms-tutorial-progress.php → Progress tracking methods
- class-aiddata-lms-tutorial-ajax.php → AJAX handlers from Phase 1
- assets/js/frontend/enrollment.js → Frontend JavaScript foundation

**Objective:**
Create an interactive, user-friendly interface for active tutorial navigation with step display, progress tracking, and mobile optimization.

**Instructions:**

1. **Create Active Tutorial Template** (`templates/template-parts/active-tutorial.php`)
   
   ```php
   <?php
   /**
    * Active Tutorial Interface Template
    */
   if ( ! defined( 'ABSPATH' ) ) exit;
   
   global $post;
   $tutorial_id = $post->ID;
   $user_id = get_current_user_id();
   
   // Get managers
   $progress_manager = new AidData_LMS_Tutorial_Progress();
   $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
   
   // Verify enrollment
   if ( ! $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) ) {
       wp_safe_redirect( get_permalink( $tutorial_id ) );
       exit;
   }
   
   // Get tutorial data
   $steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );
   if ( ! is_array( $steps ) || empty( $steps ) ) {
       esc_html_e( 'This tutorial has no steps yet.', 'aiddata-lms' );
       return;
   }
   
   // Get progress
   $progress = $progress_manager->get_progress( $user_id, $tutorial_id );
   $current_step_index = $progress ? $progress->current_step : 0;
   $completed_steps = $progress && $progress->completed_steps ? explode( ',', $progress->completed_steps ) : array();
   
   // Ensure valid step index
   if ( ! isset( $steps[ $current_step_index ] ) ) {
       $current_step_index = 0;
   }
   
   $current_step = $steps[ $current_step_index ];
   ?>
   
   <div class="active-tutorial-container" data-tutorial-id="<?php echo esc_attr( $tutorial_id ); ?>">
       
       <!-- Tutorial Header -->
       <div class="tutorial-header">
           <div class="header-left">
               <a href="<?php echo esc_url( get_permalink( $tutorial_id ) ); ?>" class="back-link">
                   <span class="dashicons dashicons-arrow-left-alt2"></span>
                   <?php esc_html_e( 'Back to Overview', 'aiddata-lms' ); ?>
               </a>
               <h1 class="tutorial-title"><?php echo esc_html( get_the_title() ); ?></h1>
           </div>
           
           <div class="header-right">
               <div class="progress-indicator">
                   <span class="progress-text">
                       <?php printf( esc_html__( 'Step %1$d of %2$d', 'aiddata-lms' ), $current_step_index + 1, count( $steps ) ); ?>
                   </span>
                   <div class="progress-bar-mini">
                       <div class="progress-fill" style="width: <?php echo esc_attr( $progress ? $progress->progress_percent : 0 ); ?>%;"></div>
                   </div>
               </div>
           </div>
       </div>
       
       <div class="tutorial-main-content">
           
           <!-- Sidebar Navigation -->
           <aside class="tutorial-sidebar" id="tutorial-sidebar">
               <div class="sidebar-header">
                   <h2><?php esc_html_e( 'Tutorial Steps', 'aiddata-lms' ); ?></h2>
                   <button type="button" class="sidebar-toggle" id="sidebar-toggle">
                       <span class="dashicons dashicons-menu"></span>
                   </button>
               </div>
               
               <nav class="steps-navigation">
                   <ul class="steps-list">
                       <?php foreach ( $steps as $index => $step ) :
                           $is_current = ( $index === $current_step_index );
                           $is_completed = in_array( (string) $index, $completed_steps, true );
                           $is_accessible = ( $index === 0 || in_array( (string) ( $index - 1 ), $completed_steps, true ) );
                           
                           $classes = array( 'step-item' );
                           if ( $is_current ) $classes[] = 'current';
                           if ( $is_completed ) $classes[] = 'completed';
                           if ( ! $is_accessible ) $classes[] = 'locked';
                           ?>
                           <li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-step-index="<?php echo esc_attr( $index ); ?>">
                               <button 
                                   type="button" 
                                   class="step-link" 
                                   <?php echo ! $is_accessible ? 'disabled' : ''; ?>
                                   data-step-index="<?php echo esc_attr( $index ); ?>"
                               >
                                   <span class="step-number"><?php echo esc_html( $index + 1 ); ?></span>
                                   <span class="step-title"><?php echo esc_html( $step['title'] ); ?></span>
                                   <span class="step-status">
                                       <?php if ( $is_completed ) : ?>
                                           <span class="dashicons dashicons-yes"></span>
                                       <?php elseif ( ! $is_accessible ) : ?>
                                           <span class="dashicons dashicons-lock"></span>
                                       <?php endif; ?>
                                   </span>
                               </button>
                           </li>
                       <?php endforeach; ?>
                   </ul>
               </nav>
               
               <div class="sidebar-footer">
                   <div class="overall-progress">
                       <div class="progress-label">
                           <span><?php esc_html_e( 'Overall Progress', 'aiddata-lms' ); ?></span>
                           <span class="progress-percent"><?php echo esc_html( round( $progress ? $progress->progress_percent : 0 ) ); ?>%</span>
                       </div>
                       <div class="progress-bar">
                           <div class="progress-fill" style="width: <?php echo esc_attr( $progress ? $progress->progress_percent : 0 ); ?>%;"></div>
                       </div>
                   </div>
               </div>
           </aside>
           
           <!-- Step Content -->
           <main class="step-content-area" id="step-content">
               <div class="step-content" data-step-index="<?php echo esc_attr( $current_step_index ); ?>">
                   <?php echo $this->render_step_content( $current_step, $current_step_index, $tutorial_id ); ?>
               </div>
               
               <!-- Navigation Buttons -->
               <div class="step-navigation-buttons">
                   <?php if ( $current_step_index > 0 ) : ?>
                       <button type="button" class="button button-secondary nav-previous" data-step="<?php echo esc_attr( $current_step_index - 1 ); ?>">
                           <span class="dashicons dashicons-arrow-left-alt2"></span>
                           <?php esc_html_e( 'Previous Step', 'aiddata-lms' ); ?>
                       </button>
                   <?php endif; ?>
                   
                   <?php if ( ! in_array( (string) $current_step_index, $completed_steps, true ) && $current_step['required'] ) : ?>
                       <button type="button" class="button button-primary mark-complete" data-step="<?php echo esc_attr( $current_step_index ); ?>">
                           <?php esc_html_e( 'Mark as Complete', 'aiddata-lms' ); ?>
                       </button>
                   <?php endif; ?>
                   
                   <?php if ( $current_step_index < count( $steps ) - 1 ) : ?>
                       <button type="button" class="button button-primary nav-next" data-step="<?php echo esc_attr( $current_step_index + 1 ); ?>">
                           <?php esc_html_e( 'Next Step', 'aiddata-lms' ); ?>
                           <span class="dashicons dashicons-arrow-right-alt2"></span>
                       </button>
                   <?php else : ?>
                       <button type="button" class="button button-primary finish-tutorial">
                           <?php esc_html_e( 'Finish Tutorial', 'aiddata-lms' ); ?>
                           <span class="dashicons dashicons-yes"></span>
                       </button>
                   <?php endif; ?>
               </div>
           </main>
           
       </div>
       
       <!-- Mobile Bottom Navigation -->
       <div class="mobile-bottom-nav">
           <button type="button" class="mob-nav-button sidebar-toggle-mob">
               <span class="dashicons dashicons-menu"></span>
               <span><?php esc_html_e( 'Steps', 'aiddata-lms' ); ?></span>
           </button>
           <button type="button" class="mob-nav-button nav-previous-mob">
               <span class="dashicons dashicons-arrow-left-alt2"></span>
               <span><?php esc_html_e( 'Previous', 'aiddata-lms' ); ?></span>
           </button>
           <button type="button" class="mob-nav-button nav-next-mob">
               <span><?php esc_html_e( 'Next', 'aiddata-lms' ); ?></span>
               <span class="dashicons dashicons-arrow-right-alt2"></span>
           </button>
       </div>
       
   </div>
   ```
   
   File location: `/templates/template-parts/active-tutorial.php`

2. **Create Step Content Renderer Method**
   
   Add helper method to render different step types:
   ```php
   private function render_step_content( array $step, int $step_index, int $tutorial_id ): string {
       ob_start();
       
       echo '<div class="step-inner">';
       
       // Step header
       echo '<div class="step-header">';
       echo '<h2 class="step-title">' . esc_html( $step['title'] ) . '</h2>';
       if ( ! empty( $step['description'] ) ) {
           echo '<p class="step-description">' . esc_html( $step['description'] ) . '</p>';
       }
       echo '</div>';
       
       // Step content based on type
       echo '<div class="step-body">';
       
       switch ( $step['type'] ) {
           case 'video':
               $this->render_video_step( $step );
               break;
           
           case 'text':
               $this->render_text_step( $step );
               break;
           
           case 'interactive':
               $this->render_interactive_step( $step );
               break;
           
           case 'resource':
               $this->render_resource_step( $step );
               break;
           
           case 'quiz':
               $this->render_quiz_step( $step, $tutorial_id );
               break;
           
           default:
               echo '<p>' . esc_html__( 'Unknown step type.', 'aiddata-lms' ) . '</p>';
       }
       
       echo '</div>'; // .step-body
       echo '</div>'; // .step-inner
       
       return ob_get_clean();
   }
   
   private function render_video_step( array $step ): void {
       $content = $step['content'];
       $platform = $content['platform'] ?? 'html5';
       $video_url = $content['video_url'] ?? '';
       
       if ( empty( $video_url ) ) {
           echo '<p>' . esc_html__( 'No video URL provided.', 'aiddata-lms' ) . '</p>';
           return;
       }
       
       echo '<div class="video-container" data-platform="' . esc_attr( $platform ) . '" data-video-url="' . esc_url( $video_url ) . '">';
       echo '<div id="video-player"></div>';
       echo '</div>';
       
       if ( ! empty( $content['description'] ) ) {
           echo '<div class="video-description">' . wp_kses_post( $content['description'] ) . '</div>';
       }
       
       if ( ! empty( $content['transcript'] ) ) {
           echo '<details class="video-transcript">';
           echo '<summary>' . esc_html__( 'View Transcript', 'aiddata-lms' ) . '</summary>';
           echo '<div class="transcript-content">' . wp_kses_post( $content['transcript'] ) . '</div>';
           echo '</details>';
       }
   }
   
   private function render_text_step( array $step ): void {
       $content = $step['content'];
       $text_content = $content['content'] ?? '';
       
       if ( empty( $text_content ) ) {
           echo '<p>' . esc_html__( 'No content available.', 'aiddata-lms' ) . '</p>';
           return;
       }
       
       echo '<div class="text-content">';
       echo wp_kses_post( $text_content );
       echo '</div>';
       
       // Show attachments if any
       if ( ! empty( $content['attachments'] ) && is_array( $content['attachments'] ) ) {
           echo '<div class="step-attachments">';
           echo '<h3>' . esc_html__( 'Attachments', 'aiddata-lms' ) . '</h3>';
           echo '<ul>';
           foreach ( $content['attachments'] as $attachment_id ) {
               $file_url = wp_get_attachment_url( $attachment_id );
               $file_name = basename( get_attached_file( $attachment_id ) );
               if ( $file_url ) {
                   echo '<li><a href="' . esc_url( $file_url ) . '" target="_blank">' . esc_html( $file_name ) . '</a></li>';
               }
           }
           echo '</ul>';
           echo '</div>';
       }
   }
   
   private function render_interactive_step( array $step ): void {
       $content = $step['content'];
       $interaction_type = $content['interaction_type'] ?? 'iframe';
       
       if ( 'iframe' === $interaction_type || 'embed' === $interaction_type ) {
           if ( ! empty( $content['embed_code'] ) ) {
               echo '<div class="interactive-embed">';
               echo wp_kses_post( $content['embed_code'] );
               echo '</div>';
           } elseif ( ! empty( $content['url'] ) ) {
               $height = ! empty( $content['height'] ) ? absint( $content['height'] ) : 600;
               echo '<div class="interactive-iframe">';
               echo '<iframe src="' . esc_url( $content['url'] ) . '" width="100%" height="' . esc_attr( $height ) . '" frameborder="0"></iframe>';
               echo '</div>';
           }
       }
       
       if ( ! empty( $content['instructions'] ) ) {
           echo '<div class="interactive-instructions">' . wp_kses_post( $content['instructions'] ) . '</div>';
       }
   }
   
   private function render_resource_step( array $step ): void {
       $content = $step['content'];
       $resources = $content['resources'] ?? array();
       
       if ( empty( $resources ) ) {
           echo '<p>' . esc_html__( 'No resources available.', 'aiddata-lms' ) . '</p>';
           return;
       }
       
       if ( ! empty( $content['instructions'] ) ) {
           echo '<div class="resource-instructions">' . wp_kses_post( $content['instructions'] ) . '</div>';
       }
       
       echo '<div class="resources-list">';
       foreach ( $resources as $resource ) {
           $file_id = $resource['file_id'] ?? 0;
           $file_url = wp_get_attachment_url( $file_id );
           
           if ( ! $file_url ) {
               continue;
           }
           
           echo '<div class="resource-item">';
           echo '<div class="resource-icon"><span class="dashicons dashicons-download"></span></div>';
           echo '<div class="resource-details">';
           echo '<h4>' . esc_html( $resource['title'] ?? 'Untitled' ) . '</h4>';
           if ( ! empty( $resource['description'] ) ) {
               echo '<p>' . esc_html( $resource['description'] ) . '</p>';
           }
           echo '<a href="' . esc_url( $file_url ) . '" class="button button-secondary" download>' . esc_html__( 'Download', 'aiddata-lms' ) . '</a>';
           echo '</div>';
           echo '</div>';
       }
       echo '</div>';
   }
   
   private function render_quiz_step( array $step, int $tutorial_id ): void {
       // Quiz rendering will be implemented in Phase 4
       echo '<div class="quiz-placeholder">';
       echo '<p>' . esc_html__( 'Quiz functionality will be available in a future update.', 'aiddata-lms' ) . '</p>';
       echo '</div>';
   }
   ```

3. **Create Tutorial Navigation JavaScript** (`assets/js/frontend/tutorial-navigation.js`)
   
   ```javascript
   (function($) {
       'use strict';
       
       const TutorialNavigation = {
           tutorialId: null,
           currentStepIndex: 0,
           totalSteps: 0,
           
           init: function() {
               this.tutorialId = $('.active-tutorial-container').data('tutorial-id');
               if (!this.tutorialId) return;
               
               this.currentStepIndex = parseInt($('.step-content').data('step-index')) || 0;
               this.totalSteps = $('.steps-list .step-item').length;
               
               this.bindEvents();
               this.initVideoTracking();
               this.startTimeTracking();
           },
           
           bindEvents: function() {
               // Step navigation from sidebar
               $(document).on('click', '.step-link:not([disabled])', (e) => {
                   const stepIndex = parseInt($(e.currentTarget).data('step-index'));
                   this.navigateToStep(stepIndex);
               });
               
               // Previous/Next buttons
               $('.nav-previous, .nav-previous-mob').on('click', () => {
                   if (this.currentStepIndex > 0) {
                       this.navigateToStep(this.currentStepIndex - 1);
                   }
               });
               
               $('.nav-next, .nav-next-mob').on('click', () => {
                   if (this.currentStepIndex < this.totalSteps - 1) {
                       this.navigateToStep(this.currentStepIndex + 1);
                   }
               });
               
               // Mark as complete
               $('.mark-complete').on('click', () => {
                   this.markStepComplete(this.currentStepIndex);
               });
               
               // Finish tutorial
               $('.finish-tutorial').on('click', () => {
                   this.finishTutorial();
               });
               
               // Sidebar toggle
               $('#sidebar-toggle, .sidebar-toggle-mob').on('click', () => {
                   this.toggleSidebar();
               });
               
               // Keyboard navigation
               $(document).on('keydown', (e) => {
                   if (e.ctrlKey || e.metaKey) {
                       if (e.key === 'ArrowLeft') {
                           e.preventDefault();
                           $('.nav-previous').click();
                       } else if (e.key === 'ArrowRight') {
                           e.preventDefault();
                           $('.nav-next').click();
                       }
                   }
               });
           },
           
           navigateToStep: function(stepIndex) {
               if (stepIndex < 0 || stepIndex >= this.totalSteps) {
                   return;
               }
               
               // Show loading
               this.showLoading();
               
               // AJAX load step content
               $.ajax({
                   url: aiddataLMS.ajaxUrl,
                   type: 'POST',
                   data: {
                       action: 'aiddata_lms_load_step',
                       tutorial_id: this.tutorialId,
                       step_index: stepIndex,
                       nonce: aiddataLMS.progressNonce
                   },
                   success: (response) => {
                       if (response.success) {
                           this.loadStepContent(response.data.html, stepIndex);
                           this.updateURL(stepIndex);
                       } else {
                           this.showError(response.data.message);
                       }
                   },
                   error: () => {
                       this.showError('Failed to load step. Please try again.');
                   },
                   complete: () => {
                       this.hideLoading();
                   }
               });
           },
           
           loadStepContent: function(html, stepIndex) {
               const $content = $('#step-content .step-content');
               
               // Fade out
               $content.fadeOut(200, function() {
                   $content.html(html);
                   $content.data('step-index', stepIndex);
                   
                   // Update current step index
                   TutorialNavigation.currentStepIndex = stepIndex;
                   
                   // Update sidebar
                   TutorialNavigation.updateSidebarState();
                   
                   // Update navigation buttons
                   TutorialNavigation.updateNavigationButtons();
                   
                   // Re-initialize components
                   TutorialNavigation.initVideoTracking();
                   
                   // Fade in
                   $content.fadeIn(200);
                   
                   // Scroll to top
                   window.scrollTo({ top: 0, behavior: 'smooth' });
               });
           },
           
           updateSidebarState: function() {
               $('.steps-list .step-item').removeClass('current');
               $(`.steps-list .step-item[data-step-index="${this.currentStepIndex}"]`).addClass('current');
               
               // Update progress text
               $('.progress-text').text(`Step ${this.currentStepIndex + 1} of ${this.totalSteps}`);
           },
           
           updateNavigationButtons: function() {
               // Show/hide previous button
               if (this.currentStepIndex === 0) {
                   $('.nav-previous, .nav-previous-mob').hide();
               } else {
                   $('.nav-previous, .nav-previous-mob').show();
               }
               
               // Show/hide next vs finish button
               if (this.currentStepIndex === this.totalSteps - 1) {
                   $('.nav-next, .nav-next-mob').hide();
                   $('.finish-tutorial').show();
               } else {
                   $('.nav-next, .nav-next-mob').show();
                   $('.finish-tutorial').hide();
               }
           },
           
           markStepComplete: function(stepIndex) {
               $.ajax({
                   url: aiddataLMS.ajaxUrl,
                   type: 'POST',
                   data: {
                       action: 'aiddata_lms_update_step_progress',
                       tutorial_id: this.tutorialId,
                       step_index: stepIndex,
                       nonce: aiddataLMS.progressNonce
                   },
                   success: (response) => {
                       if (response.success) {
                           // Mark step as completed in sidebar
                           $(`.steps-list .step-item[data-step-index="${stepIndex}"]`).addClass('completed');
                           
                           // Update progress bar
                           const progressPercent = response.data.progress.percent;
                           $('.progress-fill').css('width', progressPercent + '%');
                           $('.progress-percent').text(Math.round(progressPercent) + '%');
                           
                           // Show success message
                           this.showSuccess('Step marked as complete!');
                           
                           // Auto-advance to next step
                           setTimeout(() => {
                               if (this.currentStepIndex < this.totalSteps - 1) {
                                   this.navigateToStep(this.currentStepIndex + 1);
                               }
                           }, 1000);
                       }
                   }
               });
           },
           
           finishTutorial: function() {
               if (confirm('Are you sure you want to finish this tutorial?')) {
                   window.location.href = aiddataLMS.tutorialUrl + '?finished=1';
               }
           },
           
           toggleSidebar: function() {
               $('#tutorial-sidebar').toggleClass('collapsed');
               $('body').toggleClass('sidebar-collapsed');
           },
           
           initVideoTracking: function() {
               // Initialize video player if video step
               const $videoContainer = $('.video-container');
               if ($videoContainer.length) {
                   const platform = $videoContainer.data('platform');
                   const videoUrl = $videoContainer.data('video-url');
                   
                   // Initialize video player based on platform
                   // Will integrate with Phase 3 video tracking system
               }
           },
           
           startTimeTracking: function() {
               // Track time spent every 30 seconds
               setInterval(() => {
                   $.ajax({
                       url: aiddataLMS.ajaxUrl,
                       type: 'POST',
                       data: {
                           action: 'aiddata_lms_update_time_spent',
                           tutorial_id: this.tutorialId,
                           seconds: 30,
                           nonce: aiddataLMS.progressNonce
                       }
                   });
               }, 30000);
           },
           
           updateURL: function(stepIndex) {
               const url = new URL(window.location);
               url.searchParams.set('step', stepIndex);
               window.history.pushState({}, '', url);
           },
           
           showLoading: function() {
               $('#step-content').append('<div class="loading-overlay"><div class="spinner"></div></div>');
           },
           
           hideLoading: function() {
               $('.loading-overlay').remove();
           },
           
           showSuccess: function(message) {
               this.showNotification(message, 'success');
           },
           
           showError: function(message) {
               this.showNotification(message, 'error');
           },
           
           showNotification: function(message, type) {
               const $notification = $(`<div class="tutorial-notification ${type}">${message}</div>`);
               $('body').append($notification);
               
               setTimeout(() => {
                   $notification.addClass('show');
               }, 10);
               
               setTimeout(() => {
                   $notification.removeClass('show');
                   setTimeout(() => {
                       $notification.remove();
                   }, 300);
               }, 3000);
           }
       };
       
       $(document).ready(function() {
           TutorialNavigation.init();
       });
       
   })(jQuery);
   ```
   
   File location: `/assets/js/frontend/tutorial-navigation.js`

4. **Create AJAX Handler for Step Loading**
   
   Add to AJAX class or create new handler:
   ```php
   public function load_step_ajax(): void {
       // Verify nonce
       check_ajax_referer( 'aiddata-lms-progress', 'nonce' );
       
       $tutorial_id = absint( $_POST['tutorial_id'] ?? 0 );
       $step_index = absint( $_POST['step_index'] ?? 0 );
       $user_id = get_current_user_id();
       
       // Verify enrollment
       $enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
       if ( ! $enrollment_manager->is_user_enrolled( $user_id, $tutorial_id ) ) {
           wp_send_json_error( array(
               'message' => __( 'You are not enrolled in this tutorial.', 'aiddata-lms' ),
           ) );
       }
       
       // Get steps
       $steps = get_post_meta( $tutorial_id, '_tutorial_steps', true );
       if ( ! isset( $steps[ $step_index ] ) ) {
           wp_send_json_error( array(
               'message' => __( 'Invalid step.', 'aiddata-lms' ),
           ) );
       }
       
       $step = $steps[ $step_index ];
       
       // Render step content
       $html = $this->render_step_content( $step, $step_index, $tutorial_id );
       
       wp_send_json_success( array(
           'html' => $html,
           'step' => $step,
       ) );
   }
   ```

5. **Create CSS for Active Tutorial** (`assets/css/frontend/tutorial-navigation.css`)
   
   Implement comprehensive styling:
   - Sidebar navigation
   - Step content area
   - Navigation buttons
   - Mobile bottom navigation
   - Progress indicators
   - Loading states
   - Notifications
   - Responsive breakpoints
   
   File location: `/assets/css/frontend/tutorial-navigation.css`

**Validation Checklist:**
- [ ] Active tutorial interface loads correctly
- [ ] Step navigation works smoothly
- [ ] Progress tracking functional
- [ ] Sidebar collapsible
- [ ] Mobile navigation accessible
- [ ] Keyboard shortcuts work
- [ ] Video steps display correctly
- [ ] Text steps render properly
- [ ] Resource downloads work
- [ ] Mark as complete functional
- [ ] Progress bar updates
- [ ] URL updates with step changes
- [ ] Time tracking operational
- [ ] Loading states show appropriately
- [ ] Error handling works
- [ ] Notifications display correctly

**Expected Outcome:**
- Fully functional active tutorial interface
- Smooth navigation between steps
- Real-time progress tracking
- Mobile-optimized experience
- Ready for progress persistence (Prompt 6)

---

### PROMPT 6: PROGRESS PERSISTENCE & RESUME FUNCTIONALITY

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 2 → Week 8 → Days 4-5 (lines 747-788)
- class-aiddata-lms-tutorial-progress.php → Progress methods (already exists from Phase 1)
- assets/js/frontend/tutorial-navigation.js → From Prompt 5

**Objective:**
Ensure tutorial progress persists correctly across sessions with resume functionality and milestone notifications.

**Instructions:**

1. **Enhance Progress Detection on Tutorial Load**
   
   Modify single tutorial template to detect resume state:
   ```php
   // In single-aiddata_tutorial.php, add after enrollment check
   if ( $is_enrolled && $progress && $progress->progress_percent > 0 && $progress->progress_percent < 100 ) {
       $last_step = $progress->current_step;
       $resume_url = add_query_arg( array(
           'action' => 'continue',
           'step' => $last_step,
       ), get_permalink( $tutorial_id ) );
       
       // Show resume banner
       ?>
       <div class="tutorial-resume-banner">
           <div class="resume-content">
               <h3><?php esc_html_e( 'Pick up where you left off', 'aiddata-lms' ); ?></h3>
               <p>
                   <?php
                   printf(
                       /* translators: 1: percentage, 2: step number */
                       esc_html__( 'You\'re %1$d%% through this tutorial. Continue from step %2$d?', 'aiddata-lms' ),
                       round( $progress->progress_percent ),
                       $last_step + 1
                   );
                   ?>
               </p>
               <div class="resume-actions">
                   <a href="<?php echo esc_url( $resume_url ); ?>" class="button button-primary">
                       <?php esc_html_e( 'Resume Tutorial', 'aiddata-lms' ); ?>
                   </a>
                   <a href="<?php echo esc_url( add_query_arg( 'action', 'continue', get_permalink( $tutorial_id ) ) ); ?>" class="button button-secondary">
                       <?php esc_html_e( 'Start from Beginning', 'aiddata-lms' ); ?>
                   </a>
               </div>
           </div>
       </div>
       <?php
   }
   ```

2. **Implement Resume from URL Parameter**
   
   In active-tutorial.php:
   ```php
   // Check for step parameter in URL
   if ( isset( $_GET['step'] ) ) {
       $requested_step = absint( $_GET['step'] );
       if ( isset( $steps[ $requested_step ] ) ) {
           $current_step_index = $requested_step;
       }
   }
   ```

3. **Add Progress Milestone Detection**
   
   Create milestone checker class:
   ```php
   class AidData_LMS_Progress_Milestones {
       
       private $milestones = array( 25, 50, 75, 100 );
       
       public function check_milestone( int $user_id, int $tutorial_id, float $new_percent ): ?int {
           foreach ( $this->milestones as $milestone ) {
               $meta_key = "_tutorial_{$tutorial_id}_milestone_{$milestone}";
               $reached = get_user_meta( $user_id, $meta_key, true );
               
               if ( ! $reached && $new_percent >= $milestone ) {
                   // Mark milestone as reached
                   update_user_meta( $user_id, $meta_key, current_time( 'mysql' ) );
                   
                   // Fire action
                   do_action( 'aiddata_lms_milestone_reached', $user_id, $tutorial_id, $milestone );
                   
                   return $milestone;
               }
           }
           
           return null;
       }
       
       public function get_milestone_message( int $milestone ): string {
           $messages = array(
               25 => __( '🎉 You\'re 25% complete! Keep going!', 'aiddata-lms' ),
               50 => __( '🌟 Halfway there! You\'re doing great!', 'aiddata-lms' ),
               75 => __( '🚀 75% complete! You\'re almost done!', 'aiddata-lms' ),
               100 => __( '🏆 Congratulations! You\'ve completed the tutorial!', 'aiddata-lms' ),
           );
           
           return $messages[ $milestone ] ?? '';
       }
       
       public function reset_milestones( int $user_id, int $tutorial_id ): void {
           foreach ( $this->milestones as $milestone ) {
               delete_user_meta( $user_id, "_tutorial_{$tutorial_id}_milestone_{$milestone}" );
           }
       }
   }
   ```

4. **Integrate Milestone Checking in Progress Update**
   
   Update AJAX progress handler:
   ```php
   public function update_step_progress_ajax(): void {
       // ... existing validation code ...
       
       $result = $progress_manager->update_progress( $user_id, $tutorial_id, $step_index );
       
       if ( ! is_wp_error( $result ) ) {
           $progress = $progress_manager->get_progress( $user_id, $tutorial_id );
           
           // Check for milestones
           $milestone_checker = new AidData_LMS_Progress_Milestones();
           $milestone = $milestone_checker->check_milestone( $user_id, $tutorial_id, $progress->progress_percent );
           
           $response_data = array(
               'progress' => array(
                   'percent' => $progress->progress_percent,
                   'current_step' => $progress->current_step,
                   'status' => $progress->status,
               ),
           );
           
           if ( $milestone ) {
               $response_data['milestone'] = array(
                   'reached' => $milestone,
                   'message' => $milestone_checker->get_milestone_message( $milestone ),
               );
           }
           
           wp_send_json_success( $response_data );
       } else {
           wp_send_json_error( array(
               'message' => $result->get_error_message(),
           ) );
       }
   }
   ```

5. **Add Milestone Notifications to JavaScript**
   
   Update tutorial-navigation.js:
   ```javascript
   markStepComplete: function(stepIndex) {
       $.ajax({
           // ... existing code ...
           success: (response) => {
               if (response.success) {
                   // ... existing code ...
                   
                   // Check for milestone
                   if (response.data.milestone) {
                       this.showMilestone(response.data.milestone);
                   }
                   
                   // ... rest of code ...
               }
           }
       });
   },
   
   showMilestone: function(milestone) {
       const $modal = $(`
           <div class="milestone-modal">
               <div class="milestone-content">
                   <div class="milestone-icon">🎉</div>
                   <h2>${milestone.message}</h2>
                   <p>You've reached ${milestone.reached}% completion!</p>
                   <button class="button button-primary close-milestone">Continue</button>
               </div>
           </div>
       `);
       
       $('body').append($modal);
       
       setTimeout(() => {
           $modal.addClass('show');
       }, 10);
       
       $modal.find('.close-milestone').on('click', function() {
           $modal.removeClass('show');
           setTimeout(() => {
               $modal.remove();
           }, 300);
       });
   }
   ```

6. **Add CSS for Resume Banner and Milestones**
   
   ```css
   /* Resume Banner */
   .tutorial-resume-banner {
       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
       color: white;
       padding: 2rem;
       border-radius: 8px;
       margin-bottom: 2rem;
   }
   
   .tutorial-resume-banner h3 {
       margin: 0 0 0.5rem 0;
       color: white;
   }
   
   .resume-actions {
       margin-top: 1rem;
       display: flex;
       gap: 1rem;
   }
   
   .resume-actions .button {
       background: white;
       color: #667eea;
       border: none;
   }
   
   .resume-actions .button-secondary {
       background: transparent;
       color: white;
       border: 2px solid white;
   }
   
   /* Milestone Modal */
   .milestone-modal {
       position: fixed;
       top: 0;
       left: 0;
       right: 0;
       bottom: 0;
       background: rgba(0, 0, 0, 0.8);
       display: flex;
       align-items: center;
       justify-content: center;
       z-index: 10000;
       opacity: 0;
       transition: opacity 0.3s ease;
   }
   
   .milestone-modal.show {
       opacity: 1;
   }
   
   .milestone-content {
       background: white;
       padding: 3rem;
       border-radius: 12px;
       text-align: center;
       max-width: 500px;
       transform: scale(0.8);
       transition: transform 0.3s ease;
   }
   
   .milestone-modal.show .milestone-content {
       transform: scale(1);
   }
   
   .milestone-icon {
       font-size: 4rem;
       margin-bottom: 1rem;
   }
   
   .milestone-content h2 {
       margin: 0 0 1rem 0;
       color: #667eea;
   }
   ```

7. **Add Progress History Tracking**
   
   Optional enhancement for detailed history:
   ```php
   public function log_progress_history( int $user_id, int $tutorial_id, string $event, array $data = array() ): void {
       global $wpdb;
       
       $table_name = $wpdb->prefix . 'aiddata_lms_progress_history';
       
       $wpdb->insert(
           $table_name,
           array(
               'user_id' => $user_id,
               'tutorial_id' => $tutorial_id,
               'event_type' => $event,
               'event_data' => wp_json_encode( $data ),
               'created_at' => current_time( 'mysql' ),
           ),
           array( '%d', '%d', '%s', '%s', '%s' )
       );
   }
   ```

**Validation Checklist:**
- [ ] Progress persists across sessions
- [ ] Resume banner displays correctly
- [ ] Resume functionality works
- [ ] Start from beginning option works
- [ ] URL parameters handled correctly
- [ ] Milestones detected accurately
- [ ] Milestone notifications display
- [ ] Milestone messages appropriate
- [ ] Progress never lost
- [ ] No duplicate milestone notifications
- [ ] Works after browser close/reopen
- [ ] Works after logout/login
- [ ] Database updates correctly
- [ ] Performance acceptable

**Expected Outcome:**
- Reliable progress persistence
- User-friendly resume functionality
- Engaging milestone celebrations
- Never losing user progress
- Enhanced user experience
- Ready for Phase 2 validation (Prompt 7)

---

### PROMPT 7: PHASE 2 COMPREHENSIVE VALIDATION

**Context Required:**
- All Phase 2 files created in Prompts 1-6
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Phase 2 Validation
- INTEGRATION_VALIDATION_MATRIX.md → All sections
- IMPLEMENTATION_CHECKLIST.md → Phase 2 items

**Objective:**
Comprehensive testing and validation of all Phase 2 features before proceeding to Phase 3.

**Instructions:**

1. **Create Phase 2 Validation Test Class** (`includes/admin/class-aiddata-lms-phase-2-validation.php`)
   
   ```php
   <?php
   /**
    * Phase 2 Validation Tests
    */
   class AidData_LMS_Phase_2_Validation {
       
       public static function run_all_tests(): array {
           $results = array();
           
           $results['tutorial_builder'] = self::test_tutorial_builder();
           $results['admin_list'] = self::test_admin_list();
           $results['frontend_display'] = self::test_frontend_display();
           $results['active_tutorial'] = self::test_active_tutorial();
           $results['progress_persistence'] = self::test_progress_persistence();
           $results['integration'] = self::test_integration();
           $results['security'] = self::test_security();
           $results['performance'] = self::test_performance();
           $results['accessibility'] = self::test_accessibility();
           
           return $results;
       }
       
       private static function test_tutorial_builder(): array {
           // Test meta boxes exist
           // Test step builder functional
           // Test data saving
           // Test validation
           // Return results
       }
       
       private static function test_admin_list(): array {
           // Test custom columns
           // Test bulk actions
           // Test filters
           // Test quick edit
           // Return results
       }
       
       private static function test_frontend_display(): array {
           // Test archive template
           // Test single template
           // Test enrollment integration
           // Test responsive design
           // Return results
       }
       
       private static function test_active_tutorial(): array {
           // Test navigation
           // Test step loading
           // Test progress tracking
           // Test mobile interface
           // Return results
       }
       
       private static function test_progress_persistence(): array {
           // Test progress saving
           // Test resume functionality
           // Test milestones
           // Test time tracking
           // Return results
       }
       
       private static function test_integration(): array {
           // Test with Phase 1 systems
           // Test enrollment integration
           // Test progress integration
           // Test email triggers
           // Return results
       }
       
       private static function test_security(): array {
           // Test nonce verification
           // Test capability checks
           // Test input sanitization
           // Test output escaping
           // Return results
       }
       
       private static function test_performance(): array {
           // Test page load times
           // Test AJAX response times
           // Test database query counts
           // Test memory usage
           // Return results
       }
       
       private static function test_accessibility(): array {
           // Test keyboard navigation
           // Test screen reader compatibility
           // Test color contrast
           // Test ARIA labels
           // Return results
       }
       
       public static function generate_report( array $results ): string {
           // Generate comprehensive validation report
           // Include pass/fail summary
           // Include detailed findings
           // Include recommendations
           // Return HTML report
       }
   }
   ```

2. **Manual Testing Checklist**
   
   Create comprehensive manual test scenarios:
   
   **Tutorial Builder Tests:**
   - [ ] Can create new tutorial
   - [ ] Basic info saves correctly
   - [ ] Settings save correctly
   - [ ] Can add all step types
   - [ ] Drag-drop reordering works
   - [ ] Step editing works
   - [ ] Step duplication works
   - [ ] Step deletion works
   - [ ] Video URL detection works
   - [ ] Data persists on save
   
   **Admin List Tests:**
   - [ ] Custom columns display
   - [ ] Enrollment counts accurate
   - [ ] Completion rates correct
   - [ ] Sortable columns work
   - [ ] Bulk duplication works
   - [ ] CSV export works
   - [ ] Filters work correctly
   - [ ] Quick edit saves data
   - [ ] Search works
   - [ ] Pagination works
   
   **Frontend Display Tests:**
   - [ ] Archive page displays tutorials
   - [ ] Tutorial cards styled correctly
   - [ ] Single tutorial page complete
   - [ ] Enrollment button works
   - [ ] Progress banner displays
   - [ ] Prerequisites shown
   - [ ] Steps accordion works
   - [ ] Mobile responsive
   - [ ] Images load correctly
   - [ ] Links functional
   
   **Active Tutorial Tests:**
   - [ ] Navigation loads correctly
   - [ ] Step content displays
   - [ ] Step navigation works
   - [ ] Mark complete works
   - [ ] Progress updates
   - [ ] Sidebar collapsible
   - [ ] Mobile nav works
   - [ ] Keyboard shortcuts work
   - [ ] Video embeds work
   - [ ] Resources downloadable
   
   **Progress Persistence Tests:**
   - [ ] Progress saves automatically
   - [ ] Resume banner appears
   - [ ] Resume loads correct step
   - [ ] Milestones trigger
   - [ ] Milestone modals display
   - [ ] Time tracking works
   - [ ] Progress survives logout
   - [ ] URL parameters work
   - [ ] No data loss
   
   **Integration Tests:**
   - [ ] Enrollment integration works
   - [ ] Progress tracking integrated
   - [ ] Email notifications trigger
   - [ ] Analytics tracking works
   - [ ] Phase 1 systems compatible
   - [ ] No conflicts
   
   **Cross-Browser Tests:**
   - [ ] Chrome (latest)
   - [ ] Firefox (latest)
   - [ ] Safari (latest)
   - [ ] Edge (latest)
   - [ ] Mobile browsers
   
   **Device Tests:**
   - [ ] Desktop (1920x1080)
   - [ ] Laptop (1366x768)
   - [ ] Tablet (iPad)
   - [ ] Mobile (iPhone, Android)

3. **Performance Benchmarks**
   
   Required metrics:
   - Tutorial builder page load: < 1s
   - Admin list page load: < 800ms
   - Archive page load: < 2s
   - Single tutorial page load: < 2s
   - Active tutorial page load: < 1.5s
   - Step navigation: < 500ms
   - AJAX step loading: < 300ms
   - Progress update: < 200ms
   - Database queries per page: < 15

4. **Security Audit Checklist**
   
   - [ ] All AJAX handlers verify nonces
   - [ ] All save operations check capabilities
   - [ ] All inputs sanitized
   - [ ] All outputs escaped
   - [ ] SQL injection prevented
   - [ ] XSS prevented
   - [ ] CSRF protected
   - [ ] File uploads validated
   - [ ] User data validated
   - [ ] No direct file access possible

5. **Accessibility Compliance**
   
   WCAG 2.1 AA requirements:
   - [ ] Keyboard accessible
   - [ ] Screen reader compatible
   - [ ] Color contrast sufficient (4.5:1)
   - [ ] Focus indicators visible
   - [ ] Alt text on images
   - [ ] ARIA labels present
   - [ ] Form labels associated
   - [ ] Error messages clear
   - [ ] Skip links available
   - [ ] Semantic HTML used

6. **Create Validation Admin Page**
   
   Add menu page for running validation:
   ```php
   public function add_validation_page(): void {
       add_submenu_page(
           'edit.php?post_type=aiddata_tutorial',
           __( 'Phase 2 Validation', 'aiddata-lms' ),
           __( 'Validation', 'aiddata-lms' ),
           'manage_options',
           'aiddata-lms-phase-2-validation',
           array( $this, 'render_validation_page' )
       );
   }
   
   public function render_validation_page(): void {
       if ( isset( $_POST['run_validation'] ) ) {
           check_admin_referer( 'aiddata_phase_2_validation' );
           
           $results = AidData_LMS_Phase_2_Validation::run_all_tests();
           $report = AidData_LMS_Phase_2_Validation::generate_report( $results );
           
           echo $report;
       } else {
           include AIDDATA_LMS_PATH . 'includes/admin/views/phase-2-validation.php';
       }
   }
   ```

**Validation Checklist:**
- [ ] All automated tests pass
- [ ] All manual tests pass
- [ ] Performance benchmarks met
- [ ] Security audit clean
- [ ] Accessibility compliant
- [ ] Cross-browser compatible
- [ ] Mobile responsive
- [ ] Integration working
- [ ] No critical bugs
- [ ] Documentation complete

**Expected Outcome:**
- Comprehensive validation report
- All Phase 2 features verified
- Performance acceptable
- Security validated
- Accessibility confirmed
- Ready for Phase 3
- Zero critical issues

---

### PROMPT 8: PHASE 2 DOCUMENTATION & HANDOFF

**Context Required:**
- All Phase 2 implementation files
- All validation reports
- IMPLEMENTATION_CHECKLIST.md

**Objective:**
Document Phase 2 completion, create integration guides, and prepare for Phase 3 handoff.

**Instructions:**

1. **Create Phase 2 Completion Report**
   
   Document:
   - Features implemented
   - Files created/modified
   - Integration points
   - Known limitations
   - Future enhancements
   - Lessons learned

2. **Create Integration Documentation**
   
   For Phase 3 developers:
   - Tutorial structure reference
   - Step data format
   - Progress tracking integration
   - Video player integration points
   - Extension hooks available

3. **Create User Documentation**
   
   - Tutorial builder guide
   - Step creation guide
   - Tutorial management guide
   - Learner experience guide

4. **Update IMPLEMENTATION_CHECKLIST.md**
   
   Mark Phase 2 items complete and document any deviations.

5. **Create PHASE_2_FINAL_SUMMARY.md**
   
   Similar to Phase 1 final summary with all deliverables and statistics.

**Validation Checklist:**
- [ ] Completion report written
- [ ] Integration docs created
- [ ] User documentation complete
- [ ] Checklist updated
- [ ] Final summary created
- [ ] Validation reports filed
- [ ] Ready for Phase 3

**Expected Outcome:**
- Complete Phase 2 documentation
- Clear handoff to Phase 3
- Integration guides available
- User documentation ready
- **PHASE 2 COMPLETE! ✅**

---

## 📋 PHASE 2 COMPLETION CHECKLIST

### Week 6 Deliverables:
- [ ] Basic information meta box functional
- [ ] Settings meta box functional
- [ ] Step builder interface complete
- [ ] All step types supported
- [ ] Drag-drop reordering works
- [ ] Step data persists correctly
- [ ] JavaScript validation working
- [ ] Auto-save compatible

### Week 7 Deliverables:
- [ ] Custom admin columns display
- [ ] Bulk actions functional
- [ ] Quick edit working
- [ ] Admin filters operational
- [ ] CSV export working
- [ ] Frontend archive page styled
- [ ] Single tutorial page complete
- [ ] Enrollment integration functional
- [ ] Responsive design verified

### Week 8 Deliverables:
- [ ] Active tutorial navigation works
- [ ] Progress tracking functional
- [ ] Resume feature operational
- [ ] Step completion tracking
- [ ] Milestone notifications working
- [ ] Time tracking accurate
- [ ] Mobile navigation optimized
- [ ] Integration with Phase 1 systems
- [ ] Video player placeholders ready for Phase 3

### Validation:
- [ ] All features tested
- [ ] No JavaScript errors
- [ ] No PHP warnings/errors
- [ ] Mobile responsive
- [ ] Accessibility compliant (WCAG 2.1 AA)
- [ ] Performance benchmarks met
- [ ] Security measures in place
- [ ] Cross-browser compatible
- [ ] All validation tests pass
- [ ] Documentation complete

### Files Created:
**Backend Classes:**
- [ ] `/includes/admin/class-aiddata-lms-tutorial-meta-boxes.php`
- [ ] `/includes/admin/class-aiddata-lms-phase-2-validation.php`
- [ ] `/includes/admin/views/tutorial-step-builder.php`
- [ ] `/includes/admin/views/step-item.php`
- [ ] `/includes/admin/views/phase-2-validation.php`

**Frontend Templates:**
- [ ] `/templates/archive-aiddata_tutorial.php`
- [ ] `/templates/single-aiddata_tutorial.php`
- [ ] `/templates/template-parts/content-tutorial-card.php`
- [ ] `/templates/template-parts/active-tutorial.php`

**JavaScript:**
- [ ] `/assets/js/admin/tutorial-meta-boxes.js`
- [ ] `/assets/js/admin/tutorial-step-builder.js`
- [ ] `/assets/js/frontend/tutorial-navigation.js`

**CSS:**
- [ ] `/assets/css/admin/tutorial-meta-boxes.css`
- [ ] `/assets/css/admin/tutorial-list.css`
- [ ] `/assets/css/frontend/tutorial-display.css`
- [ ] `/assets/css/frontend/tutorial-navigation.css`

**Enhancements to Existing Files:**
- [ ] `class-aiddata-lms-post-types.php` (custom columns, bulk actions, filters)
- [ ] `class-aiddata-lms-tutorial-ajax.php` (new AJAX endpoints)

### Documentation:
- [ ] Code comments complete
- [ ] Docblocks for all classes and methods
- [ ] Integration documentation created
- [ ] User guides written
- [ ] README updated
- [ ] Validation reports generated
- [ ] Issues documented (if any)

---

## 🎯 SUCCESS CRITERIA

Phase 2 is considered successful when:

1. ✅ **Tutorial Builder**: Admins can create/edit tutorials with multi-step wizard
2. ✅ **Step Management**: All step types (video, text, interactive, resource, quiz) supported
3. ✅ **Tutorial Management**: Enhanced admin list with analytics and bulk actions
4. ✅ **Frontend Display**: Beautiful archive and single tutorial pages
5. ✅ **Navigation**: Interactive active tutorial interface with step navigation
6. ✅ **Progress**: Step completion tracked and persisted correctly
7. ✅ **Resume**: Users can resume tutorials from where they left off
8. ✅ **Milestones**: Progress milestones trigger celebratory notifications
9. ✅ **Integration**: Seamless integration with Phase 0 and Phase 1 systems
10. ✅ **Testing**: All functionality tested and validated
11. ✅ **Performance**: Page loads <2s, interactions <500ms
12. ✅ **Security**: All inputs sanitized, outputs escaped, nonces verified
13. ✅ **Accessibility**: WCAG 2.1 AA compliance verified
14. ✅ **Standards**: Code follows WordPress and plugin coding standards
15. ✅ **Documentation**: Complete user and developer documentation

---

## 📊 PERFORMANCE BENCHMARKS

### Required Metrics:
- **Tutorial Builder Load**: < 1 second
- **Admin List Load**: < 800ms
- **Archive Page Load**: < 2 seconds
- **Single Tutorial Load**: < 2 seconds
- **Active Tutorial Load**: < 1.5 seconds
- **Step Navigation**: < 500ms
- **AJAX Step Loading**: < 300ms
- **Progress Update**: < 200ms
- **Mark Complete**: < 200ms
- **Database Queries**: < 15 per page

### Optimization Targets:
- Asset minification
- Image optimization
- Lazy loading
- Database query caching
- AJAX debouncing
- Progressive enhancement

---

## 🔒 SECURITY REQUIREMENTS

### Mandatory Security Measures:
1. **CSRF Protection**: Nonce verification on all POST requests
2. **XSS Prevention**: All output escaped with appropriate functions
3. **SQL Injection**: All queries use prepared statements
4. **Capability Checks**: User permissions verified
5. **Input Validation**: All user input validated and sanitized
6. **File Security**: No direct file access without ABSPATH check
7. **Data Privacy**: User data handled per GDPR requirements
8. **Error Handling**: No sensitive information in error messages

---

## ♿ ACCESSIBILITY REQUIREMENTS

### WCAG 2.1 AA Compliance:
- **Keyboard Navigation**: All interactive elements accessible via keyboard
- **Screen Readers**: Proper ARIA labels and semantic HTML
- **Color Contrast**: Minimum 4.5:1 ratio for normal text
- **Focus Indicators**: Visible focus states on all interactive elements
- **Alt Text**: Descriptive alt text on all images
- **Form Labels**: All form inputs properly labeled
- **Error Identification**: Clear error messages
- **Consistent Navigation**: Predictable navigation patterns
- **Skip Links**: Skip to main content available
- **Responsive Text**: Text resizable up to 200%

---

## 📚 DOCUMENT REFERENCES FOR EACH PROMPT

### Prompt 1 (Basic Info & Settings):
- IMPLEMENTATION_PATHWAY.md lines 545-585
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md Section 3.1 & 3.3
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 24-149

### Prompt 2 (Step Builder):
- IMPLEMENTATION_PATHWAY.md lines 587-625
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md Section 3.1
- class-aiddata-lms-tutorial-progress.php (Phase 1)

### Prompt 3 (Admin List):
- IMPLEMENTATION_PATHWAY.md lines 627-663
- class-aiddata-lms-post-types.php (Phase 0)
- class-aiddata-lms-tutorial-enrollment.php (Phase 1)

### Prompt 4 (Frontend Display):
- IMPLEMENTATION_PATHWAY.md lines 665-706
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md Section 3.4
- class-aiddata-lms-tutorial-enrollment.php (Phase 1)

### Prompt 5 (Active Tutorial):
- IMPLEMENTATION_PATHWAY.md lines 709-746
- class-aiddata-lms-tutorial-progress.php (Phase 1)
- class-aiddata-lms-tutorial-ajax.php (Phase 1)

### Prompt 6 (Progress Persistence):
- IMPLEMENTATION_PATHWAY.md lines 747-788
- class-aiddata-lms-tutorial-progress.php (Phase 1)
- assets/js/frontend/tutorial-navigation.js (Phase 2, Prompt 5)

### Prompt 7 (Validation):
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md Section 5 (Phase 2)
- INTEGRATION_VALIDATION_MATRIX.md All sections
- IMPLEMENTATION_CHECKLIST.md Phase 2 items

### Prompt 8 (Documentation):
- All Phase 2 implementation files
- All validation reports
- IMPLEMENTATION_CHECKLIST.md

---

## 🚀 NEXT STEPS AFTER PHASE 2

Once Phase 2 validation passes:

1. **Review Phase 3 Requirements**: Read IMPLEMENTATION_PATHWAY.md Phase 3 (lines 791-965)
2. **Prepare Video Integration**: Review video platform APIs (Panopto, YouTube, Vimeo)
3. **Create Sprint 4**: Use SPRINT_PLANNING_TEMPLATE.md (if available)
4. **Load Context**: Reference documents for Phase 3:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md Section 3.5 (Video Tracking)
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md Section 6 (Phase 3 Validation)
   - INTEGRATION_VALIDATION_MATRIX.md Section 8 (Video Integration)
5. **Begin Phase 3**: Start with universal video player wrapper
6. **Integration Point**: Phase 2 has prepared video player containers; Phase 3 will implement actual players

---

## 🔄 INTEGRATION WITH PHASE 0 & 1

### Phase 0 Integration:
- ✅ Uses database tables created in Phase 0
- ✅ Extends tutorial post type from Phase 0
- ✅ Uses taxonomies (categories, tags, difficulty) from Phase 0
- ✅ Utilizes autoloader from Phase 0
- ✅ Follows core plugin structure from Phase 0

### Phase 1 Integration:
- ✅ Enrollment button integration (from Prompt 4)
- ✅ Progress tracking integration (from Prompt 2)
- ✅ AJAX handlers extension (from Prompt 3)
- ✅ Email triggers on milestones (will use Prompt 6)
- ✅ Analytics events on tutorial interactions (Prompt 7)
- ✅ Dashboard widgets display tutorial data (Prompt 8)

### Phase 3 Preparation:
- ✅ Video step containers ready
- ✅ Video platform detection implemented
- ✅ Video URL validation in place
- ✅ Video progress tracking hooks prepared
- ✅ Step content rendering extensible

---

## 💡 BEST PRACTICES APPLIED

### Code Organization:
- Modular class structure
- Separation of concerns
- DRY principle (Don't Repeat Yourself)
- Single Responsibility Principle
- Dependency injection where appropriate

### WordPress Standards:
- WordPress Coding Standards followed
- Hooks used for extensibility
- Custom post types and taxonomies properly registered
- Template hierarchy respected
- Admin UI consistent with WordPress

### User Experience:
- Progressive disclosure of complexity
- Immediate feedback on actions
- Clear error messages
- Consistent navigation patterns
- Mobile-first responsive design

### Performance:
- Lazy loading of assets
- AJAX for dynamic content
- Database query optimization
- Asset minification
- Browser caching headers

### Security:
- Defense in depth approach
- Principle of least privilege
- Input validation and sanitization
- Output escaping
- Secure coding practices

---

## 🎓 LEARNING OUTCOMES

After Phase 2 implementation, developers should understand:

1. **WordPress Admin UI Development**: Meta boxes, custom columns, bulk actions
2. **JavaScript UI Development**: Drag-drop, dynamic forms, AJAX interactions
3. **Template Development**: WordPress template hierarchy, custom templates
4. **Frontend JavaScript**: Step navigation, progress tracking, real-time updates
5. **Data Persistence**: Progress tracking, session management, resume functionality
6. **Integration**: Connecting multiple systems seamlessly
7. **Performance Optimization**: Frontend and backend optimization techniques
8. **Accessibility**: WCAG compliance implementation
9. **Security**: WordPress security best practices
10. **Testing & Validation**: Comprehensive testing methodologies

---

## 📝 KNOWN LIMITATIONS

### Intentional Limitations (Will be addressed in later phases):
- Quiz step rendering is placeholder (Phase 4 will implement full quiz system)
- Video tracking is placeholder (Phase 3 will implement actual video tracking)
- Certificate generation not yet triggered (Phase 4 will implement)
- Advanced analytics not yet available (Phase 5 will enhance)
- Mobile app integration not included (Future phase)

### Technical Limitations:
- Browser compatibility: IE11 not supported (modern browsers only)
- File size limits: Depend on WordPress/server configuration
- Video platforms: Limited to Panopto, YouTube, Vimeo, HTML5
- Step types: Fixed set of types (extensible via hooks in future)

---

## 🤝 CONTRIBUTION GUIDELINES

For developers extending Phase 2:

### Adding New Step Types:
1. Add step type to allowed types array
2. Create renderer method in active-tutorial.php
3. Add step editor template in step-builder.js
4. Update validation to handle new type
5. Add CSS styling for new type
6. Document the new step type

### Extending Tutorial Builder:
1. Use `aiddata_lms_tutorial_meta_saved` hook
2. Create custom meta box with separate nonce
3. Follow validation pattern from existing meta boxes
4. Ensure JavaScript doesn't conflict
5. Add to validation test suite

### Frontend Customization:
1. Templates can be overridden in theme
2. Use CSS classes for styling hooks
3. JavaScript events fired for extensibility
4. Hooks available for content modification
5. Filters available for data transformation

---

## ✅ PHASE 2 PROMPTS DOCUMENT COMPLETE

This comprehensive document provides detailed, context-aware prompts for implementing Phase 2 of the AidData LMS Tutorial Builder. Each prompt:

- References specific documentation sections for context permanence
- Builds systematically on Phase 0 and Phase 1 foundations
- Includes detailed implementation instructions
- Provides validation checklists
- Specifies expected outcomes
- Ensures integration compatibility

**Key Features of This Document:**
- **8 Detailed Prompts** covering 3 weeks of development
- **Comprehensive Context Loading** instructions for each prompt
- **Integration Points** clearly documented with Phase 0 & 1
- **Validation Criteria** for each deliverable
- **Security & Accessibility** requirements embedded
- **Performance Benchmarks** specified
- **Best Practices** applied throughout
- **Future Phase Preparation** considered

**Remember:**
- **ALWAYS** load ALL referenced documents into context before starting each prompt
- **FOLLOW** coding standards strictly (WordPress, PHP, JavaScript)
- **TEST** thoroughly after each prompt (manual and automated)
- **UPDATE** IMPLEMENTATION_CHECKLIST.md as you progress
- **DOCUMENT** any deviations, issues, or challenges encountered
- **ENSURE** integration with existing Phase 0 and 1 systems
- **VALIDATE** security, accessibility, and performance
- **PREPARE** validation reports for each prompt completion

**Critical Success Factors:**
1. ✅ Complete context loading before each prompt
2. ✅ Systematic building on previous phases
3. ✅ Comprehensive testing and validation
4. ✅ Clear documentation of all changes
5. ✅ Integration verification with existing systems
6. ✅ Security and accessibility compliance
7. ✅ Performance optimization
8. ✅ User experience excellence

**Phase 2 Implementation Goal:**
Create a fully functional, professional-grade tutorial builder and management system that enables administrators to create rich, multi-step tutorials and provides learners with an engaging, trackable learning experience.

---

## 🎉 READY FOR PHASE 2 IMPLEMENTATION!

**This document is your comprehensive guide for Phase 2 development. Good luck! 🚀**

---

**Document Version:** 1.0  
**Created:** October 22, 2025  
**Last Updated:** October 22, 2025  
**Status:** COMPLETE ✅  
**Next Phase:** Phase 3 - Video Tracking System

---

**End of Document**