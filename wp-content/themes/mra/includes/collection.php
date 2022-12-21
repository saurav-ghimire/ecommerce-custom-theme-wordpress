<section class="collection">
<ul>
	
		<?php
					$args = [
						'post_type' => 'collection-section',
						'posts_per_page' => 8,
						'order' => 'ASC',
					];
					$loop = new WP_Query($args);
					while ($loop->have_posts()) {
					$loop->the_post(); ?>

					<li>
						<?php the_post_thumbnail(); ?>
						<a href="<?php bloginfo('url'); ?>/product-category/<?php echo get_field('button_link'); ?>"><?php echo get_field('button_name'); ?></a>
					</li>
<?php } ?>
	
</ul>
</section>