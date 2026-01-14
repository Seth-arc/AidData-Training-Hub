/**
 * Page Transitions Enhancement
 * Provides smooth transitions between pages for better UX
 */

class PageTransitions {
    constructor() {
        this.isTransitioning = false;
        this.transitionDuration = 300;
        
        this.init();
    }

    init() {
        this.createTransitionOverlay();
        this.bindEvents();
        this.handlePageLoad();
    }

    createTransitionOverlay() {
        const overlayHTML = `
            <div class="page-transition-overlay" id="pageTransitionOverlay">
                <div class="transition-content">
                    <div class="transition-logo">
                        <img src="${window.location.origin}/wp-content/themes/twentytwentyfour/assets/images/logodark.png" alt="AidData">
                    </div>
                    <div class="transition-spinner">
                        <div class="spinner-ring"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', overlayHTML);
        this.overlay = document.getElementById('pageTransitionOverlay');
    }

    bindEvents() {
        // Handle external navigation links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && this.shouldTransition(link)) {
                e.preventDefault();
                this.transitionToPage(link.href);
            }
        });

        // Handle browser navigation
        window.addEventListener('beforeunload', () => {
            if (!this.isTransitioning) {
                this.showTransition();
            }
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && this.isTransitioning) {
                this.hideTransition();
            }
        });
    }

    shouldTransition(link) {
        const href = link.getAttribute('href');
        
        // Skip if it's a hash link, external link, or has special attributes
        if (!href || 
            href.startsWith('#') || 
            href.startsWith('mailto:') || 
            href.startsWith('tel:') ||
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            href.includes('wp-admin') ||
            href.includes('wp-login')) {
            return false;
        }

        // Only transition for internal course links and main navigation
        const isInternalLink = href.startsWith('/') || href.includes(window.location.hostname);
        const isCourseLink = href.includes('/course') || href.includes('/t-h/');
        
        return isInternalLink && (isCourseLink || link.classList.contains('nav-link'));
    }

    showTransition() {
        if (this.isTransitioning) return;
        
        this.isTransitioning = true;
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    hideTransition() {
        if (!this.isTransitioning) return;
        
        this.overlay.classList.add('fade-out');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            this.overlay.classList.remove('active', 'fade-out');
            this.isTransitioning = false;
        }, this.transitionDuration);
    }

    transitionToPage(url) {
        this.showTransition();
        
        // Add a small delay to show the transition, then navigate
        setTimeout(() => {
            window.location.href = url;
        }, 200);
    }

    handlePageLoad() {
        // Hide transition overlay when page loads
        window.addEventListener('load', () => {
            this.hideTransition();
        });

        // Fallback: hide after maximum time
        setTimeout(() => {
            this.hideTransition();
        }, 2000);
    }
}

// Initialize page transitions
document.addEventListener('DOMContentLoaded', () => {
    new PageTransitions();
});

// CSS Styles for Page Transitions
const transitionStyles = `
<style>
.page-transition-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    z-index: 9998;
    display: none;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease-out;
}

.page-transition-overlay.active {
    display: flex;
    opacity: 1;
}

.page-transition-overlay.fade-out {
    opacity: 0;
    transition: opacity 0.3s ease-in;
}

.transition-content {
    text-align: center;
    transform: translateY(10px);
    opacity: 0;
    animation: transitionFadeIn 0.4s ease-out 0.1s forwards;
}

@keyframes transitionFadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.transition-logo img {
    width: 60px;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.transition-spinner {
    display: flex;
    justify-content: center;
}

.transition-spinner .spinner-ring {
    width: 32px;
    height: 32px;
    border: 2px solid rgba(17, 87, 64, 0.2);
    border-radius: 50%;
    border-top: 2px solid #115740;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (prefers-reduced-motion: reduce) {
    .page-transition-overlay,
    .transition-content,
    .transition-spinner .spinner-ring {
        animation: none !important;
        transition: none !important;
    }
    
    .transition-content {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
`;

// Inject styles
document.head.insertAdjacentHTML('beforeend', transitionStyles);
