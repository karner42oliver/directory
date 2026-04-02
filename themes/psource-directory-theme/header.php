<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="generator" content="WordPress <?php bloginfo('version'); ?>">
    <title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <link rel="icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" type="image/x-icon">
    <?php do_action('dir_theme_colors'); ?>
    <?php do_action('dir_theme_options'); ?>
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" media="all">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen">
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/custom.css" type="text/css" media="all">
    <?php if ( is_singular() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php locate_template( array( '/components/navigation.php' ), true ); ?>
    <div id="site-wrapper">
        <div id="header">
            <?php locate_template( array( '/components/banner-header.php' ), true ); ?>
            <?php locate_template( array( '/components/branding-header.php' ), true ); ?>
        </div><!-- #header -->
        <?php locate_template( array( '/components/searchcontainer.php' ), true ); ?>
        <?php locate_template( array( '/components/actionbuttons.php' ), true ); ?>
        <div id="container"><!-- start #container -->
