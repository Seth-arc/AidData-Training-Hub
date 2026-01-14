<?php
defined( 'ABSPATH' ) || exit;

$lesson_id = get_the_ID();
?>

<section class="llms-lesson-video">

  <?php
  /**
   * Outputs the lesson video embed
   * (Vimeo / YouTube / self-hosted)
   */
  echo lifterlms_get_video();
  ?>

</section>