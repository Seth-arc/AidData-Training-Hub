<?php
/**
 * Template for displaying title of lesson.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $lesson ) ) {
	return;
}

$title = $lesson->get_title( 'display' );

if ( ! $title ) {
	return;
}

/**
 * Intentionally left blank to suppress the lesson title in the enrolled view.
 * Keep the early returns above so upstream hooks/filters still run as expected.
 */

return;
