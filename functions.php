<?php
/*-----------------------------------------------------------------------------------*/
/*	Do not remove these lines, sky will fall on your head.
/*-----------------------------------------------------------------------------------*/
define( 'MTS_THEME_NAME', 'schema' );
define( 'MTS_THEME_VERSION', '3.1.1' );

require_once( dirname( __FILE__ ) . '/theme-options.php' );
if ( ! isset( $content_width ) ) {
	$content_width = 680;
}

/*-----------------------------------------------------------------------------------*/
/*	Load Options
/*-----------------------------------------------------------------------------------*/
$mts_options = get_option( MTS_THEME_NAME );

/**
 * Register supported theme features, image sizes and nav menus.
 * Also loads translated strings.
 */
function mts_after_setup_theme() {
    if ( ! defined( 'MTS_THEME_WHITE_LABEL' ) ) {
        define( 'MTS_THEME_WHITE_LABEL', false );
    }

    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );

    load_theme_textdomain( 'schema', get_template_directory().'/lang' );

    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 223, 137, true );
    add_image_size( 'schema-featured', 680, 350, true ); //featured
    add_image_size( 'schema-related', 211, 150, true ); //related
    add_image_size( 'schema-widgetthumb', 70, 60, true ); //widget
    add_image_size( 'schema-widgetfull', 300, 200, true ); //sidebar full width
    add_image_size( 'schema-slider', 772, 350, true ); //slider

    register_nav_menus( array(
      'primary-menu'    => __( 'Primary', 'schema' ),
      'secondary-menu'  => __( 'Secondary', 'schema' ),
      'mobile'          => __( 'Mobile', 'schema' )
    ) );

    if ( mts_is_wc_active() ) {
        add_theme_support( 'woocommerce' );
    }
}
add_action('after_setup_theme', 'mts_after_setup_theme' );

/*
 * Disable theme updates from WordPress.org theme repository.
 * Check if MTS Connect plugin already does this.
 */
if ( !class_exists('mts_connection') ) {
    /**
     * If wrong updates are already shown, delete transient so that we can run our workaround
     */
    function mts_hide_themes_plugins() {
        if ( !is_admin() ) return;
        if ( false === get_site_transient( 'mts_wp_org_check_disabled' ) ) { // run only once
            delete_site_transient('update_themes' );
            delete_site_transient('update_plugins' );

            add_action('current_screen', 'mts_remove_themes_plugins_from_update' );
        }
    }
    add_action('init', 'mts_hide_themes_plugins');

    /**
     * Hide mts themes/plugins.
     *
     * @param WP_Screen $screen
     */
    function mts_remove_themes_plugins_from_update( $screen ) {
        $run_on_screens = array( 'themes', 'themes-network', 'plugins', 'plugins-network', 'update-core', 'network-update-core' );
        if ( in_array( $screen->base, $run_on_screens ) ) {
            //Themes
            if ( $themes_transient = get_site_transient( 'update_themes' ) ) {
                if ( property_exists( $themes_transient, 'response' ) && is_array( $themes_transient->response ) ) {
                    foreach ( $themes_transient->response as $key => $value ) {
                        $theme = wp_get_theme( $value['theme'] );
                        $theme_uri = $theme->get( 'ThemeURI' );
                        if ( 0 !== strpos( $theme_uri, 'mythemeshop.com' ) ) {
                            unset( $themes_transient->response[$key] );
                        }
                    }
                    set_site_transient( 'update_themes', $themes_transient );
                }
            }
            //Plugins
            if ( $plugins_transient = get_site_transient( 'update_plugins' ) ) {
                if ( property_exists( $plugins_transient, 'response' ) && is_array( $plugins_transient->response ) ) {
                    foreach ( $plugins_transient->response as $key => $value ) {
                        $plugin = get_plugin_data( WP_PLUGIN_DIR.'/'.$key, false, false );
                        $plugin_uri = $plugin['PluginURI'];
                        if ( 0 !== strpos( $plugin_uri, 'mythemeshop.com' ) ) {
                            unset( $plugins_transient->response[$key] );
                        }
                    }
                    set_site_transient( 'update_plugins', $plugins_transient );
                }
            }
            set_site_transient( 'mts_wp_org_check_disabled', time() );
        }
    }

    /**
     * Delete `mts_wp_org_check_disabled` transient.
     */
    function mts_clear_check_transient(){
        delete_site_transient( 'mts_wp_org_check_disabled');
    }
    add_action( 'load-themes.php', 'mts_clear_check_transient' );
    add_action( 'load-plugins.php', 'mts_clear_check_transient' );
    add_action( 'upgrader_process_complete', 'mts_clear_check_transient' );
}

// Disable auto-updating the theme.
function mts_disable_auto_update_theme( $update, $item ) {
    if ( $item->slug == MTS_THEME_NAME ) {
        return false;
    }
    return $update;
}
add_filter( 'auto_update_theme', 'mts_disable_auto_update_theme', 10, 2 );

/**
 * Disable Google Typography plugin
 */
function mts_deactivate_google_typography_plugin() {
    if ( in_array( 'google-typography/google-typography.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        deactivate_plugins( 'google-typography/google-typography.php' );
    }
}
add_action( 'admin_init', 'mts_deactivate_google_typography_plugin' );

/**
 * Determines whether the WooCommerce plugin is active or not.
 * @return bool
 */
function mts_is_wc_active() {
    if ( is_multisite() ) {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        return is_plugin_active( 'woocommerce/woocommerce.php' );
    } else {
        return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
    }
}

/**
 * MTS icons for use in nav menus and icon select option.
 *
 * @return array
 */
function mts_get_icons() {
    $icons = array(
        __( 'Web Application Icons', 'schema' ) => array(
            'adjust', 'anchor', 'archive', 'area-chart', 'arrows', 'arrows-h', 'arrows-v', 'asterisk', 'at', 'balance-scale', 'ban', 'bar-chart', 'barcode', 'bars', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'briefcase', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'cart-arrow-down', 'cart-plus', 'cc', 'certificate', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'child', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clock-o', 'clone', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'coffee', 'cog', 'cogs', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'copyright', 'creative-commons', 'credit-card', 'crop', 'crosshairs', 'cube', 'cubes', 'cutlery', 'database', 'desktop', 'diamond', 'dot-circle-o', 'download', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-o', 'envelope-square', 'eraser', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'fax', 'female', 'fighter-jet', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-pdf-o', 'file-powerpoint-o', 'file-video-o', 'file-word-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'flag', 'flag-checkered', 'flag-o', 'flask', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gift', 'glass', 'globe', 'graduation-cap', 'hand-lizard-o', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'hdd-o', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hourglass', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'i-cursor', 'inbox', 'industry', 'info', 'info-circle', 'key', 'keyboard-o', 'language', 'laptop', 'leaf', 'lemon-o', 'level-down', 'level-up', 'life-ring', 'lightbulb-o', 'line-chart', 'location-arrow', 'lock', 'magic', 'magnet', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'meh-o', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mobile', 'money', 'moon-o', 'motorcycle', 'mouse-pointer', 'music', 'newspaper-o', 'object-group', 'object-ungroup', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paw', 'pencil', 'pencil-square', 'pencil-square-o', 'phone', 'phone-square', 'picture-o', 'pie-chart', 'plane', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'power-off', 'print', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'quote-left', 'quote-right', 'random', 'recycle', 'refresh', 'registered', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'rss', 'rss-square', 'search', 'search-minus', 'search-plus', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shopping-cart', 'sign-in', 'sign-out', 'signal', 'sitemap', 'sliders', 'smile-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'space-shuttle', 'spinner', 'spoon', 'square', 'square-o', 'star', 'star-half', 'star-half-o', 'star-o', 'sticky-note', 'sticky-note-o', 'street-view', 'suitcase', 'sun-o', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'television', 'terminal', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'tint', 'toggle-off', 'toggle-on', 'trademark', 'trash', 'trash-o', 'tree', 'trophy', 'truck', 'tty', 'umbrella', 'university', 'unlock', 'unlock-alt', 'upload', 'user', 'user-plus', 'user-secret', 'user-times', 'users', 'video-camera', 'volume-down', 'volume-off', 'volume-up', 'wheelchair', 'wifi', 'wrench'
        ),
        __( 'Hand Icons', 'schema' ) => array(
            'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up'
        ),
        __( 'Transportation Icons', 'schema' ) => array(
            'ambulance', 'bicycle', 'bus', 'car', 'fighter-jet', 'motorcycle', 'plane', 'rocket', 'ship', 'space-shuttle', 'subway', 'taxi', 'train', 'truck', 'wheelchair'
        ),
        __( 'Gender Icons', 'schema' ) => array(
            'genderless', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mercury', 'neuter', 'transgender', 'transgender-alt', 'venus', 'venus-double', 'venus-mars'
        ),
        __( 'File Type Icons', 'schema' ) => array(
            'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-o', 'file-pdf-o', 'file-powerpoint-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o'
        ),
        __( 'Spinner Icons', 'schema' ) => array(
            'circle-o-notch', 'cog', 'refresh', 'spinner'
        ),
        __( 'Form Control Icons', 'schema' ) => array(
            'check-square', 'check-square-o', 'circle', 'circle-o', 'dot-circle-o', 'minus-square', 'minus-square-o', 'plus-square', 'plus-square-o', 'square', 'square-o'
        ),
        __( 'Payment Icons', 'schema' ) => array(
            'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'credit-card', 'google-wallet', 'paypal'
        ),
        __( 'Chart Icons', 'schema' ) => array(
            'area-chart', 'bar-chart', 'line-chart', 'pie-chart'
        ),
        __( 'Currency Icons', 'schema' ) => array(
            'btc', 'eur', 'gbp', 'gg', 'gg-circle', 'ils', 'inr', 'jpy', 'krw', 'money', 'rub', 'try', 'usd'
        ),
        __( 'Text Editor Icons', 'schema' ) => array(
            'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'chain-broken', 'clipboard', 'columns', 'eraser', 'file', 'file-o', 'file-text', 'file-text-o', 'files-o', 'floppy-o', 'font', 'header', 'indent', 'italic', 'link', 'list', 'list-alt', 'list-ol', 'list-ul', 'outdent', 'paperclip', 'paragraph', 'repeat', 'scissors', 'strikethrough', 'subscript', 'superscript', 'table', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'underline', 'undo'
        ),
        __( 'Directional Icons', 'schema' ) => array(
            'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'exchange', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up'
        ),
        __( 'Video Player Icons', 'schema' ) => array(
            'arrows-alt', 'backward', 'compress', 'eject', 'expand', 'fast-backward', 'fast-forward', 'forward', 'pause', 'play', 'play-circle', 'play-circle-o', 'random', 'step-backward', 'step-forward', 'stop', 'youtube-play'
        ),
        __( 'Brand Icons', 'schema' ) => array(
            '500px', 'adn', 'amazon', 'android', 'angellist', 'apple', 'behance', 'behance-square', 'bitbucket', 'bitbucket-square', 'black-tie', 'btc', 'buysellads', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'chrome', 'codepen', 'connectdevelop', 'contao', 'css3', 'dashcube', 'delicious', 'deviantart', 'digg', 'dribbble', 'dropbox', 'drupal', 'empire', 'expeditedssl', 'facebook', 'facebook-official', 'facebook-square', 'firefox', 'flickr', 'fonticons', 'forumbee', 'foursquare', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'google', 'google-plus', 'google-plus-square', 'google-wallet', 'gratipay', 'hacker-news', 'houzz', 'html5', 'instagram', 'internet-explorer', 'ioxhost', 'joomla', 'jsfiddle', 'lastfm', 'lastfm-square', 'leanpub', 'linkedin', 'linkedin-square', 'linux', 'maxcdn', 'meanpath', 'medium', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'pagelines', 'paypal', 'pied-piper', 'pied-piper-alt', 'pinterest', 'pinterest-p', 'pinterest-square', 'qq', 'rebel', 'reddit', 'reddit-square', 'renren', 'safari', 'sellsy', 'share-alt', 'share-alt-square', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'skype', 'slack', 'slideshare', 'soundcloud', 'spotify', 'stack-exchange', 'stack-overflow', 'steam', 'steam-square', 'stumbleupon', 'stumbleupon-circle', 'tencent-weibo', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'viacoin', 'vimeo', 'vimeo-square', 'vine', 'vk', 'weibo', 'weixin', 'whatsapp', 'wikipedia-w', 'windows', 'wordpress', 'xing', 'xing-square', 'y-combinator', 'yahoo', 'yelp', 'youtube', 'youtube-play', 'youtube-square'
        ),
        __( 'Medical Icons', 'schema' ) => array(
            'ambulance', 'h-square', 'heart', 'heart-o', 'heartbeat', 'hospital-o', 'medkit', 'plus-square', 'stethoscope', 'user-md', 'wheelchair'
        )
    );

    return $icons;
}


/**
 * Get the current post's thumbnail URL.
 *
 * @param string $size
 *
 * @return string
 */
function mts_get_thumbnail_url( $size = 'full' ) {
    $post_id = get_the_ID() ;
    if (has_post_thumbnail( $post_id ) ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
        return $image[0];
    }

    // use first attached image
    $images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
    if (!empty($images)) {
        $image = reset($images);
        $image_data = wp_get_attachment_image_src( $image->ID, $size );
        return $image_data[0];
    }

    // use no preview fallback
    if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) ) {
	    return get_template_directory_uri().'/images/nothumb-'.$size.'.png';
    }

	return '';
}

/**
 * Create and show column for featured in portfolio items list admin page.
 * @param $post_ID
 *
 * @return string url
 */
function mts_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'schema-widgetfull');
        return $post_thumbnail_img[0];
    }
}

/**
 * Adds a `Featured Image` column header in the item list admin page.
 *
 * @param array $defaults
 *
 * @return array
 */
function mts_columns_head($defaults) {
    if (get_post_type() == 'post') {
	    $defaults['featured_image'] = __('Featured Image', 'schema' );
    }

    return $defaults;
}
add_filter('manage_posts_columns', 'mts_columns_head');

/**
 * Adds a `Featured Image` column row value in the item list admin page.
 *
 * @param string $column_name The name of the column to display.
 * @param int $post_ID The ID of the current post.
 */
function mts_columns_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = mts_get_featured_image($post_ID);
        if ($post_featured_image) {
            echo '<img width="150" height="100" src="' . esc_url( $post_featured_image ) . '" />';
        }
    }
}
add_action('manage_posts_custom_column', 'mts_columns_content', 10, 2);

/**
 * Change the HTML markup of the post thumbnail.
 *
 * @param string $html
 * @param int $post_id
 * @param string $post_image_id
 * @param int $size
 * @param string $attr
 *
 * @return string
 */
function mts_post_image_html( $html, $post_id, $post_image_id, $size, $attr ) {
    if ( has_post_thumbnail( $post_id ) || 'shop_thumbnail' === $size )
        return $html;

    // use first attached image
    $images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
    if (!empty($images)) {
        $image = reset($images);
        return wp_get_attachment_image( $image->ID, $size, false, $attr );
    }

    // use no preview fallback
    if ( file_exists( get_template_directory().'/images/nothumb-'.$size.'.png' ) ) {
        $placeholder = get_template_directory_uri().'/images/nothumb-'.$size.'.png';
        $mts_options = get_option( MTS_THEME_NAME );
        if ( ! empty( $mts_options['mts_lazy_load'] ) && ! empty( $mts_options['mts_lazy_load_thumbs'] ) ) {
            $placeholder_src = '';
            $layzr_attr = ' data-layzr="'.esc_attr( $placeholder ).'"';
        } else {
            $placeholder_src = $placeholder;
            $layzr_attr = '';
        }

        $placeholder_classs = 'attachment-'.$size.' wp-post-image';
        return '<img src="'.esc_url( $placeholder_src ).'" class="'.esc_attr( $placeholder_classs ).'" alt="'.esc_attr( get_the_title() ).'"'.$layzr_attr.'>';
    }

	return '';
}
add_filter( 'post_thumbnail_html', 'mts_post_image_html', 10, 5 );

/**
 * Add data-layzr attribute to featured image ( for lazy load )
 *
 * @param array $attr
 * @param WP_Post $attachment
 * @param string|array $size
 *
 * @return array
 */
function mts_image_lazy_load_attr( $attr, $attachment, $size ) {
    if ( is_admin() || is_feed() ) return $attr;
    $mts_options = get_option( MTS_THEME_NAME );
    if ( ! empty( $mts_options['mts_lazy_load'] ) && ! empty( $mts_options['mts_lazy_load_thumbs'] ) ) {
        $attr['data-layzr'] = $attr['src'];
        $attr['src'] = '';
        if ( isset( $attr['srcset'] ) ) {
            $attr['data-layzr-srcset'] = $attr['srcset'];
            $attr['srcset'] = '';
        }
    }

    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'mts_image_lazy_load_attr', 10, 3 );

/**
 * Add data-layzr attribute to post content images ( for lazy load )
 *
 * @param string $content
 *
 * @return string
 */

function mts_content_image_lazy_load_attr( $content ) {
    $mts_options = get_option( MTS_THEME_NAME );
    if ( ! empty( $mts_options['mts_lazy_load'] )
         && ! empty( $mts_options['mts_lazy_load_content'] )
         && ! empty( $content ) ) {
        $content = preg_replace_callback(
            '/<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>/',
            'mts_content_image_lazy_load_attr_callback',
            $content
        );
    }

    return $content;
}
add_filter('the_content', 'mts_content_image_lazy_load_attr');

/**
 * Callback to move src to data-src and replace it with a 1x1 tranparent image.
 *
 * @param $matches
 *
 * @return string
 */
function mts_content_image_lazy_load_attr_callback( $matches ) {
    $transparent_img = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    if ( preg_match( '/ data-lazy *= *"false" */', $matches[0] ) ) {
        return '<img' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '>';
    } else {
        return '<img' . $matches[1] . 'src="' . $transparent_img . '" data-layzr="' . $matches[2] . '"' . str_replace( 'srcset=', 'data-layzr-srcset=', $matches[3]). '>';
    }
}

/**
 * Enable Widgetized sidebar and Footer
 */
function mts_register_sidebars() {
    $mts_options = get_option( MTS_THEME_NAME );

    // Default sidebar
    register_sidebar( array(
        'name' => __('Sidebar', 'schema'),
        'description'   => __( 'Default sidebar.', 'schema' ),
        'id' => 'sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );

    // Top level footer widget areas
    if ( !empty( $mts_options['mts_top_footer'] )) {
        if ( empty( $mts_options['mts_top_footer_num'] )) $mts_options['mts_top_footer_num'] = 4;
        register_sidebars( $mts_options['mts_top_footer_num'], array(
            'name' => __( 'Footer %d', 'schema' ),
            'description'   => __( 'Appears at the top of the footer.', 'schema' ),
            'id' => 'footer-top',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
    }

    // Custom sidebars
    if ( !empty( $mts_options['mts_custom_sidebars'] ) && is_array( $mts_options['mts_custom_sidebars'] )) {
        foreach( $mts_options['mts_custom_sidebars'] as $sidebar ) {
            if ( !empty( $sidebar['mts_custom_sidebar_id'] ) && !empty( $sidebar['mts_custom_sidebar_id'] ) && $sidebar['mts_custom_sidebar_id'] != 'sidebar-' ) {
                register_sidebar( array( 'name' => ''.$sidebar['mts_custom_sidebar_name'].'', 'id' => ''.sanitize_title( strtolower( $sidebar['mts_custom_sidebar_id'] )).'', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h3>', 'after_title' => '</h3>' ));
            }
        }
    }

    if ( mts_is_wc_active() ) {
        // Register WooCommerce Shop and Single Product Sidebar
        register_sidebar( array(
            'name' => __('Shop Page Sidebar', 'schema' ),
            'description'   => __( 'Appears on Shop main page and product archive pages.', 'schema' ),
            'id' => 'shop-sidebar',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        register_sidebar( array(
            'name' => __('Single Product Sidebar', 'schema' ),
            'description'   => __( 'Appears on single product pages.', 'schema' ),
            'id' => 'product-sidebar',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
    }
}

add_action( 'widgets_init', 'mts_register_sidebars' );


/**
 * Retrieve the ID of the sidebar to use on the active page.
 *
 * @return string
 */
function mts_custom_sidebar() {
    $mts_options = get_option( MTS_THEME_NAME );

	// Default sidebar.
	$sidebar = 'sidebar';

	if ( is_home() && !empty( $mts_options['mts_sidebar_for_home'] )) $sidebar = $mts_options['mts_sidebar_for_home'];
    if ( is_single() && !empty( $mts_options['mts_sidebar_for_post'] )) $sidebar = $mts_options['mts_sidebar_for_post'];
    if ( is_page() && !empty( $mts_options['mts_sidebar_for_page'] )) $sidebar = $mts_options['mts_sidebar_for_page'];

    // Archives.
	if ( is_archive() && !empty( $mts_options['mts_sidebar_for_archive'] )) $sidebar = $mts_options['mts_sidebar_for_archive'];
	if ( is_category() && !empty( $mts_options['mts_sidebar_for_category'] )) $sidebar = $mts_options['mts_sidebar_for_category'];
    if ( is_tag() && !empty( $mts_options['mts_sidebar_for_tag'] )) $sidebar = $mts_options['mts_sidebar_for_tag'];
    if ( is_date() && !empty( $mts_options['mts_sidebar_for_date'] )) $sidebar = $mts_options['mts_sidebar_for_date'];
	if ( is_author() && !empty( $mts_options['mts_sidebar_for_author'] )) $sidebar = $mts_options['mts_sidebar_for_author'];

    // Other.
    if ( is_search() && !empty( $mts_options['mts_sidebar_for_search'] )) $sidebar = $mts_options['mts_sidebar_for_search'];
	if ( is_404() && !empty( $mts_options['mts_sidebar_for_notfound'] )) $sidebar = $mts_options['mts_sidebar_for_notfound'];

    // Woocommerce.
    if ( mts_is_wc_active() ) {
        if ( is_shop() || is_product_category() ) {
	        $sidebar = 'shop-sidebar';
            if ( !empty( $mts_options['mts_sidebar_for_shop'] )) {
	            $sidebar = $mts_options['mts_sidebar_for_shop'];
            }
        }
	    if ( is_product() ) {
		    $sidebar = 'product-sidebar';
		    if ( !empty( $mts_options['mts_sidebar_for_product'] )) {
			    $sidebar = $mts_options['mts_sidebar_for_product'];
		    }
	    }
    }

	// Page/post specific custom sidebar-
	if ( is_page() || is_single() ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
        $custom = get_post_meta( get_the_ID(), '_mts_custom_sidebar', true );
		if ( !empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	return $sidebar;
}

/*-----------------------------------------------------------------------------------*/
/*  Load Widgets, Actions and Libraries
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget.
include_once( "functions/widget-ad125.php" );

// Add the 300x250 Ad Block Custom Widget.
include_once( "functions/widget-ad300.php" );

// Add the Latest Tweets Custom Widget.
include_once( "functions/widget-tweets.php" );

// Add Recent Posts Widget.
include_once( "functions/widget-recentposts.php" );

// Add Related Posts Widget.
include_once( "functions/widget-relatedposts.php" );

// Add Author Posts Widget.
include_once( "functions/widget-authorposts.php" );

// Add Popular Posts Widget.
include_once( "functions/widget-popular.php" );

// Add Facebook Like box Widget.
include_once( "functions/widget-fblikebox.php" );

// Add Social Profile Widget.
include_once( "functions/widget-social.php" );

// Add Category Posts Widget.
include_once( "functions/widget-catposts.php" );

// Add Category Posts Widget.
include_once( "functions/widget-postslider.php" );

// Add Welcome message.
include_once( "functions/welcome-message.php" );

// Template Functions.
include_once( "functions/theme-actions.php" );

// Post/page editor meta boxes.
include_once( "functions/metaboxes.php" );

// TGM Plugin Activation.
include_once( "functions/plugin-activation.php" );

// AJAX Contact Form - `mts_contact_form()`.
include_once( 'functions/contact-form.php' );

// Custom menu walker.
include_once( 'functions/nav-menu.php' );

/*-----------------------------------------------------------------------------------*/
/*
/*-----------------------------------------------------------------------------------*/
if ( ! empty( $mts_options['mts_rtl'] ) ) {
    /**
     * RTL language support
     *
     * @see mts_load_footer_scripts()
     */
    function mts_rtl() {
        global $wp_locale, $wp_styles;
        $wp_locale->text_direction = 'rtl';
    	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
    		$wp_styles = new WP_Styles();
    		$wp_styles->text_direction = 'rtl';
    	}
    }
    add_action( 'init', 'mts_rtl' );
}

/**
 * Replace `no-js` with `js` from the body's class name.
 */
function mts_nojs_js_class() {
    echo '<script type="text/javascript">document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/,\'js\' );</script>';
}
add_action( 'wp_head', 'mts_nojs_js_class', 1 );

/**
 * Enqueue .js files.
 */
function mts_add_scripts() {
	$mts_options = get_option( MTS_THEME_NAME );

	wp_enqueue_script( 'jquery' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_register_script( 'customscript', get_template_directory_uri() . '/js/customscript.js', true );
    if ( ! empty( $mts_options['mts_show_primary_nav'] ) ) {
        $nav_menu = 'both';
    } else {
	    $nav_menu = 'none';

        if ( ! empty( $mts_options['mts_show_primary_nav'] ) ) {
            $nav_menu = 'primary';
        } else {
            $nav_menu = 'secondary';
        }
    }
    wp_localize_script(
    	'customscript',
    	'mts_customscript',
    	array(
            'responsive' => ( empty( $mts_options['mts_responsive'] ) ? false : true ),
            'nav_menu' => $nav_menu
        )
    );
	wp_enqueue_script( 'customscript' );

    // Slider
    wp_register_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array(), null, true);
    wp_localize_script('owl-carousel', 'slideropts', array('rtl_support' => $mts_options['mts_rtl']));
    if(!empty($mts_options['mts_featured_slider'])) {
        wp_enqueue_script ('owl-carousel');
    }

    // Animated single post/page header
    if ( is_singular() ) {
        $header_animation = mts_get_post_header_effect();
        if ( 'parallax' == $header_animation ) {
            wp_enqueue_script( 'jquery-parallax', get_template_directory_uri() . '/js/parallax.js', array( 'jquery' ) );
        } else if ( 'zoomout' == $header_animation ) {
            wp_enqueue_script( 'jquery-zoomout', get_template_directory_uri() . '/js/zoomout.js', array( 'jquery' ) );
        }
    }

	global $is_IE;
    if ( $is_IE ) {
        wp_enqueue_script( 'html5shim', "//html5shim.googlecode.com/svn/trunk/html5.js" );
	}

}
add_action( 'wp_enqueue_scripts', 'mts_add_scripts' );

/**
 * Load assets to be loaded in the footer.
 */
function mts_load_footer_scripts() {
	$mts_options = get_option( MTS_THEME_NAME );

	//Lightbox
	if ( ! empty( $mts_options['mts_lightbox'] ) ) {
        wp_enqueue_script( 'magnificPopup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ) );
	}

	//Sticky Nav
	if ( ! empty( $mts_options['mts_sticky_nav'] ) ) {
        wp_enqueue_script( 'StickyNav', get_template_directory_uri() . '/js/sticky.js' );
	}

    // RTL
    if ( ! empty( $mts_options['mts_rtl'] ) ) {
        wp_enqueue_style( 'mts_rtl', get_template_directory_uri() . '/css/rtl.css', 'schema-style' );
	}

    // Lazy Load
    if ( ! empty( $mts_options['mts_lazy_load'] ) ) {
        if ( ! empty( $mts_options['mts_lazy_load_thumbs'] ) || ( ! empty( $mts_options['mts_lazy_load_content'] ) && is_singular() ) ) {
            wp_enqueue_script( 'layzr', get_template_directory_uri() . '/js/layzr.min.js', '', '', true );
        }
    }

    // Ajax Load More and Search Results
    wp_register_script( 'mts_ajax', get_template_directory_uri() . '/js/ajax.js', true );
	if( ! empty( $mts_options['mts_pagenavigation_type'] ) && $mts_options['mts_pagenavigation_type'] >= 2 && !is_singular() ) {
		wp_enqueue_script( 'mts_ajax' );

        wp_enqueue_script( 'historyjs', get_template_directory_uri() . '/js/history.js' );

        // Add parameters for the JS
        global $wp_query;
        $max = $wp_query->max_num_pages;
        $paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;
        $autoload = ( $mts_options['mts_pagenavigation_type'] == 3 );
        wp_localize_script(
        	'mts_ajax',
        	'mts_ajax_loadposts',
        	array(
        		'startPage' => $paged,
        		'maxPages' => $max,
        		'nextLink' => next_posts( $max, false ),
                'autoLoad' => $autoload,
                'i18n_loadmore' => __( 'Load More Posts', 'schema' ),
                'i18n_loading' => __('Loading...', 'schema' ),
                'i18n_nomore' => __( 'No more posts.', 'schema' )
        	 )
        );
	}
    if ( ! empty( $mts_options['mts_ajax_search'] ) ) {
        wp_enqueue_script( 'mts_ajax' );
        wp_localize_script(
        	'mts_ajax',
        	'mts_ajax_search',
        	array(
				'url' => admin_url( 'admin-ajax.php' ),
        		'ajax_search' => '1'
        	 )
        );
    }

}
add_action( 'wp_footer', 'mts_load_footer_scripts' );

/**
 * Load CSS files.
 */
function mts_enqueue_css() {
	$mts_options = get_option( MTS_THEME_NAME );

    wp_enqueue_style( 'schema-stylesheet', get_stylesheet_uri() );

	// Slider
    // also enqueued in slider widget
    wp_register_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), null);
    if(!empty($mts_options['mts_featured_slider'])) {
        wp_enqueue_style('owl-carousel');
    }

	$handle = 'schema-stylesheet';

    // WooCommerce
    if ( mts_is_wc_active() ) {
        if ( empty( $mts_options['mts_optimize_wc'] ) || ( ! empty( $mts_options['mts_optimize_wc'] ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) ) {
            wp_enqueue_style( 'woocommerce', get_template_directory_uri() . '/css/woocommerce2.css' );
            $handle = 'woocommerce';
        }
    }

	// Lightbox
	if ( ! empty( $mts_options['mts_lightbox'] ) ) {
        wp_register_style( 'magnificPopup', get_template_directory_uri() . '/css/magnific-popup.css' );
        wp_enqueue_style( 'magnificPopup' );
	}

	//Font Awesome
	wp_register_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'fontawesome' );

	//Responsive
	if ( ! empty( $mts_options['mts_responsive'] ) ) {
        wp_enqueue_style( 'responsive', get_template_directory_uri() . '/css/responsive.css' );
	}

    $mts_footer_bg = '';
    if ( $mts_options['mts_footer_bg_pattern_upload'] != '' ) {
        $mts_footer_bg = $mts_options['mts_footer_bg_pattern_upload'];
    } else {
        if( !empty( $mts_options['mts_footer_bg_pattern'] )) {
            $mts_footer_bg = get_template_directory_uri().'/images/'.$mts_options['mts_footer_bg_pattern'].'.png';
        }
    }

	$mts_sclayout = '';
	$mts_shareit_left = '';
	$mts_shareit_right = '';
	$mts_author = '';
	$mts_header_section = '';
	$mts_sidebar_location = '';

    if ( is_page() || is_single() ) {
        $mts_sidebar_location = get_post_meta( get_the_ID(), '_mts_sidebar_location', true );
    }
	if ( $mts_sidebar_location != 'right' && ( $mts_options['mts_layout'] == 'sclayout' || $mts_sidebar_location == 'left' )) {
		$mts_sclayout = '.article { float: right;}
		.sidebar.c-4-12 { float: left; padding-right: 0; }';
		if( isset( $mts_options['mts_social_button_position'] ) && $mts_options['mts_social_button_position'] == 'floating' ) {
			$mts_shareit_right = '.shareit { margin: 0 730px 0; border-left: 0; }';
		}
	}
	if ( empty( $mts_options['mts_header_section2'] ) ) {
		$mts_header_section = '.logo-wrap, .widget-header { display: none; }
		.navigation { border-top: 0; }
		#header { min-height: 47px; }';
	}
	if ( isset( $mts_options['mts_social_button_position'] ) && $mts_options['mts_social_button_position'] == 'floating' ) {
		$mts_shareit_left = '.shareit { top: 282px; left: auto; margin: 0 0 0 -135px; width: 90px; position: fixed; padding: 5px; border:none; border-right: 0;}
		.share-item {margin: 2px;}';
	}
	if ( ! empty( $mts_options['mts_author_comment'] ) ) {
		$mts_author = '.bypostauthor { padding: 3%!important; background: #222; width: 94%!important; color: #AAA; }
		.bypostauthor:after { content: "\f044"; position: absolute; font-family: fontawesome; right: 0; top: 0; padding: 1px 10px; color: #535353; font-size: 32px; }';
	}
    $mts_bg = mts_get_background_styles( 'mts_background' );
	$custom_css = "
         body {{$mts_bg}}
        .pace .pace-progress, #mobile-menu-wrapper ul li a:hover, .page-numbers.current, .pagination a:hover, .single .pagination a:hover .current { background: {$mts_options['mts_color_scheme']}; }
        .postauthor h5, .single_post a, .textwidget a, .pnavigation2 a, .sidebar.c-4-12 a:hover, footer .widget li a:hover, .sidebar.c-4-12 a:hover, .reply a, .title a:hover, .post-info a:hover, .widget .thecomment, #tabber .inside li a:hover, .readMore a:hover, .fn a, a, a:hover, #secondary-navigation .navigation ul li a:hover, .readMore a, #primary-navigation a:hover, #secondary-navigation .navigation ul .current-menu-item a, .widget .wp_review_tab_widget_content a, .sidebar .wpt_widget_content a { color:{$mts_options['mts_color_scheme']}; }
         a#pull, #commentform input#submit, #mtscontact_submit, .mts-subscribe input[type='submit'], .widget_product_search input[type='submit'], #move-to-top:hover, .currenttext, .pagination a:hover, .pagination .nav-previous a:hover, .pagination .nav-next a:hover, #load-posts a:hover, .single .pagination a:hover .currenttext, .single .pagination > .current .currenttext, #tabber ul.tabs li a.selected, .tagcloud a, .navigation ul .sfHover a, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce .bypostauthor:after, #searchsubmit, .woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce #content nav.woocommerce-pagination ul li span.current, .woocommerce-page #content nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .latestPost-review-wrapper, .latestPost .review-type-circle.latestPost-review-wrapper, #wpmm-megamenu .review-total-only, .sbutton, #searchsubmit, .widget .wpt_widget_content #tags-tab-content ul li a, .widget .review-total-only.large-thumb { background-color:{$mts_options['mts_color_scheme']}; color: #fff!important; }
        .related-posts .title a:hover, .latestPost .title a { color: {$mts_options['mts_color_scheme']}; }
        .navigation #wpmm-megamenu .wpmm-pagination a { background-color: {$mts_options['mts_color_scheme']}!important; }
        footer {background-color:{$mts_options['mts_footer_bg_color']}; }
        footer {background-image: url( {$mts_footer_bg} );}
        .copyrights { background-color: {$mts_options['mts_copyrights_bg_color']}; }
        .flex-control-thumbs .flex-active{ border-top:3px solid {$mts_options['mts_color_scheme']};}
        .wpmm-megamenu-showing.wpmm-light-scheme { background-color:{$mts_options['mts_color_scheme']}!important; }
        {$mts_sclayout}
        {$mts_shareit_left}
        {$mts_shareit_right}
        {$mts_author}
        {$mts_header_section}
        {$mts_options['mts_custom_css']}
			";
	wp_add_inline_style( $handle, $custom_css );
}
add_action( 'wp_enqueue_scripts', 'mts_enqueue_css', 99 );

/**
 * Wrap videos in .responsive-video div
 *
 * @param $html
 * @param $url
 * @param $attr
 *
 * @return string
 */
function mts_responsive_video( $html, $url, $attr ) {

    // Only video embeds
    $video_providers = array(
        'youtube',
        'vimeo',
        'dailymotion',
        'wordpress.tv',
        'vine.co',
        'animoto',
        'blip.tv',
        'collegehumor.com',
        'funnyordie.com',
        'hulu.com',
        'revision3.com',
        'ted.com',
    );

    // Allow user to wrap other embeds
    $providers = apply_filters('mts_responsive_video', $video_providers );

    foreach ( $providers as $provider ) {
        if ( strstr($url, $provider) ) {
            $html = '<div class="flex-video flex-video-' . sanitize_html_class( $provider ) . '">' . $html . '</div>';
            break;// Break if video found
        }
    }

    return $html;
}
add_filter( 'embed_oembed_html', 'mts_responsive_video', 99, 3 );

if ( ! function_exists( 'mts_comments' ) ) {
    /**
     * Custom comments template.
     * @param $comment
     * @param $args
     * @param $depth
     */
    function mts_comments( $comment, $args, $depth ) {
    	$GLOBALS['comment'] = $comment;
        $mts_options = get_option( MTS_THEME_NAME ); ?>
    	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    		<?php
            switch( $comment->comment_type ) :
                case 'pingback':
                case 'trackback': ?>
                    <div id="comment-<?php comment_ID(); ?>">
                        <div class="comment-author vcard">
                            Pingback: <?php comment_author_link(); ?>
                            <?php if ( ! empty( $mts_options['mts_comment_date'] ) ) { ?>
                                <span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
                            <?php } ?>
                            <span class="comment-meta">
                                <?php edit_comment_link( __( '( Edit )', 'schema' ), '  ', '' ) ?>
                            </span>
                        </div>
                        <?php if ( $comment->comment_approved == '0' ) : ?>
                            <em><?php _e( 'Your comment is awaiting moderation.', 'schema' ) ?></em>
                            <br />
                        <?php endif; ?>
                    </div>
                <?php
                    break;

                default: ?>
                    <div id="comment-<?php comment_ID(); ?>" itemscope itemtype="http://schema.org/UserComments">
                        <div class="comment-author vcard">
                            <?php echo get_avatar( $comment->comment_author_email, 80 ); ?>
                            <?php printf( '<span class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person"><span itemprop="name">%s</span></span>', get_comment_author_link() ) ?>
                            <?php if ( ! empty( $mts_options['mts_comment_date'] ) ) { ?>
                                <span class="ago"><?php comment_date( get_option( 'date_format' ) ); ?></span>
                            <?php } ?>
                            <span class="comment-meta">
                                <?php edit_comment_link( __( '( Edit )', 'schema' ), '  ', '' ) ?>
                            </span>
                        </div>
                        <?php if ( $comment->comment_approved == '0' ) : ?>
                            <em><?php _e( 'Your comment is awaiting moderation.', 'schema' ) ?></em>
                            <br />
                        <?php endif; ?>
                        <div class="commentmetadata">
                            <div class="commenttext" itemprop="commentText">
                                <?php comment_text() ?>
                            </div>
                            <div class="reply">
                                <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] )) ) ?>
                            </div>
                        </div>
                    </div>
                <?php
                   break;
             endswitch; ?>
    	<!-- WP adds </li> -->
    <?php }
}

/**
 * Increase excerpt length to 100.
 *
 * @param $length
 *
 * @return int
 */
function mts_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'mts_excerpt_length', 20 );

/**
 * Remove [...] and shortcodes
 *
 * @param $output
 *
 * @return string
 */
function mts_custom_excerpt( $output ) {
  return preg_replace( '/\[[^\]]*]/', '', $output );
}
add_filter( 'get_the_excerpt', 'mts_custom_excerpt' );

/**
 * Truncate string to x letters/words.
 *
 * @param $str
 * @param int $length
 * @param string $units
 * @param string $ellipsis
 *
 * @return string
 */
function mts_truncate( $str, $length = 40, $units = 'letters', $ellipsis = '&nbsp;&hellip;' ) {
    if ( $units == 'letters' ) {
        if ( mb_strlen( $str ) > $length ) {
            return mb_substr( $str, 0, $length ) . $ellipsis;
        } else {
            return $str;
        }
    } else {
        $words = explode( ' ', $str );
        if ( count( $words ) > $length ) {
            return implode( " ", array_slice( $words, 0, $length ) ) . $ellipsis;
        } else {
            return $str;
        }
    }
}

if ( ! function_exists( 'mts_excerpt' ) ) {
    /**
     * Get HTML-escaped excerpt up to the specified length.
     *
     * @param int $limit
     *
     * @return string
     */
    function mts_excerpt( $limit = 40 ) {
      return esc_html( mts_truncate( get_the_excerpt(), $limit, 'words' ) );
    }
}

/**
 * Change the "read more..." link to "".
 * @param $more_link
 * @param $more_link_text
 *
 * @return string
 */
function mts_remove_more_link( $more_link, $more_link_text ) {
	return '';
}
add_filter( 'the_content_more_link', 'mts_remove_more_link', 10, 2 );

/**
 * Shorthand function to check for more tag in post.
 *
 * @return bool|int
 */
function mts_post_has_moretag() {
    return strpos( get_the_content(), '<!--more-->' );
}

if ( ! function_exists( 'mts_readmore' ) ) {
    /**
     * Display a "read more" link.
     */
    function mts_readmore() {
        ?>
        <div class="readMore">
            <a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
                <?php _e( '[Continue Reading...]', 'schema' ); ?>
            </a>
        </div>
        <?php
    }
}

/**
 * Exclude trackbacks from the comment count.
 *
 * @param $count
 *
 * @return int
 */
function mts_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
        $comments = get_comments( 'status=approve&post_id=' . $id );
        $comments_by_type = separate_comments( $comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}
add_filter( 'get_comments_number', 'mts_comment_count', 0 );

/**
 * Add `has_thumb` to the post's class name if it has a thumbnail.
 *
 * @param $classes
 *
 * @return array
 */
function has_thumb_class( $classes ) {
    if( has_post_thumbnail( get_the_ID() ) ) { $classes[] = 'has_thumb'; }
        return $classes;
}
add_filter( 'post_class', 'has_thumb_class' );

/*-----------------------------------------------------------------------------------*/
/*
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( '_wp_render_title_tag' ) ) {
    /**
     *  Add the title tag for compability with older WP versions.
     */
    function theme_slug_render_title() { ?>
       <title><?php wp_title( '|', true, 'right' ); ?></title>
   <?php }
    add_action( 'wp_head', 'theme_slug_render_title' );
}

/**
 * Handle AJAX search queries.
 */
function ajax_mts_search() {
    $query = $_REQUEST['q']; // It goes through esc_sql() in WP_Query
    $search_query = new WP_Query( array( 's' => $query, 'posts_per_page' => 3, 'post_status' => 'publish' ));
    $search_count = new WP_Query( array( 's' => $query, 'posts_per_page' => -1, 'post_status' => 'publish' ));
    $search_count = $search_count->post_count;
    if ( !empty( $query ) && $search_query->have_posts() ) :
        //echo '<h5>Results for: '. $query.'</h5>';
        echo '<ul class="ajax-search-results">';
        while ( $search_query->have_posts() ) : $search_query->the_post();
            ?><li>
    			<a href="<?php echo esc_url( get_the_permalink() ); ?>">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <?php the_post_thumbnail( 'schema-widgetthumb', array( 'title' => '' ) ); ?>
                    <?php } else { ?>
                        <img class="wp-post-image" src="<?php echo get_template_directory_uri() . '/images/nothumb-schema-widgetthumb.png'; ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"/>
                    <?php } ?>
                    <?php the_title(); ?>
    			</a>
    			<div class="meta">
    				<span class="thetime"><?php the_time( 'F j, Y' ); ?></span>
    			</div> <!-- / .meta -->
    		</li>
    		<?php
        endwhile;
        echo '</ul>';
        echo '<div class="ajax-search-meta"><span class="results-count">'.$search_count.' '.__( 'Results', 'schema' ).'</span><a href="'.esc_url( get_search_link( $query ) ).'" class="results-link">'.__('Show all results.', 'schema' ).'</a></div>';
    else:
        echo '<div class="no-results">'.__( 'No results found.', 'schema' ).'</div>';
    endif;
    wp_reset_postdata();
    exit; // required for AJAX in WP
}
if( !empty( $mts_options['mts_ajax_search'] )) {
    add_action( 'wp_ajax_mts_search', 'ajax_mts_search' );
    add_action( 'wp_ajax_nopriv_mts_search', 'ajax_mts_search' );
}

if ( trim( $mts_options['mts_feedburner'] ) !== '' ) {
    /**
     * Redirect feed to FeedBurner if a FeedBurner URL has been set.
     */
    function mts_rss_feed_redirect() {
        $mts_options = get_option( MTS_THEME_NAME );
        global $feed;
        $new_feed = $mts_options['mts_feedburner'];
        if ( !is_feed() ) {
                return;
        }
        if ( preg_match( '/feedburner/i', $_SERVER['HTTP_USER_AGENT'] )){
                return;
        }
        if ( $feed != 'comments-rss2' ) {
                if ( function_exists( 'status_header' )) status_header( 302 );
                header( "Location:" . $new_feed );
                header( "HTTP/1.1 302 Temporary Redirect" );
                exit();
        }
    }
    add_action( 'template_redirect', 'mts_rss_feed_redirect' );
}

/**
 * Single Post Pagination - Numbers + Previous/Next.
 *
 * @param $args
 *
 * @return mixed
 */
function mts_wp_link_pages_args( $args ) {
    global $page, $numpages, $more, $pagenow;
    if ( !$args['next_or_number'] == 'next_and_number' ) {
	    return $args;
    }

    $args['next_or_number'] = 'number';

    if ( !$more ) {
	    return $args;
    }

    if( $page-1 ) {
	    $args['before'] .= _wp_link_page( $page-1 )
                        . $args['link_before']. $args['previouspagelink'] . $args['link_after'] . '</a>';
    }

    if ( $page<$numpages ) {
	    $args['after'] = _wp_link_page( $page+1 )
	                     . $args['link_before'] . $args['nextpagelink'] . $args['link_after'] . '</a>'
	                     . $args['after'];
    }

    return $args;
}
add_filter( 'wp_link_pages_args', 'mts_wp_link_pages_args' );

/**
 * Remove hentry class from pages
 *
 * @param $classes
 *
 * @return array
 */
function mts_remove_hentry( $classes ) {
    if ( is_page() ) {
        $classes = array_diff( $classes, array( 'hentry' ) );
    }
    return $classes;
}
add_filter( 'post_class','mts_remove_hentry' );

/*-----------------------------------------------------------------------------------*/
/* WooCommerce
/*-----------------------------------------------------------------------------------*/
if ( mts_is_wc_active() ) {
    if ( !function_exists( 'mts_loop_columns' )) {
        /**
         * Change number or products per row to 3
         *
         * @return int
         */
    	function mts_loop_columns() {
    		return 3; // 3 products per row
    	}
    }
    add_filter( 'loop_shop_columns', 'mts_loop_columns' );

    /**
     * Redefine woocommerce_output_related_products()
     */
    function woocommerce_output_related_products() {
        $args = array(
            'posts_per_page' => 3,
            'columns' => 1,
        );
        woocommerce_related_products($args); // Display 3 products in rows of 1
    }

    global $pagenow;
    if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
        /**
         * Define WooCommerce image sizes.
         */
        function mts_woocommerce_image_dimensions() {
            $catalog = array(
                'width' 	=> '209',	// px
                'height'	=> '209',	// px
                'crop'		=> 1 		// true
            );
            $single = array(
                'width' 	=> '326',	// px
                'height'	=> '326',	// px
                'crop'		=> 1 		// true
            );
            $thumbnail = array(
                'width' 	=> '74',	// px
                'height'	=> '74',	// px
                'crop'		=> 0 		// false
            );
            // Image sizes
            update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
            update_option( 'shop_single_image_size', $single ); 		// Single product image
            update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
        }
	    add_action( 'init', 'mts_woocommerce_image_dimensions', 1 );
    }


    /**
     * Change the number of product thumbnails to show per row to 4.
     *
     * @return int
     */
    function mts_thumb_cols() {
     return 4; // .last class applied to every 4th thumbnail
    }
    add_filter( 'woocommerce_product_thumbnails_columns', 'mts_thumb_cols' );

    /**
     * Change the number of WooCommerce products to show per page.
     *
     * @return mixed
     */
    function mts_products_per_page() {
        $mts_options = get_option( MTS_THEME_NAME );
        return $mts_options['mts_shop_products'];
    }
    add_filter( 'loop_shop_per_page', 'mts_products_per_page', 20 );

    /**
     * Ensure cart contents update when products are added to the cart via AJAX.
     *
     * @param $fragments
     *
     * @return mixed
     */
    function mts_header_add_to_cart_fragment( $fragments ) {
    	global $woocommerce;
    	ob_start();	?>

    	<a class="cart-contents" href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'schema' ); ?>"><?php echo sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'schema' ), $woocommerce->cart->cart_contents_count );?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>

    	<?php $fragments['a.cart-contents'] = ob_get_clean();
    	return $fragments;
    }
    add_filter( 'add_to_cart_fragments', 'mts_header_add_to_cart_fragment' );

    /**
     * Optimize WooCommerce Scripts
     * Updated for WooCommerce 2.0+
     * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
     */
    function mts_child_manage_woocommerce_styles() {
        //remove generator meta tag
        remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

        //first check that woo exists to prevent fatal errors
        if ( function_exists( 'is_woocommerce' ) ) {
            //dequeue scripts and styles
            if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
    			wp_dequeue_style( 'woocommerce-layout' );
    			wp_dequeue_style( 'woocommerce-smallscreen' );
    			wp_dequeue_style( 'woocommerce-general' );
    			wp_dequeue_style( 'wc-bto-styles' ); //Composites Styles
    			wp_dequeue_script( 'wc-add-to-cart' );
    			wp_dequeue_script( 'wc-cart-fragments' );
    			wp_dequeue_script( 'woocommerce' );
    			wp_dequeue_script( 'jquery-blockui' );
    			wp_dequeue_script( 'jquery-placeholder' );
            }
        }
	}
	if ( ! empty( $mts_options['mts_optimize_wc'] ) ) {
		add_action( 'wp_enqueue_scripts', 'mts_child_manage_woocommerce_styles', 99 );
	}

    // Remove WooCommerce generator tag.
    remove_action('wp_head', 'wc_generator_tag');
}

/**
 * Add <!-- next-page --> button to tinymce.
 *
 * @param $mce_buttons
 *
 * @return array
 */
function mts_wysiwyg_editor( $mce_buttons ) {
   $pos = array_search( 'wp_more', $mce_buttons, true );
   if ( $pos !== false ) {
       $tmp_buttons = array_slice( $mce_buttons, 0, $pos+1 );
       $tmp_buttons[] = 'wp_page';
       $mce_buttons = array_merge( $tmp_buttons, array_slice( $mce_buttons, $pos+1 ));
   }
   return $mce_buttons;
}
add_filter( 'mce_buttons', 'mts_wysiwyg_editor' );

/**
 * Get Post header animation.
 *
 * @return string
 */
function mts_get_post_header_effect() {
    $postheader_effect = get_post_meta( get_the_ID(), '_mts_postheader', true );

    return $postheader_effect;
}

/**
 * Add Custom Gravatar Support.
 *
 * @param $avatar_defaults
 *
 * @return mixed
 */
function mts_custom_gravatar( $avatar_defaults ) {
    $mts_avatar = get_template_directory_uri() . '/images/gravatar.png';
    $avatar_defaults[$mts_avatar] = __( 'Custom Gravatar ( /images/gravatar.png )', 'schema' );
    return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'mts_custom_gravatar' );

/**
 * Add `#primary-navigation` the WP Mega Menu's
 * @param $selector
 *
 * @return string
 */
function mts_megamenu_parent_element( $selector ) {
    return '.navigation';
}
add_filter( 'wpmm_container_selector', 'mts_megamenu_parent_element' );

/**
 * Change the image size of WP Mega Menu's thumbnails.
 *
 * @param $thumbnail_html
 * @param $post_id
 *
 * @return string
 */
function mts_megamenu_thumbnails( $thumbnail_html, $post_id ) {
    $thumbnail_html = '<div class="wpmm-thumbnail">';
    $thumbnail_html .= '<a title="'.get_the_title( $post_id ).'" href="'.get_permalink( $post_id ).'">';
    if(has_post_thumbnail($post_id)):
        $thumbnail_html .= get_the_post_thumbnail($post_id, 'schema-widgetfull', array('title' => ''));
    else:
        $thumbnail_html .= '<img src="'.get_template_directory_uri().'/images/nothumb-schema-widgetfull.png" alt="'.__('No Preview', 'schema').'"  class="wp-post-image" />';
    endif;
    $thumbnail_html .= '</a>';

    // WP Review
    $thumbnail_html .= (function_exists('wp_review_show_total') ? wp_review_show_total(false) : '');

    $thumbnail_html .= '</div>';

    return $thumbnail_html;
}
add_filter( 'wpmm_thumbnail_html', 'mts_megamenu_thumbnails', 10, 2 );

/*-----------------------------------------------------------------------------------*/
/*  WP Review Support
/*-----------------------------------------------------------------------------------*/

/**
 * Set default colors for new reviews.
 *
 * @param $colors
 *
 * @return array
 */
function mts_new_default_review_colors( $colors ) {
    $colors = array(
        'color' => '#FFCA00',
        'fontcolor' => '#fff',
        'bgcolor1' => '#151515',
        'bgcolor2' => '#151515',
        'bordercolor' => '#151515'
    );
  return $colors;
}
add_filter( 'wp_review_default_colors', 'mts_new_default_review_colors' );

/**
 * Set default location for new reviews.
 *
 * @param $position
 *
 * @return string
 */
function mts_new_default_review_location( $position ) {
  $position = 'top';
  return $position;
}
add_filter( 'wp_review_default_location', 'mts_new_default_review_location' );

/**
 * Thumbnail Upscale
 *  Enables upscaling of thumbnails for small media attachments,
 *  to make sure it fits into it's supposed location.
 *  Cannot be used in conjunction with Retina Support.
 *
 * @param $default
 * @param $orig_w
 * @param $orig_h
 * @param $new_w
 * @param $new_h
 * @param $crop
 *
 * @return array|null
 */
function mts_image_crop_dimensions( $default, $orig_w, $orig_h, $new_w, $new_h, $crop ) {

    if( !$crop || ($orig_w == 512 && $orig_h == 512) )
    	return null; // let the wordpress default function handle this

    $aspect_ratio = $orig_w / $orig_h;
    $size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

    $crop_w = round( $new_w / $size_ratio );
    $crop_h = round( $new_h / $size_ratio );

    $s_x = floor( ( $orig_w - $crop_w ) / 2 );
    $s_y = floor( ( $orig_h - $crop_h ) / 2 );

    return array( 0, 0, ( int ) $s_x, ( int ) $s_y, ( int ) $new_w, ( int ) $new_h, ( int ) $crop_w, ( int ) $crop_h );
}
add_filter( 'image_resize_dimensions', 'mts_image_crop_dimensions', 10, 6 );


/*-----------------------------------------------------------------------------------*/
/* Post view count
/* AJAX is used to support caching plugins - it is possible to disable with filter
/* It is also possible to exclude admins with another filter
/*-----------------------------------------------------------------------------------*/

/**
 * Append JS to content for AJAX call on single.
 *
 * @param $content
 *
 * @return string
 */
function mts_view_count_js( $content ) {
    $id = get_the_ID();
    $use_ajax = apply_filters( 'mts_view_count_cache_support', true );

    $exclude_admins = apply_filters( 'mts_view_count_exclude_admins', false ); // pass in true or a user capability
    if ($exclude_admins === true) {
	    $exclude_admins = 'edit_posts';
    }
    if ($exclude_admins && current_user_can( $exclude_admins )) {
	    return $content; // do not count post views here
    }

    if (is_single()) {
        if ($use_ajax) {
            // enqueue jquery
            wp_enqueue_script( 'jquery' );

            $url = admin_url( 'admin-ajax.php' );
            $content .= "
<script type=\"text/javascript\">
jQuery(document).ready(function($) {
    $.post('".esc_js($url)."', {action: 'mts_view_count', id: '".esc_js($id)."'});
});
</script>";

        }

        if (!$use_ajax) {
            mts_update_view_count($id);
        }
    }

    return $content;
}
add_filter('the_content', 'mts_view_count_js');

/**
 * Call mts_update_view_count on AJAX.
 */
function mts_ajax_mts_view_count() {
    // do count
    $post_id = absint( $_POST['id'] );
    mts_update_view_count( $post_id );
}
add_action('wp_ajax_mts_view_count', 'mts_ajax_mts_view_count');
add_action('wp_ajax_nopriv_mts_view_count','mts_ajax_mts_view_count');

/**
 * Update the view count of a post.
 *
 * @param $post_id
 */
function mts_update_view_count( $post_id ) {
    $count = get_post_meta( $post_id, '_mts_view_count', true );
    update_post_meta( $post_id, '_mts_view_count', $count + 1 );

    do_action( 'mts_view_count_after_update', $post_id );
}

/**
 * Convert color format from HEX to HSL.
 * @param $color
 *
 * @return array
 */
function mts_hex_to_hsl( $color ){

    // Sanity check
    $color = mts_check_hex_color($color);

    // Convert HEX to DEC
    $R = hexdec($color[0].$color[1]);
    $G = hexdec($color[2].$color[3]);
    $B = hexdec($color[4].$color[5]);

    $HSL = array();

    $var_R = ($R / 255);
    $var_G = ($G / 255);
    $var_B = ($B / 255);

    $var_Min = min($var_R, $var_G, $var_B);
    $var_Max = max($var_R, $var_G, $var_B);
    $del_Max = $var_Max - $var_Min;

    $L = ($var_Max + $var_Min)/2;

    if ($del_Max == 0) {
        $H = 0;
        $S = 0;
    } else {
        if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
        else            $S = $del_Max / ( 2 - $var_Max - $var_Min );

        $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
        $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
        $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

        if      ($var_R == $var_Max) $H = $del_B - $del_G;
        else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
        else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

        if ($H<0) $H++;
        if ($H>1) $H--;
    }

    $HSL['H'] = ($H*360);
    $HSL['S'] = $S;
    $HSL['L'] = $L;

    return $HSL;
}

/**
 * Convert color format from HSL to HEX.
 *
 * @param array $hsl
 *
 * @return string
 */
function mts_hsl_to_hex( $hsl = array() ){

    list($H,$S,$L) = array( $hsl['H']/360,$hsl['S'],$hsl['L'] );

    if( $S == 0 ) {
        $r = $L * 255;
        $g = $L * 255;
        $b = $L * 255;
    } else {

        if($L<0.5) {
            $var_2 = $L*(1+$S);
        } else {
            $var_2 = ($L+$S) - ($S*$L);
        }

        $var_1 = 2 * $L - $var_2;

        $r = round(255 * mts_huetorgb( $var_1, $var_2, $H + (1/3) ));
        $g = round(255 * mts_huetorgb( $var_1, $var_2, $H ));
        $b = round(255 * mts_huetorgb( $var_1, $var_2, $H - (1/3) ));
    }

    // Convert to hex
    $r = dechex($r);
    $g = dechex($g);
    $b = dechex($b);

    // Make sure we get 2 digits for decimals
    $r = (strlen("".$r)===1) ? "0".$r:$r;
    $g = (strlen("".$g)===1) ? "0".$g:$g;
    $b = (strlen("".$b)===1) ? "0".$b:$b;

    return $r.$g.$b;
}

/**
 * Convert color format from Hue to RGB.
 *
 * @param $v1
 * @param $v2
 * @param $vH
 *
 * @return mixed
 */
function mts_huetorgb( $v1,$v2,$vH ) {
    if( $vH < 0 ) {
        $vH += 1;
    }

    if( $vH > 1 ) {
        $vH -= 1;
    }

    if( (6*$vH) < 1 ) {
           return ($v1 + ($v2 - $v1) * 6 * $vH);
    }

    if( (2*$vH) < 1 ) {
        return $v2;
    }

    if( (3*$vH) < 2 ) {
        return ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
    }

    return $v1;

}

/**
 * Get the 6-digit hex color.
 *
 * @param $hex
 *
 * @return mixed|string
 */
function mts_check_hex_color( $hex ) {
    // Strip # sign is present
    $color = str_replace("#", "", $hex);

    // Make sure it's 6 digits
    if( strlen($color) == 3 ) {
        $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
    }

    return $color;
}

/**
 * Check if color is considered light or not.
 * @param $color
 *
 * @return bool
 */
function mts_is_light_color( $color ){

    $color = mts_check_hex_color( $color );

    // Calculate straight from rbg
    $r = hexdec($color[0].$color[1]);
    $g = hexdec($color[2].$color[3]);
    $b = hexdec($color[4].$color[5]);

    return ( ( $r*299 + $g*587 + $b*114 )/1000 > 130 );
}

/**
 * Darken color by given amount in %.
 *
 * @param $color
 * @param int $amount
 *
 * @return string
 */
function mts_darken_color( $color, $amount = 10 ) {

    $hsl = mts_hex_to_hsl( $color );

    // Darken
    $hsl['L'] = ( $hsl['L'] * 100 ) - $amount;
    $hsl['L'] = ( $hsl['L'] < 0 ) ? 0 : $hsl['L']/100;

    // Return as HEX
    return mts_hsl_to_hex($hsl);
}

/**
 * Lighten color by given amount in %.
 *
 * @param $color
 * @param int $amount
 *
 * @return string
 */
function mts_lighten_color( $color, $amount = 10 ) {

    $hsl = mts_hex_to_hsl( $color );

    // Lighten
    $hsl['L'] = ( $hsl['L'] * 100 ) + $amount;
    $hsl['L'] = ( $hsl['L'] > 100 ) ? 1 : $hsl['L']/100;

    // Return as HEX
    return mts_hsl_to_hex($hsl);
}

/**
 * Generate css from background theme option.
 *
 * @param $option_id
 *
 * @return string|void
 */
function mts_get_background_styles( $option_id ) {

    $mts_options = get_option( MTS_THEME_NAME );

    if ( ! isset( $mts_options[ $option_id ]) ) {
	    return;
    }

    $background_option = $mts_options[ $option_id ];
    $output = '';
    $background_image_type = isset( $background_option['use'] ) ? $background_option['use'] : '';

    if ( isset( $background_option['color'] ) && !empty( $background_option['color'] ) && 'gradient' !== $background_image_type ) {
        $output .= 'background-color:'.$background_option['color'].';';
    }

    if ( !empty( $background_image_type ) ) {

        if ( 'upload' == $background_image_type ) {

            if ( isset( $background_option['image_upload'] ) && !empty( $background_option['image_upload'] ) ) {
                $output .= 'background-image:url('.$background_option['image_upload'].');';
            }
            if ( isset( $background_option['repeat'] ) && !empty( $background_option['repeat'] ) ) {
                $output .= 'background-repeat:'.$background_option['repeat'].';';
            }
            if ( isset( $background_option['attachment'] ) && !empty( $background_option['attachment'] ) ) {
                $output .= 'background-attachment:'.$background_option['attachment'].';';
            }
            if ( isset( $background_option['position'] ) && !empty( $background_option['position'] ) ) {
                $output .= 'background-position:'.$background_option['position'].';';
            }
            if ( isset( $background_option['size'] ) && !empty( $background_option['size'] ) ) {
                $output .= 'background-size:'.$background_option['size'].';';
            }

        } else if ( 'gradient' == $background_image_type ) {

            $from      = $background_option['gradient']['from'];
            $to        = $background_option['gradient']['to'];
            $direction = $background_option['gradient']['direction'];

            if ( !empty( $from ) && !empty( $to ) ) {

                $output .= 'background: '.$background_option['color'].';';

                if ( 'horizontal' == $direction ) {

                    $output .= 'background: -moz-linear-gradient(left, '.$from.' 0%, '.$to.' 100%);';
                    $output .= 'background: -webkit-gradient(linear, left top, right top, color-stop(0%,'.$from.'), color-stop(100%,'.$to.'));';
                    $output .= 'background: -webkit-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
                    $output .= 'background: -o-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
                    $output .= 'background: -ms-linear-gradient(left, '.$from.' 0%,'.$to.' 100%);';
                    $output .= 'background: linear-gradient(to right, '.$from.' 0%,'.$to.' 100%);';
                    $output .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$from."', endColorstr='".$to."',GradientType=1 );";

                } else {

                    $output .= 'background: -moz-linear-gradient(top, '.$from.' 0%, '.$to.' 100%);';
                    $output .= 'background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'.$from.'), color-stop(100%,'.$to.'));';
                    $output .= 'background: -webkit-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
                    $output .= 'background: -o-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
                    $output .= 'background: -ms-linear-gradient(top, '.$from.' 0%,'.$to.' 100%);';
                    $output .= 'background: linear-gradient(to bottom, '.$from.' 0%,'.$to.' 100%);';
                    $output .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$from."', endColorstr='".$to."',GradientType=0 );";
                }
            }

        } else if ( 'pattern' == $background_image_type ) {

            $output .= 'background-image:url('.get_template_directory_uri().'/images/'.$background_option['image_pattern'].'.png'.');';
        }
    }

    return $output;
}

function mts_admin_bar_link() {
    /** @var WP_Admin_bar $wp_admin_bar */
    global $wp_admin_bar;

    if( current_user_can( 'edit_theme_options' ) ) {
        $wp_admin_bar->add_menu( array(
            'id' => 'mts-theme-options',
            'title' => 'Theme Options',
            'href' => admin_url( 'themes.php?page=theme_options' )
        ) );
    }
}
add_action( 'admin_bar_menu', 'mts_admin_bar_link', 65 );

/**
 * Retrieves the attachment ID from the file URL
 *
 * @param $image_url
 *
 * @return string
 */
function mts_get_image_id_from_url( $image_url ) {
    global $wpdb;
    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
    if ( isset( $attachment[0] ) ) {
        return $attachment[0];
    } else {
        return false;
    }
}

/**
 * Remove new line tags from string
 *
 * @param $text
 *
 * @return string
 */
function mts_escape_text_tags( $text ) {
    return (string) str_replace( array( "\r", "\n" ), '', strip_tags( $text ) );
}

/**
 * Remove new line tags from string
 *
 * @return string
 */
function mts_single_post_schema() {

    if ( is_singular( 'post' ) ) {

        global $post, $mts_options;

        if ( has_post_thumbnail( $post->ID ) && !empty( $mts_options['mts_logo'] ) ) {

            $logo_id = mts_get_image_id_from_url( $mts_options['mts_logo'] );

            if ( $logo_id ) {
                
                $images  = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
                $logo    = wp_get_attachment_image_src( $logo_id, 'full' );
                $excerpt = mts_escape_text_tags( $post->post_excerpt );
                $content = $excerpt === "" ? mb_substr( mts_escape_text_tags( $post->post_content ), 0, 110 ) : $excerpt;

                $args = array(
                    "@context" => "http://schema.org",
                    "@type"    => "BlogPosting",
                    "mainEntityOfPage" => array(
                        "@type" => "WebPage",
                        "@id"   => get_permalink( $post->ID )
                    ),
                    "headline" => ( function_exists( '_wp_render_title_tag' ) ? wp_get_document_title() : wp_title( '', false, 'right' ) ),
                    "image"    => array(
                        "@type"  => "ImageObject",
                        "url"    => $images[0],
                        "width"  => $images[1],
                        "height" => $images[2]
                    ),
                    "datePublished" => get_the_time( DATE_ISO8601, $post->ID ),
                    "dateModified"  => get_post_modified_time(  DATE_ISO8601, __return_false(), $post->ID ),
                    "author" => array(
                        "@type" => "Person",
                        "name"  => mts_escape_text_tags( get_the_author_meta( 'display_name', $post->post_author ) )
                    ),
                    "publisher" => array(
                        "@type" => "Organization",
                        "name"  => get_bloginfo( 'name' ),
                        "logo"  => array(
                            "@type"  => "ImageObject",
                            "url"    => esc_url( $mts_options['mts_logo'] ),
                            "width"  => $logo[1],
                            "height" => $logo[2]
                        )
                    ),
                    "description" => ( class_exists('WPSEO_Meta') ? WPSEO_Meta::get_value( 'metadesc' ) : $content )
                );

                echo '<script type="application/ld+json">' , PHP_EOL;
                echo wp_json_encode( $args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) , PHP_EOL;
                echo '</script>' , PHP_EOL;
            }
        }
    }
}
add_action( 'wp_head', 'mts_single_post_schema' );

if ( ! empty( $mts_options['mts_async_js'] ) ) {
    function mts_js_async_attr($tag){

        if (is_admin())
            return $tag;

        $async_files = apply_filters( 'mts_js_async_files', array(
            get_template_directory_uri() . '/js/ajax.js',
            get_template_directory_uri() . '/js/contact.js',
            get_template_directory_uri() . '/js/customscript.js',
            get_template_directory_uri() . '/js/jquery.magnific-popup.min.js',
            get_template_directory_uri() . '/js/layzr.min.js',
            get_template_directory_uri() . '/js/owl.carousel.min.js',
            get_template_directory_uri() . '/js/parallax.js',
            get_template_directory_uri() . '/js/retina.js',
            get_template_directory_uri() . '/js/sticky.js',
            get_template_directory_uri() . '/js/zoomout.js',
         ) );

        $add_async = false;
        foreach ($async_files as $file) {
            if (strpos($tag, $file) !== false) {
                $add_async = true;
                break;
            }
        }

        if ($add_async)
            $tag = str_replace( ' src', ' async="async" src', $tag );

        return $tag;
    }
    add_filter( 'script_loader_tag', 'mts_js_async_attr', 10 );
}

if ( ! empty( $mts_options['mts_remove_ver_params'] ) ) {
    function mts_remove_script_version( $src ){

        if ( is_admin() )
            return $src;

        $parts = explode( '?ver', $src );
        return $parts[0];
    }
    add_filter( 'script_loader_src', 'mts_remove_script_version', 15, 1 );
    add_filter( 'style_loader_src', 'mts_remove_script_version', 15, 1 );
}
