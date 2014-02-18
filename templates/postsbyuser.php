<div class="userpro-post-wrap">

	<?php if ($post_query->have_posts() ) { ?>
	
	<div class="userpro-posts">
	
	<?php while ($post_query->have_posts()) { $post_query->the_post(); ?>
	
		<?php if ($postsbyuser_mode == 'compact' ) { ?>
		
		<div class="userpro-post userpro-post-compact">

			<?php if ($postsbyuser_showthumb == 1) {?>
			<div class="userpro-post-img">
				<a href="<?php the_permalink(); ?>"><?php echo $userpro->post_thumb( $post->ID, $postsbyuser_thumb ); ?><span class="shadowed"></span><span class="iconed"></span></a>
			</div>
			<?php } ?>
			
			<div class="userpro-post-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
			
			<div class="userpro-post-stat">
				<a href="<?php the_permalink(); ?>#comments"><i class="userpro-icon-comment"></i> <?php echo get_comments_number(); ?></a>
			</div>
			
			<div class="userpro-clear"></div>
		
		</div><div class="userpro-clear"></div>
		
		<?php } else { ?>
		
		<div class="userpro-post">

			<div class="userpro-post-img">
				<a href="<?php the_permalink(); ?>"><?php echo $userpro->post_thumb( $post->ID ); ?><span class="shadowed"></span><span class="iconed"></span></a>
			</div>
			
			<div class="userpro-post-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
			
			<div class="userpro-post-stat">
				<a href="<?php the_permalink(); ?>#comments"><i class="userpro-icon-comment"></i> <?php echo get_comments_number(); ?></a>
			</div>
		
		</div>
		
		<?php } ?>
	
	<?php } ?>
	
	</div>
		
	<?php } else { // no results ?>
		
	<?php } ?>
	
</div><div class="userpro-clear"></div>