<?php
/**
* Template Name: Shop Page
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
get_header();
?>
<div class="content-banner">
    <div class="container">
       <div class="breadcrumb">
           <a href="<?php bloginfo('url'); ?>">Home</a> <i class="fa fa-chevron-right"></i> <?php the_title(); ?>
       </div>
    </div>
</div>
<div class="custom-shop-page">
    <div class="container-fluid">
        <div class="content-block">
            <div class="row">
                <div class="col-sm-3">
                    <div class="custom-shop-page-sidecat">
                        <h3>Product Filter</h3>
                        <?php echo do_shortcode('[woof]'); ?>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="custom-shop-right">
                        <?php
                    while ( have_posts() ) :
                    the_post();
                    the_content();
                    endwhile; // End of the loop.
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();