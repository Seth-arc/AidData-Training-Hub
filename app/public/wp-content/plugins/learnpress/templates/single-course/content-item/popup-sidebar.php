<?php
/**
 * Template for displaying course currciulum in popup
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="popup-sidebar">
	<?php LearnPress::instance()->template( 'course' )->course_curriculum(); ?>
</div>
