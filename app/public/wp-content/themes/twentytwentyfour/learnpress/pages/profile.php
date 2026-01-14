<?php
/**
 * Custom LearnPress profile page with dashboard header.
 *
 * Mirrors the front-page / dashboard experience for LearnPress profile routes
 * while preserving core LearnPress rendering beneath the hero.
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $profile ) ) {
    return;
}

if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( get_permalink() ) );
    exit;
}

$current_user  = wp_get_current_user();
$profile_user  = $profile->get_user();
$statistic     = method_exists( $profile, 'get_statistic_info' ) ? (array) $profile->get_statistic_info() : array();
$enrolled      = intval( $statistic['enrolled_courses'] ?? 0 );
$completed     = intval( $statistic['completed_courses'] ?? 0 );
$certificates  = intval( apply_filters( 'aiddata/profile/certificates_count', 0, $profile_user ) );
$template_uri  = get_template_directory_uri();
$ajax_endpoint = admin_url( 'admin-ajax.php' );
$nonce         = wp_create_nonce( 'dashboard_nonce' );
$profile_avatar_html = '';

if ( $profile_user && method_exists( $profile_user, 'get_profile_picture' ) ) {
    $profile_avatar_html = $profile_user->get_profile_picture();
} elseif ( $current_user instanceof WP_User ) {
    $profile_avatar_html = get_avatar( $current_user->ID, 192 );
}

wp_enqueue_style( 'auth-styles', $template_uri . '/assets/css/auth-styles.css', array(), '1.0.0' );
wp_enqueue_style( 'loading-screen', $template_uri . '/assets/css/loading-screen.css', array(), '1.0.0' );

$profile_script_dependencies = array(
    'wp-element',
    'wp-components',
    'wp-data',
    'wp-url',
    'wp-api-fetch',
    'wp-hooks',
    'wp-i18n',
    'lodash',
);

foreach ( $profile_script_dependencies as $script_handle ) {
    if ( wp_script_is( $script_handle, 'registered' ) ) {
        wp_enqueue_script( $script_handle );
    }
}

if ( wp_style_is( 'wp-components', 'registered' ) ) {
    wp_enqueue_style( 'wp-components' );
}
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    *, *::before, *::after {
        font-family: 'Inter', sans-serif !important;
    }

    body, html {
        margin: 0;
        padding: 0;
        background: #ffffff;
        min-height: 100vh;
        color: #1a1a1a;
        -webkit-font-smoothing: antialiased;
    }

    .site-header, #masthead, header.wp-block-template-part {
        display: none !important;
    }

    hr.wp-block-separator {
        display: none;
    }

    :root {
        --primary-color: #115740;
    }

    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #115740;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #0a3d2c;
    }

    .learnpress-dashboard-wrapper {
        background: #fff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
    }

    .learnpress-dashboard-wrapper > * {
        margin-top: 0;
    }

    .lms-header {
        background: rgba(255, 255, 255, 0.98);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        padding: 1.5rem 0;
        box-shadow: 0 10px 30px rgba(17, 87, 64, 0.05);
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 4rem;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 4rem;
        align-items: center;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .logo {
        height: 30px;
        width: auto;
        opacity: 0.95;
        transition: all 0.3s ease;
        filter: drop-shadow(0 1px 2px rgba(17, 87, 64, 0.1));
    }

    .logo:hover {
        opacity: 1;
        transform: translateY(-1px);
    }

    .header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-left: auto;
        gap: 1rem;
    }

    .header-icons {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }

    .header-button {
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
    }

    .header-button:hover {
        background: rgba(17, 87, 64, 0.08);
        transform: translateY(-1px);
    }

    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
        display: none;
    }

    .profile-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        min-width: 280px;
        padding: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .profile-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 20px;
        border-bottom: 1px solid #f1f3f5;
    }

    .dropdown-user-info .user-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }

    .dropdown-user-info .user-email {
        font-size: 14px;
        color: #6c757d;
    }

    .dropdown-item {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
        color: #115740;
    }

    .dropdown-item.logout-button {
        color: #dc3545;
        border-top: 1px solid #f1f3f5;
    }

    .dashboard-content {
        flex: 1;
        margin-top: 0;
        padding-top: 0;
        background: #ffffff;
    }

    .dashboard-hero {
        margin-top: 0;
        padding: calc(90px + 0.5rem) 2rem 4rem;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.02), rgba(26, 128, 95, 0.08));
    }

    .dashboard-hero-content {
        position: relative;
        z-index: 10;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        text-align: center;
    }

    .dashboard-shapes {
        position: absolute;
        inset: 0;
        z-index: 1;
        overflow: hidden;
    }

    .dashboard-shape {
        position: absolute;
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        animation: floatTriangle1 18s infinite;
        opacity: 0.4;
    }

    .dashboard-shape-1 {
        width: 360px;
        height: 360px;
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.04), rgba(26, 128, 95, 0.07));
        top: -180px;
        left: -100px;
    }

    .dashboard-shape-2 {
        width: 280px;
        height: 280px;
        background: linear-gradient(135deg, rgba(26, 128, 95, 0.03), rgba(17, 87, 64, 0.05));
        top: 40%;
        right: -140px;
        animation-duration: 22s;
    }

    .dashboard-shape-3 {
        width: 220px;
        height: 220px;
        background: linear-gradient(135deg, rgba(17, 87, 64, 0.02), rgba(26, 128, 95, 0.04));
        bottom: -110px;
        left: 45%;
        animation-duration: 26s;
    }

    @keyframes floatTriangle1 {
        0%, 100% {
            transform: translateY(0) rotate(12deg);
        }
        50% {
            transform: translateY(-20px) rotate(18deg);
        }
    }

    .hero-top {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center;
        margin-bottom: 2rem;
    }

    .hero-avatar {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        padding: 6px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 20px 40px rgba(17, 87, 64, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
    }

    .hero-avatar::after {
        content: '';
        position: absolute;
        inset: 10px;
        border-radius: 50%;
        border: 1px solid rgba(17, 87, 64, 0.15);
        pointer-events: none;
    }

    .hero-avatar .lp-user-profile-avatar,
    .hero-avatar .lp-user-profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
    }

    .user-info h1 {
        font-size: clamp(2.4rem, 5vw, 3.2rem);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .user-info .user-email {
        color: #5f6b6f;
        font-size: 1rem;
    }


    .dashboard-main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 2rem 4rem;
        width: 100%;
        box-sizing: border-box;
    }

    #learn-press-profile {
        background: transparent;
        border: none;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
    }

    .lp-profile-content {
        background: transparent;
        position: relative;
        min-height: 320px;
    }

    .lp-profile-content::after,
    .lp-profile-content::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .lp-profile-content::after {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(4px);
        z-index: 1;
    }

    .lp-profile-content::before {
        top: 50%;
        left: 50%;
        right: auto;
        bottom: auto;
        width: 48px;
        height: 48px;
        margin: -24px 0 0 -24px;
        border-radius: 50%;
        border: 3px solid rgba(17, 87, 64, 0.25);
        border-top-color: #115740;
        animation: profileContentSpinner 0.8s linear infinite;
        z-index: 2;
    }

    .lp-profile-content.is-loading::after,
    .lp-profile-content.is-loading::before {
        opacity: 1;
        pointer-events: auto;
    }

    @keyframes profileContentSpinner {
        to {
            transform: rotate(360deg);
        }
    }

    #learn-press-profile .lp-user-profile-avatar,
    #learn-press-profile .lp-profile-username {
        display: none;
    }

    #learn-press-profile #profile-sidebar,
    #learn-press-profile .lp-profile-content {
        border-top: 1px solid rgba(17, 87, 64, 0.15);
    }

    #learn-press-profile #profile-nav .lp-profile-nav-tabs > li > a::after,
    #learn-press-profile #profile-nav .lp-profile-nav-tabs > li > a i {
        display: none !important;
        content: none !important;
    }

    @media (max-width: 1024px) {
        .header-content {
            padding: 0 2rem;
            gap: 2rem;
        }

        .dashboard-hero {
            padding: calc(80px + 0.5rem) 2rem 3rem;
        }

        .dashboard-main {
            padding: 2rem 1.5rem 3rem;
        }

        #learn-press-profile {
            padding: 1.5rem;
        }
    }

        /* Footer Styles */
        .site-footer {
            background-color: #115740;
            color: white;
            padding: 3rem 1rem 0;
            margin-top: 4rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .footer-content {
                grid-template-columns: 2fr 1fr 1fr 1fr;
            }
        }

        .footer-section h4 {
            font-size: 1rem;
            margin-bottom: 1rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif !important;
            text-transform: uppercase;
            color: #d2bb93;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif !important;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.2s;
            font-family: 'Inter', sans-serif !important;
        }

        .footer-section a:hover {
            color: white;
            text-decoration: underline;
        }

        .footer-logo {
            height: 40px;
            margin-bottom: 1rem;
        }

        .footer-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0 0 1.5rem;
            font-family: 'Inter', sans-serif !important;
        }

        .social-links {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .social-links svg {
            width: 20px;
            height: 20px;
        }

        .footer-bottom-content {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 2rem;
        }

        address {
            font-style: normal;
            line-height: 1.6;
        }

    @media (max-width: 768px) {
        .lms-header {
            padding: 1rem 0;
        }

        .header-content {
            padding: 0 1.25rem;
        }

        .dashboard-hero {
            padding: calc(70px + 0.5rem) 1.5rem 2.5rem;
        }

        .hero-avatar {
            width: 120px;
            height: 120px;
        }

        .dashboard-hero-content {
            padding: 1.5rem 1rem;
        }
    }

    @media (max-width: 480px) {
        .dashboard-hero {
            padding: calc(60px + 0.5rem) 1rem 2rem;
        }

        .hero-avatar {
            width: 100px;
            height: 100px;
        }
    }
</style>

<div class="learnpress-dashboard-wrapper">
    <div class="loading-screen">
        <div class="loading-triangles-container">
            <div class="loading-triangle triangle-move-1"></div>
            <div class="loading-triangle triangle-move-2"></div>
            <div class="loading-triangle triangle-move-3"></div>
        </div>
        <div class="loading-content">
            <img src="<?php echo esc_url( $template_uri ); ?>/assets/images/logodark.png" alt="AidData Logo" class="loading-logo">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
            </div>
            <p class="loading-text">Loading Profile</p>
        </div>
    </div>

    <header class="lms-header">
        <div class="header-content">
            <div class="logo-section">
                <a href="https://www.aiddata.org" target="_blank" rel="noopener">
                    <img src="<?php echo esc_url( $template_uri ); ?>/assets/images/logodark.png" alt="AidData Logo" class="logo">
                </a>
            </div>
            <div class="header-actions">
                <div class="header-icons">
                    <button class="header-button menu-button" aria-label="Profile menu" aria-haspopup="true">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18" />
                        </svg>
                    </button>
                    <div class="profile-dropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-user-info">
                                <span class="user-name"><?php echo esc_html( $current_user->display_name ); ?></span>
                                <span class="user-email"><?php echo esc_html( $current_user->user_email ); ?></span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="dropdown-item">Home</a>
                        <a href="<?php echo esc_url( home_url( '/help.html' ) ); ?>" class="dropdown-item">Help</a>
                        <button class="dropdown-item logout-button" onclick="location.href='<?php echo esc_url( wp_logout_url( home_url() ) ); ?>'">Sign Out</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="dashboard-hero">
            <div class="dashboard-shapes">
                <div class="dashboard-shape dashboard-shape-1"></div>
                <div class="dashboard-shape dashboard-shape-2"></div>
                <div class="dashboard-shape dashboard-shape-3"></div>
            </div>
            <div class="dashboard-hero-content">
                <div class="hero-top">
                    <?php if ( ! empty( $profile_avatar_html ) ) : ?>
                        <div class="hero-avatar">
                            <div class="lp-user-profile-avatar">
                                <?php echo wp_kses_post( $profile_avatar_html ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="user-info">
                        <h1><?php echo esc_html( $profile_user ? $profile_user->get_display_name() : $current_user->display_name ); ?></h1>
                        <div class="user-email"><?php echo esc_html( $profile_user ? $profile_user->get_email() : $current_user->user_email ); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-main">
            <div id="learn-press-profile" <?php $profile->main_class(); ?>>
                <div class="lp-content-area">
                    <?php do_action( 'learn-press/user-profile', $profile ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="AidData Logo" class="footer-logo">
            <p>AidData is a research lab at William & Mary's Global Research Institute.</p>
            <div class="social-links">
                <a href="https://twitter.com/AidData" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                </a>
                <a href="https://www.linkedin.com/company/aiddata" target="_blank" rel="noopener noreferrer" aria-label="Connect with us on LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
                <a href="https://github.com/aiddata" target="_blank" rel="noopener noreferrer" aria-label="View our code on GitHub">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                </a>
                <a href="https://www.instagram.com/aiddata?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Instagram">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
            </div>
            <button onclick="window.open('https://www.aiddata.org/newsletter', '_blank')" class="newsletter-button" aria-label="Get our Newsletter" style="display: block; background-color: #026447; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; margin-top: 20px; font-weight: 600; transition: all 0.3s ease; font-family: inherit; font-size: 13px; width: fit-content; box-shadow: 0 2px 4px rgba(0,0,0,0.15); letter-spacing: 0.3px; text-transform: uppercase;">
                <span>Subscribe to Newsletter</span>
            </button>
        </div>

        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="https://www.aiddata.org/about" target="_blank">About Us</a></li>
                <li><a href="https://www.aiddata.org/data" target="_blank">Data</a></li>
                <li><a href="https://www.aiddata.org/publications" target="_blank">Publications</a></li>
                <li><a href="https://www.aiddata.org/blog" target="_blank">Blog</a></li>
                <li><a href="https://www.aiddata.org/careers" target="_blank">Careers</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Resources</h4>
            <ul>
                <li><a href="https://www.aiddata.org/methods" target="_blank">Methods</a></li>
                <li><a href="https://www.aiddata.org/datasets" target="_blank">Datasets</a></li>
                <li><a href="https://www.aiddata.org/geoquery" target="_blank">GeoQuery</a></li>
                <li><a href="https://www.aiddata.org/china-research-lab" target="_blank">China Research Lab</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contact</h4>
            <address>
                If you're looking to partner up for online, hybrid or in-person trainings, please reach out to<br>
                <a href="mailto:training@aiddata.org" style="color: #ffffff;">training@aiddata.org</a>
            </address>
        </div>
    </div>
  
    <hr style="border: 0; height: 1px; background-color: white; width: 100%; margin: 20px 0;">
  
    <div class="footer-bottom" style="background: transparent;">
        <div class="footer-bottom-content">
            <a href="https://www.wm.edu" target="_blank" rel="noopener noreferrer">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/wm_logo_white.png" alt="William & Mary Logo" class="footer-bottom-logo" style="max-height: 60px;">
            </a>
        </div>
    </div>
</footer>

<script>
(function() {
    const adminAjax = <?php echo wp_json_encode( $ajax_endpoint ); ?>;
    const nonce = <?php echo wp_json_encode( $nonce ); ?>;

    const hideLoading = () => {
        const loadingScreen = document.querySelector('.loading-screen');
        if (!loadingScreen) {
            return;
        }
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
    };

    const animateNumber = (element, targetNumber) => {
        if (!element) {
            return;
        }
        const currentText = element.textContent.trim();
        const currentNumber = currentText === '...' ? 0 : parseInt(currentText, 10) || 0;
        if (currentNumber === targetNumber) {
            return;
        }
        const duration = 800;
        const steps = 20;
        const increment = (targetNumber - currentNumber) / steps;
        let currentStep = 0;
        const timer = setInterval(() => {
            currentStep += 1;
            const newValue = Math.round(currentNumber + (increment * currentStep));
            element.textContent = newValue;
            if (currentStep >= steps) {
                element.textContent = targetNumber;
                clearInterval(timer);
            }
        }, duration / steps);
    };

    const updateStats = (stats) => {
        const totalEnrolled = (stats.courses?.enrolled_courses || 0) + (stats.tutorials?.total_tutorials || 0);
        const totalCompleted = (stats.courses?.completed_courses || 0) + (stats.tutorials?.completed_tutorials || 0);
        const totalCertificates = stats.certificates?.total_certificates || 0;

        animateNumber(document.getElementById('stat-enrolled'), totalEnrolled);
        animateNumber(document.getElementById('stat-completed'), totalCompleted);
        animateNumber(document.getElementById('stat-certificates'), totalCertificates);
    };

    const loadStats = () => {
        const body = new URLSearchParams({
            action: 'get_dashboard_stats',
            nonce
        });

        fetch(adminAjax, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body
        })
            .then((response) => response.json())
            .then((data) => {
                if (data?.success && data?.data) {
                    updateStats(data.data);
                }
            })
            .catch(() => {
                // Silently ignore; server rendered numbers act as fallback.
            });
    };

    const initDropdown = () => {
        const menuButton = document.querySelector('.menu-button');
        const dropdown = document.querySelector('.profile-dropdown');
        if (!menuButton || !dropdown) {
            return;
        }

        menuButton.addEventListener('click', (event) => {
            event.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', () => dropdown.classList.remove('show'));
        dropdown.addEventListener('click', (event) => event.stopPropagation());
    };

    const initProfileAjaxNavigation = () => {
        const profileRoot = document.getElementById('learn-press-profile');
        if (!profileRoot || profileRoot.dataset.ajaxNavReady === '1') {
            return;
        }

        const navTabs =
            profileRoot.querySelector('#profile-nav .lp-profile-nav-tabs') ||
            profileRoot.querySelector('#profile-sidebar .lp-profile-nav-tabs') ||
            profileRoot.querySelector('.lp-profile-nav-tabs');
        const content = profileRoot.querySelector('.lp-profile-content');
        const navOriginSelectors = ['#profile-nav', '#profile-sidebar', '.lp-profile-nav', '.lp-profile-nav-tabs'];
        const navLinkSelectors = navOriginSelectors.map((selector) => `${selector} a`).join(', ');

        if (!content || !window.history?.pushState) {
            return;
        }

        profileRoot.dataset.ajaxNavReady = '1';

        const createController = () => (typeof AbortController !== 'undefined' ? new AbortController() : null);
        let activeController = null;

        const setLoadingState = (isLoading) => {
            content.classList.toggle('is-loading', !!isLoading);
        };

        const normalizeUrl = (href) => {
            try {
                return new URL(href, window.location.origin).toString();
            } catch (error) {
                return null;
            }
        };

        const prepareNavLinks = () => {
            const navLinks = profileRoot.querySelectorAll(navLinkSelectors);
            navLinks.forEach((link) => {
                if (!link || link.dataset.lpNavSanitized === '1') {
                    return;
                }
                const parentLi = link.closest('li');
                if (parentLi?.classList.contains('logout')) {
                    return;
                }
                const targetHref = link.getAttribute('href');
                const normalized = targetHref && targetHref !== '#' ? normalizeUrl(targetHref) : null;
                if (!normalized) {
                    return;
                }
                link.dataset.lpAjaxUrl = normalized;
                link.dataset.lpNavSanitized = '1';
                link.setAttribute('href', '#');
                if (!link.getAttribute('role')) {
                    link.setAttribute('role', 'button');
                }
                if (!link.getAttribute('tabindex')) {
                    link.setAttribute('tabindex', '0');
                }
            });
        };

        const getTargetUrl = (link) => {
            if (!link) {
                return null;
            }
            const candidate = link.dataset.lpAjaxUrl || link.dataset.href || link.getAttribute('data-href');
            if (candidate) {
                return normalizeUrl(candidate);
            }
            const rawHref = link.getAttribute('href');
            if (!rawHref || rawHref === '#' || rawHref.startsWith('javascript:')) {
                return null;
            }
            return normalizeUrl(rawHref);
        };

        const isModifiedEvent = (event) => event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;

        const isWithinNav = (link) => navOriginSelectors.some((selector) => link.closest(selector));

        const shouldHandleLink = (link) => {
            if (!link || link.dataset.noAjax === 'true') {
                return false;
            }
            if (link.hasAttribute('download')) {
                return false;
            }
            if (link.target && link.target !== '_self') {
                return false;
            }
            const parentLi = link.closest('li');
            if (parentLi?.classList.contains('logout')) {
                return false;
            }
            if (!isWithinNav(link)) {
                return false;
            }
            return true;
        };

        const swapFromDocument = (doc) => {
            const incomingContent =
                doc.querySelector('#learn-press-profile .lp-profile-content') ||
                doc.querySelector('.lp-profile-content');

            if (!incomingContent) {
                return false;
            }

            const incomingTabs =
                doc.querySelector('#profile-nav .lp-profile-nav-tabs') ||
                doc.querySelector('.lp-profile-nav-tabs');

            content.innerHTML = incomingContent.innerHTML;

            if (incomingTabs && navTabs) {
                navTabs.innerHTML = incomingTabs.innerHTML;
            }

            prepareNavLinks();

            const newTitle = doc.querySelector('title');
            if (newTitle) {
                document.title = newTitle.textContent.trim();
            }

            const scrollTarget = document.querySelector('.dashboard-hero') || profileRoot;
            scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });

            return true;
        };

        const loadProfileFragment = (url, pushState = true) => {
            if (activeController && typeof activeController.abort === 'function') {
                activeController.abort();
            }
            activeController = createController();
            const controller = activeController;
            setLoadingState(true);

            const fetchOptions = {
                credentials: 'same-origin'
            };

            if (controller) {
                fetchOptions.signal = controller.signal;
            }

            fetch(url, fetchOptions)
                .then((response) => response.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const swapped = swapFromDocument(doc);
                    if (!swapped) {
                        window.location.href = url;
                        return;
                    }

                    if (pushState) {
                        window.history.pushState({ lpProfileUrl: url }, '', url);
                    }

                    const detail = { url };
                    profileRoot.dispatchEvent(new CustomEvent('lpProfileContentUpdated', { detail }));
                    document.dispatchEvent(new CustomEvent('lpProfileContentUpdated', { detail }));
                })
                .catch((error) => {
                    if (error.name !== 'AbortError') {
                        window.location.href = url;
                    }
                })
                .finally(() => {
                    const wasAborted = controller && controller.signal && controller.signal.aborted;
                    const isLatestRequest = activeController === controller;
                    if (!wasAborted && isLatestRequest) {
                        setLoadingState(false);
                        activeController = null;
                    }
                });
        };

        const stopEvent = (event) => {
            event.preventDefault();
            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }
            event.stopPropagation();
        };

        const handleNavClick = (event) => {
            if (event.defaultPrevented || event.button !== 0 || isModifiedEvent(event)) {
                return;
            }
            const link = event.target.closest('a');
            if (!link || !shouldHandleLink(link)) {
                return;
            }
            const targetUrl = getTargetUrl(link);
            if (!targetUrl || !targetUrl.startsWith(window.location.origin) || targetUrl === window.location.href) {
                return;
            }
            stopEvent(event);
            loadProfileFragment(targetUrl, true);
        };

        if (window.history.replaceState) {
            window.history.replaceState({ lpProfileUrl: window.location.href }, '', window.location.href);
        }

        prepareNavLinks();

        profileRoot.addEventListener('click', handleNavClick, true);

        window.addEventListener('popstate', (event) => {
            if (!event.state?.lpProfileUrl) {
                return;
            }
            loadProfileFragment(event.state.lpProfileUrl, false);
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        hideLoading();
        initDropdown();
        initProfileAjaxNavigation();
        loadStats();
        setInterval(loadStats, 30000);

        ['stat-enrolled', 'stat-completed', 'stat-certificates'].forEach((id) => {
            const el = document.getElementById(id);
            if (!el) {
                return;
            }
            const initial = parseInt(el.dataset.initial || el.textContent, 10) || 0;
            el.textContent = initial;
        });
    });
})();
</script>
