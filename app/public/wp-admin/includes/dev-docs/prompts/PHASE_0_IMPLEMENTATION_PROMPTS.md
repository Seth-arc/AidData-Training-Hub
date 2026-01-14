# PHASE 0 IMPLEMENTATION PROMPTS
## AidData LMS Tutorial Builder - Foundation & Setup (Weeks 1-2)

**Version:** 1.0  
**Date:** October 22, 2025  
**Purpose:** Detailed prompts for AI agent implementation of Phase 0  
**Context Documents:** Reference these for persistent context throughout implementation

---

## 📚 REQUIRED CONTEXT DOCUMENTS

**CRITICAL: Load these documents into context before starting each prompt:**

### Primary References
1. **TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md**
   - Section 2.2: Database Schema (lines 258-654)
   - Section 2.3: File Structure (lines 656-730)
   - Section 2.4: Class Architecture (lines 732-856)

2. **IMPLEMENTATION_PATHWAY.md**
   - Phase 0: Foundation & Setup (lines 97-281)
   - Development Standards (lines 2046-2180)

3. **CODE_STANDARDS_AND_VALIDATION_GUIDE.md**
   - Section 1: Code Standards & Rules (lines 21-402)
   - Section 3: Phase 0 Validation (lines 486-541)

4. **INTEGRATION_VALIDATION_MATRIX.md**
   - Section 2: Database Integration Matrix (lines 69-224)
   - Section 7: Conflict Prevention Rules (lines 586-698)

### Supporting References
5. **IMPLEMENTATION_CHECKLIST.md** - Phase 0 checklist (lines 7-25)
6. **QUALITY_ASSURANCE_SUMMARY.md** - Phase 0 validation (lines 172-193)

---

## 🎯 PHASE 0 OVERVIEW

**Goal:** Establish development environment, database architecture, and core plugin structure

**Duration:** 2 weeks (10 working days)

**Deliverables:**
- ✅ Working local development environment
- ✅ Complete database schema with all tables
- ✅ Core plugin structure with autoloader
- ✅ Custom post types and taxonomies registered
- ✅ Plugin activates without errors

---

## WEEK 1: ENVIRONMENT & DATABASE

---

### PROMPT 1: PROJECT SETUP & ENVIRONMENT CONFIGURATION

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 0 → Week 1 → Days 1-2
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 1
- Project directory structure from workspace

**Objective:**
Set up the basic WordPress plugin structure with proper file organization, security headers, and configuration files.

**Instructions:**

1. **Create Main Plugin File** (`aiddata-lms.php`)
   
   Requirements:
   - WordPress plugin header with all required fields
   - PHP version check (minimum 8.1)
   - WordPress version check (minimum 6.4)
   - Security check (`ABSPATH` defined)
   - Plugin constants definition (VERSION, PATH, URL, BASENAME)
   - Textdomain for internationalization
   - Activation/deactivation hooks
   - Include autoloader
   
   Reference:
   - IMPLEMENTATION_PATHWAY.md lines 193-219 for structure
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 24-83 for PHP standards
   
   File location: `/aiddata-lms.php`

2. **Create Composer Configuration** (`composer.json`)
   
   Requirements:
   - PSR-4 autoloading for `AidData_LMS` namespace
   - Development dependencies (PHPUnit, PHP_CodeSniffer, PHPStan)
   - Scripts for linting, testing, validation
   - Minimum PHP version 8.1
   - WordPress as platform requirement
   
   File location: `/composer.json`

3. **Create Package Configuration** (`package.json`)
   
   Requirements:
   - Project metadata
   - Development dependencies (ESLint, Stylelint, Jest)
   - Build scripts for assets
   - Linting scripts
   - Testing scripts
   - Minimum Node.js version 18
   
   File location: `/package.json`

4. **Create EditorConfig** (`.editorconfig`)
   
   Requirements:
   - Tabs for indentation in PHP files
   - Spaces for JavaScript, CSS, JSON
   - UTF-8 charset
   - Unix line endings
   - Trim trailing whitespace
   - Insert final newline
   
   File location: `/.editorconfig`

5. **Create Git Ignore** (`.gitignore`)
   
   Requirements:
   - Ignore node_modules/
   - Ignore vendor/
   - Ignore .DS_Store and OS files
   - Ignore IDE files (.vscode, .idea)
   - Ignore built assets (compiled CSS/JS)
   - Keep source files in Git
   
   File location: `/.gitignore`

6. **Create Directory Structure**
   
   Create the following directories:
   ```
   /includes/
   /includes/admin/
   /includes/tutorials/
   /includes/video/
   /includes/quiz/
   /includes/certificates/
   /includes/email/
   /includes/analytics/
   /includes/api/
   /assets/
   /assets/js/
   /assets/js/admin/
   /assets/js/frontend/
   /assets/js/blocks/
   /assets/css/
   /assets/css/admin/
   /assets/css/frontend/
   /assets/css/blocks/
   /assets/templates/
   /assets/templates/email/
   /assets/templates/certificates/
   /templates/
   /templates/template-parts/
   /languages/
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 656-730 for complete structure
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 408-441 for validation

**Validation Checklist:**
- [ ] Main plugin file has correct header
- [ ] Security check present (`ABSPATH`)
- [ ] All constants defined
- [ ] Version numbers consistent across files
- [ ] composer.json valid JSON
- [ ] package.json valid JSON
- [ ] All directories created
- [ ] No syntax errors in any file

**Expected Outcome:**
- Basic plugin structure exists
- Configuration files ready
- Directory structure matches specification
- Ready for autoloader implementation

---

### PROMPT 2: AUTOLOADER IMPLEMENTATION

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 0 → Week 2 → Days 1-3 (lines 221-237)
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 1.1 (PHP standards)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 2.4 (Class Architecture)

**Objective:**
Implement PSR-4 compliant autoloader for the plugin with proper namespace mapping.

**Instructions:**

1. **Create Autoloader Class** (`includes/class-aiddata-lms-autoloader.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Autoloader`
   - Static method: `register()`
   - Static method: `autoload( $class )`
   - PSR-4 compliant class loading
   - Namespace: `AidData_LMS`
   - Map namespace to `/includes/` directory
   - Convert underscores to directory separators
   - Convert class names to filenames (lowercase with hyphens)
   - Validate file exists before requiring
   - Handle nested namespaces
   
   Mapping logic:
   ```php
   AidData_LMS_Tutorial → /includes/class-aiddata-lms-tutorial.php
   AidData_LMS_Tutorial_Enrollment → /includes/tutorials/class-aiddata-lms-tutorial-enrollment.php
   AidData_LMS_Video_Tracker → /includes/video/class-aiddata-lms-video-tracker.php
   ```
   
   Reference:
   - IMPLEMENTATION_PATHWAY.md lines 221-237 for structure
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 28-73 for coding standards
   
   File location: `/includes/class-aiddata-lms-autoloader.php`

2. **Update Main Plugin File**
   
   Requirements:
   - Require autoloader file
   - Register autoloader with `spl_autoload_register()`
   - Add error handling if autoloader fails
   - Place before any class usage
   
   Example:
   ```php
   require_once AIDDATA_LMS_PATH . 'includes/class-aiddata-lms-autoloader.php';
   AidData_LMS_Autoloader::register();
   ```
   
   File location: `/aiddata-lms.php` (update existing)

3. **Create Test Classes**
   
   Create simple test classes to verify autoloader:
   - `includes/class-aiddata-lms-test.php`
   - `includes/admin/class-aiddata-lms-admin-test.php`
   - `includes/tutorials/class-aiddata-lms-tutorial-test.php`
   
   Each should have a simple method that returns a success message.

**Validation Checklist:**
- [ ] Autoloader class follows naming conventions
- [ ] Docblocks complete
- [ ] Type hints used
- [ ] Error handling present
- [ ] Test classes load without error
- [ ] Nested namespaces work
- [ ] No PHP warnings or notices
- [ ] Follows PSR-4 standard

**Expected Outcome:**
- Autoloader functional
- Classes load automatically when instantiated
- No need for manual `require` statements
- Test classes verify autoloader works

---

### PROMPT 3: DATABASE SCHEMA IMPLEMENTATION (Part 1: Core Tables)

**Context Required:**
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 2.2 Database Schema (lines 258-654)
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 1.3 Database Standards (lines 262-337)
- INTEGRATION_VALIDATION_MATRIX.md → Section 2 Database Integration (lines 69-224)

**Objective:**
Implement the core database tables with proper structure, foreign keys, and indexes.

**Instructions:**

1. **Create Database Installation Class** (`includes/class-aiddata-lms-install.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Install`
   - Static method: `install()`
   - Static method: `create_tables()`
   - Static method: `create_default_options()`
   - Static method: `create_capabilities()`
   - Use `dbDelta()` for table creation
   - Store database version in options
   - Handle upgrade scenarios
   
   File location: `/includes/class-aiddata-lms-install.php`

2. **Implement Tutorial Enrollments Table**
   
   Table name: `{$wpdb->prefix}aiddata_lms_tutorial_enrollments`
   
   Schema:
   ```sql
   CREATE TABLE {$wpdb->prefix}aiddata_lms_tutorial_enrollments (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       user_id BIGINT UNSIGNED NOT NULL,
       tutorial_id BIGINT UNSIGNED NOT NULL,
       enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       completed_at DATETIME NULL,
       status VARCHAR(20) NOT NULL DEFAULT 'active',
       source VARCHAR(50) NULL DEFAULT 'web',
       UNIQUE KEY user_tutorial (user_id, tutorial_id),
       KEY tutorial_id (tutorial_id),
       KEY status (status),
       KEY enrolled_at (enrolled_at),
       KEY completed_at (completed_at),
       CONSTRAINT fk_enrollment_user 
           FOREIGN KEY (user_id) 
           REFERENCES {$wpdb->prefix}users(ID) 
           ON DELETE CASCADE,
       CONSTRAINT fk_enrollment_tutorial 
           FOREIGN KEY (tutorial_id) 
           REFERENCES {$wpdb->prefix}posts(ID) 
           ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 267-301
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 266-291

3. **Implement Tutorial Progress Table**
   
   Table name: `{$wpdb->prefix}aiddata_lms_tutorial_progress`
   
   Schema:
   ```sql
   CREATE TABLE {$wpdb->prefix}aiddata_lms_tutorial_progress (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       user_id BIGINT UNSIGNED NOT NULL,
       tutorial_id BIGINT UNSIGNED NOT NULL,
       enrollment_id BIGINT UNSIGNED NULL,
       current_step INT UNSIGNED NOT NULL DEFAULT 0,
       completed_steps TEXT NULL,
       progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
       status VARCHAR(20) NOT NULL DEFAULT 'not_started',
       quiz_passed TINYINT(1) NOT NULL DEFAULT 0,
       quiz_score DECIMAL(5,2) NULL,
       quiz_attempts INT UNSIGNED NOT NULL DEFAULT 0,
       last_accessed DATETIME NULL,
       completed_at DATETIME NULL,
       time_spent INT UNSIGNED NOT NULL DEFAULT 0,
       updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       UNIQUE KEY user_tutorial (user_id, tutorial_id),
       KEY tutorial_id (tutorial_id),
       KEY enrollment_id (enrollment_id),
       KEY status (status),
       KEY progress_percent (progress_percent),
       KEY last_accessed (last_accessed),
       CONSTRAINT fk_progress_user 
           FOREIGN KEY (user_id) 
           REFERENCES {$wpdb->prefix}users(ID) 
           ON DELETE CASCADE,
       CONSTRAINT fk_progress_tutorial 
           FOREIGN KEY (tutorial_id) 
           REFERENCES {$wpdb->prefix}posts(ID) 
           ON DELETE CASCADE,
       CONSTRAINT fk_progress_enrollment 
           FOREIGN KEY (enrollment_id) 
           REFERENCES {$wpdb->prefix}aiddata_lms_tutorial_enrollments(id) 
           ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 338-382

4. **Implement Video Progress Table**
   
   Table name: `{$wpdb->prefix}aiddata_lms_video_progress`
   
   Schema:
   ```sql
   CREATE TABLE {$wpdb->prefix}aiddata_lms_video_progress (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       user_id BIGINT UNSIGNED NOT NULL,
       tutorial_id BIGINT UNSIGNED NOT NULL,
       step_index INT UNSIGNED NOT NULL,
       video_url VARCHAR(500) NOT NULL,
       video_platform VARCHAR(50) NOT NULL,
       current_position INT UNSIGNED NOT NULL DEFAULT 0,
       total_duration INT UNSIGNED NOT NULL DEFAULT 0,
       watch_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
       completed TINYINT(1) NOT NULL DEFAULT 0,
       completed_at DATETIME NULL,
       watch_sessions INT UNSIGNED NOT NULL DEFAULT 0,
       total_watch_time INT UNSIGNED NOT NULL DEFAULT 0,
       last_position_update DATETIME NULL,
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       UNIQUE KEY user_tutorial_step (user_id, tutorial_id, step_index),
       KEY tutorial_id (tutorial_id),
       KEY step_index (step_index),
       KEY completed (completed),
       CONSTRAINT fk_video_user 
           FOREIGN KEY (user_id) 
           REFERENCES {$wpdb->prefix}users(ID) 
           ON DELETE CASCADE,
       CONSTRAINT fk_video_tutorial 
           FOREIGN KEY (tutorial_id) 
           REFERENCES {$wpdb->prefix}posts(ID) 
           ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 384-426

5. **Register Activation Hook**
   
   In main plugin file (`aiddata-lms.php`), add:
   ```php
   register_activation_hook( __FILE__, array( 'AidData_LMS_Install', 'install' ) );
   ```

**Validation Checklist:**
- [ ] All table names use correct prefix (`wp_aiddata_lms_`)
- [ ] All primary keys are `BIGINT UNSIGNED AUTO_INCREMENT`
- [ ] All foreign keys reference correct tables
- [ ] All foreign keys have `ON DELETE CASCADE`
- [ ] All tables use InnoDB engine
- [ ] All tables use utf8mb4 charset
- [ ] All indexes created
- [ ] Unique constraints on user_id + tutorial_id combinations
- [ ] No syntax errors in SQL
- [ ] dbDelta() requirements met (spaces, formatting)

**Expected Outcome:**
- Installation class created
- Core tables defined correctly
- Activation hook registered
- Database schema validation passes

---

### PROMPT 4: DATABASE SCHEMA IMPLEMENTATION (Part 2: Supporting Tables)

**Context Required:**
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 2.2 Database Schema (lines 258-654)
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 1.3 Database Standards
- INTEGRATION_VALIDATION_MATRIX.md → Section 2.3 Data Consistency (lines 107-155)

**Objective:**
Complete the database schema with certificates, analytics, and email queue tables.

**Instructions:**

1. **Implement Certificates Table**
   
   Table name: `{$wpdb->prefix}aiddata_lms_certificates`
   
   Schema:
   ```sql
   CREATE TABLE {$wpdb->prefix}aiddata_lms_certificates (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       certificate_code VARCHAR(32) UNIQUE NOT NULL,
       user_id BIGINT UNSIGNED NOT NULL,
       tutorial_id BIGINT UNSIGNED NOT NULL,
       user_name VARCHAR(255) NOT NULL,
       tutorial_title VARCHAR(255) NOT NULL,
       completion_date DATE NOT NULL,
       issued_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       template_id VARCHAR(50) NULL DEFAULT 'default',
       certificate_data TEXT NULL,
       pdf_path VARCHAR(500) NULL,
       verification_url VARCHAR(500) NULL,
       downloads INT UNSIGNED NOT NULL DEFAULT 0,
       last_downloaded DATETIME NULL,
       status VARCHAR(20) NOT NULL DEFAULT 'active',
       revoked_at DATETIME NULL,
       revoked_reason TEXT NULL,
       UNIQUE KEY user_tutorial (user_id, tutorial_id),
       KEY tutorial_id (tutorial_id),
       KEY certificate_code (certificate_code),
       KEY issued_date (issued_date),
       KEY status (status),
       CONSTRAINT fk_cert_user 
           FOREIGN KEY (user_id) 
           REFERENCES {$wpdb->prefix}users(ID) 
           ON DELETE CASCADE,
       CONSTRAINT fk_cert_tutorial 
           FOREIGN KEY (tutorial_id) 
           REFERENCES {$wpdb->prefix}posts(ID) 
           ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 428-470

2. **Implement Tutorial Analytics Table**
   
   Table name: `{$wpdb->prefix}aiddata_lms_tutorial_analytics`
   
   Schema:
   ```sql
   CREATE TABLE {$wpdb->prefix}aiddata_lms_tutorial_analytics (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       tutorial_id BIGINT UNSIGNED NOT NULL,
       user_id BIGINT UNSIGNED NULL,
       event_type VARCHAR(50) NOT NULL,
       event_data TEXT NULL,
       session_id VARCHAR(64) NULL,
       ip_address VARCHAR(45) NULL,
       user_agent TEXT NULL,
       referrer VARCHAR(500) NULL,
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       KEY tutorial_id (tutorial_id),
       KEY user_id (user_id),
       KEY event_type (event_type),
       KEY session_id (session_id),
       KEY created_at (created_at),
       CONSTRAINT fk_analytics_tutorial 
           FOREIGN KEY (tutorial_id) 
           REFERENCES {$wpdb->prefix}posts(ID) 
           ON DELETE CASCADE,
       CONSTRAINT fk_analytics_user 
           FOREIGN KEY (user_id) 
           REFERENCES {$wpdb->prefix}users(ID) 
           ON DELETE SET NULL
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 472-509

3. **Implement Email Queue Table**
   
   Table name: `{$wpdb->prefix}aiddata_lms_email_queue`
   
   Schema:
   ```sql
   CREATE TABLE {$wpdb->prefix}aiddata_lms_email_queue (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       recipient_email VARCHAR(255) NOT NULL,
       recipient_name VARCHAR(255) NULL,
       user_id BIGINT UNSIGNED NULL,
       subject VARCHAR(500) NOT NULL,
       message LONGTEXT NOT NULL,
       email_type VARCHAR(50) NOT NULL,
       template_id VARCHAR(50) NULL,
       template_data TEXT NULL,
       priority TINYINT UNSIGNED NOT NULL DEFAULT 5,
       status VARCHAR(20) NOT NULL DEFAULT 'pending',
       attempts INT UNSIGNED NOT NULL DEFAULT 0,
       last_attempt DATETIME NULL,
       scheduled_for DATETIME NULL,
       sent_at DATETIME NULL,
       error_message TEXT NULL,
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       KEY recipient_email (recipient_email),
       KEY user_id (user_id),
       KEY email_type (email_type),
       KEY status (status),
       KEY priority (priority),
       KEY scheduled_for (scheduled_for),
       KEY created_at (created_at),
       CONSTRAINT fk_email_user 
           FOREIGN KEY (user_id) 
           REFERENCES {$wpdb->prefix}users(ID) 
           ON DELETE SET NULL
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 511-552

4. **Add Table Creation to Install Class**
   
   Update `includes/class-aiddata-lms-install.php`:
   - Add all 4 new tables to `create_tables()` method
   - Use `dbDelta()` for each table
   - Handle errors gracefully
   - Log any issues
   
5. **Create Database Helper Class** (`includes/class-aiddata-lms-database.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Database`
   - Static method: `table_exists( $table_name )`
   - Static method: `get_table_name( $table_key )`
   - Static method: `verify_schema()`
   - Static method: `get_all_tables()`
   - Table name constants (e.g., `TABLE_ENROLLMENTS`)
   
   Reference:
   - IMPLEMENTATION_PATHWAY.md lines 165-173

**Validation Checklist:**
- [ ] All tables follow naming conventions
- [ ] All foreign keys defined correctly
- [ ] All indexes created
- [ ] dbDelta() compatible SQL
- [ ] Helper class provides table name access
- [ ] Schema verification method works
- [ ] All validation queries from INTEGRATION_VALIDATION_MATRIX.md can run

**Expected Outcome:**
- All 7 database tables defined
- Database helper class functional
- Schema verification method working
- Ready for plugin activation test

---

### PROMPT 5: DATABASE TESTING & VALIDATION

**Context Required:**
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 3 Phase 0 Validation (lines 486-541)
- INTEGRATION_VALIDATION_MATRIX.md → Section 2 Database Integration (lines 69-224)
- IMPLEMENTATION_CHECKLIST.md → Phase 0 checklist

**Objective:**
Test database installation, verify schema integrity, and validate all tables.

**Instructions:**

1. **Create Database Test Class** (`includes/class-aiddata-lms-database-test.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Database_Test`
   - Static method: `run_tests()`
   - Static method: `test_table_exists( $table_name )`
   - Static method: `test_foreign_keys( $table_name )`
   - Static method: `test_indexes( $table_name )`
   - Static method: `test_data_integrity()`
   - Return array of test results
   - Log all results
   
   File location: `/includes/class-aiddata-lms-database-test.php`

2. **Implement Table Existence Tests**
   
   Verify all tables exist:
   - wp_aiddata_lms_tutorial_enrollments
   - wp_aiddata_lms_tutorial_progress
   - wp_aiddata_lms_video_progress
   - wp_aiddata_lms_certificates
   - wp_aiddata_lms_tutorial_analytics
   - wp_aiddata_lms_email_queue
   
   Use: `SHOW TABLES LIKE '{table_name}'`

3. **Implement Schema Validation Tests**
   
   For each table, verify:
   - Primary key exists and is BIGINT UNSIGNED AUTO_INCREMENT
   - All foreign keys exist
   - All indexes exist
   - Column types match specification
   - Character set is utf8mb4
   - Collation is utf8mb4_unicode_ci
   - Engine is InnoDB
   
   Use: `DESCRIBE {table_name}` and `SHOW CREATE TABLE {table_name}`

4. **Implement Foreign Key Validation**
   
   Run validation queries from INTEGRATION_VALIDATION_MATRIX.md:
   ```sql
   -- Verify no orphaned records (should return 0)
   SELECT COUNT(*) FROM wp_aiddata_lms_tutorial_enrollments e
   LEFT JOIN wp_users u ON e.user_id = u.ID
   WHERE u.ID IS NULL;
   
   SELECT COUNT(*) FROM wp_aiddata_lms_tutorial_enrollments e
   LEFT JOIN wp_posts p ON e.tutorial_id = p.ID
   WHERE p.ID IS NULL;
   
   -- Similar queries for all foreign keys
   ```
   
   Reference:
   - INTEGRATION_VALIDATION_MATRIX.md lines 95-105

5. **Create Admin Test Page**
   
   Create admin page to run tests:
   - File: `includes/admin/class-aiddata-lms-admin-database-test.php`
   - Menu item: "Database Tests" under plugin menu
   - Display test results in admin
   - Show pass/fail for each test
   - Allow manual test execution
   - Show SQL queries used
   
6. **Create Activation Test**
   
   Requirements:
   - Activate plugin in test environment
   - Verify no PHP errors
   - Verify all tables created
   - Verify database version option saved
   - Verify capabilities created
   - Run all validation tests
   - Generate validation report

**Validation Checklist:**
- [ ] All tables exist after activation
- [ ] All tables have correct structure
- [ ] All foreign keys functional
- [ ] All indexes exist
- [ ] Character set utf8mb4
- [ ] Collation utf8mb4_unicode_ci
- [ ] Engine InnoDB
- [ ] No orphaned records possible
- [ ] Database version stored
- [ ] Validation tests pass

**Expected Outcome:**
- Database installation verified
- All tests passing
- Validation report generated
- Ready for core plugin development

---

## WEEK 2: CORE PLUGIN STRUCTURE

---

### PROMPT 6: CORE PLUGIN CLASS IMPLEMENTATION

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 0 → Week 2 → Days 1-3 (lines 188-243)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 2.4 Class Architecture (lines 732-856)
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 1.1 PHP Standards (lines 24-149)

**Objective:**
Implement the core plugin class using singleton pattern with dependency injection container.

**Instructions:**

1. **Create Core Plugin Class** (`includes/class-aiddata-lms.php`)
   
   Requirements:
   - Class name: `AidData_LMS`
   - Singleton pattern implementation
   - Static method: `instance()`
   - Private constructor: `__construct()`
   - Method: `define_constants()`
   - Method: `load_dependencies()`
   - Method: `set_locale()`
   - Method: `define_admin_hooks()`
   - Method: `define_public_hooks()`
   - Method: `run()`
   - Property: `$loader` (hook loader)
   - Property: `$version`
   - Property: `$plugin_name`
   
   Singleton implementation:
   ```php
   private static $instance = null;
   
   public static function instance() {
       if ( null === self::$instance ) {
           self::$instance = new self();
       }
       return self::$instance;
   }
   
   private function __construct() {
       $this->version = AIDDATA_LMS_VERSION;
       $this->plugin_name = 'aiddata-lms';
       
       $this->load_dependencies();
       $this->set_locale();
       $this->define_admin_hooks();
       $this->define_public_hooks();
   }
   ```
   
   Reference:
   - IMPLEMENTATION_PATHWAY.md lines 229-237
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 28-73
   
   File location: `/includes/class-aiddata-lms.php`

2. **Create Hook Loader Class** (`includes/class-aiddata-lms-loader.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Loader`
   - Property: `$actions` (array)
   - Property: `$filters` (array)
   - Method: `add_action( $hook, $component, $callback, $priority, $accepted_args )`
   - Method: `add_filter( $hook, $component, $callback, $priority, $accepted_args )`
   - Method: `run()` - Registers all hooks
   
   Purpose: Centralize WordPress hook management
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 756-789
   
   File location: `/includes/class-aiddata-lms-loader.php`

3. **Create Internationalization Class** (`includes/class-aiddata-lms-i18n.php`)
   
   Requirements:
   - Class name: `AidData_LMS_i18n`
   - Method: `load_plugin_textdomain()`
   - Set textdomain: 'aiddata-lms'
   - Load from: '/languages/'
   
   File location: `/includes/class-aiddata-lms-i18n.php`

4. **Implement Dependency Injection Container** (Simple version)
   
   Add to core class:
   ```php
   private $container = array();
   
   public function set( $key, $value ) {
       $this->container[ $key ] = $value;
   }
   
   public function get( $key ) {
       return isset( $this->container[ $key ] ) ? $this->container[ $key ] : null;
   }
   
   public function has( $key ) {
       return isset( $this->container[ $key ] );
   }
   ```

5. **Update Main Plugin File**
   
   Replace initialization code:
   ```php
   function aiddata_lms_init() {
       return AidData_LMS::instance();
   }
   add_action( 'plugins_loaded', 'aiddata_lms_init' );
   ```

**Validation Checklist:**
- [ ] Singleton pattern correctly implemented
- [ ] Only one instance possible
- [ ] Hook loader functional
- [ ] All hooks registered correctly
- [ ] Internationalization working
- [ ] Dependency container functional
- [ ] No global variables used
- [ ] All methods have docblocks
- [ ] Type hints used
- [ ] Error handling present

**Expected Outcome:**
- Core plugin class functional
- Hook management centralized
- Ready for feature module registration
- Plugin initializes correctly

---

### PROMPT 7: CUSTOM POST TYPES REGISTRATION

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 0 → Week 2 → Days 4-5 (lines 245-280)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.1 Tutorial CPT (lines 886-991)
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 3 Phase 0 Validation (lines 519-533)

**Objective:**
Register the `aiddata_tutorial` custom post type with Gutenberg support, REST API, and custom capabilities.

**Instructions:**

1. **Create Post Types Class** (`includes/class-aiddata-lms-post-types.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Post_Types`
   - Method: `__construct()`
   - Method: `register_post_types()`
   - Method: `register_tutorial_post_type()`
   - Method: `register_quiz_post_type()`
   - Hook: `add_action( 'init', array( $this, 'register_post_types' ) )`
   
   File location: `/includes/class-aiddata-lms-post-types.php`

2. **Implement Tutorial Post Type**
   
   Configuration:
   ```php
   $labels = array(
       'name'                  => _x( 'Tutorials', 'Post type general name', 'aiddata-lms' ),
       'singular_name'         => _x( 'Tutorial', 'Post type singular name', 'aiddata-lms' ),
       'menu_name'             => _x( 'Tutorials', 'Admin Menu text', 'aiddata-lms' ),
       'name_admin_bar'        => _x( 'Tutorial', 'Add New on Toolbar', 'aiddata-lms' ),
       'add_new'               => __( 'Add New', 'aiddata-lms' ),
       'add_new_item'          => __( 'Add New Tutorial', 'aiddata-lms' ),
       'new_item'              => __( 'New Tutorial', 'aiddata-lms' ),
       'edit_item'             => __( 'Edit Tutorial', 'aiddata-lms' ),
       'view_item'             => __( 'View Tutorial', 'aiddata-lms' ),
       'all_items'             => __( 'All Tutorials', 'aiddata-lms' ),
       'search_items'          => __( 'Search Tutorials', 'aiddata-lms' ),
       'parent_item_colon'     => __( 'Parent Tutorials:', 'aiddata-lms' ),
       'not_found'             => __( 'No tutorials found.', 'aiddata-lms' ),
       'not_found_in_trash'    => __( 'No tutorials found in Trash.', 'aiddata-lms' ),
       'featured_image'        => _x( 'Tutorial Cover Image', 'Overrides the "Featured Image" phrase', 'aiddata-lms' ),
       'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'aiddata-lms' ),
       'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'aiddata-lms' ),
       'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'aiddata-lms' ),
       'archives'              => _x( 'Tutorial archives', 'The post type archive label used in nav menus', 'aiddata-lms' ),
       'insert_into_item'      => _x( 'Insert into tutorial', 'Overrides the "Insert into post" phrase', 'aiddata-lms' ),
       'uploaded_to_this_item' => _x( 'Uploaded to this tutorial', 'Overrides the "Uploaded to this post" phrase', 'aiddata-lms' ),
       'filter_items_list'     => _x( 'Filter tutorials list', 'Screen reader text', 'aiddata-lms' ),
       'items_list_navigation' => _x( 'Tutorials list navigation', 'Screen reader text', 'aiddata-lms' ),
       'items_list'            => _x( 'Tutorials list', 'Screen reader text', 'aiddata-lms' ),
   );
   
   $args = array(
       'labels'             => $labels,
       'description'        => __( 'Tutorial courses and lessons', 'aiddata-lms' ),
       'public'             => true,
       'publicly_queryable' => true,
       'show_ui'            => true,
       'show_in_menu'       => true,
       'query_var'          => true,
       'rewrite'            => array( 'slug' => 'tutorial' ),
       'capability_type'    => array( 'aiddata_tutorial', 'aiddata_tutorials' ),
       'map_meta_cap'       => true,
       'has_archive'        => true,
       'hierarchical'       => false,
       'menu_position'      => 20,
       'menu_icon'          => 'dashicons-welcome-learn-more',
       'show_in_rest'       => true,
       'rest_base'          => 'tutorials',
       'rest_controller_class' => 'WP_REST_Posts_Controller',
       'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
   );
   
   register_post_type( 'aiddata_tutorial', $args );
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 886-991

3. **Implement Quiz Post Type**
   
   Configuration:
   ```php
   $labels = array(
       'name'               => _x( 'Quizzes', 'Post type general name', 'aiddata-lms' ),
       'singular_name'      => _x( 'Quiz', 'Post type singular name', 'aiddata-lms' ),
       'menu_name'          => _x( 'Quizzes', 'Admin Menu text', 'aiddata-lms' ),
       'add_new'            => __( 'Add New', 'aiddata-lms' ),
       'add_new_item'       => __( 'Add New Quiz', 'aiddata-lms' ),
       'new_item'           => __( 'New Quiz', 'aiddata-lms' ),
       'edit_item'          => __( 'Edit Quiz', 'aiddata-lms' ),
       'view_item'          => __( 'View Quiz', 'aiddata-lms' ),
       'all_items'          => __( 'All Quizzes', 'aiddata-lms' ),
       'search_items'       => __( 'Search Quizzes', 'aiddata-lms' ),
       'not_found'          => __( 'No quizzes found.', 'aiddata-lms' ),
       'not_found_in_trash' => __( 'No quizzes found in Trash.', 'aiddata-lms' ),
   );
   
   $args = array(
       'labels'             => $labels,
       'description'        => __( 'Tutorial quizzes and assessments', 'aiddata-lms' ),
       'public'             => true,
       'publicly_queryable' => true,
       'show_ui'            => true,
       'show_in_menu'       => 'edit.php?post_type=aiddata_tutorial',
       'query_var'          => true,
       'rewrite'            => array( 'slug' => 'quiz' ),
       'capability_type'    => array( 'aiddata_quiz', 'aiddata_quizzes' ),
       'map_meta_cap'       => true,
       'has_archive'        => false,
       'hierarchical'       => false,
       'menu_position'      => null,
       'show_in_rest'       => true,
       'rest_base'          => 'quizzes',
       'supports'           => array( 'title', 'custom-fields', 'revisions' ),
   );
   
   register_post_type( 'aiddata_quiz', $args );
   ```

4. **Add Capabilities**
   
   Update Installation class to add capabilities:
   ```php
   public static function create_capabilities() {
       global $wp_roles;
       
       if ( ! isset( $wp_roles ) ) {
           $wp_roles = new WP_Roles();
       }
       
       // Administrator gets all capabilities
       $admin = $wp_roles->get_role( 'administrator' );
       if ( $admin ) {
           $caps = array(
               // Tutorial capabilities
               'edit_aiddata_tutorial',
               'read_aiddata_tutorial',
               'delete_aiddata_tutorial',
               'edit_aiddata_tutorials',
               'edit_others_aiddata_tutorials',
               'publish_aiddata_tutorials',
               'read_private_aiddata_tutorials',
               'delete_aiddata_tutorials',
               'delete_private_aiddata_tutorials',
               'delete_published_aiddata_tutorials',
               'delete_others_aiddata_tutorials',
               'edit_private_aiddata_tutorials',
               'edit_published_aiddata_tutorials',
               // Quiz capabilities
               'edit_aiddata_quiz',
               'read_aiddata_quiz',
               'delete_aiddata_quiz',
               'edit_aiddata_quizzes',
               'edit_others_aiddata_quizzes',
               'publish_aiddata_quizzes',
               'read_private_aiddata_quizzes',
               'delete_aiddata_quizzes',
               'delete_private_aiddata_quizzes',
               'delete_published_aiddata_quizzes',
               'delete_others_aiddata_quizzes',
               'edit_private_aiddata_quizzes',
               'edit_published_aiddata_quizzes',
           );
           
           foreach ( $caps as $cap ) {
               $admin->add_cap( $cap );
           }
       }
       
       // Editor gets same capabilities
       $editor = $wp_roles->get_role( 'editor' );
       if ( $editor ) {
           foreach ( $caps as $cap ) {
               $editor->add_cap( $cap );
           }
       }
   }
   ```

5. **Flush Rewrite Rules**
   
   Add to installation:
   ```php
   public static function install() {
       self::create_tables();
       self::create_default_options();
       self::create_capabilities();
       
       // Register post types for rewrite rules
       $post_types = new AidData_LMS_Post_Types();
       $post_types->register_post_types();
       
       // Flush rewrite rules
       flush_rewrite_rules();
       
       update_option( 'aiddata_lms_db_version', AIDDATA_LMS_VERSION );
   }
   ```

6. **Register with Core Class**
   
   In `class-aiddata-lms.php`, add:
   ```php
   private function load_dependencies() {
       $this->loader = new AidData_LMS_Loader();
       
       // Register post types
       new AidData_LMS_Post_Types();
   }
   ```

**Validation Checklist:**
- [ ] Tutorial post type registered
- [ ] Quiz post type registered
- [ ] Both appear in admin menu
- [ ] Gutenberg editor works
- [ ] REST API endpoints available
- [ ] Capabilities created
- [ ] Rewrite rules flushed
- [ ] Permalinks work
- [ ] No naming conflicts
- [ ] Follows WordPress standards

**Expected Outcome:**
- Tutorial post type functional in admin
- Can create, edit, delete tutorials
- Quiz post type functional
- Custom capabilities working
- Ready for taxonomy registration

---

### PROMPT 8: TAXONOMIES REGISTRATION

**Context Required:**
- IMPLEMENTATION_PATHWAY.md → Phase 0 → Week 2 → Days 4-5 (lines 258-280)
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md → Section 3.1 Taxonomies (lines 993-1051)
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 3 Phase 0 Validation (lines 526-533)

**Objective:**
Register tutorial categories, tags, and difficulty level taxonomies with REST API support.

**Instructions:**

1. **Create Taxonomies Class** (`includes/class-aiddata-lms-taxonomies.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Taxonomies`
   - Method: `__construct()`
   - Method: `register_taxonomies()`
   - Method: `register_tutorial_category()`
   - Method: `register_tutorial_tag()`
   - Method: `register_tutorial_difficulty()`
   - Hook: `add_action( 'init', array( $this, 'register_taxonomies' ), 0 )`
   
   Note: Priority 0 to register before post types
   
   File location: `/includes/class-aiddata-lms-taxonomies.php`

2. **Implement Tutorial Category Taxonomy**
   
   Configuration:
   ```php
   $labels = array(
       'name'              => _x( 'Tutorial Categories', 'taxonomy general name', 'aiddata-lms' ),
       'singular_name'     => _x( 'Tutorial Category', 'taxonomy singular name', 'aiddata-lms' ),
       'search_items'      => __( 'Search Categories', 'aiddata-lms' ),
       'all_items'         => __( 'All Categories', 'aiddata-lms' ),
       'parent_item'       => __( 'Parent Category', 'aiddata-lms' ),
       'parent_item_colon' => __( 'Parent Category:', 'aiddata-lms' ),
       'edit_item'         => __( 'Edit Category', 'aiddata-lms' ),
       'update_item'       => __( 'Update Category', 'aiddata-lms' ),
       'add_new_item'      => __( 'Add New Category', 'aiddata-lms' ),
       'new_item_name'     => __( 'New Category Name', 'aiddata-lms' ),
       'menu_name'         => __( 'Categories', 'aiddata-lms' ),
   );
   
   $args = array(
       'labels'            => $labels,
       'hierarchical'      => true,
       'public'            => true,
       'show_ui'           => true,
       'show_admin_column' => true,
       'show_in_nav_menus' => true,
       'show_tagcloud'     => true,
       'show_in_rest'      => true,
       'rest_base'         => 'tutorial-categories',
       'rewrite'           => array( 'slug' => 'tutorial-category' ),
   );
   
   register_taxonomy( 'aiddata_tutorial_cat', array( 'aiddata_tutorial' ), $args );
   ```
   
   Reference:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 993-1022

3. **Implement Tutorial Tag Taxonomy**
   
   Configuration:
   ```php
   $labels = array(
       'name'                       => _x( 'Tutorial Tags', 'taxonomy general name', 'aiddata-lms' ),
       'singular_name'              => _x( 'Tutorial Tag', 'taxonomy singular name', 'aiddata-lms' ),
       'search_items'               => __( 'Search Tags', 'aiddata-lms' ),
       'popular_items'              => __( 'Popular Tags', 'aiddata-lms' ),
       'all_items'                  => __( 'All Tags', 'aiddata-lms' ),
       'edit_item'                  => __( 'Edit Tag', 'aiddata-lms' ),
       'update_item'                => __( 'Update Tag', 'aiddata-lms' ),
       'add_new_item'               => __( 'Add New Tag', 'aiddata-lms' ),
       'new_item_name'              => __( 'New Tag Name', 'aiddata-lms' ),
       'separate_items_with_commas' => __( 'Separate tags with commas', 'aiddata-lms' ),
       'add_or_remove_items'        => __( 'Add or remove tags', 'aiddata-lms' ),
       'choose_from_most_used'      => __( 'Choose from the most used tags', 'aiddata-lms' ),
       'not_found'                  => __( 'No tags found.', 'aiddata-lms' ),
       'menu_name'                  => __( 'Tags', 'aiddata-lms' ),
   );
   
   $args = array(
       'labels'            => $labels,
       'hierarchical'      => false,
       'public'            => true,
       'show_ui'           => true,
       'show_admin_column' => true,
       'show_in_nav_menus' => true,
       'show_tagcloud'     => true,
       'show_in_rest'      => true,
       'rest_base'         => 'tutorial-tags',
       'rewrite'           => array( 'slug' => 'tutorial-tag' ),
   );
   
   register_taxonomy( 'aiddata_tutorial_tag', array( 'aiddata_tutorial' ), $args );
   ```

4. **Implement Tutorial Difficulty Taxonomy**
   
   Configuration:
   ```php
   $labels = array(
       'name'              => _x( 'Difficulty Levels', 'taxonomy general name', 'aiddata-lms' ),
       'singular_name'     => _x( 'Difficulty Level', 'taxonomy singular name', 'aiddata-lms' ),
       'search_items'      => __( 'Search Levels', 'aiddata-lms' ),
       'all_items'         => __( 'All Levels', 'aiddata-lms' ),
       'edit_item'         => __( 'Edit Level', 'aiddata-lms' ),
       'update_item'       => __( 'Update Level', 'aiddata-lms' ),
       'add_new_item'      => __( 'Add New Level', 'aiddata-lms' ),
       'new_item_name'     => __( 'New Level Name', 'aiddata-lms' ),
       'menu_name'         => __( 'Difficulty', 'aiddata-lms' ),
   );
   
   $args = array(
       'labels'            => $labels,
       'hierarchical'      => true,
       'public'            => true,
       'show_ui'           => true,
       'show_admin_column' => true,
       'show_in_nav_menus' => true,
       'show_tagcloud'     => false,
       'show_in_rest'      => true,
       'rest_base'         => 'tutorial-difficulty',
       'rewrite'           => array( 'slug' => 'difficulty' ),
       'meta_box_cb'       => 'post_categories_meta_box',
   );
   
   register_taxonomy( 'aiddata_tutorial_difficulty', array( 'aiddata_tutorial' ), $args );
   ```

5. **Insert Default Difficulty Terms**
   
   Add to installation class:
   ```php
   public static function create_default_options() {
       // Create default difficulty levels
       $difficulty_terms = array(
           'Beginner' => 'For those new to the topic',
           'Intermediate' => 'Requires some prior knowledge',
           'Advanced' => 'For experienced learners',
       );
       
       foreach ( $difficulty_terms as $term => $description ) {
           if ( ! term_exists( $term, 'aiddata_tutorial_difficulty' ) ) {
               wp_insert_term(
                   $term,
                   'aiddata_tutorial_difficulty',
                   array( 'description' => $description )
               );
           }
       }
       
       // Set default options
       update_option( 'aiddata_lms_version', AIDDATA_LMS_VERSION );
       update_option( 'aiddata_lms_settings', array(
           'enable_enrollments' => true,
           'enable_certificates' => true,
           'enable_analytics' => true,
       ) );
   }
   ```

6. **Register with Core Class**
   
   In `class-aiddata-lms.php`, update:
   ```php
   private function load_dependencies() {
       $this->loader = new AidData_LMS_Loader();
       
       // Register taxonomies (before post types)
       new AidData_LMS_Taxonomies();
       
       // Register post types
       new AidData_LMS_Post_Types();
   }
   ```

7. **Add Custom Admin Columns**
   
   Add methods to Post Types class:
   ```php
   public function __construct() {
       add_action( 'init', array( $this, 'register_post_types' ) );
       add_filter( 'manage_aiddata_tutorial_posts_columns', array( $this, 'tutorial_columns' ) );
       add_action( 'manage_aiddata_tutorial_posts_custom_column', array( $this, 'tutorial_column_content' ), 10, 2 );
   }
   
   public function tutorial_columns( $columns ) {
       $new_columns = array();
       $new_columns['cb'] = $columns['cb'];
       $new_columns['title'] = $columns['title'];
       $new_columns['tutorial_category'] = __( 'Category', 'aiddata-lms' );
       $new_columns['tutorial_difficulty'] = __( 'Difficulty', 'aiddata-lms' );
       $new_columns['author'] = $columns['author'];
       $new_columns['date'] = $columns['date'];
       return $new_columns;
   }
   
   public function tutorial_column_content( $column, $post_id ) {
       switch ( $column ) {
           case 'tutorial_category':
               $terms = get_the_terms( $post_id, 'aiddata_tutorial_cat' );
               if ( ! empty( $terms ) ) {
                   $output = array();
                   foreach ( $terms as $term ) {
                       $output[] = sprintf(
                           '<a href="%s">%s</a>',
                           esc_url( add_query_arg( array( 'aiddata_tutorial_cat' => $term->slug ), 'edit.php?post_type=aiddata_tutorial' ) ),
                           esc_html( $term->name )
                       );
                   }
                   echo implode( ', ', $output );
               } else {
                   echo '—';
               }
               break;
           
           case 'tutorial_difficulty':
               $terms = get_the_terms( $post_id, 'aiddata_tutorial_difficulty' );
               if ( ! empty( $terms ) ) {
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
   ```

**Validation Checklist:**
- [ ] All taxonomies registered
- [ ] Hierarchical taxonomies show parent/child
- [ ] Tags show tag cloud
- [ ] Difficulty has 3 default terms
- [ ] All appear in admin UI
- [ ] Admin columns display correctly
- [ ] REST API endpoints available
- [ ] Rewrite rules work
- [ ] No naming conflicts
- [ ] Follows WordPress standards

**Expected Outcome:**
- All taxonomies functional
- Can categorize tutorials
- Admin interface enhanced
- REST API endpoints working
- Ready for meta box implementation

---

### PROMPT 9: PHASE 0 FINAL VALIDATION & TESTING

**Context Required:**
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md → Section 3 Phase 0 Validation (complete)
- INTEGRATION_VALIDATION_MATRIX.md → Section 2 Database Integrity
- IMPLEMENTATION_CHECKLIST.md → Phase 0 items
- QUALITY_ASSURANCE_SUMMARY.md → Phase 0 criteria

**Objective:**
Comprehensive validation of all Phase 0 deliverables before proceeding to Phase 1.

**Instructions:**

1. **Create Validation Test Suite** (`includes/admin/class-aiddata-lms-phase-0-validation.php`)
   
   Requirements:
   - Class name: `AidData_LMS_Phase_0_Validation`
   - Static method: `run_all_tests()`
   - Static method: `test_environment()`
   - Static method: `test_database_schema()`
   - Static method: `test_post_types()`
   - Static method: `test_taxonomies()`
   - Static method: `test_autoloader()`
   - Static method: `test_core_classes()`
   - Static method: `generate_report()`
   - Return comprehensive validation report

2. **Environment Tests**
   
   Verify:
   - PHP version >= 8.1
   - WordPress version >= 6.4
   - MySQL version >= 8.0
   - Required PHP extensions (mbstring, mysqli, json)
   - Required WordPress functions
   - File permissions correct
   - Memory limit adequate (128MB+)
   - Max execution time adequate (30s+)

3. **Database Schema Tests**
   
   Run all validation queries from INTEGRATION_VALIDATION_MATRIX.md:
   - All tables exist
   - All foreign keys functional
   - All indexes present
   - Character set utf8mb4
   - Collation utf8mb4_unicode_ci
   - Engine InnoDB
   - No orphaned records
   - No data integrity issues

4. **Post Type Tests**
   
   Verify:
   - `aiddata_tutorial` post type registered
   - `aiddata_quiz` post type registered
   - Both visible in admin menu
   - Gutenberg editor enabled
   - REST API endpoints exist:
     - `/wp-json/wp/v2/tutorials`
     - `/wp-json/wp/v2/quizzes`
   - Capabilities created
   - Rewrite rules functional
   - Can create, edit, delete posts

5. **Taxonomy Tests**
   
   Verify:
   - `aiddata_tutorial_cat` registered (hierarchical)
   - `aiddata_tutorial_tag` registered (flat)
   - `aiddata_tutorial_difficulty` registered (hierarchical)
   - Default difficulty terms exist (Beginner, Intermediate, Advanced)
   - All visible in admin
   - REST API endpoints exist:
     - `/wp-json/wp/v2/tutorial-categories`
     - `/wp-json/wp/v2/tutorial-tags`
     - `/wp-json/wp/v2/tutorial-difficulty`
   - Can assign to tutorials

6. **Autoloader Tests**
   
   Test class loading:
   - Load test classes created earlier
   - Verify nested namespace loading
   - Test class name variations
   - Verify proper file path mapping
   - Test non-existent class handling

7. **Core Classes Tests**
   
   Verify:
   - `AidData_LMS` singleton working
   - Only one instance possible
   - Hook loader registering hooks
   - Internationalization loading
   - Dependency container functional
   - No PHP errors or warnings

8. **Integration Tests**
   
   Test complete workflows:
   - Create tutorial post
   - Assign category
   - Assign tags
   - Assign difficulty
   - Save post
   - Verify data saved
   - Retrieve via REST API
   - Delete post
   - Verify cleanup

9. **Security Tests**
   
   Verify:
   - ABSPATH check present in all files
   - No direct file access possible
   - Capabilities required for admin actions
   - SQL queries use prepare()
   - Output escaped where needed
   - Input sanitized where needed

10. **Performance Tests**
    
    Measure:
    - Plugin activation time
    - Database query count on admin page
    - Database query count on frontend
    - Memory usage
    - File load times

11. **Generate Validation Report**
    
    Create comprehensive report with:
    - Test summary (passed/failed/skipped)
    - Environment details
    - Database schema validation results
    - Post type and taxonomy status
    - Performance metrics
    - Security check results
    - Issues found (if any)
    - Recommendations
    - Pass/fail determination for Phase 0

12. **Create Admin Validation Page**
    
    Add menu item:
    - "Phase 0 Validation" under plugin menu
    - Display validation report
    - Allow re-running tests
    - Export report as PDF/text
    - Color-coded results (green/red)
    - Show SQL queries used
    - Link to documentation for failures

**Validation Checklist (Must ALL Pass):**

**Environment:**
- [ ] PHP 8.1+ installed
- [ ] WordPress 6.4+ running
- [ ] MySQL 8.0+ connected
- [ ] Required extensions present
- [ ] File permissions correct
- [ ] Memory limit adequate

**Database:**
- [ ] All 6 tables created
- [ ] All tables have correct structure
- [ ] All foreign keys functional
- [ ] All indexes present
- [ ] Character set utf8mb4
- [ ] Collation utf8mb4_unicode_ci
- [ ] Engine InnoDB

**Post Types:**
- [ ] Tutorial post type registered
- [ ] Quiz post type registered
- [ ] Both in admin menu
- [ ] Gutenberg enabled
- [ ] REST API endpoints functional
- [ ] Capabilities created

**Taxonomies:**
- [ ] Categories registered (hierarchical)
- [ ] Tags registered (flat)
- [ ] Difficulty registered (hierarchical)
- [ ] Default terms created
- [ ] REST API endpoints functional
- [ ] Can assign to posts

**Core Classes:**
- [ ] Autoloader functional
- [ ] Core plugin class working
- [ ] Singleton pattern correct
- [ ] Hook loader working
- [ ] Internationalization loaded

**Integration:**
- [ ] Can create tutorial posts
- [ ] Can assign taxonomies
- [ ] Can save and retrieve data
- [ ] REST API returns data
- [ ] No errors or warnings

**Security:**
- [ ] ABSPATH checks present
- [ ] Capabilities required
- [ ] SQL queries use prepare()
- [ ] Output escaped
- [ ] Input sanitized

**Performance:**
- [ ] Activation < 3 seconds
- [ ] < 10 queries on admin page
- [ ] < 5 queries on frontend
- [ ] Memory usage < 32MB
- [ ] File load time < 1 second

**Expected Outcome:**
- Comprehensive validation report
- All tests passing
- No critical issues
- Performance acceptable
- Security verified
- Ready for Phase 1

**Phase 0 Exit Criteria:**
✅ All environment requirements met
✅ Database schema complete and validated
✅ Plugin structure implemented
✅ Post types and taxonomies functional
✅ All tests passing
✅ No critical issues
✅ Documentation updated
✅ **APPROVED TO PROCEED TO PHASE 1**

---

## 📋 PHASE 0 COMPLETION CHECKLIST

Review this before declaring Phase 0 complete:

### Week 1 Deliverables:
- [ ] Main plugin file created (`aiddata-lms.php`)
- [ ] Directory structure established
- [ ] Composer and npm configured
- [ ] Autoloader implemented and tested
- [ ] Database installation class created
- [ ] All 6 database tables defined
- [ ] Database helper class created
- [ ] Database tests implemented
- [ ] Plugin activates without errors

### Week 2 Deliverables:
- [ ] Core plugin class implemented (singleton)
- [ ] Hook loader class created
- [ ] Internationalization setup
- [ ] Tutorial post type registered
- [ ] Quiz post type registered
- [ ] Tutorial categories taxonomy registered
- [ ] Tutorial tags taxonomy registered
- [ ] Difficulty taxonomy registered with defaults
- [ ] Custom admin columns added
- [ ] Capabilities created and assigned

### Validation:
- [ ] All validation tests pass
- [ ] Database integrity verified
- [ ] No PHP errors or warnings
- [ ] Performance benchmarks met
- [ ] Security checks passed
- [ ] Code follows standards
- [ ] Documentation complete

### Files Created:
- [ ] `/aiddata-lms.php`
- [ ] `/composer.json`
- [ ] `/package.json`
- [ ] `/.editorconfig`
- [ ] `/.gitignore`
- [ ] `/includes/class-aiddata-lms-autoloader.php`
- [ ] `/includes/class-aiddata-lms-install.php`
- [ ] `/includes/class-aiddata-lms-database.php`
- [ ] `/includes/class-aiddata-lms.php`
- [ ] `/includes/class-aiddata-lms-loader.php`
- [ ] `/includes/class-aiddata-lms-i18n.php`
- [ ] `/includes/class-aiddata-lms-post-types.php`
- [ ] `/includes/class-aiddata-lms-taxonomies.php`
- [ ] `/includes/admin/class-aiddata-lms-phase-0-validation.php`

### Database Tables Created:
- [ ] `wp_aiddata_lms_tutorial_enrollments`
- [ ] `wp_aiddata_lms_tutorial_progress`
- [ ] `wp_aiddata_lms_video_progress`
- [ ] `wp_aiddata_lms_certificates`
- [ ] `wp_aiddata_lms_tutorial_analytics`
- [ ] `wp_aiddata_lms_email_queue`

### Documentation:
- [ ] Code comments complete
- [ ] Docblocks for all classes and methods
- [ ] README updated if needed
- [ ] Validation report generated
- [ ] Issues documented (if any)

---

## 🎯 SUCCESS CRITERIA

Phase 0 is considered successful when:

1. ✅ **Plugin Activation**: Plugin activates without any PHP errors or warnings
2. ✅ **Database Schema**: All tables created with correct structure, foreign keys, and indexes
3. ✅ **Post Types**: Tutorial and Quiz post types visible and functional in admin
4. ✅ **Taxonomies**: Categories, Tags, and Difficulty taxonomies working with default terms
5. ✅ **Autoloader**: Classes load automatically without manual requires
6. ✅ **Core Structure**: Singleton pattern, hook loader, and i18n functional
7. ✅ **Validation**: All tests pass with no critical issues
8. ✅ **Performance**: Meets performance benchmarks
9. ✅ **Security**: Security checks pass
10. ✅ **Standards**: Code follows WordPress and plugin coding standards

---

## 📚 DOCUMENT REFERENCES FOR EACH PROMPT

### Prompt 1 (Project Setup):
- IMPLEMENTATION_PATHWAY.md lines 97-138
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 21-149
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 656-730

### Prompt 2 (Autoloader):
- IMPLEMENTATION_PATHWAY.md lines 221-237
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 28-73

### Prompt 3 (Database Part 1):
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 258-426
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 262-337
- INTEGRATION_VALIDATION_MATRIX.md lines 69-105

### Prompt 4 (Database Part 2):
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 428-552
- INTEGRATION_VALIDATION_MATRIX.md lines 107-155

### Prompt 5 (Database Testing):
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 486-541
- INTEGRATION_VALIDATION_MATRIX.md lines 69-224

### Prompt 6 (Core Plugin Class):
- IMPLEMENTATION_PATHWAY.md lines 188-243
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 732-856
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 24-149

### Prompt 7 (Post Types):
- IMPLEMENTATION_PATHWAY.md lines 245-280
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 886-991
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 519-533

### Prompt 8 (Taxonomies):
- IMPLEMENTATION_PATHWAY.md lines 258-280
- TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md lines 993-1051
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 526-533

### Prompt 9 (Final Validation):
- CODE_STANDARDS_AND_VALIDATION_GUIDE.md lines 486-541
- INTEGRATION_VALIDATION_MATRIX.md lines 69-224
- IMPLEMENTATION_CHECKLIST.md lines 7-25
- QUALITY_ASSURANCE_SUMMARY.md lines 172-193

---

## 🚀 NEXT STEPS AFTER PHASE 0

Once Phase 0 validation passes:

1. **Review Phase 1 Requirements**: Read IMPLEMENTATION_PATHWAY.md Phase 1
2. **Create Sprint 2**: Use SPRINT_PLANNING_TEMPLATE.md
3. **Load Context**: Reference documents for Phase 1:
   - TUTORIAL_BUILDER_PROJECT_SPECIFICATIONS.md Sections 3-4 (Enrollment)
   - CODE_STANDARDS_AND_VALIDATION_GUIDE.md Section 3 Phase 1
   - INTEGRATION_VALIDATION_MATRIX.md Section 4 (Frontend-Backend)
4. **Begin Phase 1**: Start with enrollment system implementation

---

## ✅ PHASE 0 PROMPTS DOCUMENT COMPLETE

This document provides detailed, context-aware prompts for implementing Phase 0 of the AidData LMS Tutorial Builder. Each prompt references specific documentation sections to maintain context permanence during development.

**Remember:**
- Always load referenced documents into context before starting each prompt
- Follow coding standards strictly
- Run validation tests after each prompt
- Update IMPLEMENTATION_CHECKLIST.md as you progress
- Document any deviations or issues encountered

**Good luck with Phase 0 implementation! 🎉**

