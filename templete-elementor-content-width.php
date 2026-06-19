<?php
/**
 * Template Name: Elementor Content Width
 * Template Post Type: page
 */

get_header();
?>

<div id="primary" class="content-area elementor section"  >
    <main id="main" class="site-main">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </main>
</div>

<?php
get_sidebar();
get_footer(); 