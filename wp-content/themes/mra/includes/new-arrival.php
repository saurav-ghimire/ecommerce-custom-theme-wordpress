
<section class="new-arrival">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="title-section wow fadeInUp">
					<h2>New Arrivals</h2>
					<p>We just stocked up on these hot items</p>
				</div>
			</div>
			<div class="col-md-12">
				<div class="customproducts ">
    			<div class="row">
    				<?php
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 6,
            'order' => 'ASC',
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
			                 
            			</div>
            		</div>
            <?php endwhile;
        } else {
            echo __( 'No products found' );
        }
        wp_reset_postdata();
    ?>
    			</div>
</div>
			</div>
			<div class="col-md-12">
				<div class="more">
					<a href="<?php bloginfo('url'); ?>/shop">View All</a>
				</div>
			</div>
		</div>
	</div>
</section>