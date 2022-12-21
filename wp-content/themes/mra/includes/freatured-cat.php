<section class="freatured-cat-wrapper">
	<div class="container">
		<div class="title-section wow fadeInUp">
			<h2>Featured Categories</h2>
			<p>You asked for them and we got them! here our customer favorites</p>
		</div>
		<div id="services-slider" class="owl-carousel owl-theme">
			<?php
			$args = [
				'post_type' => 'featured-categories',
				'posts_per_page' => -1,
				'order' => 'ASC',
			];
			$loop = new WP_Query($args);
			while ($loop->have_posts()) {
			$loop->the_post(); ?>

			<div class="item wow zoomIn">
				<div class="freatured-cat transition wow fadeInUp">
					<div class="featured-cat-image">
						<a href="<?php bloginfo('url'); ?>/product-category/<?php echo get_field('category_link'); ?>"><?php the_post_thumbnail(); ?></a>
					</div>
					<a href="<?php bloginfo('url'); ?>/product-category/<?php echo get_field('category_link'); ?>"><h4><?php the_title(); ?></h4></a>
					<div class="freatured-cat-content">
					</div>
					<a href="<?php bloginfo('url'); ?>/product-category/<?php echo get_field('category_link'); ?>"><?php the_content(); ?></a>
				</div>
			</div>

			<?php } ?>
		</div>
	</div>
</section>