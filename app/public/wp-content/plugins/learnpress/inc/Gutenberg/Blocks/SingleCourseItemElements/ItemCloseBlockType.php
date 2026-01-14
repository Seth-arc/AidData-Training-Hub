<?php

namespace LearnPress\Gutenberg\Blocks\SingleCourseItemElements;

use LP_Debug;
use Throwable;

/**
 * Class ItemCloseBlockType
 *
 * Handle register, render block template
 */
class ItemCloseBlockType extends AbstractCourseItemBlockType {
	public $block_name = 'item-close';
	public function get_supports(): array {
		return [
			'align'                => [ 'wide', 'full' ],
			'color'                => [
				'gradients'  => true,
				'background' => true,
				'text'       => true,
			],
			'typography'           => [
				'fontSize'                    => true,
				'__experimentalFontWeight'    => true,
				'__experimentalTextTransform' => true,
			],
			'shadow'               => true,
			'spacing'              => [
				'padding' => true,
				'margin'  => true,
			],
			'__experimentalBorder' => [
				'color'  => true,
				'radius' => true,
				'width'  => true,
			],
		];
	}

	/**
	 * Render content of block tag
	 *
	 * @param array $attributes | Attributes of block tag.
	 *
	 * @return false|string
	 */
	public function render_content_block_template( array $attributes, $content, $block ): string {
		$html = '';

		try {
			$courseModel = $this->get_course( $attributes, $block );
			if ( ! $courseModel ) {
				return $html;
			}

			$html_progress = sprintf(
				'<a href="%1$s" class="back-course lp-button button button-complete-item button-complete-lesson lp-btn-complete-item" aria-label="%2$s" data-title="%2$s" data-confirm="%4$s">
					<span class="back-course__label">%3$s</span>
				</a>',
				esc_url_raw( $courseModel->get_permalink() ),
				esc_attr__( 'Exit Course', 'learnpress' ),
				esc_html__( 'Exit Course', 'learnpress' ),
				esc_attr__( 'Are you sure?', 'learnpress' )
			);

			$html = $this->get_output( $html_progress );
		} catch ( Throwable $e ) {
			LP_Debug::error_log( $e );
		}

		return $html;
	}
}
