<?php
/**
 * The main template file.
 *
 * Used to display the homepage when home.php doesn't exist.
 */
?>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<?php get_header(); ?>
<div id="page">
	<div class="article">
		<div id="content_box">
            <?php if ( !is_paged() ) { ?>

                <?php if ( is_home() && $mts_options['mts_featured_slider'] == '1' ) { ?>

                    <div class="primary-slider-container clearfix loading">
                        <div id="slider" class="primary-slider">
                        <?php if ( empty( $mts_options['mts_custom_slider'] ) ) { ?>
                            <?php
                            // prevent implode error
                            if ( empty( $mts_options['mts_featured_slider_cat'] ) || !is_array( $mts_options['mts_featured_slider_cat'] ) ) {
                                $mts_options['mts_featured_slider_cat'] = array('0');
                            }

                            $slider_cat = implode( ",", $mts_options['mts_featured_slider_cat'] );
                            $slider_query = new WP_Query('cat='.$slider_cat.'&posts_per_page='.$mts_options['mts_featured_slider_num']);
                            while ( $slider_query->have_posts() ) : $slider_query->the_post();
                            ?>
                            <div> 
                                <a href="<?php echo esc_url( get_the_permalink() ); ?>">
                                    <?php the_post_thumbnail('schema-slider',array('title' => '')); ?>
                                    <div class="slide-caption">
                                        <h2 class="slide-title"><?php the_title(); ?></h2>
                                    </div>
                                </a> 
                            </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php } else { ?>
                            <?php foreach( $mts_options['mts_custom_slider'] as $slide ) : ?>
                                <div>
                                    <a href="<?php echo esc_url( $slide['mts_custom_slider_link'] ); ?>">
                                        <?php echo wp_get_attachment_image( $slide['mts_custom_slider_image'], 'schema-slider', false, array('title' => '') ); ?>
                                        <div class="slide-caption">
                                            <h2 class="slide-title"><?php echo esc_html( $slide['mts_custom_slider_title'] ); ?></h2>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php } ?>
                        </div><!-- .primary-slider -->
                    </div><!-- .primary-slider-container -->

                <?php } ?>

                <?php
                $featured_categories = array();
                if ( !empty( $mts_options['mts_featured_categories'] ) ) {
                    foreach ( $mts_options['mts_featured_categories'] as $section ) {
                        $category_id = $section['mts_featured_category'];
                        $featured_categories[] = $category_id;
                        $posts_num = $section['mts_featured_category_postsnum'];
                        if ( 'latest' == $category_id ) { ?>
                            <?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                                <article class="latestPost excerpt <?php echo (++$j % 3 == 0) ? 'last' : ''; ?>">
                				    <?php mts_archive_post(); ?>
                                </article>
                			<?php endwhile; endif; ?>
                			
                            <?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
                                <?php mts_pagination(); ?>
                            <?php } ?>
                            
                        <?php } else { // if $category_id != 'latest': ?>
                            <h3 class="featured-category-title"><a href="<?php echo esc_url( get_category_link( $category_id ) ); ?>" title="<?php echo esc_attr( get_cat_name( $category_id ) ); ?>"><?php echo esc_html( get_cat_name( $category_id ) ); ?></a></h3>
                            <?php
                            $j = 0;
                            $cat_query = new WP_Query('cat='.$category_id.'&posts_per_page='.$posts_num);
                            if ( $cat_query->have_posts() ) : while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
                                <article class="latestPost excerpt <?php echo (++$j % 3 == 0) ? 'last' : ''; ?>">
                				    <?php mts_archive_post(); ?>
                                </article>
                			<?php
                            endwhile; endif; wp_reset_postdata();
                        }
                    }
                }
                ?>

            <?php } else { //Paged ?>

                <?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article class="latestPost excerpt <?php echo (++$j % 3 == 0) ? 'last' : ''; ?>">
                    <?php mts_archive_post(); ?>
                </article>
                <?php endwhile; endif; ?>

                <?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
                    <?php mts_pagination(); ?>
                <?php } ?>

            <?php } ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>