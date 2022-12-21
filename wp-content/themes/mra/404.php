<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package mra
 */

get_header();
?>
<div class="content-banner">
    <div class="container">
       <div class="breadcrumb">
           <a href="<?php bloginfo('url'); ?>">Home</a> <i class="fa fa-chevron-right"></i> 404 | Page Not Found
       </div>
    </div>
</div>
<div class="content">
    <div class="container">
        <div class="content-block page_not_found">
            <h2>404</h2>
            <p>Page Not Found</p>
            <a href="<?php bloginfo('url'); ?>">Back To Home</a>
        </div>
    </div>
</div>
<?php
get_footer();