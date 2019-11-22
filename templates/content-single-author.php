<?php
/**
 * The template for displaying author content in the single-author.php template
 * This template can be overridden by copying it to your theme as content-single-author.php.
 * Based on the WooCommmerce approach
 */

defined( 'ABSPATH' ) || exit;

global $post,$author;
$author = $post;

do_action( 'litteraturnett_before_single_author' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
	<?php
	do_action( 'litteraturnett_before_single_author_summary' );
	?>
        <?php
	do_action( 'litteraturnett_single_author_summary' );
	?>
	<?php
	do_action( 'litteraturnett_after_single_author_summary' );
	?>
	<?php
	do_action( 'litteraturnett_single_author_content' );
	?>

<?php do_action( 'litteraturnett_after_single_author' ); ?>
