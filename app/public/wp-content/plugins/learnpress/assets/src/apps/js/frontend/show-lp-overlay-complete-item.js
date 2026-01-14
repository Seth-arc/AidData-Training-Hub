import lpModalOverlay from '../utils/lp-modal-overlay';

const lpModalOverlayCompleteItem = {
	elBtnFinishCourse: null,
	elBtnCompleteItem: null,
	init() {
		if ( ! lpModalOverlay.init() ) {
			return;
		}

		if ( undefined === lpGlobalSettings || 'yes' !== lpGlobalSettings.option_enable_popup_confirm_finish ) {
			return;
		}

		this.elBtnFinishCourse = document.querySelectorAll( '.lp-btn-finish-course' );
		this.elBtnCompleteItem = document.querySelectorAll( '.lp-btn-complete-item' );

		if ( this.elBtnCompleteItem.length ) {
			this.elBtnCompleteItem.forEach( ( element ) => {
				element.addEventListener( 'click', ( e ) => {
					e.preventDefault();

					const target = e.currentTarget;
					const form = target.closest( 'form' );
					const destination = target.getAttribute( 'href' );
					const title = ( form && form.dataset.title ) || target.dataset.title || '';
					const confirmMessage = ( form && form.dataset.confirm ) || target.dataset.confirm || 'Are you sure?';

					lpModalOverlay.elLPOverlay.show();
					lpModalOverlay.setTitleModal( title );
					lpModalOverlay.setContentModal( '<div class="pd-2em">' + confirmMessage + '</div>' );
					lpModalOverlay.callBackYes = () => {
						if ( form ) {
							form.submit();
						} else if ( destination ) {
							window.location.href = destination;
						}
					};
				} );
			} );
		}

		if ( this.elBtnFinishCourse ) {
			this.elBtnFinishCourse.forEach( ( element ) => element.addEventListener( 'click', ( e ) => {
				e.preventDefault();

				const form = e.target.closest( 'form' );

				lpModalOverlay.elLPOverlay.show();
				lpModalOverlay.setTitleModal( form.dataset.title );
				lpModalOverlay.setContentModal( '<div class="pd-2em">' + form.dataset.confirm + '</div>' );
				lpModalOverlay.callBackYes = () => {
					form.submit();
				};
			} ) );
		}
	},
};

export default lpModalOverlayCompleteItem;
