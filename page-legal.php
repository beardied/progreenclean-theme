<?php
/**
 * Template: Legal Pages (Privacy Policy, Terms, etc.)
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 16px 0;"><?php the_title(); ?></h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem;">Important information about using our services</p>
    </div>
</section>

<!-- Content Section -->
<section class="pgc-section" style="padding: 60px 0;">
    <div class="pgc-container">
        <div class="pgc-card pgc-card--glass" style="max-width: 900px; margin: 0 auto; padding: 50px;">
            <div class="legal-content" style="color: var(--pgc-gray-700); line-height: 1.8;">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; endif; ?>
            </div>
        </div>
        
        <!-- Back to Home -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo home_url('/'); ?>" class="pgc-btn pgc-btn-outline">← Back to Home</a>
        </div>
    </div>
</section>

<style>
.legal-content h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--pgc-gray-900);
    margin: 40px 0 20px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(8, 145, 178, 0.1);
}
.legal-content h2:first-child {
    margin-top: 0;
}
.legal-content p {
    margin: 0 0 20px 0;
}
.legal-content ul {
    margin: 0 0 20px 0;
    padding-left: 25px;
}
.legal-content li {
    margin-bottom: 10px;
}
.legal-content strong {
    color: var(--pgc-gray-900);
}
@media (max-width: 768px) {
    .pgc-card {
        padding: 30px 20px !important;
    }
}
</style>

<?php get_footer(); ?>
