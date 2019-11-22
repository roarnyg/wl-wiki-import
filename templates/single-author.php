<?php
/**
 * Standard template for displaying Authors
 *
 * This template can be overridden by creating a "single-author.php" template in your theme
 * Based on the WooCommerce templates.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'author' ); ?>

	<?php
		do_action( 'litteraturnett_author_before_page_wrapper' );
		do_action( 'litteraturnett_author_page_wrapper' );
		do_action( 'litteraturnett_author_before_main_content' );
	?>
		 <?php while ( have_posts() ) : the_post(); ?>
		 	<?php Litteraturnett::get_template_part( 'content', 'single-author' ); ?>
		 <?php endwhile; // end of the loop. ?>

	<?php
		do_action( 'litteraturnett_author_after_main_content' );
	?>

	<?php
		do_action( 'litteraturnett_before_author_sidebar' );
		do_action( 'litteraturnett_author_sidebar' );
		do_action( 'litteraturnett_after_author_sidebar' );

		do_action( 'litteraturnett_author_page_wrapper_end' );
		do_action( 'litteraturnett_author_after_page_wrapper' );
	?>

<?php get_footer( 'author' );
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
