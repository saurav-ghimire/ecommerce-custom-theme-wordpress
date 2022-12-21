<?php
/**
 * The Bella Brow Studio functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package mra
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function mra_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on The Bella Brow Studio, use a find and replace
		* to change 'mra' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'mra', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'mra' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'mra_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'mra_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function mra_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'mra_content_width', 640 );
}
add_action( 'after_setup_theme', 'mra_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function mra_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Newsletter', 'mra' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'mra' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'mra_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function mra_scripts() {
	wp_enqueue_style( 'mra-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'mra-style', 'rtl', 'replace' );

	wp_enqueue_script( 'mra-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mra_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Import Bootstrap Navwalker
 */
require_once('wp-bootstrap-navwalker.php');

// *********************************** Roles **************************************
function add_theme_caps() {
    $role_object = get_role( 'editor' );
    $role_object->add_cap( 'edit_theme_options' ); 
}
add_action( 'admin_init', 'add_theme_caps');

// *********************************** Wp Enques ***********************************
// The CSS files for theme
function wp_custom_theme_styles() {
    wp_enqueue_style('bundle_style_css', get_template_directory_uri() . '/assets/css/style-bundle.css');
    wp_enqueue_style('roboto-font_css', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;900&display=swap');
}
add_action('wp_enqueue_scripts', 'wp_custom_theme_styles');

// The JavaScript files for theme
function wp_custom_theme_js(){
    wp_enqueue_script('bundle_js', get_template_directory_uri() . '/assets/js/js-bundle.js');
}
add_action('wp_enqueue_scripts', 'wp_custom_theme_js');

// *********************************** Walker Bootstrap Menu ***********************
// Bootstrap Navigation
function bootstrap_nav(){
    wp_nav_menu( array(
        'theme_location'    => 'header-menu',
        'depth'             => 2,
        'container'         => 'false',
        'menu_class'        => 'nav navbar-nav',
        'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
        'walker'            => new wp_bootstrap_navwalker())
    );
}

function register_header_menu() {
    register_nav_menus(
        array(
            'header-menu' => __( 'Header Menu' ),
            'footer-menu' => __( 'Footer Links' )
        )
    );
}
add_action( 'init', 'register_header_menu' );

// *********************************** Breadcrumb Setup ****************************
function get_breadcrumb() {
    echo '<a href="'.home_url().'" rel="nofollow">Home</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        // Get Current Post Type Name
        $post = get_queried_object();
        $postType = get_post_type_object(get_post_type($post));
        if ($postType) { ?>
        <?php echo esc_html($postType->labels->singular_name); ?>
            <!-- <a href="<?php //get_post_type_archive_link( $post_type ); ?>"> <?php //echo esc_html($postType->labels->singular_name); ?> </a> -->
        <?php
        }
        // Get Current Post Type List Name
        the_category(' &bull; ');
            if (is_single()) {
                echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
                the_title();
            }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}

// *********************************** Excerpt Length ******************************
function excerpt($limit) {
    $excerpt = explode(' ', get_the_excerpt(), $limit);

    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt) . '...';
    } else {
        $excerpt = implode(" ", $excerpt);
    }

    $excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);

    return $excerpt;
}

function content($limit) {
    $content = explode(' ', get_the_content(), $limit);

    if (count($content) >= $limit) {
        array_pop($content);
        $content = implode(" ", $content) . '...';
    } else {
        $content = implode(" ", $content);
    }

    $content = preg_replace('/\[.+\]/','', $content);
    $content = apply_filters('the_content', $content); 
    $content = str_replace(']]>', ']]&gt;', $content);

    return $content;
}

// *********************************** Excerpt Length ******************************
function wp_custom_theme_post() {
    register_post_type( 'banner-slider',
        array(
            'labels' => array(
                'name' => __( 'Banner Slider' ),
                'singular_name' => __( 'Banner Slider' )
            ),
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            'has_archive' => true,
            'menu_icon'  => 'dashicons-embed-photo',
        )
    );

    register_post_type( 'banner-featured',
        array(
            'labels' => array(
                'name' => __( 'Banner Categories' ),
                'singular_name' => __( 'Banner Categories' )
            ),
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-table-col-before'
        )
    );

    register_post_type( 'featured-categories',
        array(
            'labels' => array(
                'name' => __( 'Custom Featured Categories' ),
                'singular_name' => __( 'Custom Featured Categories' )
            ),
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-insert'
        )
    );

    register_post_type( 'collection-section',
        array(
            'labels' => array(
                'name' => __( 'Collection Shop' ),
                'singular_name' => __( 'Collection Shop' )
            ),
            'supports' => array('title', 'thumbnail'),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-format-aside'
        )
    );

    register_post_type( 'whychooseus-section',
        array(
            'labels' => array(
                'name' => __( 'Why Choose us' ),
                'singular_name' => __( 'Why Choose us' )
            ),
            'supports' => array('title', 'editor' ,'thumbnail'),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-editor-ul'
        )
    );

    
}
add_action( 'init', 'wp_custom_theme_post' );

// *********************************** Customizer **********************************
// Theme Home Page Setting Setup
function my_custom_home_page_customize( $wp_customize ) {
    $wp_customize->add_panel( 'my_custom_home_page_customize', array(
        'priority' => 1,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'My Home Page Setting', 'wp_custom_theme' ),
        'description' => __( 'Description of what this panel does.', 'wp_custom_theme' ),
    ));

    // ******************** Social Media Section
    $wp_customize->add_section( 'social_section', array(
        'priority' => 1,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Social Media Section', 'wp_custom_theme' ),
        'description' => 'Enter your social media link here.',
        'panel' => 'my_custom_home_page_customize',
    ));

    $wp_customize->add_setting( 'facebook', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_url',
    ));

    $wp_customize->add_control( 'facebook', array(
        'type' => 'url',
        'priority' => 1,
        'section' => 'social_section',
        'label' => __( 'Facebook', 'wp_custom_theme' ),
        'description' => 'Your Facebook Account URL',
    ));

    $wp_customize->add_setting( 'twitter', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_url',
    ));

    $wp_customize->add_control( 'twitter', array(
        'type' => 'url',
        'priority' => 2,
        'section' => 'social_section',
        'label' => __( 'Twitter', 'wp_custom_theme' ),
        'description' => 'Your Twitter Account URL',
    ));

    $wp_customize->add_setting( 'linkedin', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_url',
    ));

    $wp_customize->add_control( 'linkedin', array(
        'type' => 'url',
        'priority' => 3,
        'section' => 'social_section',
        'label' => __( 'Linkedin', 'wp_custom_theme' ),
        'description' => 'Your Linkedin Account URL',
    ));

    $wp_customize->add_setting( 'instagram', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_url',
    ));

    $wp_customize->add_control( 'instagram', array(
        'type' => 'url',
        'priority' => 4,
        'section' => 'social_section',
        'label' => __( 'Instagram', 'wp_custom_theme' ),
        'description' => 'Your Instagram Account URL',
    ));


    // ******************** Contact Information Section
    $wp_customize->add_section( 'contact_information_section', array(
        'priority' => 2,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Contact Information Section', 'wp_custom_theme' ),
        'description' => 'Enter your Company Contact Information.',
        'panel' => 'my_custom_home_page_customize',
    ));

    $wp_customize->add_setting( 'phone', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_textarea',
    ));

    $wp_customize->add_control( 'phone', array(
        'type' => 'text',
        'priority' => 2,
        'section' => 'contact_information_section',
        'label' => __( 'Phone', 'wp_custom_theme' ),
        'description' => 'Your Company Phone Number',
    ));

    $wp_customize->add_setting( 'email', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_textarea',
    ));

    $wp_customize->add_control( 'email', array(
        'type' => 'text',
        'priority' => 3,
        'section' => 'contact_information_section',
        'label' => __( 'Email', 'wp_custom_theme' ),
        'description' => 'Your Company Email',
    ));

    $wp_customize->add_setting( 'address', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_textarea',
    ));

    $wp_customize->add_control( 'address', array(
        'type' => 'textarea',
        'priority' => 4,
        'section' => 'contact_information_section',
        'label' => __( 'Address', 'wp_custom_theme' ),
        'description' => 'Your Company Address',
    ));


$wp_customize->add_section( 'business_hours_info', array(
        'priority' => 3,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __( 'Business Hours Section', 'wp_custom_theme' ),
        'description' => 'Business Hours Information.',
        'panel' => 'my_custom_home_page_customize',
    ));

    $wp_customize->add_setting( 'businesshours', array(
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'transport' => '',
        'sanitize_callback' => 'esc_textarea',
    ));

    $wp_customize->add_control( 'businesshours', array(
        'type' => 'text',
        'priority' => 2,
        'section' => 'business_hours_info',
        'label' => __( 'Business Hours', 'wp_custom_theme' ),
        'description' => 'Your Company Business Hours',
    ));


    
}
add_action( 'customize_register', 'my_custom_home_page_customize' );
