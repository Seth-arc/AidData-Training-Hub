<?php
/**
 * Temporary script to delete the "Navigating Global Development Finance" course
 * 
 * IMPORTANT: Delete this file after use
 */

// Load WordPress environment
require_once('../../../../wp-load.php');

// Find posts with the title containing "Navigating Global Development Finance"
$args = array(
    'post_type' => 'course',
    'post_status' => 'any',
    'posts_per_page' => -1,
    's' => 'Navigating Global Development Finance' // Using search instead of exact title
);

$query = new WP_Query($args);

if (!$query->have_posts()) {
    echo "No courses found with the title 'Navigating Global Development Finance'";
} else {
    echo "Found " . $query->post_count . " courses with the title 'Navigating Global Development Finance':<br>";
    
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $post_title = get_the_title();
        
        echo "ID: " . $post_id . " - Title: " . $post_title . "<br>";
        
        // Delete the post
        $result = wp_delete_post($post_id, true);
        
        if ($result) {
            echo "Successfully deleted course with ID " . $post_id . "<br>";
        } else {
            echo "Failed to delete course with ID " . $post_id . "<br>";
        }
    }
    wp_reset_postdata();
}

echo "<br>Operation complete. You can now delete this file.";

// Make sure all output is sent to the browser
flush(); 