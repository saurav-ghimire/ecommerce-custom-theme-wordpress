<?php
/**
* Template Name: Contact Us Page
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
<div class="content contact-page">
    <div class="container">
        <div class="content-block">
            <div class="row">
                 <div class="col-sm-6">
                    <h2>Contact Us</h2>
                    <div class="sectiontitleunderline"></div>
                    <p>Please note all fields are required.</p>
                    <div class="form_section">
                        <?php echo do_shortcode('[contact-form-7 id="5" title="Contact Form"]'); ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <?php
                    while ( have_posts() ) :
                    the_post();
                    the_content();
                    endwhile; // End of the loop.
                    ?>

                    <div class="googlemap">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3348.4159341739287!2d-97.14224098462654!3d32.94002638296254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x864dd45e66d53855%3A0x2bd940c0b88a8259!2s771%20E%20Southlake%20Blvd%2C%20Southlake%2C%20TX%2076092%2C%20USA!5e0!3m2!1sen!2snp!4v1662150399740!5m2!1sen!2snp" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();