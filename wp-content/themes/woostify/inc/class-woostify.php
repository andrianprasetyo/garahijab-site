<?php
/**
 * Woostify Class
 *
 * @package  woostify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'woostify' ) ) :

	/**
	 * The main Woostify class
	 */
	class Woostify {

		/**
		 * Setup class.
		 */
		public function __construct() {
			// Set the content width based on the theme's design and stylesheet.
			$this->woostify_content_width();

			add_action( 'after_setup_theme', array( $this, 'woostify_setup' ) );
			add_action( 'wp', array( $this, 'woostify_init' ) );
			add_action( 'widgets_init', array( $this, 'woostify_widgets_init' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'woostify_scripts' ), 10 );
			add_filter( 'wpcf7_load_css', '__return_false' );
			add_filter( 'excerpt_length', array( $this, 'woostify_limit_excerpt_character' ), 99 );

			// ELEMENTOR.
			add_action( 'elementor/theme/register_locations', array( $this, 'woostify_register_elementor_locations' ) );
			add_action( 'elementor/elements/categories_registered', array( $this, 'woostify_widget_categories' ) );
			add_action( 'elementor/preview/enqueue_scripts', array( $this, 'woostify_elementor_preview_scripts' ) );

			// Add Image column on blog list in admin screen.
			add_filter( 'manage_post_posts_columns', array( $this, 'woostify_columns_head' ), 10 );
			add_action( 'manage_post_posts_custom_column', array( $this, 'woostify_columns_content' ), 10, 2 );

			// After WooCommerce.
			add_action( 'wp_enqueue_scripts', array( $this, 'woostify_child_scripts' ), 30 );

			add_filter( 'body_class', array( $this, 'woostify_body_classes' ) );
			add_filter( 'woostify_header_class', array( $this, 'woostify_header_classes' ) );
			add_filter( 'wp_page_menu_args', array( $this, 'woostify_page_menu_args' ) );
			add_filter( 'navigation_markup_template', array( $this, 'woostify_navigation_markup_template' ) );
			add_action( 'customize_preview_init', array( $this, 'woostify_customize_live_preview' ) );
			add_filter( 'wp_tag_cloud', array( $this, 'woostify_remove_tag_inline_style' ) );
			add_filter( 'excerpt_more', array( $this, 'woostify_modify_excerpt_more' ) );
		}

		/**
		 * Set the content width based on the theme's design and stylesheet.
		 */
		public function woostify_content_width() {
			if ( ! isset( $content_width ) ) {
				// Pixel.
				$content_width = 1170;
			}
		}

		/**
		 * Get featured image
		 *
		 * @param      int $post_ID The post id.
		 * @return     string Image src.
		 */
		public function woostify_get_featured_image_src( $post_ID ) {
			$img_id  = get_post_thumbnail_id( $post_ID );
			$img_src = WOOSTIFY_THEME_URI . 'assets/images/thumbnail-default.jpg';

			if ( $img_id ) {
				$src     = wp_get_attachment_image_src( $img_id, 'thumbnail' );
				if ( $src ) {
					$img_src = $src[0];
				}
			}

			return $img_src;
		}

		/**
		 * Column head
		 *
		 * @param      array $defaults  The defaults.
		 */
		public function woostify_columns_head( $defaults ) {
			// See: https://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns.
			$order    = array();
			$checkbox = 'cb';
			foreach ( $defaults as $key => $value ) {
				$order[ $key ] = $value;
				if ( $key === $checkbox ) {
					$order['thumbnail_image'] = __( 'Image', 'woostify' );
				}
			}

			return $order;
		}

		/**
		 * Column content
		 *
		 * @param      string $column_name  The column name.
		 * @param      int    $post_ID      The post id.
		 */
		public function woostify_columns_content( $column_name, $post_ID ) {
			if ( 'thumbnail_image' === $column_name ) {
				$_img_src = $this->woostify_get_featured_image_src( $post_ID );
				?>
					<a href="<?php echo esc_url( get_edit_post_link( $post_ID ) ); ?>">
						<img src="<?php echo esc_url( $_img_src ); ?>"/>
					</a>
				<?php
			}
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function woostify_is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 */
		public function woostify_setup() {
			/*
			 * Load Localisation files.
			 *
			 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
			 */

			// Loads wp-content/languages/themes/woostify-it_IT.mo.
			load_theme_textdomain( 'woostify', WP_LANG_DIR . '/themes/' );

			// Loads wp-content/themes/child-theme-name/languages/it_IT.mo.
			load_theme_textdomain( 'woostify', get_stylesheet_directory() . '/languages' );

			// Loads wp-content/themes/woostify/languages/it_IT.mo.
			load_theme_textdomain( 'woostify', WOOSTIFY_THEME_DIR . 'languages' );

			/**
			 * Add default posts and comments RSS feed links to head.
			 */
			add_theme_support( 'automatic-feed-links' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#Post_Thumbnails
			 */
			add_theme_support( 'post-thumbnails' );

			// Post formats.
			add_theme_support(
				'post-formats',
				array(
					'gallery',
					'image',
					'link',
					'quote',
					'video',
					'audio',
					'status',
					'aside',
				)
			);

			/**
			 * Enable support for site logo.
			 */
			add_theme_support(
				'custom-logo',
				apply_filters(
					'woostify_custom_logo_args',
					array(
						'height'      => 110,
						'width'       => 470,
						'flex-width'  => true,
						'flex-height' => true,
					)
				)
			);

			/**
			 * Register menu locations.
			 */
			register_nav_menus(
				apply_filters(
					'woostify_register_nav_menus',
					array(
						'primary' => __( 'Primary Menu', 'woostify' ),
						'footer'  => __( 'Footer Menu', 'woostify' ),
					)
				)
			);

			/*
			 * Switch default core markup for search form, comment form, comments, galleries, captions and widgets
			 * to output valid HTML5.
			 */
			add_theme_support(
				'html5',
				apply_filters(
					'woostify_html5_args',
					array(
						'search-form',
						'comment-form',
						'comment-list',
						'gallery',
						'caption',
						'widgets',
					)
				)
			);

			/**
			 * Setup the WordPress core custom background feature.
			 */
			add_theme_support(
				'custom-background',
				apply_filters(
					'woostify_custom_background_args',
					array(
						'default-color' => apply_filters( 'woostify_default_background_color', 'ffffff' ),
						'default-image' => '',
					)
				)
			);

			/**
			 * Declare support for title theme feature.
			 */
			add_theme_support( 'title-tag' );

			/**
			 * Declare support for selective refreshing of widgets.
			 */
			add_theme_support( 'customize-selective-refresh-widgets' );

			/**
			 * Gutenberg.
			 */
			$options = woostify_options( false );

			// Default block styles.
			add_theme_support( 'wp-block-styles' );

			// Responsive embedded content.
			add_theme_support( 'responsive-embeds' );

			// Editor styles.
			add_theme_support( 'editor-styles' );

			// Wide Alignment.
			add_theme_support( 'align-wide' );

			// Editor Color Palette.
			add_theme_support(
				'editor-color-palette',
				array(
					array(
						'name'  => __( 'Primary Color', 'woostify' ),
						'slug'  => 'woostify-primary',
						'color' => $options['theme_color'],
					),
					array(
						'name'  => __( 'Heading Color', 'woostify' ),
						'slug'  => 'woostify-heading',
						'color' => $options['heading_color'],
					),
					array(
						'name'  => __( 'Text Color', 'woostify' ),
						'slug'  => 'woostify-text',
						'color' => $options['text_color'],
					),
				)
			);

			// Block Font Sizes.
			add_theme_support(
				'editor-font-sizes',
				array(
					array(
						'name'      => __( 'H6', 'woostify' ),
						'size'      => $options['heading_h6_font_size'],
						'slug'      => 'woostify-heading-6',
					),
					array(
						'name'      => __( 'H5', 'woostify' ),
						'size'      => $options['heading_h5_font_size'],
						'slug'      => 'woostify-heading-5',
					),
					array(
						'name'      => __( 'H4', 'woostify' ),
						'size'      => $options['heading_h4_font_size'],
						'slug'      => 'woostify-heading-4',
					),
					array(
						'name'      => __( 'H3', 'woostify' ),
						'size'      => $options['heading_h3_font_size'],
						'slug'      => 'woostify-heading-3',
					),
					array(
						'name'      => __( 'H2', 'woostify' ),
						'size'      => $options['heading_h2_font_size'],
						'slug'      => 'woostify-heading-2',
					),
					array(
						'name'      => __( 'H1', 'woostify' ),
						'size'      => $options['heading_h1_font_size'],
						'slug'      => 'woostify-heading-1',
					),
				)
			);

			// Header Footer Elementor plugin support.
			add_theme_support( 'header-footer-elementor' );
		}

		/**
		 * Init
		 */
		public function woostify_init() {
			// Support Elementor Pro - Theme Builder.
			if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
				return;
			}

			if ( woostify_elementor_has_location( 'header' ) && woostify_elementor_has_location( 'footer' ) ) {
				add_action( 'woostify_theme_header', 'woostify_view_open', 0 );
				add_action( 'woostify_after_footer', 'woostify_view_close', 0 );
			} elseif ( woostify_elementor_has_location( 'header' ) && ! woostify_elementor_has_location( 'footer' ) ) {
				add_action( 'woostify_theme_header', 'woostify_view_open', 0 );
			} elseif ( ! woostify_elementor_has_location( 'header' ) && woostify_elementor_has_location( 'footer' ) ) {
				add_action( 'woostify_after_footer', 'woostify_view_close', 0 );
			}
		}

		/**
		 * Register widget area.
		 *
		 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		public function woostify_widgets_init() {
			$sidebar_args['sidebar'] = array(
				'name'          => __( 'Main Sidebar', 'woostify' ),
				'id'            => 'sidebar',
				'description'   => __( 'Appears in the sidebar of the site.', 'woostify' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
			);

			if ( class_exists( 'woocommerce' ) ) {
				$sidebar_args['shop_sidebar'] = array(
					'name'          => __( 'Woocommerce Sidebar', 'woostify' ),
					'id'            => 'sidebar-shop',
					'description'   => __( ' Appears in the sidebar of shop/product page.', 'woostify' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
				);
			}

			$sidebar_args['footer'] = array(
				'name'          => __( 'Footer Widget', 'woostify' ),
				'id'            => 'footer',
				'description'   => __( 'Appears in the footer section of the site.', 'woostify' ),
				'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
				'after_widget'  => '</div>',
			);

			foreach ( $sidebar_args as $sidebar => $args ) {
				$widget_tags = array(
					'before_title'  => '<h6 class="widget-title">',
					'after_title'   => '</h6>',
				);

				/**
				 * Dynamically generated filter hooks. Allow changing widget wrapper and title tags. See the list below.
				 */
				$filter_hook = sprintf( 'woostify_%s_widget_tags', $sidebar );
				$widget_tags = apply_filters( $filter_hook, $widget_tags );

				if ( is_array( $widget_tags ) ) {
					register_sidebar( $args + $widget_tags );
				}
			}

			// Recent post with thumbnail.
			register_widget( 'Woostify_Recent_Post_Thumbnail' );
		}

		/**
		 * Enqueue scripts and styles.
		 */
		public function woostify_scripts() {
			$options = woostify_options( false );

			/**
			 * Styles
			 */
			wp_enqueue_style(
				'woostify-style',
				get_stylesheet_uri(),
				array(),
				woostify_version()
			);

			/**
			 * Scripts
			 */
			// For IE.
			if ( 'ie' == woostify_browser_detection() ) {
				// Fetch API polyfill.
				wp_enqueue_script(
					'woostify-fetch-api-polyfill',
					WOOSTIFY_THEME_URI . 'assets/js/fetch-api-polyfill' . woostify_suffix() . '.js',
					array(),
					woostify_version(),
					true
				);

				// foreach polyfill.
				wp_enqueue_script(
					'woostify-for-each-polyfill',
					WOOSTIFY_THEME_URI . 'assets/js/for-each-polyfill' . woostify_suffix() . '.js',
					array(),
					woostify_version(),
					true
				);
			}

			// General script.
			wp_enqueue_script(
				'woostify-general',
				WOOSTIFY_THEME_URI . 'assets/js/general' . woostify_suffix() . '.js',
				array( 'jquery' ),
				woostify_version(),
				true
			);

			// Mobile menu.
			wp_enqueue_script(
				'woostify-navigation',
				WOOSTIFY_THEME_URI . 'assets/js/navigation' . woostify_suffix() . '.js',
				array( 'jquery' ),
				woostify_version(),
				true
			);

			// Quantity button.
			wp_register_script(
				'woostify-quantity-button',
				WOOSTIFY_THEME_URI . 'assets/js/woocommerce/quantity-button' . woostify_suffix() . '.js',
				array(),
				woostify_version(),
				true
			);

			// Woocommerce sidebar for mobile.
			wp_register_script(
				'woostify-woocommerce-sidebar',
				WOOSTIFY_THEME_URI . 'assets/js/woocommerce/woocommerce-sidebar' . woostify_suffix() . '.js',
				array(),
				woostify_version(),
				true
			);

			// Woocommerce.
			wp_register_script(
				'woostify-woocommerce',
				WOOSTIFY_THEME_URI . 'assets/js/woocommerce/woocommerce' . woostify_suffix() . '.js',
				array( 'jquery', 'woostify-quantity-button' ),
				woostify_version(),
				true
			);

			if ( $options['shop_single_image_zoom'] ) {
				// Product gallery zoom.
				wp_register_script(
					'easyzoom',
					WOOSTIFY_THEME_URI . 'assets/js/easyzoom' . woostify_suffix() . '.js',
					array( 'jquery' ),
					woostify_version(),
					true
				);

				// Product gallery zoom handle.
				wp_register_script(
					'easyzoom-handle',
					WOOSTIFY_THEME_URI . 'assets/js/woocommerce/easyzoom-handle' . woostify_suffix() . '.js',
					array( 'easyzoom' ),
					woostify_version(),
					true
				);
			}

			// Product varitions.
			wp_register_script(
				'woostify-product-variation',
				WOOSTIFY_THEME_URI . 'assets/js/woocommerce/product-variation' . woostify_suffix() . '.js',
				array( 'jquery' ),
				woostify_version(),
				true
			);

			// Tiny slider js.
			wp_register_script(
				'tiny-slider',
				WOOSTIFY_THEME_URI . 'assets/js/tiny-slider' . woostify_suffix() . '.js',
				array(),
				woostify_version(),
				true
			);

			// Product images ( Tiny slider ).
			wp_register_script(
				'woostify-product-images',
				WOOSTIFY_THEME_URI . 'assets/js/woocommerce/product-images' . woostify_suffix() . '.js',
				array( 'jquery', 'tiny-slider' ),
				woostify_version(),
				true
			);

			if ( $options['shop_single_image_lightbox'] ) {
				// Photoswipe init js.
				wp_register_script(
					'photoswipe-init',
					WOOSTIFY_THEME_URI . 'assets/js/photoswipe-init' . woostify_suffix() . '.js',
					array( 'photoswipe', 'photoswipe-ui-default' ),
					woostify_version(),
					true
				);
			}

			// Comment reply.
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		/**
		 * Support Elementor Location
		 *
		 * @param      array|object $elementor_theme_manager  The elementor theme manager.
		 */
		public function woostify_register_elementor_locations( $elementor_theme_manager ) {
			$elementor_theme_manager->register_location(
				'header',
				[
					'hook'         => 'woostify_theme_header',
					'remove_hooks' => [ 'woostify_template_header' ],
				]
			);
			$elementor_theme_manager->register_location(
				'footer',
				[
					'hook'         => 'woostify_theme_footer',
					'remove_hooks' => [ 'woostify_template_footer' ],
				]
			);
			$elementor_theme_manager->register_location(
				'single',
				[
					'hook'         => 'woostify_theme_single',
					'remove_hooks' => [ 'woostify_template_single' ],
				]
			);
			$elementor_theme_manager->register_location(
				'product_archive',
				[
					'hook'         => 'woostify_theme_archive',
					'remove_hooks' => [ 'woostify_template_archive' ],
				]
			);
			$elementor_theme_manager->register_location(
				'404',
				[
					'hook'         => 'woostify_theme_404',
					'remove_hooks' => [ 'woostify_template_404' ],
					'label'        => __( 'Woostify 404', 'woostify' ),
				]
			);
		}

		/**
		 * Add Elementor Category
		 *
		 * @param      Elements_Manager $elements_manager The elements manager.
		 */
		public function woostify_widget_categories( $elements_manager ) {
			$elements_manager->add_category(
				'woostify-theme',
				array(
					'title' => esc_html__( 'Woostify Theme', 'woostify' ),
				)
			);
		}

		/**
		 * Elementor pewview scripts
		 */
		public function woostify_elementor_preview_scripts() {
			// Elementor widgets js.
			wp_enqueue_script(
				'woostify-elementor-live-preview',
				WOOSTIFY_THEME_URI . 'assets/js/elementor-preview' . woostify_suffix() . '.js',
				array(),
				woostify_version()
			);
		}

		/**
		 * Limit the character length in exerpt
		 *
		 * @param      int $length The length.
		 */
		public function woostify_limit_excerpt_character( $length ) {
			// Don't change anything inside /wp-admin/.
			if ( is_admin() ) {
				return $length;
			}

			$options = woostify_options( false );
			$length  = $options['blog_list_limit_exerpt'];
			return $length;
		}

		/**
		 * Enqueue child theme stylesheet.
		 * A separate function woostify_is required as the child theme css needs to be enqueued _after_ the parent theme
		 * primary css and the separate WooCommerce css.
		 */
		public function woostify_child_scripts() {
			if ( is_child_theme() ) {
				$child_theme = wp_get_theme( get_stylesheet() );
				wp_enqueue_style(
					'woostify-child-style',
					get_stylesheet_uri(),
					array(),
					$child_theme->get( 'Version' )
				);
			}
		}

		/**
		 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
		 *
		 * @param array $args Configuration arguments.
		 * @return array
		 */
		public function woostify_page_menu_args( $args ) {
			$args['show_home'] = true;
			return $args;
		}

		/**
		 * Adds custom classes to the array of body classes.
		 *
		 * @param array $classes Classes for the body element.
		 * @return array
		 */
		public function woostify_body_classes( $classes ) {
			// Get theme options.
			$options   = woostify_options( false );

			// Theme version.
			$classes[] = 'woostify-' . woostify_version();

			// Woostify Pro Version.
			if ( defined( 'WOOSTIFY_PRO_VERSION' ) ) {
				$classes[] = 'woostify-pro-' . WOOSTIFY_PRO_VERSION;
			}

			// Broser detection.
			if ( '' != woostify_browser_detection() ) {
				$classes[] = woostify_browser_detection() . '-detected';
			}

			// Site container layout.
			$customizer_container = $options['default_container'];
			$metabox_container    = woostify_get_metabox( false, 'site-container' );
			$container            = 'default' != $metabox_container ? $metabox_container : $customizer_container;
			$classes[]            = 'site-' . $container . '-container';

			// Header layout.
			$header_class_name = defined( 'WOOSTIFY_PRO_MULTIPLE_HEADER' ) ? $options['header_layout'] : 'layout-1';
			$classes[] = 'has-header-' . $header_class_name;

			// Header transparent.
			if ( true == woostify_header_transparent() ) {
				$classes[] = 'has-header-transparent header-transparent-for-' . $options['header_transparent_enable_on'];
			}

			// Sidebar class detected.
			$classes[] = woostify_sidebar_class();

			// Product style layout.
			$product_style_class_name = defined( 'WOOSTIFY_PRO_PRODUCT_STYLE' ) ? $options['product_style'] : 'layout-1';
			$classes[] = 'ps-' . $product_style_class_name;

			// Blog page layout.
			if ( woostify_is_blog() && ! is_singular( 'post' ) ) {
				$classes[] = 'blog-layout-' . $options['blog_list_layout'];
			}

			return $classes;
		}

		/**
		 * Adds custom classes to the array of header classes.
		 *
		 * @param array $classes Classes for the header element.
		 */
		public function woostify_header_classes( $classes ) {
			$options           = woostify_options( false );
			$header_class_name = defined( 'WOOSTIFY_PRO_VERSION' ) ? $options['header_layout'] : 'layout-1';

			$classes[] = 'header-' . $header_class_name;

			return $classes;
		}

		/**
		 * Custom navigation markup template hooked into `navigation_markup_template` filter hook.
		 */
		public function woostify_navigation_markup_template() {
			$template  = '<nav class="post-navigation navigation %1$s" aria-label="' . esc_attr__( 'Post Pagination', 'woostify' ) . '">';
			$template .= '<h2 class="screen-reader-text">%2$s</h2>';
			$template .= '<div class="nav-links">%3$s</div>';
			$template .= '</nav>';

			return apply_filters( 'woostify_navigation_markup_template', $template );
		}

		/**
		 * Customizer live preview
		 */
		public function woostify_customize_live_preview() {
			wp_enqueue_script(
				'woostify-customizer-preview',
				WOOSTIFY_THEME_URI . 'assets/js/customizer-preview' . woostify_suffix() . '.js',
				array( 'jquery' ),
				woostify_version(),
				true
			);
		}

		/**
		 * Remove inline css on tag cloud
		 *
		 * @param string $string tagCloud.
		 */
		public function woostify_remove_tag_inline_style( $string ) {
			return preg_replace( '/ style=("|\')(.*?)("|\')/', '', $string );
		}


		/**
		 * Modify excerpt more to `...`
		 *
		 * @param string $more More exerpt.
		 */
		public function woostify_modify_excerpt_more( $more ) {
			// Don't change anything inside /wp-admin/.
			if ( is_admin() ) {
				return $more;
			}

			$more = apply_filters( 'woostify_excerpt_more', '...' );
			return $more;
		}
	}
endif;

$woostify = new Woostify();
