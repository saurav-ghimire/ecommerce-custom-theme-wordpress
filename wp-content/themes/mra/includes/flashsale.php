<div class="flashsale-wrapper">
	<div class="container">
		<div class="flash-title-wrapper wow fadeInUp">
			<div class="title-section">
				<h2>Flash Sale</h2>
				<p>SPECIAL OFFER. DON'T MISS OUT !!!</p>
			</div>
			<div class="more">
				<a href="<?php bloginfo('url'); ?>/shop">View all</a>
			</div>
		</div>
		<!-- WooCommerce On-Sale Products -->
<div class="customproducts">
    <?php
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 6,
            'meta_query'     => array(
                    'relation' => 'OR',
                    array( // Simple products type
                        'key'           => '_sale_price',
                        'value'         => 0,
                        'compare'       => '>',
                        'type'          => 'numeric'
                    ),
                    array( // Variable products type
                        'key'           => '_min_variation_sale_price',
                        'value'         => 0,
                        'compare'       => '>',
                        'type'          => 'numeric'
                    )
                )
        );
        $loop = new WP_Query( $args );
        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) : $loop->the_post(); ?>
            		<div class="col-md-4 col-sm-6 wow fadeInUp">
            			<div class="product-single-wrapper">
            				<div class="product-image-custom">
		            			<?php the_post_thumbnail(); ?>
			            		</div>
			                 <div class="product-titles">
			                 	<h3><?php the_title(); ?></h3>
			                 	<h4><?php echo $product->get_price_html();  ?></h4>
			                 </div>
			                 <div class="for-hiding-custom">
			                 	     <?php woocommerce_get_template_part( 'content', 'product' ); ?>
			                 	     <a href="<?php the_permalink(); ?> " class="custom-buy-btn">Buy Now</a>
			                 </div>
			                 <div class="customproducts-wish">
			                 	<?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
			                 </div>
			                 <div class="custom-sale">
			                 	sale!!
			                 </div>
            			</div>
            		</div>
            <?php endwhile;
        } else {
            echo __( 'No products found' );
        }
        wp_reset_postdata();
    ?>
</div>
<!-- WooCommerce On-Sale Products -->
	</div>
</div>