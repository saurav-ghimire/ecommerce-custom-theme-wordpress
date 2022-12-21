<?php
/**
 * Template Name: Home Page
 *
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package mra
 */

get_header(); ?>

    <!-- Banner Section -->
    <?php include_once('includes/banner.php'); ?>
    
    <!-- freatured secttion -->
    <?php include_once('includes/freatured-cat.php'); ?>

    <!-- flashsale Section -->
    <?php include_once('includes/flashsale.php'); ?>

    <!-- collection Section -->
    <?php include_once('includes/collection.php'); ?>

    <!-- New arrival Section -->
    <?php include_once('includes/new-arrival.php'); ?>

    <!-- Testimonials Section -->
    <?php include_once('includes/feature.php'); ?>
    
<?php get_footer();
