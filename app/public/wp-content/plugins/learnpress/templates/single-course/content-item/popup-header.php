<?php
/**
 * Template for displaying header of single course popup.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/header.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.3
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $course ) || ! isset( $user ) || ! isset( $percentage ) ||
	! isset( $completed_items ) || ! isset( $total_items ) ) {
	return;
}
?>

<div id="popup-header">
	<?php
	/**
	 * @since 4.0.6
	 * @see single-button-toggle-sidebar - 5
	 */
	do_action( 'learn-press/single-button-toggle-sidebar' );
	?>
	<style id="lp-sidebar-toggle-desktop">
		body.lp-sidebar-toggle__close #popup-sidebar {
			flex: 0 0 0;
			max-width: 0;
			opacity: 0;
			pointer-events: none;
		}
		body.lp-sidebar-toggle__close #popup-content {
			flex: 1 1 auto;
		}
		body.lp-sidebar-toggle__close #popup-footer {
			left: 0;
			max-width: 100%;
		}
		#popup-header .back-course {
			background: #115740;
			border-color: #115740;
			color: #fff;
		}
		#popup-header .back-course:hover {
			background: #0d4a32;
			border-color: #0d4a32;
		}
	</style>
	<style id="lp-aiddata-quiz-minimal">
		:root {
			--lp-aiddata-accent: #115740;
		}

		#popup-course .quiz-status,
		#popup-course .quiz-progress,
		#popup-course .quiz-result,
		#popup-course .quiz-intro,
		#popup-course .quiz-questions,
		#popup-course .quiz-buttons,
		#popup-course .quiz-attempts,
		#popup-course .lp-quiz-buttons {
			background: transparent;
			border: 0;
			box-shadow: none;
			border-radius: 0;
			color: #16202f;
		}

		#popup-course .quiz-status {
			background: var( --lp-aiddata-accent );
			color: #fff;
		}

		#popup-course .quiz-result .result-heading,
		#popup-course .quiz-intro-item__title,
		#popup-course .quiz-status .questions-index span,
		#popup-course .quiz-result .result-achieved,
		#popup-course .quiz-result .result-grade .result-message strong,
		#popup-course .quiz-progress .progress-items .progress-item .progress-number,
		#popup-course .quiz-progress .progress-items .progress-item i {
			color: var( --lp-aiddata-accent );
		}

		#popup-course .quiz-progress .progress-items .progress-item,
		#popup-course .quiz-result .result-message,
		#popup-course .quiz-intro-item,
		#popup-course .quiz-attempts table tr th,
		#popup-course .quiz-attempts table tr td {
			background: transparent;
			border: 0;
			box-shadow: none;
		}

		#popup-course .quiz-result .result-grade::before,
		#popup-course .quiz-result .result-grade svg,
		#popup-course .quiz-result .result-message::after {
			display: none;
		}

		#popup-course .quiz-questions .question,
		#popup-course .quiz-questions .lp-fib-input > input,
		#popup-course .quiz-questions .lp-fib-answered,
		#popup-course .quiz-questions .lp-sorting-choice__check-answer {
			border: 0;
			box-shadow: none;
			border-radius: 0;
		}

		#popup-course .quiz-questions .question:hover {
			background: #f0f2f1;
		}
		#popup-course .lp-quiz-buttons .lp-button,
		.quiz-status .submit-quiz button,
		.content-item-wrap .quiz-buttons .lp-button {
			background: var( --lp-aiddata-accent );
			color: #fff;
			border: 0;
			border-radius: 0;
			box-shadow: none;
		}

		#popup-course .lp-quiz-buttons .lp-button:hover,
		.quiz-status .submit-quiz button:hover,
		.content-item-wrap .quiz-buttons .lp-button:hover {
			background: #0d4a32;
		}

		#popup-course .lp-quiz-buttons .back-quiz,
		#popup-course .lp-quiz-buttons .review-quiz {
			background: transparent;
			color: var( --lp-aiddata-accent );
			border: 0;
		}

		#popup-course .quiz-status .countdown .fas,
		#popup-course .quiz-status .countdown .clock {
			color: var( --lp-aiddata-accent );
		}
	</style>
	<?php
	$aiddata_logo = plugins_url( 'assets/images/aiddata_logodark.png', LP_PLUGIN_FILE );
	?>
	<div class="popup-header__inner">
		<img class="popup-course-logo" src="<?php echo esc_url( $aiddata_logo ); ?>" alt="AidData" />
		<h2 class="course-title">
			<a
				href="<?php echo esc_url_raw( $course->get_permalink() ); ?>"><?php echo wp_kses_post( $course->get_title() ); ?></a>
		</h2>
	</div>
	<a
		href="<?php echo esc_url( 'http://localhost:10016/' ); ?>"
		class="back-course lp-button button button-complete-item button-complete-lesson lp-btn-complete-item"
		aria-label="<?php esc_attr_e( 'Exit Course', 'learnpress' ); ?>"
		data-title="<?php esc_attr_e( 'Exit Course', 'learnpress' ); ?>"
		data-confirm="<?php esc_attr_e( 'You are about to exit this course and return to the AidData learning portal. Your progress is saved automatically. Continue?', 'learnpress' ); ?>"
	>
		<span class="back-course__label"><?php esc_html_e( 'Exit Course', 'learnpress' ); ?></span>
	</a>
</div>

	<script id="lp-lesson-transitions">
	( function () {
		if ( window.lpLessonTransitionsInit ) {
			return;
		}
		window.lpLessonTransitionsInit = true;
		const doc = document;
		const body = doc.body;
		const motionQuery = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' );
		if ( motionQuery && motionQuery.matches ) {
			return;
		}
		const navSelectors = [
			'#popup-sidebar .course-item a',
			'#popup-footer .course-item-nav a',
			'#popup-footer .lp-footer-complete button',
			'#popup-footer .lp-footer-complete .lp-button',
			'#popup-footer .lp-btn-complete-item'
		];
		const startTransition = () => body.classList.add( 'lp-lesson-transitioning' );
		doc.addEventListener( 'click', ( event ) => {
			if ( navSelectors.some( ( selector ) => event.target.closest && event.target.closest( selector ) ) ) {
				startTransition();
			}
		} );
		doc.addEventListener( 'keyup', ( event ) => {
			if ( event.key === 'ArrowRight' || event.key === 'ArrowLeft' ) {
				startTransition();
			}
		} );
		const playEnter = () => {
			const root = doc.getElementById( 'popup-content' );
			if ( ! root ) {
				return;
			}
			const wrap = root.querySelector( '.content-item-wrap' );
			body.classList.remove( 'lp-lesson-transitioning' );
			if ( ! wrap ) {
				return;
			}
			wrap.classList.remove( 'lp-lesson-enter' );
			void wrap.offsetWidth;
			wrap.classList.add( 'lp-lesson-enter' );
			window.setTimeout( () => wrap.classList.remove( 'lp-lesson-enter' ), 500 );
		};
		const attachObserver = () => {
			const root = doc.getElementById( 'popup-content' );
			if ( ! root || root.dataset.lpLessonObserved ) {
				return;
			}
			root.dataset.lpLessonObserved = 'true';
			const observer = new MutationObserver( ( mutations ) => {
				const hasNewLesson = mutations.some( ( mutation ) => {
					if ( mutation.type !== 'childList' ) {
						return false;
					}
					return [ ...mutation.addedNodes ].some( ( node ) => {
						return node.nodeType === 1 && ( node.id === 'learn-press-content-item' || ( node.matches && node.matches( '.content-item-wrap' ) ) );
					} );
				} );
				if ( hasNewLesson ) {
					requestAnimationFrame( playEnter );
				}
			} );
			observer.observe( root, { childList: true } );
		};
		const watchRoot = new MutationObserver( ( mutations ) => {
			const replaced = mutations.some( ( mutation ) => {
				return [ ...mutation.addedNodes ].some( ( node ) => node.nodeType === 1 && node.id === 'popup-content' );
			} );
			if ( replaced ) {
				attachObserver();
				requestAnimationFrame( playEnter );
			}
		} );
		watchRoot.observe( doc.body, { childList: true, subtree: true } );
		const ready = () => {
			attachObserver();
			playEnter();
		};
		if ( doc.readyState === 'complete' ) {
			ready();
		} else {
			window.addEventListener( 'load', ready, { once: true } );
		}
	} )();
	</script>

	<script id="lp-exit-course-modal">
	( function () {
		const ready = () => {
			const exitButton = document.querySelector( '#popup-header .back-course' );
			if ( ! exitButton ) {
				return;
			}

			exitButton.addEventListener( 'click', ( event ) => {
				event.preventDefault();
				event.stopImmediatePropagation();
				const destination = exitButton.getAttribute( 'href' );
				const overlay = window.lpModalOverlay;
				const fallbackTitle = '<?php echo esc_js( __( 'Exit Course', 'learnpress' ) ); ?>';
				const fallbackConfirm = '<?php echo esc_js( __( 'Are you sure you want to exit this course?', 'learnpress' ) ); ?>';
				const title = exitButton.dataset.title || exitButton.getAttribute( 'aria-label' ) || fallbackTitle;
				const confirmMessage = exitButton.dataset.confirm || fallbackConfirm;

				if ( ! overlay || ! overlay.init || ! overlay.init() ) {
					if ( destination ) {
						window.location.href = destination;
					}
					return;
				}

				overlay.elLPOverlay.show();
				overlay.setTitleModal( title );
				overlay.setContentModal( '<div class="pd-2em">' + confirmMessage + '</div>' );
				overlay.callBackYes = () => {
					overlay.elLPOverlay.hide();
					if ( destination ) {
						window.location.href = destination;
					}
				};
			}, true );
		};

		if ( document.readyState === 'complete' || document.readyState === 'interactive' ) {
			ready();
		} else {
			document.addEventListener( 'DOMContentLoaded', ready );
		}
	} )();
	</script>
