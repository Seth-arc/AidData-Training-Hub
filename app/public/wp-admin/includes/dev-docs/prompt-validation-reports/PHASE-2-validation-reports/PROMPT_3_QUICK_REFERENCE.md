# PROMPT 3 QUICK REFERENCE
## Admin List Interface & Bulk Actions

**Date:** October 22, 2025  
**Status:** ✅ COMPLETE  
**Purpose:** Quick reference for admin list enhancements

---

## 📋 WHAT WAS IMPLEMENTED

### 1. Custom Admin Columns
- **Thumbnail** - Post featured image (60x60px)
- **Steps** - Tutorial step count badge
- **Enrollments** - Total enrollment count (linked)
- **Active** - Active enrollments count
- **Completion** - Completion rate with color coding
- **Difficulty** - Tutorial difficulty term

### 2. Bulk Actions
- **Duplicate** - Creates draft copies with all meta and taxonomies
- **Export Data** - Generates CSV with 10 data columns
- **Toggle Enrollment** - Toggles enrollment status meta

### 3. Admin Filters
- **Difficulty** - Filter by taxonomy term
- **Enrollment Status** - Open/Closed
- **Steps Count** - Empty, 1-5, 6-10, 11+

### 4. Quick Edit Fields
- **Duration** - Minutes (number)
- **Enrollment Limit** - Max enrollments (number)
- **Allow Enrollment** - Boolean (checkbox)
- **Show in Catalog** - Boolean (checkbox)

---

## 🔧 KEY METHODS

### In `class-aiddata-lms-post-types.php`

```php
// Columns
public function add_tutorial_columns( array $columns ): array
public function render_tutorial_column( string $column, int $post_id ): void
public function sortable_tutorial_columns( array $columns ): array

// Bulk Actions
public function add_bulk_actions( array $actions ): array
public function handle_bulk_actions( string $redirect_to, string $action, array $post_ids ): string
private function duplicate_tutorials( array $post_ids ): int
private function duplicate_post_meta( int $source_id, int $target_id ): void
private function duplicate_post_taxonomies( int $source_id, int $target_id ): void
private function export_tutorials_data( array $post_ids ): void
private function toggle_enrollment_status( array $post_ids ): int

// Filters
public function add_admin_filters(): void
public function filter_tutorials_query( WP_Query $query ): void

// Quick Edit
public function add_quick_edit_fields( string $column_name, string $post_type ): void
public function save_quick_edit_data( int $post_id ): void

// Notices & Assets
public function bulk_action_notices(): void
public function enqueue_admin_assets( string $hook ): void

// Utility
private function get_completion_color( float $rate ): string
```

---

## 📊 COMPLETION RATE COLOR CODING

```php
if ( $rate >= 75 ) {
    return '#46b450'; // Green
} elseif ( $rate >= 50 ) {
    return '#ffb900'; // Yellow
} else {
    return '#dc3232'; // Red
}
```

---

## 🔄 INTEGRATION POINTS

### Uses Phase 1 Classes
```php
// Enrollment counts
$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
$total = $enrollment_manager->get_enrollment_count( $post_id );
$active = $enrollment_manager->get_enrollment_count( $post_id, 'active' );
$completed = $enrollment_manager->get_enrollment_count( $post_id, 'completed' );
```

### Uses Post Meta
```php
get_post_meta( $post_id, '_tutorial_step_count', true );
get_post_meta( $post_id, '_tutorial_duration', true );
get_post_meta( $post_id, '_tutorial_allow_enrollment', true );
get_post_meta( $post_id, '_tutorial_enrollment_limit', true );
```

### Uses Taxonomies
```php
get_the_terms( $post_id, 'aiddata_tutorial_difficulty' );
get_the_terms( $post_id, 'aiddata_tutorial_cat' );
get_the_terms( $post_id, 'aiddata_tutorial_tag' );
```

---

## 📁 FILES CREATED/MODIFIED

### Modified
- `includes/class-aiddata-lms-post-types.php` (+580 lines)

### Created
- `assets/css/admin/tutorial-list.css` (240 lines)

---

## 🎨 CSS CLASSES

### Column Styling
- `.column-thumbnail` - 80px width, rounded images
- `.column-steps .step-count` - Blue badge
- `.column-enrollments a` - Bold, linked
- `.column-active .active-count` - Bold count
- `.column-completion_rate .completion-rate` - Color-coded percentage
- `.column-difficulty` - 120px width

### Quick Edit
- `fieldset.inline-edit-col-right` - Right column fieldset
- `.inline-edit-col label` - Individual field labels
- `.checkbox-title` - Checkbox label text

---

## 🔒 SECURITY FEATURES

### Input Sanitization
```php
absint( $_POST['tutorial_duration'] )
absint( $_POST['tutorial_enrollment_limit'] )
sanitize_key( $source )
```

### Output Escaping
```php
esc_html( $term->name )
esc_url( $url )
esc_attr( $color )
```

### Nonce Verification
```php
wp_nonce_field( 'aiddata_quick_edit', 'aiddata_quick_edit_nonce' );
wp_verify_nonce( $_POST['aiddata_quick_edit_nonce'], 'aiddata_quick_edit' );
```

### Capability Checks
```php
current_user_can( 'edit_post', $post_id )
```

---

## 📤 CSV EXPORT FORMAT

**Columns:**
1. ID
2. Title
3. Status
4. Steps
5. Duration
6. Enrollments
7. Active
8. Completed
9. Completion Rate
10. Created

**Filename:** `tutorials-export-YYYY-MM-DD-HHMMSS.csv`

---

## 🎯 ADMIN FILTER QUERIES

### Enrollment Status
```php
$meta_query[] = array(
    'key'     => '_tutorial_allow_enrollment',
    'value'   => ( 'open' === $status ) ? '1' : '0',
    'compare' => '=',
);
```

### Steps Count
```php
// For ranges like 1-5
$meta_query[] = array(
    'key'     => '_tutorial_step_count',
    'value'   => array( 1, 5 ),
    'compare' => 'BETWEEN',
    'type'    => 'NUMERIC',
);

// For 11+
$meta_query[] = array(
    'key'     => '_tutorial_step_count',
    'value'   => 10,
    'compare' => '>',
    'type'    => 'NUMERIC',
);
```

---

## ✅ TESTING CHECKLIST

- [ ] Custom columns display correctly
- [ ] Enrollment counts accurate
- [ ] Completion rate calculates correctly
- [ ] Bulk duplicate works
- [ ] Duplicate includes all meta
- [ ] CSV export downloads
- [ ] Export includes all data
- [ ] Enrollment toggle works
- [ ] Difficulty filter works
- [ ] Enrollment status filter works
- [ ] Steps count filter works
- [ ] Quick edit opens
- [ ] Quick edit saves data
- [ ] Admin notices display
- [ ] CSS loads on list page
- [ ] Responsive on mobile
- [ ] Keyboard accessible

---

## 🚀 USAGE EXAMPLES

### Get Enrollment Data in Column
```php
$enrollment_manager = new AidData_LMS_Tutorial_Enrollment();
$total = $enrollment_manager->get_enrollment_count( $post_id );
```

### Duplicate Tutorial Meta
```php
$meta_keys = array(
    '_tutorial_short_description',
    '_tutorial_duration',
    '_tutorial_steps',
    // ... 17 total keys
);

foreach ( $meta_keys as $key ) {
    $value = get_post_meta( $source_id, $key, true );
    if ( $value ) {
        update_post_meta( $target_id, $key, $value );
    }
}
```

### Export to CSV
```php
$output = fopen( 'php://output', 'w' );
fputcsv( $output, $header_array );
foreach ( $post_ids as $post_id ) {
    fputcsv( $output, $data_array );
}
fclose( $output );
exit;
```

---

## 🔗 RELATED DOCUMENTATION

- **Main Prompt:** `PHASE_2_IMPLEMENTATION_PROMPTS.md` lines 1112-1745
- **Validation Report:** `PROMPT_3_VALIDATION_REPORT.md`
- **Phase 1 Enrollment:** `includes/tutorials/class-aiddata-lms-tutorial-enrollment.php`
- **Post Types:** `includes/class-aiddata-lms-post-types.php`

---

## 💡 TIPS

### Performance
- Enrollment counts query database on each row
- Consider caching for large tutorial lists
- CSS loads conditionally (only on edit.php for tutorials)

### Extensibility
- Use `apply_filters()` to extend bulk actions
- Hook into `aiddata_lms_` action hooks
- Add custom columns via filter

### Customization
- Override CSS in theme
- Add custom bulk actions via filter
- Extend export columns

---

**Quick Reference Date:** October 22, 2025  
**Status:** ACTIVE  
**Phase 2 Progress:** 37.5% Complete (3 of 8 prompts)

