<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package mra
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="author" content="Jasper IT Inc.">

    <title><?php bloginfo( 'name' ); ?></title>
    <meta name="description" content="<?php bloginfo( 'name' ); ?>">
    <meta name="keywords" content="<?php bloginfo( 'name' ); ?>">

    <?php wp_head(); ?>
</head>
<body>
    <div class="wrapper">
        <header class="header">
            <div class="header-top">
                <div class="container">
                    <div class="row">
                        <div class="top-header-wrapper">
                            <div class="logo">
                                 <?php
                                    if(function_exists('the_custom_logo')) {
                                        the_custom_logo();
                                    }
                                ?>
                            </div>
                            <div class="header-top-content">
                                <div class="header-top-call">
                                    <p>
                                        <i class="fa fa-mobile-phone"></i>
                                    CALL OUR CUSTOMER SERVICES : <span>0123-456-789</span>
                                    </p>
                                </div>
                                <div class="header-top-search">
                                    <?php echo do_shortcode('[fibosearch]'); ?>
                                </div>
                                <div class="header-top-account">
                                    <a href="<?php bloginfo('url'); ?>/my-account">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon_account.png" alt="Welcome Thumbnail" class="transition">
                                    </a>
                                    <a href="#" class="icon_cart">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon_cart.png" alt="Welcome Thumbnail" class="transition">
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="header-bottom hidden-xs">
                <div class="container">
                    <div class="menu-wrapper-section">
                        <div class="header-navigation">
                            <?php wp_nav_menu(array('theme_location' => 'header-menu')); ?>
                        </div>
                        <div class="menu-social-media">
                            
                                <a href="<?php esc_html_e( get_theme_mod( 'facebook' ) ); ?>" target="_blank">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            
                                <a href="<?php esc_html_e( get_theme_mod( 'twitter' ) ); ?>" target="_blank">
                                    <i class="fa fa-twitter"></i>
                                </a>
                            
                                <a href="<?php esc_html_e( get_theme_mod( 'instagram' ) ); ?>" target="_blank">
                                    <i class="fa fa-instagram"></i>
                                </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigatio for Mobile or Small Devices -->
        <div class="mobile_menu hidden-lg hidden-md hidden-sm">
            <div class="menu_icon_box">
                <div class="tab_button">
                    <a class="tab_button1" href="tel:<?php esc_html_e( get_theme_mod( 'phone' ) ); ?>"><i class="fa fa-phone"></i> Click To Call</a>
                    <a class="tab_button2" href="sms:(408) 529-4789"><i class="fa fa-comments"></i> Tap To Text</a>
                </div>
                <?php wp_nav_menu(array('theme_location' => 'header-menu')); ?>
            </div>
            
        </div>

       <div class=social_media_block>
            <div class="share_icon">
                <i class="fa fa-share-alt"></i>
                <div class="social_icon">
                    <ul>
                        <li>
                            <a href="<?php esc_html_e( get_theme_mod( 'facebook' ) ); ?>" target="_blank">
                                <i class="fa fa-facebook"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php esc_html_e( get_theme_mod( 'twitter' ) ); ?>" target="_blank">
                                <i class="fa fa-twitter"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php esc_html_e( get_theme_mod( 'instagram' ) ); ?>" target="_blank">
                                <i class="fa fa-instagram"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div> 
        </div>
        
       <script>
        $('#myModal').modal({
          keyboard: false,
          backdrop:false
        })

            // Modern Social Media Icon Display
            $(".share_icon").mouseover(function(){
                $(".social_icon").addClass("active");
            });
            $(".share_icon").mouseout(function(){
                $(".social_icon").removeClass("active");
            });

            $('#searchBtn').click(function(){
                $('#searchForm').toggleClass("active");
            });

            // Side Bar Menu
            $(".menu_icon").click(function() {
                $(".menu_icon").toggleClass("active");
            });
            $(".menu_icon").click(function() {
                $(".sidebar").toggleClass("active");
            });
            $(".menu_icon").click(function() {
                $(".mobile_menu").toggleClass("active");
            });
            $("#notold").hide();
            $("#ifno").click(function() {
                $("#notold").show();
            });

        </script>