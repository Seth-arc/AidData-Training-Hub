<?php
/**
 * Content displayed after the single lesson main content.
 *
 * Child theme override path:
 * /wp-content/themes/your-child-theme/lifterlms/content-single-lesson-after.php
 */

defined( 'ABSPATH' ) || exit;

$lesson_id = get_the_ID();
$user_id   = get_current_user_id();

// Try to resolve the parent course ID (varies by version).
$course_id = 0;

if ( function_exists( 'llms_get_post_parent_course' ) ) {
	$course_id = (int) llms_get_post_parent_course( $lesson_id );
} elseif ( function_exists( 'llms_get_parent_course' ) ) {
	$course_id = (int) llms_get_parent_course( $lesson_id );
} else {
	// Fallback: try common meta keys (may not exist in your setup).
	$maybe = get_post_meta( $lesson_id, '_llms_parent_course', true );
	if ( $maybe ) {
		$course_id = (int) $maybe;
	}
}

?>
<section class="llms-lesson-after">

	<!-- QUIZ GATE -->
	<div class="llms-lesson-gate">

		<?php
		/**
		 * Preferred: render the quiz block via LifterLMS template functions (guarded).
		 * Depending on your LifterLMS version, one of these may exist.
		 */
		if ( function_exists( 'lifterlms_template_single_quiz' ) ) {
			lifterlms_template_single_quiz();
		} elseif ( function_exists( 'lifterlms_template_lesson_quiz' ) ) {
			lifterlms_template_lesson_quiz();
		} else {
			/**
			 * Fallback: fire common hooks so LifterLMS can inject quiz UI if your version uses actions.
			 * If nothing appears, your quiz is rendered elsewhere (often inside the quiz template itself).
			 */
			do_action( 'lifterlms_single_lesson_after_content' );
			do_action( 'llms_single_lesson_after_content' );
		}
		?>

	</div>

	<!-- NAVIGATION (Continue / Next / Prev) -->
	<div class="llms-lesson-nav">

		<?php
		if ( function_exists( 'lifterlms_template_single_lesson_navigation' ) ) {
			lifterlms_template_single_lesson_navigation();
		} elseif ( function_exists( 'lifterlms_template_lesson_navigation' ) ) {
			lifterlms_template_lesson_navigation();
		} else {
			do_action( 'lifterlms_single_lesson_after_navigation' );
			do_action( 'llms_single_lesson_after_navigation' );
		}
		?>

	</div>

	<!-- TEMP CERTIFICATE CTA (only show when course is completed) -->
	<?php
	$course_complete = false;

	if ( $user_id && $course_id ) {

		// Common completion helper in some LifterLMS setups (guarded).
		if ( function_exists( 'llms_is_complete' ) ) {
			$course_complete = (bool) llms_is_complete( $user_id, 'course', $course_id );
		} elseif ( function_exists( 'llms_get_user_course_progress' ) ) {
			// If your install has a progress helper, you can adapt it later.
			// Leaving as false unless you wire it.
			$course_complete = false;
		}

		// Fallback: if your certificate system stores a meta flag, check it here later.
	}

	if ( $course_complete ) : ?>
		<div class="llms-certificate-cta">
			<a class="button llms-cert-btn"
			   href="<?php echo esc_url( add_query_arg( array(
					'generate_certificate' => 1,
					'course_id'            => $course_id,
				), home_url( add_query_arg( array(), $wp->request ?? '' ) ) ) ); ?>">
				Download Certificate (PDF)
			</a>
		</div>
	<?php endif; ?>

</section>