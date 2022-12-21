<section class="feature">
	<div class="container">
		<div class="feature-content">
			<ul class="row">
				<?php
                $args = [
                    'post_type' => 'whychooseus-section',
                    'posts_per_page' => 4,
                    'order' => 'ASC',
                ];
                $loop = new WP_Query($args);
                while ($loop->have_posts()) {
                $loop->the_post(); ?>
					<li class="col-md-3 col-sm-6">
						<div class="feature-content-item wow fadeInUp" >
							<div class="feature-content-details">
								<span class="feature-icon">
									<?php the_post_thumbnail(); ?>
								</span>
								<h4><?php the_title(); ?></h4>
								<?php the_content(); ?>
							</div>
						</div>
					</li>

				<?php } ?>

							</ul>
		</div>
	</div>
</section>
