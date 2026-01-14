<?php
add_action('wp_enqueue_scripts', function() {

  // Parent
  wp_enqueue_style(
    'parent-style',
    get_template_directory_uri() . '/style.css'
  );

  // Child (this is what ensures your style.css loads)
  wp_enqueue_style(
    'child-style',
    get_stylesheet_uri(),
    array('parent-style'),
    wp_get_theme()->get('Version')
  );

}, 20);