<?php
/**

 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
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