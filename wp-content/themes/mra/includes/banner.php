<section class="banner">
	<div id="banner-sliders" class="owl-carousel owl-theme">
		<?php
			$args = [
				'post_type' => 'banner-slider',
				'posts_per_page' => -1,
				'order' => 'ASC',
			];
			$loop = new WP_Query($args);
			while ($loop->have_posts()) {
			$loop->the_post(); ?>
			<div class="item">
				<div class="banner-image">
					<?php the_post_thumbnail(); ?>
					<div class="banner-content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

	<ul class="banner-featured">
		<?php
			$args = [
				'post_type' => 'banner-featured',
				'posts_per_page' => 6,
				'order' => 'ASC',
			];
			$loop = new WP_Query($args);
			while ($loop->have_posts()) {
			$loop->the_post(); ?>
			<li>
				<a href="<?php bloginfo('url'); ?>/product-category/<?php echo get_field('link_for_category'); ?>">
					<div class="banner-featured-wrapper">
						<?php the_post_thumbnail(); ?>
						<div class="banner-featured-content">
							<h4><?php the_title(); ?></h4>
							<?php the_content(); ?>
						</div>
					</div>
				</a>
			</li>
		<?php } ?>
	</ul>
</section>