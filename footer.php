<footer class="pgc-footer">
    <div class="pgc-container">
        <div class="pgc-footer-grid">
            <div>
                <div class="pgc-footer-logo"><img src="<?php echo esc_url(PGC_URL . '/assets/images/logo-inverted.png'); ?>" alt="ProGreenClean" style="height: 45px; width: auto; object-fit: contain;"></div>
                <p class="pgc-footer-desc">Professional eco-friendly cleaning services in Epsom and across Surrey. 10+ years experience, 500+ happy customers, fully insured.</p>
            </div>
            
            <div>
                <h4 class="pgc-footer-title">Services</h4>
                <nav class="pgc-footer-links">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-services',
                        'container' => false,
                        'items_wrap' => '%3$s',
                        'fallback_cb' => function() {
                            // Default links if no menu assigned
                            echo '<a href="' . home_url('/services/window-cleaning/') . '">Window Cleaning</a>';
                            echo '<a href="' . home_url('/services/gutter-cleaning/') . '">Gutter Cleaning</a>';
                            echo '<a href="' . home_url('/services/oven-cleaning/') . '">Oven Cleaning</a>';
                            echo '<a href="' . home_url('/services/carpet-cleaning/') . '">Carpet Cleaning</a>';
                        }
                    ]);
                    ?>
                </nav>
            </div>
            
            <div>
                <h4 class="pgc-footer-title">Locations</h4>
                <nav class="pgc-footer-links">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer-locations',
                        'container' => false,
                        'items_wrap' => '%3$s',
                        'fallback_cb' => function() {
                            // Default links if no menu assigned
                            echo '<a href="' . home_url('/locations/epsom/') . '">Epsom</a>';
                            echo '<a href="' . home_url('/locations/sutton/') . '">Sutton</a>';
                            echo '<a href="' . home_url('/locations/kingston/') . '">Kingston</a>';
                            echo '<a href="' . home_url('/locations/') . '">View All</a>';
                        }
                    ]);
                    ?>
                </nav>
            </div>
            
            <div>
                <h4 class="pgc-footer-title">Contact</h4>
                <nav class="pgc-footer-links">
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', get_option('pgc_phone', '0800 123 4567'))); ?>">
                        <?php echo esc_html(get_option('pgc_phone', '0800 123 4567')); ?>
                    </a>
                    <a href="mailto:<?php echo esc_attr(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?>">
                        <?php echo esc_html(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?>
                    </a>
                    <span><?php echo nl2br(esc_html(get_option('pgc_opening_hours', 'Mon-Fri: 8am - 6pm'))); ?></span>
                </nav>
            </div>
        </div>
        
        <div class="pgc-footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ProGreenClean. All rights reserved.</p>
            <nav style="display: flex; gap: 24px;">
                <a href="<?php echo home_url('/privacy-policy/'); ?>">Privacy Policy</a>
                <a href="<?php echo home_url('/terms/'); ?>">Terms</a>
            </nav>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
