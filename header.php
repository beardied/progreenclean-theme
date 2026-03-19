<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url(PGC_URL . '/assets/images/favicon-32.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url(PGC_URL . '/assets/images/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo esc_url(PGC_URL . '/assets/images/logo-icon-512.png'); ?>">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="pgc-header">
    <div class="pgc-container">
        <div class="pgc-header-inner">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="pgc-logo">
                <img src="<?php echo esc_url(PGC_URL . '/assets/images/logo.png'); ?>" alt="ProGreenClean" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            
            <nav class="pgc-nav">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'pgc-nav-list',
                    'fallback_cb' => false,
                ]);
                ?>
            </nav>
            
            <div style="display: flex; gap: 16px; align-items: center;">
                <a href="tel:<?php echo esc_attr(get_option('pgc_phone', '08001234567')); ?>" style="color: var(--pgc-gray-600); font-weight: 600; font-size: 15px;">
                    <?php echo esc_html(get_option('pgc_phone', '0800 123 4567')); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/get-a-quote/')); ?>" class="pgc-btn pgc-btn-primary">
                    Get Quote
                </a>
            </div>
        </div>
    </div>
</header>
