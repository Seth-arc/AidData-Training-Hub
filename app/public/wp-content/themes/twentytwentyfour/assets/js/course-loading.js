/**
 * Enhanced Course Loading Experience
 * Smooth transitions with course-specific branding and optimized UX
 */

class CourseLoader {
    constructor() {
        this.overlay = null;
        this.progressBar = null;
        this.messageElement = null;
        this.isLoading = false;
        this.currentStep = 0;
        this.loadingSteps = [
            "Initializing course environment",
            "Loading interactive content", 
            "Preparing your dashboard",
            "Launching experience"
        ];
        this.courseImages = {
            'Navigating Global Development Finance': 'global_finance.png',
            'Critical Data Analysis and Visualization': 'data_analysis.png',
            'Securing Development Funding': 'port_project.png',
            'Navigating Debt Distress': 'debt_distress.png',
            'China.AidData.org Dashboard Tutorial': 'china_dashboard.png',
            'Credit Shopper Tool': 'credit_shopper.png',
            'Balancing the Scales': 'balance_scales.png',
            'How China Lends Dashboard Tutorial': 'china_lends.png'
        };
        
        this.init();
    }

    init() {
        this.createLoadingHTML();
        this.bindEvents();
    }

    createLoadingHTML() {
        const logoUrl = window.location.origin + '/wp-content/themes/twentytwentyfour/assets/images/logodark.png';
        
        const overlayHTML = `
            <div class="course-loading-overlay" id="courseLoadingOverlay">
                <div class="course-loading-content">
                    <div class="course-loading-header">
                        <div class="course-hero-section">
                            <img src="${logoUrl}" alt="AidData" class="course-loading-logo">
                            <div class="course-preview-image" id="coursePreviewImage"></div>
                        </div>
                        <h2 class="course-loading-title" id="courseLoadingTitle">Getting Started</h2>
                        <p class="course-loading-subtitle" id="courseLoadingSubtitle">Preparing your course experience</p>
                    </div>
                    
                    <div class="transition-indicator">
                        <div class="loading-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    
                    <div class="course-loading-progress">
                        <div class="progress-line">
                            <div class="progress-fill" id="courseProgressFill"></div>
                            <div class="progress-glow"></div>
                        </div>
                        <p class="loading-message" id="courseLoadingMessage">Initializing...</p>
                        <div class="loading-details">
                            <span class="loading-time">Estimated time: 3-5 seconds</span>
                        </div>
                    </div>
                </div>
                
                <!-- Background Elements -->
                <div class="loading-background">
                    <div class="geometric-container">
                        <div class="floating-triangles">
                            <div class="triangle triangle-1"></div>
                            <div class="triangle triangle-2"></div>
                            <div class="triangle triangle-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', overlayHTML);
        
        this.overlay = document.getElementById('courseLoadingOverlay');
        this.progressBar = document.getElementById('courseProgressFill');
        this.messageElement = document.getElementById('courseLoadingMessage');
    }

    bindEvents() {
        // Bind click events to course buttons
        document.addEventListener('click', (e) => {
            const courseButton = e.target.closest('[data-course]');
            if (courseButton) {
                e.preventDefault();
                
                const courseUrl = courseButton.getAttribute('data-course');
                const courseTitle = this.extractCourseTitle(courseButton);
                
                // Add visual feedback immediately
                courseButton.style.transform = 'scale(0.98)';
                courseButton.style.opacity = '0.8';
                
                setTimeout(() => {
                    courseButton.style.transform = '';
                    courseButton.style.opacity = '';
                }, 150);
                
                // Show loading with course-specific content
                this.showLoading(courseTitle, courseButton);
                this.navigateToCourse(courseUrl);
            }
        });
        
        // Handle browser back button during loading
        window.addEventListener('beforeunload', () => {
            if (this.isLoading) {
                this.hideLoading();
            }
        });
        
        // Preload course images for faster display
        this.preloadCourseImages();
    }

    extractCourseTitle(button) {
        // Try to find title from various sources
        const cardElement = button.closest('.featured-course, .course-card');
        
        if (cardElement) {
            const titleElement = cardElement.querySelector('h3, h4, .course-title, .card-title');
            if (titleElement) {
                return titleElement.textContent.trim();
            }
        }
        
        return 'Course';
    }

    preloadCourseImages() {
        Object.values(this.courseImages).forEach(imageName => {
            const img = new Image();
            img.src = `${window.location.origin}/wp-content/themes/twentytwentyfour/assets/images/${imageName}`;
        });
    }

    setCourseImage(courseTitle, courseButton) {
        const previewElement = document.getElementById('coursePreviewImage');
        if (!previewElement) return;
        
        // Get image from course card or use mapping
        let imageSrc = '';
        const cardElement = courseButton.closest('.featured-course, .course-card');
        
        if (cardElement) {
            const imgElement = cardElement.querySelector('.preview-image, .course-image, img');
            if (imgElement) {
                imageSrc = imgElement.src;
            }
        }
        
        // Fallback to course mapping
        if (!imageSrc && this.courseImages[courseTitle]) {
            imageSrc = `${window.location.origin}/wp-content/themes/twentytwentyfour/assets/images/${this.courseImages[courseTitle]}`;
        }
        
        if (imageSrc) {
            previewElement.innerHTML = `<img src="${imageSrc}" alt="${courseTitle}" class="course-preview-img">`;
            previewElement.style.display = 'block';
        }
    }

    getShortCourseTitle(title) {
        // Shorten long titles for better display
        const titleMap = {
            'Navigating Global Development Finance': 'Development Finance',
            'Critical Data Analysis and Visualization': 'Data Analysis',
            'Securing Development Funding': 'Development Funding',
            'Navigating Debt Distress': 'Debt Distress',
            'China.AidData.org Dashboard Tutorial': 'China Dashboard',
            'Credit Shopper Tool': 'Credit Shopper',
            'Balancing the Scales': 'Balancing Scales',
            'How China Lends Dashboard Tutorial': 'China Lends'
        };
        
        return titleMap[title] || title;
    }

    showLoading(courseTitle, courseButton) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.currentStep = 0;
        
        // Update title and subtitle
        const titleElement = document.getElementById('courseLoadingTitle');
        const subtitleElement = document.getElementById('courseLoadingSubtitle');
        
        if (titleElement && subtitleElement) {
            titleElement.textContent = this.getShortCourseTitle(courseTitle);
            subtitleElement.textContent = 'Loading your personalized experience';
        }
        
        // Set course-specific image
        this.setCourseImage(courseTitle, courseButton);
        
        // Show overlay with smooth animation
        this.overlay.classList.add('active');
        
        // Disable scrolling
        document.body.style.overflow = 'hidden';
        
        // Start loading sequence
        this.startLoadingSequence();
    }

    startLoadingSequence() {
        // Reset progress
        if (this.progressBar) {
            this.progressBar.style.width = '0%';
            this.progressBar.style.animation = 'none';
            // Force reflow to restart animation
            this.progressBar.offsetHeight;
            this.progressBar.style.animation = 'progressSmooth 2.5s ease-out forwards';
        }
        
        // Start message sequence
        this.updateLoadingMessage();
        
        // Set optimized auto-advance timer
        const messageInterval = setInterval(() => {
            this.currentStep++;
            
            if (this.currentStep < this.loadingSteps.length) {
                this.updateLoadingMessage();
            } else {
                clearInterval(messageInterval);
            }
        }, 450); // Slightly faster for better UX
    }

    updateLoadingMessage() {
        if (!this.messageElement) return;
        
        const currentMessage = this.loadingSteps[this.currentStep] || 'Ready to launch!';
        
        // Smooth transition with enhanced feedback
        this.messageElement.style.opacity = '0.5';
        this.messageElement.style.transform = 'translateY(2px)';
        
        setTimeout(() => {
            this.messageElement.textContent = currentMessage;
            this.messageElement.classList.add('active');
            this.messageElement.style.opacity = '1';
            this.messageElement.style.transform = 'translateY(0)';
        }, 120);
        
        // Remove active class after a moment
        setTimeout(() => {
            this.messageElement.classList.remove('active');
        }, 500);
    }

    navigateToCourse(courseUrl) {
        // Optimized delay for smoother transition
        setTimeout(() => {
            try {
                // Add fade effect before navigation
                this.overlay.style.background = 'linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)';
                
                // Validate URL before navigation
                if (this.isValidUrl(courseUrl)) {
                    // Navigate to course with smooth transition
                    window.location.href = courseUrl;
                } else {
                    this.handleNavigationError('Invalid course URL');
                }
            } catch (error) {
                this.handleNavigationError(error.message);
            }
        }, 1900); // Reduced timing for faster navigation
    }

    isValidUrl(url) {
        try {
            new URL(url, window.location.origin);
            return true;
        } catch {
            return false;
        }
    }

    handleNavigationError(errorMessage) {
        console.error('Course navigation error:', errorMessage);
        
        // Update loading message to show error
        if (this.messageElement) {
            this.messageElement.textContent = 'Connection issue detected. Retrying...';
            this.messageElement.style.color = '#dc3545';
        }
        
        // Attempt to retry or fallback
        setTimeout(() => {
            this.hideLoading();
            
            // Show user-friendly error message
            const errorNotification = document.createElement('div');
            errorNotification.className = 'course-error-notification';
            errorNotification.innerHTML = `
                <div class="error-content">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Unable to load course. Please try again or check your connection.</span>
                    <button class="retry-button" onclick="this.parentElement.parentElement.remove()">Dismiss</button>
                </div>
            `;
            
            document.body.appendChild(errorNotification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (errorNotification.parentElement) {
                    errorNotification.remove();
                }
            }, 5000);
        }, 2000);
    }

    hideLoading() {
        if (!this.isLoading) return;
        
        this.overlay.classList.add('fade-out');
        
        // Re-enable scrolling
        document.body.style.overflow = '';
        
        setTimeout(() => {
            this.overlay.classList.remove('active', 'fade-out');
            this.isLoading = false;
        }, 300);
    }
}

// Initialize course loader when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('[data-course]')) {
        new CourseLoader();
    }
});

// Handle page visibility changes to prevent glitches
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        // Pause animations when tab is not visible
        const overlay = document.getElementById('courseLoadingOverlay');
        if (overlay && overlay.classList.contains('active')) {
            overlay.style.animationPlayState = 'paused';
        }
    } else {
        // Resume animations when tab becomes visible
        const overlay = document.getElementById('courseLoadingOverlay');
        if (overlay && overlay.classList.contains('active')) {
            overlay.style.animationPlayState = 'running';
        }
    }
}); 