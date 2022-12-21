<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package mra
 */

?>
        <div class="newsletter-wrapper">
            <div class="container">
                <div class="news-letter-content">
                    <div class="footer-social">
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
                    <div class="footer-newsletter">
                        <h5>News Letter</h5>
                            <?php dynamic_sidebar( 'sidebar-1' ); ?>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3">
                        <h4>Save Money</h4>
                        <ul>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/discounts-codes">Discounts Codes</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/on-sales">On Sales</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/special-offers">Special Offers</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-3">
                        <h4>Customer Service</h4>
                        <ul>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/contact-us">Contact us</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/">Help and Advice</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/">Delivery</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/terms-and-conditions">Terms and conditions</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/faqs">Faqs</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-3">
                        <h4>Who are we</h4>
                        <ul>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/about-us">About us</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/what-our-client-says">What our Client Says</a>
                            </li>
                            <li>
                                <a href="<?php bloginfo('url'); ?>/reason-to-shop">Reason to shop</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-3">
                        <h4>Contact us</h4>
                        <ul>
                            <li>
                                <span><i class="fa fa-location-arrow"></i></span>
                                <p><?php esc_html_e( get_theme_mod( 'address' ) ); ?></p>
                            </li>
                            <li>
                                <span><i class="fa fa-phone"></i></span>
                                <p><?php esc_html_e( get_theme_mod( 'phone' ) ); ?></p>
                            </li>
                            <li>
                                <span><i class="fa fa-envelope"></i></span>
                                <p><?php esc_html_e( get_theme_mod( 'email' ) ); ?></p>
                            </li>
                            <li>
                                <span><i class="fa fa-clock-o"></i></span>
                                <p><strong>Opening Hours: </strong><?php esc_html_e( get_theme_mod( 'businesshours' ) ); ?></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Copyright Section -->
        <section class="copyright">
            <div class="container wow zoomIn">
                <div class="copy-right-wrapper">
                    <p>© <?php echo date('Y'); ?> - <?php bloginfo('name'); ?>. All Rights Reserved.</p>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment.png" alt="Payment Thumbnail" class="transition">
                </div>
            </div>
        </section>

        <!-- Go to Top Start --> 
        <div id="back-top">
            <a href="#top">
                <span>
                    <i class="fa fa-angle-double-up" aria-hidden="true"></i>
                </span>
            </a>
        </div>

        <script>
            $(document).ready(function(){
                $("#back-top").hide();
                $(function () {
                    $(window).scroll(function () {
                        if ($(this).scrollTop() > 100) {
                            $('#back-top').fadeIn();
                        } else {
                            $('#back-top').fadeOut();
                        }
                    });
                    $('#back-top a').click(function () {
                        $('body,html').animate({
                            scrollTop: 0
                        }, 800);
                        return false;
                    });
                });
            });
        </script>
        <!--if user is logged in-->
       
    </div>

    <?php wp_footer(); ?>

</body>
</html>