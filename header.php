<?php
/**
 * The template for displaying the header.
 *
 * Displays everything from the doctype declaration down to the navigation.
 */
?>
<!DOCTYPE html>
<?php
$mts_options = get_option(MTS_THEME_NAME); 
$header_class = $mts_options['mts_header_style'];
$logo_id = mts_get_image_id_from_url( $mts_options['mts_logo'] );
$logo_w_h = '';
if ( $logo_id ) {
	$logo     = wp_get_attachment_image_src( $logo_id, 'full' );
	$logo_w_h = ' width="'.$logo[1].'" height="'.$logo[2].'"';
}
?>
<html class="no-js" <?php language_attributes(); ?>>
<head itemscope itemtype="http://schema.org/WebSite">
	<meta charset="<?php bloginfo('charset'); ?>">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<!--[if IE ]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<?php mts_meta(); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
</head>
<body id="blog" <?php body_class('main'); ?> itemscope itemtype="http://schema.org/WebPage">       
	<div class="main-container">
		<header id="site-header" class="main-header <?php echo $header_class; ?>" role="banner" itemscope itemtype="http://schema.org/WPHeader">
			<?php if ( $mts_options['mts_show_primary_nav'] == '1' ) { ?>
				<div id="primary-nav">
					<div class="container">
				        <div id="primary-navigation" class="primary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					        <nav class="navigation clearfix">
						     <?php if ( has_nav_menu( 'primary-menu' ) ) { ?>
							<?php wp_nav_menu(  array( 'theme_location' => 'primary-menu', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
						    <?php } else { ?>
							<ul class="menu clearfix">
								<?php wp_list_pages('title_li='); ?>
							</ul>
						    <?php } ?>
				            <?php if ( $mts_options['mts_header_social_icons'] == '1' && !empty($mts_options['mts_header_social']) && is_array($mts_options['mts_header_social'])) { ?>
								<div class="header-social-icons">
							        <?php foreach( $mts_options['mts_header_social'] as $header_icons ) : ?>
							            <?php if( ! empty( $header_icons['mts_header_icon'] ) && isset( $header_icons['mts_header_icon'] ) ) : ?>
							                <a href="<?php print $header_icons['mts_header_icon_link'] ?>" class="header-<?php print $header_icons['mts_header_icon'] ?>" target="_blank"><span class="fa fa-<?php print $header_icons['mts_header_icon'] ?>"></span></a>
							            <?php endif; ?>
							        <?php endforeach; ?>
							    </div>
							<?php } ?>
							<?php mts_cart(); ?>
					        </nav>
				    	</div>
				    </div>
			    </div>
			<?php } ?>
			<?php if ($mts_options['mts_header_style'] == 'regular_header') { ?>
			    <div id="regular-header">
			    	<div class="container">
						<div class="logo-wrap">
							<?php if ($mts_options['mts_logo'] != '') { ?>
								<?php if( is_front_page() || is_home() || is_404() ) { ?>
										<h1 id="logo" class="image-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><img src="<?php echo $mts_options['mts_logo']; ?>" alt="<?php bloginfo( 'name' ); ?>"<?php echo $logo_w_h; ?> /></a>
										</h1><!-- END #logo -->
								<?php } else { ?>
										<h2 id="logo" class="image-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><img src="<?php echo $mts_options['mts_logo']; ?>" alt="<?php bloginfo( 'name' ); ?>"<?php echo $logo_w_h; ?> /></a>
										</h2><!-- END #logo -->
								<?php } ?>
							<?php } else { ?>
								<?php if( is_front_page() || is_home() || is_404() ) { ?>
										<h1 id="logo" class="text-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
										</h1><!-- END #logo -->
								<?php } else { ?>
									  <h2 id="logo" class="text-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
										</h2><!-- END #logo -->
								<?php } ?>
							<?php } ?>
						</div>
						<?php if($mts_options['mts_header_adcode'] != '') { ?>
							<div class="widget-header"><?php echo $mts_options['mts_header_adcode']; ?></div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if($mts_options['mts_sticky_nav'] == '1') { ?>
			<div class="clear" id="catcher"></div>
			<div id="header" class="sticky-navigation">
			<?php } else { ?>
			<div id="header">
			<?php } ?>
			    <div class="container">
				    <?php if ($mts_options['mts_header_style'] == 'logo_in_nav_header') { ?>
						<div class="logo-wrap">
							<?php if ($mts_options['mts_logo'] != '') { ?>
								<?php
								$logo_id = mts_get_image_id_from_url( $mts_options['mts_logo'] );
            					$logo    = wp_get_attachment_image_src( $logo_id, 'full' );
            					?>
								<?php if( is_front_page() || is_home() || is_404() ) { ?>
										<h1 id="logo" class="image-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><img src="<?php echo $mts_options['mts_logo']; ?>" alt="<?php bloginfo( 'name' ); ?>"<?php echo $logo_w_h; ?> /></a>
										</h1><!-- END #logo -->
								<?php } else { ?>
									  <h2 id="logo" class="image-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><img src="<?php echo $mts_options['mts_logo']; ?>" alt="<?php bloginfo( 'name' ); ?>"<?php echo $logo_w_h; ?> /></a>
										</h2><!-- END #logo -->
								<?php } ?>
							<?php } else { ?>
								<?php if( is_front_page() || is_home() || is_404() ) { ?>
										<h1 id="logo" class="text-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
										</h1><!-- END #logo -->
								<?php } else { ?>
									  <h2 id="logo" class="text-logo" itemprop="headline">
											<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
										</h2><!-- END #logo -->
								<?php } ?>
							<?php } ?>
						</div>
					<?php } ?>
					<div id="secondary-navigation" class="secondary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
						<a href="#" id="pull" class="toggle-mobile-menu"><?php _e('Menu', 'schema' ); ?></a>
						<?php if ( has_nav_menu( 'mobile' ) ) { ?>
							<nav class="navigation clearfix">
								<?php if ( has_nav_menu( 'secondary-menu' ) ) { ?>
									<?php wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
								<?php } else { ?>
									<ul class="menu clearfix">
										<?php wp_list_categories('title_li='); ?>
									</ul>
								<?php } ?>
							</nav>
							<nav class="navigation mobile-only clearfix mobile-menu-wrapper">
								<?php wp_nav_menu( array( 'theme_location' => 'mobile', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
							</nav>
						<?php } else { ?>
							<nav class="navigation clearfix mobile-menu-wrapper">
								<?php if ( has_nav_menu( 'secondary-menu' ) ) { ?>
									<?php wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
								<?php } else { ?>
									<ul class="menu clearfix">
										<?php wp_list_categories('title_li='); ?>
									</ul>
								<?php } ?>
							</nav>
						<?php } ?>
					</div>         
				</div><!--.container-->
			</div>
		</header>
		<?php if ($mts_options['mts_header_style'] == 'logo_in_nav_header' && $mts_options['mts_header_adcode'] != '') { ?>
			<div class="container small-header">
				<div class="widget-header"><?php echo $mts_options['mts_header_adcode']; ?></div>
			</div>
		<?php } ?>