<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php while ( have_posts() ): the_post() ?>
	<h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
	<?php the_content() ?>
<?php endwhile; ?>

<?php wp_footer(); ?>
</body>
</html>
