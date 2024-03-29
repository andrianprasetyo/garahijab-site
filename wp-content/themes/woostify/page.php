<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package woostify
 */

get_header();

?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<?php
			while ( have_posts() ) :
				the_post();

				do_action( 'woostify_page_before' );

				get_template_part( 'template-parts/content', 'page' );

				/**
				 * Functions hooked in to woostify_page_after action
				 *
				 * @hooked woostify_display_comments - 10
				 */
				do_action( 'woostify_page_after' );

			endwhile;
			?>
		</main>
	</div>

<?php
do_action( 'woostify_sidebar' );

get_footer();
