<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package mra
 */

get_header();
?>
<div class="content-banner">
    <div class="container">
       <div class="breadcrumb">
           <a href="<?php bloginfo('url'); ?>">Home</a> <i class="fa fa-chevron-right"></i> <?php the_title(); ?>
       </div>
    </div>
</div>
<div class="content">
    <div class="container">
        <div class="content-block">
            <h2><?php the_title(); ?></h2>
            <div class="sectiontitleunderline"></div>
            <?php
                while ( have_posts() ) :
                the_post(); ?>
                <?php the_content(); ?>
                <?php endwhile;
            ?>
        </div>
    </div>
</div>
<?php
get_footer();
