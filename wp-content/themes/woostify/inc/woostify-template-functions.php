<?php
/**
 * Woostify template functions.
 *
 * @package woostify
 */

if ( ! function_exists( 'woostify_post_related' ) ) {
	/**
	 * Display related post.
	 */
	function woostify_post_related() {
		$options = woostify_options( false );

		if ( false == $options['blog_single_related_post'] ) {
			return;
		}

		$id = get_queried_object_id();

		$args = array(
			'post_type'           => 'post',
			'post__not_in'        => array( $id ),
			'posts_per_page'      => 3,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			?>
			<div class="related-box">
				<div class="row">
					<h3 class="related-title"><?php esc_html_e( 'Related Posts', 'woostify' ); ?></h3>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();

						$post_id = get_the_ID();
						?>
						<div class="related-post col-md-4">
							<?php if ( has_post_thumbnail() ) { ?>
								<a href="<?php echo esc_url( get_permalink() ); ?>" class="entry-header">
									<?php the_post_thumbnail( 'medium' ); ?>
								</a>
							<?php } ?>

							<div class="posted-on"><?php echo get_the_date(); ?></div>
							<h2 class="entry-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo get_the_title(); ?></a></h2>
							<a class="post-read-more" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Read more', 'woostify' ); ?></a>
						</div>
					<?php endwhile; ?>
				</div>
			</div>
			<?php
			wp_reset_postdata();
		endif;
	}
}

if ( ! function_exists( 'woostify_display_comments' ) ) {
	/**
	 * Woostify display comments
	 */
	function woostify_display_comments() {
		// If comments are open or we have at least one comment, load up the comment template.
		if ( is_single() || is_page() ) {
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		}
	}
}

if ( ! function_exists( 'woostify_relative_time' ) ) {

	/**
	 * Display relative time for comment
	 *
	 * @param      string $type `comment` or `post`.
	 * @return     string real_time relative time
	 */
	function woostify_relative_time( $type = 'comment' ) {
		$time      = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
		$real_time = human_time_diff( $time( 'U' ), current_time( 'timestamp' ) ) . ' ' . esc_html__( 'ago', 'woostify' );

		return apply_filters( 'woostify_real_time_comment', $real_time );
	}
}

if ( ! function_exists( 'woostify_comment' ) ) {
	/**
	 * Woostify comment template
	 *
	 * @param array $comment the comment array.
	 * @param array $args the comment args.
	 * @param int   $depth the comment depth.
	 */
	function woostify_comment( $comment, $args, $depth ) {
		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
		?>

		<<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">
			<div class="comment-body">
				<?php if ( get_avatar( get_the_author_meta( 'ID' ) ) ) { ?>
					<div class="comment-author vcard">
						<?php echo get_avatar( $comment, 70 ); ?>
					</div>
				<?php } ?>

				<?php if ( 'div' != $args['style'] ) : ?>
				<div id="div-comment-<?php comment_ID(); ?>" class="comment-content">
				<?php endif; ?>

					<div class="comment-meta commentmetadata">
						<?php printf( wp_kses_post( '<cite class="fn">%s</cite>', 'woostify' ), get_comment_author_link() ); ?>

						<?php if ( '0' == $comment->comment_approved ) : ?>
							<em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'woostify' ); ?></em>
						<?php endif; ?>

						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" class="comment-date">
							<?php echo esc_html( woostify_relative_time() ); ?>
							<?php echo '<time datetime="' . esc_attr( get_comment_date( 'c' ) ) . '" class="sr-only">' . esc_html( get_comment_date() ) . '</time>'; ?>
						</a>
					</div>

					<div class="comment-text">
					  <?php comment_text(); ?>
					</div>

					<div class="reply">
						<?php
							comment_reply_link(
								array_merge(
									$args, array(
										'add_below' => $add_below,
										'depth' => $depth,
										'max_depth' => $args['max_depth'],
									)
								)
							);
						?>
						<?php edit_comment_link( __( 'Edit', 'woostify' ), '  ', '' ); ?>
					</div>

				<?php if ( 'div' != $args['style'] ) : ?>
				</div>
				<?php endif; ?>
			</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_footer_widgets' ) ) {
	/**
	 * Display the footer widget regions.
	 */
	function woostify_footer_widgets() {

		// Default values.
		$option        = woostify_options( false );
		$footer_column = (int) $option['footer_column'];

		if ( 0 == $footer_column ) {
			return;
		}

		if ( is_active_sidebar( 'footer' ) ) {
			?>
			<div class="site-footer-widget footer-widget-col-<?php echo esc_attr( $footer_column ); ?>">
				<?php dynamic_sidebar( 'footer' ); ?>
			</div>
			<?php
		} elseif ( is_user_logged_in() ) {
			?>
			<div class="site-footer-widget footer-widget-col-<?php echo esc_attr( $footer_column ); ?>">
				<div class="widget widget_text default-widget">
					<h6 class="widgettitle"><?php esc_html_e( 'Footer Widget', 'woostify' ); ?></h6>
					<div class="textwidget">
						<p>
							<?php
							printf(
								/* translators: 1: admin URL */
								__( 'Replace this widget content by going to <a href="%1$s"><strong>Appearance / Widgets / Footer Widget</strong></a> and dragging widgets into this widget area.', 'woostify' ),
								esc_url( admin_url( 'widgets.php' ) )
							);  // WPCS: XSS ok.
							?>
						</p>
					</div>
				</div>

				<div class="widget widget_text default-widget">
					<h6 class="widgettitle"><?php esc_html_e( 'Footer Widget', 'woostify' ); ?></h6>
					<div class="textwidget">
						<p>
							<?php
							printf(
								/* translators: 1: admin URL */
								__( 'Replace this widget content by going to <a href="%1$s"><strong>Appearance / Widgets / Footer Widget</strong></a> and dragging widgets into this widget area.', 'woostify' ),
								esc_url( admin_url( 'widgets.php' ) )
							);  // WPCS: XSS ok.
							?>
						</p>
					</div>
				</div>

				<div class="widget widget_text default-widget">
					<h6 class="widgettitle"><?php esc_html_e( 'Footer Widget', 'woostify' ); ?></h6>
					<div class="textwidget">
						<p>
							<?php
							printf(
								/* translators: 1: admin URL */
								__( 'Replace this widget content by going to <a href="%1$s"><strong>Appearance / Widgets / Footer Widget</strong></a> and dragging widgets into this widget area.', 'woostify' ),
								esc_url( admin_url( 'widgets.php' ) )
							);  // WPCS: XSS ok.
							?>
						</p>
					</div>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'woostify_footer_custom_text' ) ) {
	/**
	 * Footer custom text
	 *
	 * @return string $content Footer custom text
	 */
	function woostify_footer_custom_text() {
		$content = '&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . ' ';

		if ( apply_filters( 'woostify_credit_info', true ) ) {

			if ( apply_filters( 'woostify_privacy_policy_link', true ) && function_exists( 'the_privacy_policy_link' ) ) {
				$content .= get_the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
			}

			$content .= __( 'All rights reserved. Designed &amp; developed by woostify&trade;', 'woostify' );
		}

		return $content;
	}
}

if ( ! function_exists( 'woostify_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @return void
	 */
	function woostify_credit() {
		$options = woostify_options( false );
		if ( '' == $options['footer_custom_text'] && ! has_nav_menu( 'footer' ) ) {
			return;
		}
		?>
		<div class="site-info">
			<?php if ( '' != $options['footer_custom_text'] ) { ?>
				<div class="site-infor-col">
					<?php echo wp_kses_post( $options['footer_custom_text'] ); ?>
				</div>
			<?php } ?>

			<?php
			if ( has_nav_menu( 'footer' ) ) {
				echo '<div class="site-infor-col">';
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'menu_class'     => 'woostify-footer-menu',
						'container'      => '',
						'depth'          => 1,
					));
				echo '</div>';
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_site_branding' ) ) {
	/**
	 * Site branding wrapper and display
	 *
	 * @return void
	 */
	function woostify_site_branding() {
		// Default values.
		$class           = '';
		$mobile_logo_src = '';
		$options         = woostify_options( false );

		if ( '' != $options['logo_mobile'] ) {
			$mobile_logo_src = $options['logo_mobile'];
			$class           = 'has-custom-mobile-logo';
		}

		?>
		<div class="site-branding <?php echo esc_attr( $class ); ?>">
			<?php
			woostify_site_title_or_logo();

			// Custom mobile logo.
			if ( '' != $mobile_logo_src ) {
				$mobile_logo_id  = attachment_url_to_postid( $mobile_logo_src );
				$mobile_logo_alt = woostify_image_alt( $mobile_logo_id, __( 'Woostify mobile logo', 'woostify' ) );
				?>
					<a class="custom-mobile-logo-url" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url">
						<img class="custom-mobile-logo" src="<?php echo esc_url( $mobile_logo_src ); ?>" alt="<?php echo esc_attr( $mobile_logo_alt ); ?>" itemprop="logo">
					</a>
				<?php
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_replace_logo_attr' ) ) {
	/**
	 * Replace header logo.
	 *
	 * @param array  $attr Image.
	 * @param object $attachment Image obj.
	 * @param sting  $size Size name.
	 *
	 * @return array Image attr.
	 */
	function woostify_replace_logo_attr( $attr, $attachment, $size ) {

		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$options        = woostify_options( false );

		if ( $custom_logo_id == $attachment->ID ) {

			$attr['alt'] = woostify_image_alt( $custom_logo_id, __( 'Woostify logo', 'woostify' ) );

			$attach_data = array();
			if ( ! is_customize_preview() ) {
				$attach_data = wp_get_attachment_image_src( $attachment->ID, 'full' );

				if ( isset( $attach_data[0] ) ) {
					$attr['src'] = $attach_data[0];
				}
			}

			$file_type      = wp_check_filetype( $attr['src'] );
			$file_extension = $file_type['ext'];

			if ( 'svg' == $file_extension ) {
				$attr['width']  = '100%';
				$attr['height'] = '100%';
				$attr['class']  = 'woostify-logo-svg';
			}

			// Retina logo.
			$retina_logo = $options['retina_logo'];

			$attr['srcset'] = '';

			if ( $retina_logo ) {
				$cutom_logo     = wp_get_attachment_image_src( $custom_logo_id, 'full' );
				$cutom_logo_url = $cutom_logo[0];
				$attr['alt']    = woostify_image_alt( $custom_logo_id, __( 'Woostify retina logo', 'woostify' ) );

				// Replace logo src on IE.
				if ( 'ie' == woostify_browser_detection() ) {
					$attr['src'] = $retina_logo;
				}

				$attr['srcset'] = $cutom_logo_url . ' 1x, ' . $retina_logo . ' 2x';

			}
		}

		return apply_filters( 'woostify_replace_logo_attr', $attr );
	}
	add_filter( 'wp_get_attachment_image_attributes', 'woostify_replace_logo_attr', 10, 3 );
}

if ( ! function_exists( 'woostify_get_logo_image_url' ) ) {
	/**
	 * Get logo image url
	 *
	 * @param string $size The image size.
	 */
	function woostify_get_logo_image_url( $size = 'full' ) {
		$options   = woostify_options( false );
		$image_src = '';

		if ( $options['retina_logo'] ) {
			$image_src = $options['retina_logo'];
		} elseif ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$image_id  = get_theme_mod( 'custom_logo' );
			$image     = wp_get_attachment_image_src( $image_id, $size );
			$image_src = $image[0];
		}

		return $image_src;
	}
}

if ( ! function_exists( 'woostify_site_title_or_logo' ) ) {
	/**
	 * Display the site title or logo
	 *
	 * @param bool $echo Echo the string or return it.
	 * @return string
	 */
	function woostify_site_title_or_logo( $echo = true ) {
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			// Image logo.
			$logo = get_custom_logo();
			$html = is_home() ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
		} else {
			$tag = is_home() ? 'h1' : 'div';

			$html = '<' . esc_attr( $tag ) . ' class="beta site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></' . esc_attr( $tag ) . '>';
			$html .= '<span class="site-description">' . esc_html( get_bloginfo( 'description' ) ) . '</span>';
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // WPCS: XSS ok.
	}
}

if ( ! function_exists( 'woostify_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 */
	function woostify_primary_navigation() {
		// Customize disable primary menu.
		$options             = woostify_options( false );
		$header_primary_menu = $options['header_primary_menu'];

		if ( ! $header_primary_menu ) {
			return;
		}
		?>

		<div class="site-navigation">
			<?php do_action( 'woostify_before_main_nav' ); ?>

			<nav class="main-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'woostify' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_class'     => 'primary-navigation',
							'container'      => '',
						)
					);
				} elseif ( is_user_logged_in() ) {
					?>
					<a class="add-menu" href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"><?php esc_html_e( 'Add a Primary Menu', 'woostify' ); ?></a>
				<?php } ?>
			</nav>

			<?php do_action( 'woostify_after_main_nav' ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_skip_links' ) ) {
	/**
	 * Skip links
	 */
	function woostify_skip_links() {
		?>
		<a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_html_e( 'Skip to navigation', 'woostify' ); ?></a>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'woostify' ); ?></a>
		<?php
	}
}

if ( ! function_exists( 'woostify_breadcrumb' ) ) {
	/**
	 * Woostify breadcrumb
	 */
	function woostify_breadcrumb() {
		$page_id     = woostify_get_page_id();
		$options     = woostify_options( false );
		$breadcrumb  = $options['page_header_breadcrumb'];
		$container[] = 'woostify-breadcrumb';

		if ( class_exists( 'woocommerce' ) ) {
			if ( is_singular( 'product' ) ) {
				$breadcrumb  = $options['shop_single_breadcrumb'];
				$container[] = woostify_site_container();
			} elseif ( woostify_is_woocommerce_page() ) {
				$breadcrumb = $options['shop_page_breadcrumb'];
			}
		}

		$container = implode( $container, ' ' );

		if ( is_front_page() || false == $breadcrumb ) {
			return;
		}
		?>

		<nav class="<?php echo esc_attr( $container ); ?>" itemscope itemtype="http://schema.org/BreadcrumbList">
			<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a itemprop="item" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span itemprop="name"><?php echo esc_html( apply_filters( 'woostify_breadcrumb_home', get_bloginfo( 'name' ) ) ); ?></span>
				</a>
				<meta itemprop="position" content="1"></span>
			</span>

			<?php
			if ( class_exists( 'woocommerce' ) && is_singular( 'product' ) ) {
				$terms = get_the_terms( $page_id, 'product_cat' );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					?>
					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ); ?>">
							<span itemprop="name"><?php esc_html_e( 'Shop', 'woostify' ); ?></span>
						</a>
						<meta itemprop="position" content="2"></span>
					</span>

					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( get_term_link( $terms[0]->term_id, 'product_cat' ) ); ?>">
							<span itemprop="name"><?php echo esc_html( $terms[0]->name ); ?></span>
						</a>
						<meta itemprop="position" content="3"></span>
					</span>

					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( home_url( '/' ) ); ?>"></a>
						<span itemprop="name"><?php echo get_the_title( $page_id ); ?></span>
						<meta itemprop="position" content="4"></span>
					</span>
					<?php
				}
			} elseif ( is_single() ) {
				$cat = get_the_category();
				if ( ! empty( $cat ) && ! is_wp_error( $cat ) ) {
					?>
					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( get_permalink( $page_id ) ); ?>">
							<span itemprop="name"><?php esc_html_e( 'Blog', 'woostify' ); ?></span>
						</a>
						<meta itemprop="position" content="2"></span>
					</span>

					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( get_term_link( $cat[0]->term_id ) ); ?>">
							<span itemprop="name"><?php echo esc_html( $cat[0]->name ); ?></span>
						</a>
						<meta itemprop="position" content="3"></span>
					</span>

					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( home_url( '/' ) ); ?>"></a>
						<span itemprop="name"><?php echo get_the_title(); ?></span>
						<meta itemprop="position" content="4"></span>
					</span>
					<?php
				}
			} else {
				?>
					<span class="item-bread" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="<?php echo esc_url( home_url( '/' ) ); ?>"></a>
						<span itemprop="name">
							<?php
							if ( is_day() ) {
								/* translators: post date */
								printf( esc_html__( 'Daily Archives: %s', 'woostify' ), get_the_date() );
							} elseif ( is_month() ) {
								/* translators: post date */
								printf( esc_html__( 'Monthly Archives: %s', 'woostify' ), get_the_date( esc_html_x( 'F Y', 'monthly archives date format', 'woostify' ) ) );
							} elseif ( is_home() ) {
								echo esc_html( get_the_title( $page_id ) );
							} elseif ( is_author() ) {
								$author = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
								echo esc_html( $author->display_name );
							} elseif ( is_category() || is_tax() ) {
								single_term_title();
							} elseif ( is_year() ) {
								/* translators: post date */
								printf( esc_html__( 'Yearly Archives: %s', 'woostify' ), get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'woostify' ) ) );
							} elseif ( is_search() ) {
								esc_html_e( 'Search results: ', 'woostify' );
								echo get_search_query();
							} elseif ( class_exists( 'woocommerce' ) && is_shop() ) {
								esc_html_e( 'Shop', 'woostify' );
							} elseif ( class_exists( 'woocommerce' ) && ( is_product_tag() || is_tag() ) ) {
								esc_html_e( 'Tags: ', 'woostify' );
								single_tag_title();
							} elseif ( is_page() ) {
								echo get_the_title();
							} else {
								esc_html_e( 'Archives', 'woostify' );
							}
							?>
						</span>
						<meta itemprop="position" content="2"></span>
					</span>
				<?php
			}
			?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'woostify_page_header' ) ) {
	/**
	 * Display the page header
	 */
	function woostify_page_header() {
		// Not showing page title on Product page.
		if ( is_singular( 'product' ) ) {
			return;
		}

		$page_id       = woostify_get_page_id();
		$options       = woostify_options( false );
		$page_header   = $options['page_header_display'];
		$metabox       = woostify_get_metabox( false, 'site-page-header' );
		$title         = get_the_title( $page_id );
		$disable_title = $options['page_header_title'];

		$classes[]     = 'woostify-container';
		$classes[]     = 'content-align-' . $options['page_header_text_align'];
		$classes       = implode( $classes, ' ' );

		if ( class_exists( 'woocommerce' ) && is_shop() ) {
			if ( true != $options['shop_page_title'] ) {
				$disable_title = false;
			}
		} elseif ( is_archive() ) {
			$title = get_the_archive_title( $page_id );
		} elseif ( is_home() ) {
			$title = __( 'Blog', 'woostify' );
		} elseif ( is_search() ) {
			$title = __( 'Search', 'woostify' );
		} elseif ( is_404() ) {
			$disable_title = false;
		}

		// Metabox option.
		if ( 'default' != $metabox ) {
			if ( 'enabled' == $metabox ) {
				$page_header = true;
			} else {
				$page_header = false;
			}
		}

		if ( false == $page_header ) {
			return;
		}

		?>

		<div class="page-header">
			<div class="<?php echo esc_attr( $classes ); ?>">
				<?php do_action( 'woostify_page_header_start' ); ?>

				<?php if ( $disable_title ) { ?>
					<h1 class="entry-title"><?php echo wp_kses_post( $title ); ?></h1>
				<?php } ?>

				<?php
					/**
					 * Functions hooked in to woostify_page_header_end
					 *
					 * @hooked woostify_breadcrumb   - 10
					 */
					do_action( 'woostify_page_header_end' );
				?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_page_content' ) ) {
	/**
	 * Display the post content
	 */
	function woostify_page_content() {
		the_content();

		wp_link_pages(
			array(
				'before'      => '<div class="page-links">' . __( 'Pages:', 'woostify' ),
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			)
		);
	}
}

if ( ! function_exists( 'woostify_post_header_open' ) ) {
	/**
	 * Post header wrapper
	 *
	 * @return void
	 */
	function woostify_post_header_open() {
		?>
			<header class="entry-header">
		<?php
	}
}

if ( ! function_exists( 'woostify_post_header_close' ) ) {
	/**
	 * Post header wrapper close
	 *
	 * @return void
	 */
	function woostify_post_header_close() {
		?>
			</header>
		<?php
	}
}

if ( ! function_exists( 'woostify_get_post_thumbnail' ) ) {
	/**
	 * Get post thumbnail
	 *
	 * @var $size thumbnail size. thumbnail|medium|large|full|$custom
	 * @uses has_post_thumbnail()
	 * @uses the_post_thumbnail
	 * @param string  $size The post thumbnail size.
	 * @param boolean $echo Echo.
	 */
	function woostify_get_post_thumbnail( $size = 'full', $echo = true ) {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$image = '';

		ob_start();

		if ( ! is_single() ) {
			?>
			<div class="entry-header-item post-cover-image">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php the_post_thumbnail( $size ); ?>
				</a>
			</div>
		<?php } else { ?>
			<div class="entry-header-item post-cover-image">
				<?php the_post_thumbnail( $size ); ?>
			</div>
		<?php
		}

		$image = ob_get_clean();

		if ( $echo ) {
			echo $image; // WPCS XSS: ok.
		} else {
			return $image;
		}
	}
}

if ( ! function_exists( 'woostify_get_post_title' ) ) {
	/**
	 * Display the post header with a link to the single post
	 *
	 * @param boolean $echo Echo.
	 */
	function woostify_get_post_title( $echo = true ) {
		$title_tag = apply_filters( 'woostify_post_title_html_tag', 'h2' );

		$title = '<' . esc_attr( $title_tag ) . ' class="entry-header-item alpha entry-title">';
		$title .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
		$title .= get_the_title();
		$title .= '</a>';
		$title .= '</' . esc_attr( $title_tag ) . '>';

		if ( $echo ) {
			echo $title; // WPCS XSS: ok.
		} else {
			return $title;
		}
	}
}

if ( ! function_exists( 'woostify_get_post_structure' ) ) {
	/**
	 * Get post structure
	 *
	 * @param string  $option_name The option name.
	 * @param boolean $echo        Echo.
	 */
	function woostify_get_post_structure( $option_name, $echo = true ) {
		$output    = '';
		$options   = woostify_options( false );
		$meta_data = $options[ $option_name ];

		if ( ! $meta_data || empty( $meta_data ) ) {
			return $output;
		}

		$filter_key  = is_single() ? 'woostify_post_single_structure_' : 'woostify_post_structure_';
		$option_name = is_single() ? 'blog_single_post_meta' : 'blog_list_post_meta';

		foreach ( $meta_data as $key ) {
			switch ( $key ) {
				case 'image':
					$output .= woostify_get_post_thumbnail( 'full', false );
					break;
				case 'title-meta':
					$output .= woostify_get_post_title( false );
					break;
				case 'post-meta':
					$output .= woostify_get_post_meta( $option_name, false );
					break;
				default:
					$output = apply_filters( $filter_key . $key, $output );
					break;
			}
		}

		if ( $echo ) {
			echo $output; // WPCS XSS: ok.
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'woostify_get_post_meta' ) ) {
	/**
	 * Get output order post meta
	 *
	 * @param string  $option_name The option name.
	 * @param boolean $echo        Echo.
	 */
	function woostify_get_post_meta( $option_name, $echo = true ) {
		$output    = '';
		$options   = woostify_options( false );
		$meta_data = $options[ $option_name ];

		if ( ! $meta_data || empty( $meta_data ) ) {
			return $output;
		}

		$separator = apply_filters( 'woostify_post_meta_separator', '<span class="post-meta-separator">.</span>' );

		$output .= '<aside class="entry-header-item entry-meta">';

		foreach ( $meta_data as $key ) {
			switch ( $key ) {
				case 'date':
					$output .= woostify_post_meta_posted_on( false ) . $separator;
					break;
				case 'author':
					$output .= woostify_post_meta_author( false ) . $separator;
					break;
				case 'comments':
					$output .= woostify_post_meta_comments( false ) . $separator;
					break;
				case 'category':
					$output .= woostify_post_meta_category( false ) . $separator;
					break;
				default:
					$output = apply_filters( 'woostify_post_meta_' . $key, $output, $separator );
					break;
			}
		}

		$output .= '</aside>';

		if ( $echo ) {
			echo $output; // WPCS XSS: ok.
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'woostify_post_structure' ) ) {
	/**
	 * Display post structure
	 */
	function woostify_post_structure() {
		woostify_get_post_structure( 'blog_list_structure' );
	}
}

if ( ! function_exists( 'woostify_post_single_structure' ) ) {
	/**
	 * Display the single post structure
	 */
	function woostify_post_single_structure() {
		woostify_get_post_structure( 'blog_single_structure' );
	}
}

if ( ! function_exists( 'woostify_show_excerpt' ) ) {
	/**
	 * Show the blog excerpts or full posts
	 *
	 * @return bool $show_excerpt
	 */
	function woostify_show_excerpt() {
		global $post;

		// Check to see if the more tag is being used.
		$more_tag = apply_filters( 'woostify_more_tag', strpos( $post->post_content, '<!--more-->' ) );

		// Check the post format.
		$format = false != get_post_format() ? get_post_format() : 'standard';

		// If our post format isn't standard, show the full content.
		$show_excerpt = 'standard' != $format ? false : true;

		// If the more tag is found, show the full content.
		$show_excerpt = $more_tag ? false : $show_excerpt;

		// If we're on a search results page, show the excerpt.
		$show_excerpt = is_search() ? true : $show_excerpt;

		// Return our value.
		return apply_filters( 'woostify_show_excerpt', $show_excerpt );
	}
}

if ( ! function_exists( 'woostify_post_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 */
	function woostify_post_content() {

		do_action( 'woostify_post_content_before' );

		if ( woostify_show_excerpt() && ! is_single() ) {
			?>
				<div class="entry-summary summary-text">
					<?php
					the_excerpt();

					// Add 'Read More' button in Grid layout.
					$options = woostify_options( false );
					if ( 'grid' == $options['blog_list_layout'] ) {
						$read_more_text = apply_filters( 'woostify_read_more_text', __( 'Read More', 'woostify' ) );
						?>
						<span class="post-read-more">
							<a href="<?php the_permalink(); ?>">
								<?php echo esc_html( $read_more_text ); ?>
							</a>
						</span>
						<?php
					}
					?>
				</div>
			<?php
		} else {
			?>
			<div class="entry-content summary-text">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before'      => '<div class="page-links">' . __( 'Pages:', 'woostify' ),
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					)
				);

				/**
				 * Functions hooked in to woostify_post_content_after action
				 *
				 * @hooked woostify_post_read_more_button - 5
				 */
				do_action( 'woostify_post_content_after' );
				?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'woostify_post_read_more_button' ) ) {
	/**
	 * Display read more button
	 */
	function woostify_post_read_more_button() {
		if ( ! is_single() ) {
			$read_more_text = apply_filters( 'woostify_read_more_text', __( 'Read More', 'woostify' ) );
			?>

			<p class="post-read-more">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php echo esc_html( $read_more_text ); ?>
				</a>
			</p>
			<?php
		}
	}
}

if ( ! function_exists( 'woostify_post_tags' ) ) {
	/**
	 * Display post tags
	 */
	function woostify_post_tags() {
		$tags_list = get_the_tag_list( '<span class="label">' . esc_html__( 'Tags', 'woostify' ) . '</span>: ', __( ', ', 'woostify' ) );
		if ( $tags_list ) :
			?>
			<footer class="entry-footer">
				<div class="tags-links">
					<?php echo wp_kses_post( $tags_list ); ?>
				</div>
			</footer>
			<?php
		endif;
	}
}

if ( ! function_exists( 'woostify_paging_nav' ) ) {
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 */
	function woostify_paging_nav() {
		global $wp_query;

		$args = array(
			'type'      => 'list',
			'next_text' => _x( 'Next', 'Next post', 'woostify' ),
			'prev_text' => _x( 'Prev', 'Previous post', 'woostify' ),
		);

		the_posts_pagination( $args );
	}
}

if ( ! function_exists( 'woostify_post_nav' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 */
	function woostify_post_nav() {
		$args = array(
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'woostify' ) . ' </span>%title',
			'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'woostify' ) . ' </span>%title',
		);
		the_post_navigation( $args );
	}
}

if ( ! function_exists( 'woostify_post_author_box' ) ) {
	/**
	 * Display author box
	 */
	function woostify_post_author_box() {
		$options = woostify_options( false );
		if ( true != $options['blog_single_author_box'] ) {
			return;
		}

		$author_id   = get_the_author_meta( 'ID' );
		$author_avar = get_avatar_url( $author_id );
		$author_url  = get_author_posts_url( $author_id );
		$author_name = get_the_author_meta( 'nickname', $author_id );
		$author_bio  = get_the_author_meta( 'description', $author_id );
		?>

		<div class="post-author-box">
			<?php if ( $author_avar ) { ?>
				<a class="author-ava" href="<?php echo esc_url( $author_url ); ?>">
					<img src="<?php echo esc_url( $author_avar ); ?>" alt="<?php esc_attr_e( 'Author Avatar', 'woostify' ); ?>">
				</a>
			<?php } ?>

			<div class="author-content">
				<span class="author-name-before"><?php esc_html_e( 'Written by', 'woostify' ); ?></span>
				<a class="author-name" href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( $author_name ); ?></a>

				<?php if ( ! empty( $author_bio ) ) { ?>
					<div class="author-bio"><?php echo wp_kses_post( $author_bio ); ?></div>
				<?php } ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_post_meta_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @param boolean $echo Echo posted on.
	 */
	function woostify_post_meta_posted_on( $echo = true ) {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time> <time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = '<span class="sr-only">' . esc_html__( 'Posted on', 'woostify' ) . '</span>';
		$posted_on .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

		$data = wp_kses(
			apply_filters( 'woostify_single_post_posted_on_html', '<span class="post-meta-item posted-on">' . $posted_on . '</span>', $posted_on ), array(
				'span' => array(
					'class'  => array(),
				),
				'a'    => array(
					'href'  => array(),
					'title' => array(),
					'rel'   => array(),
				),
				'time' => array(
					'datetime' => array(),
					'class'    => array(),
				),
			)
		);

		if ( $echo ) {
			echo $data; // WPCS XSS: ok.
		} else {
			return $data;
		}
	}
}

if ( ! function_exists( 'woostify_post_meta_author' ) ) {
	/**
	 * Post meta author
	 *
	 * @param boolean $echo Echo author meta.
	 */
	function woostify_post_meta_author( $echo = true ) {
		$author = '<span class="post-meta-item vcard author">';
		if ( ! get_the_author() ) {
			$author .= esc_html_e( 'by Unknown author', 'woostify' );
		} else {
			$author .= '<span class="label">' . esc_html__( 'by', 'woostify' ) . '</span>';
			$author .= sprintf(
				' <a href="%1$s" class="url fn" rel="author">%2$s</a>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);
		}
		$author .= '</span>';

		if ( $echo ) {
			echo $author; // WPCS XSS: ok.
		} else {
			return $author;
		}
	}
}

if ( ! function_exists( 'woostify_post_meta_category' ) ) {
	/**
	 * Post meta category
	 *
	 * @param boolean $echo Echo post category.
	 */
	function woostify_post_meta_category( $echo = true ) {
		$categories = get_the_category_list( __( ', ', 'woostify' ) );
		if ( ! $categories ) {
			return;
		}

		$category = '<span class="post-meta-item cat-links">';
		$category .= '<span class="label sr-only">' . esc_html( __( 'Posted in', 'woostify' ) ) . '</span>';
		$category .= wp_kses_post( $categories );
		$category .= '</span>';

		if ( $echo ) {
			echo $category; // WPCS XSS: ok.
		} else {
			return $category;
		}
	}
}

if ( ! function_exists( 'woostify_post_meta_comments' ) ) {
	/**
	 * Post meta comment
	 *
	 * @param boolean $echo Echo post comment.
	 */
	function woostify_post_meta_comments( $echo = true ) {
		$comments = '';
		if ( post_password_required() || ! comments_open() ) {
			return $comments;
		}

		ob_start();
		?>

		<span class="post-meta-item comments-link">
		<?php
			comments_popup_link(
				__( 'No comments yet', 'woostify' ),
				__( '1 Comment', 'woostify' ),
				__( '% Comments', 'woostify' )
			);
		?>
		</span>

		<?php
		$comments = ob_get_clean();

		if ( $echo ) {
			echo $comments; // WPCS XSS: ok.
		} else {
			return $comments;
		}
	}
}

if ( ! function_exists( 'woostify_get_header_class' ) ) {
	/**
	 * Header class
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function woostify_get_header_class( $class = '' ) {
		$classes = array();

		$classes[] = 'site-header';

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );

		$classes = apply_filters( 'woostify_header_class', $classes, $class );

		return array_unique( $classes );
	}
}

if ( ! function_exists( 'woostify_header_class' ) ) {

	/**
	 * Display the classes for the header element.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function woostify_header_class( $class = '' ) {
		// Separates classes with a single space, collates classes for body element.
		echo 'class="' . join( ' ', woostify_get_header_class( $class ) ) . '"'; // WPCS: XSS ok.
	}
}

if ( ! function_exists( 'woostify_default_container_open' ) ) {
	/**
	 * Woostify default container open
	 */
	function woostify_default_container_open() {
		echo '<div class="woostify-container">';
	}
}

if ( ! function_exists( 'woostify_default_container_close' ) ) {
	/**
	 * Woostify default container close
	 */
	function woostify_default_container_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'woostify_container_open' ) ) {
	/**
	 * Woostify container open
	 */
	function woostify_container_open() {
		$container = woostify_site_container();
		echo '<div class="' . esc_attr( $container ) . '">';
	}
}

if ( ! function_exists( 'woostify_container_close' ) ) {
	/**
	 * Woostify container close
	 */
	function woostify_container_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'woostify_content_top' ) ) {
	/**
	 * Content top, after Header
	 */
	function woostify_content_top() {
		do_action( 'woostify_content_top' );
	}
}

if ( ! function_exists( 'woostify_content_top_open' ) ) {
	/**
	 * Woostify .content-top open
	 */
	function woostify_content_top_open() {
		echo '<div class="content-top">';
	}
}

if ( ! function_exists( 'woostify_content_top_close' ) ) {
	/**
	 * Woostify .content-top close
	 */
	function woostify_content_top_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'woostify_is_product_archive' ) ) {
	/**
	 * Checks if the current page is a product archive
	 *
	 * @return boolean
	 */
	function woostify_is_product_archive() {
		if ( ! class_exists( 'woocommerce' ) || ! woostify_is_woocommerce_activated() ) {
			return false;
		}

		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() || is_tax() ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'woostify_topbar_section' ) ) {
	/**
	 * Display topbar
	 */
	function woostify_topbar() {
		$options = woostify_options( false );
		$topbar  = woostify_get_metabox( false, 'site-topbar' );
		if ( 'disabled' == $topbar ) {
			return;
		}
		?>

		<div class="topbar">
			<div class="woostify-container">
				<div class="topbar-item topbar-left"><?php echo wp_kses_post( $options['topbar_left'] ); ?></div>
				<div class="topbar-item topbar-center"><?php echo wp_kses_post( $options['topbar_center'] ); ?></div>
				<div class="topbar-item topbar-right"><?php echo wp_kses_post( $options['topbar_right'] ); ?></div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_search' ) ) {
	/**
	 * Display Product Search
	 *
	 * @uses  woostify_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function woostify_search() {
		$options = woostify_options( false );
		if ( ! $options['header_search_icon'] ) {
			return;
		}
		?>

		<div class="site-search">
			<?php
			if ( false == $options['header_search_only_product'] ) {
				get_search_form();
			} elseif ( woostify_is_woocommerce_activated() ) {
				the_widget( 'WC_Widget_Product_Search', 'title=' );
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_dialog_search' ) ) {
	/**
	 * Display Dialog Search
	 *
	 * @uses  woostify_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function woostify_dialog_search() {
		$options = woostify_options( false );

		if ( false == $options['header_search_icon'] ) {
			return;
		}

		$image_icon = woostify_get_logo_image_url();
		$close_icon = apply_filters( 'woostify_dialog_search_close_icon', 'ti-close' );
		?>

		<div class="site-dialog-search">
			<div class="dialog-search-content">
				<?php do_action( 'woostify_dialog_search_content_start' ); ?>

				<div class="dialog-search-header">
					<?php if ( $image_icon ) { ?>
						<span class="dialog-search-icon">
							<img src="<?php echo esc_url( $image_icon ); ?>" alt="<?php esc_attr_e( 'Dialog search icon', 'woostify' ); ?>">
						</span>
					<?php } ?>

					<span class="dialog-search-title"><?php esc_html_e( 'Type to search', 'woostify' ); ?></span>

					<span class="dialog-search-close-icon <?php echo esc_attr( $close_icon ); ?>"></span>
				</div>

				<div class="dialog-search-main">
					<?php
					if ( woostify_is_woocommerce_activated() && $options['header_search_only_product'] ) {
						the_widget( 'WC_Widget_Product_Search', 'title=' );
					} else {
						get_search_form();
					}
					?>
				</div>

				<?php do_action( 'woostify_dialog_search_content_end' ); ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_product_check_in' ) ) {
	/**
	 * Check product already in cart || product quantity in cart
	 *
	 * @param      int     $pid          Product id.
	 * @param      boolean $in_cart      Check in cart.
	 * @param      boolean $qty_in_cart  Get product quantity.
	 */
	function woostify_product_check_in( $pid = null, $in_cart = true, $qty_in_cart = false ) {
		global $woocommerce;
		$_cart    = $woocommerce->cart->get_cart();
		$_product = wc_get_product( $pid );
		$variable = $_product->is_type( 'variable' );

		// Check product already in cart. Return boolean.
		if ( true == $in_cart ) {
			foreach ( $_cart as $key ) {
				$product_id = $key['product_id'];

				if ( $product_id == $pid ) {
					return true;
				}
			}

			return false;
		}

		// Get product quantity in cart. Return INT.
		if ( true == $qty_in_cart ) {
			if ( $variable ) {
				$arr = array();
				foreach ( $_cart as $key ) {
					if ( $key['product_id'] == $pid ) {
						$qty   = $key['quantity'];
						$arr[] = $qty;
					}
				}

				return array_sum( $arr );
			} else {
				foreach ( $_cart as $key ) {
					if ( $key['product_id'] == $pid ) {
						$qty = $key['quantity'];

						return $qty;
					}
				}
			}

			return 0;
		}
	}
}

if ( ! function_exists( 'woostify_get_sidebar_id' ) ) {
	/**
	 * Get sidebar by id
	 *
	 * @param      string $sidebar_id      The sidebar id.
	 * @param      string $sidebar_layout  The sidebar layout: left, right, full.
	 * @param      string $sidebar_default The sidebar layout default.
	 * @param      string $wc_sidebar      The woocommerce sidebar.
	 */
	function woostify_get_sidebar_id( $sidebar_id, $sidebar_layout, $sidebar_default, $wc_sidebar = false ) {

		$wc_sidebar_class      = true == $wc_sidebar ? ' woocommerce-sidebar' : '';
		$sidebar_layout_class  = 'full' == $sidebar_layout ? 'no-sidebar' : $sidebar_layout . '-sidebar has-sidebar' . $wc_sidebar_class;
		$sidebar_default_class = 'full' == $sidebar_default ? 'no-sidebar' : $sidebar_default . '-sidebar has-sidebar default-sidebar' . $wc_sidebar_class;

		if ( 'default' != $sidebar_layout ) {
			$sidebar = $sidebar_layout_class;
		} else {
			$sidebar = $sidebar_default_class;
		}

		return $sidebar;
	}
}

if ( ! function_exists( 'woostify_sidebar_class' ) ) {
	/**
	 * Get sidebar class
	 *
	 * @return string $sidebar Class name
	 */
	function woostify_sidebar_class() {
		// All theme options.
		$options         = woostify_options( false );

		// Metabox options.
		$metabox_sidebar = woostify_get_metabox( false, 'site-sidebar' );

		// Customize options.
		$sidebar             = '';
		$sidebar_default     = $options['sidebar_default'];
		$sidebar_page        = 'default' != $metabox_sidebar ? $metabox_sidebar : $options['sidebar_page'];
		$sidebar_blog        = 'default' != $metabox_sidebar ? $metabox_sidebar : $options['sidebar_blog'];
		$sidebar_blog_single = 'default' != $metabox_sidebar ? $metabox_sidebar : $options['sidebar_blog_single'];
		$sidebar_shop        = 'default' != $metabox_sidebar ? $metabox_sidebar : $options['sidebar_shop'];
		$sidebar_shop_single = 'default' != $metabox_sidebar ? $metabox_sidebar : $options['sidebar_shop_single'];

		if ( true == woostify_is_elementor_page() || is_404() ) {
			return $sidebar;
		}

		if ( true == woostify_is_product_archive() ) {
			// Product archive.
			$sidebar = woostify_get_sidebar_id( 'sidebar-shop', $sidebar_shop, $sidebar_default );
		} elseif ( is_singular( 'product' ) ) {
			// Product single.
			$sidebar = woostify_get_sidebar_id( 'sidebar-shop', $sidebar_shop_single, $sidebar_default );
		} elseif ( class_exists( 'woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			// Cart, checkout and account page.
			$sidebar = '';
		} elseif ( is_page() ) {
			// Page.
			$sidebar = woostify_get_sidebar_id( 'sidebar', $sidebar_page, $sidebar_default );
		} elseif ( is_singular( 'post' ) ) {
			// Blog page.
			$sidebar = woostify_get_sidebar_id( 'sidebar', $sidebar_blog_single, $sidebar_default );
		} else {
			// Other page.
			$sidebar = woostify_get_sidebar_id( 'sidebar', $sidebar_blog, $sidebar_default );
		}

		return $sidebar;
	}
}

if ( ! function_exists( 'woostify_get_sidebar' ) ) {
	/**
	 * Display woostify sidebar
	 *
	 * @uses get_sidebar()
	 */
	function woostify_get_sidebar() {
		$sidebar = woostify_sidebar_class();

		if ( false !== strpos( $sidebar, 'no-sidebar' ) || '' == $sidebar || true == woostify_is_elementor_page() ) {
			return;
		}

		if ( false !== strpos( $sidebar, 'woocommerce-sidebar' ) || true == woostify_is_product_archive() || is_singular( 'product' ) ) {
			get_sidebar( 'shop' );
		} else {
			get_sidebar();
		}
	}
}

if ( ! function_exists( 'woostify_menu_toggle_btn' ) ) {
	/**
	 * Menu toggle button
	 */
	function woostify_menu_toggle_btn() {
		$menu_toggle_icon  = apply_filters( 'woostify_header_menu_toggle_icon', 'woostify-icon-bar' );
		$woostify_icon_bar = apply_filters( 'woostify_header_icon_bar', '<span></span>' );
		?>
		<div class="wrap-toggle-sidebar-menu">
			<span class="toggle-sidebar-menu-btn <?php echo esc_attr( $menu_toggle_icon ); ?>">
				<?php echo wp_kses_post( $woostify_icon_bar ); ?>
			</span>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_overlay' ) ) {
	/**
	 * Woostify overlay
	 */
	function woostify_overlay() {
		echo '<div id="woostify-overlay"></div>';
	}
}

if ( ! function_exists( 'woostify_toggle_sidebar' ) ) {
	/**
	 * Toogle sidebar
	 */
	function woostify_toggle_sidebar() {
		do_action( 'woostify_toggle_sidebar' );
	}
}

if ( ! function_exists( 'woostify_sidebar_menu_open' ) ) {
	/**
	 * Sidebar menu open
	 */
	function woostify_sidebar_menu_open() {
		echo '<div class="sidebar-menu">';
	}
}

if ( ! function_exists( 'woostify_sidebar_menu_action' ) ) {
	/**
	 * Sidebar menu action
	 */
	function woostify_sidebar_menu_action() {
		if ( woostify_is_woocommerce_activated() ) {

			global $woocommerce;
			$page_account_id = get_option( 'woocommerce_myaccount_page_id' );
			$logout_url      = wp_logout_url( get_permalink( $page_account_id ) );

			if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
				$logout_url = str_replace( 'http:', 'https:', $logout_url );
			}
			?>
			<div class="sidebar-menu-bottom">
				<?php do_action( 'woostify_sidebar_account_before' ); ?>

				<ul class="sidebar-account">
					<?php do_action( 'woostify_sidebar_account_top' ); ?>

					<?php if ( ! is_user_logged_in() ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $page_account_id ) ); ?>"><?php esc_html_e( 'Login / Register', 'woostify' ); ?></a></li>
					<?php else : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $page_account_id ) ); ?>"><?php esc_html_e( 'Dashboard', 'woostify' ); ?></a>
						</li>
						<li><a href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Logout', 'woostify' ); ?></a>
						</li>
					<?php endif; ?>

					<?php do_action( 'woostify_sidebar_account_bottom' ); ?>
				</ul>

				<?php do_action( 'woostify_sidebar_account_after' ); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'woostify_sidebar_menu_close' ) ) {
	/**
	 * Sidebar menu close
	 */
	function woostify_sidebar_menu_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'woostify_wishlist_page_url' ) ) {
	/**
	 * Get YTH wishlist page url
	 */
	function woostify_wishlist_page_url() {
		if ( ! defined( 'YITH_WCWL' ) ) {
			return '#';
		}

		global $wpdb;
		$id = $wpdb->get_results( 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_content LIKE "%[yith_wcwl_wishlist]%" AND post_parent = 0' );

		if ( $id ) {
			$id  = intval( $id[0]->ID );
			$url = get_the_permalink( $id );

			return $url;
		}

		return '#';
	}
}

if ( ! function_exists( 'woostify_header_action' ) ) {
	/**
	 * Display header action
	 *
	 * @uses  woostify_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function woostify_header_action() {
		$options = woostify_options( false );

		if ( woostify_is_woocommerce_activated() ) {
			global $woocommerce;
			$page_account_id = get_option( 'woocommerce_myaccount_page_id' );
			$logout_url      = wp_logout_url( get_permalink( $page_account_id ) );

			if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
				$logout_url = str_replace( 'http:', 'https:', $logout_url );
			}

			$count = $woocommerce->cart->cart_contents_count;
		}

		$search_icon     = apply_filters( 'woostify_header_search_icon', 'ti-search' );
		$wishlist_icon   = apply_filters( 'woostify_header_wishlist_icon', 'ti-heart' );
		$my_account_icon = apply_filters( 'woostify_header_my_account_icon', 'ti-user' );
		$shop_bag_icon   = apply_filters( 'woostify_header_shop_bag_icon', 'ti-shopping-cart cart-icon-rotate' );
		?>

		<div class="site-tools">

			<?php do_action( 'woostify_site_tool_before_first_item' ); ?>

			<?php // Search icon. ?>
			<?php if ( true == $options['header_search_icon'] ) { ?>
				<span class="tools-icon header-search-icon <?php echo esc_attr( $search_icon ); ?>"></span>
			<?php } ?>

			<?php do_action( 'woostify_site_tool_before_second_item' ); ?>

			<?php // Wishlist icon. ?>
			<?php if ( defined( 'YITH_WCWL' ) && true == $options['header_wishlist_icon'] ) { ?>
				<a href="<?php echo esc_url( woostify_wishlist_page_url() ); ?>" class="tools-icon header-wishlist-icon <?php echo esc_attr( $wishlist_icon ); ?>"></a>
			<?php } ?>

			<?php do_action( 'woostify_site_tool_before_third_item' ); ?>

			<?php if ( woostify_is_woocommerce_activated() ) { ?>
				<?php // My account icon. ?>
				<?php if ( true == $options['header_account_icon'] ) { ?>
					<div class="tools-icon my-account">
						<a href="<?php echo esc_url( get_permalink( $page_account_id ) ); ?>" class="tools-icon my-account-icon <?php echo esc_attr( $my_account_icon ); ?>"></a>
						<div class="subbox">
							<ul>
								<?php if ( ! is_user_logged_in() ) : ?>
									<li><a href="<?php echo esc_url( get_permalink( $page_account_id ) ); ?>" class="text-center"><?php esc_html_e( 'Login / Register', 'woostify' ); ?></a></li>
								<?php else : ?>
									<li>
										<a href="<?php echo esc_url( get_permalink( $page_account_id ) ); ?>"><?php esc_html_e( 'Dashboard', 'woostify' ); ?></a>
									</li>
									<li><a href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Logout', 'woostify' ); ?></a>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				<?php } ?>

				<?php do_action( 'woostify_site_tool_before_fourth_item' ); ?>

				<?php // Shopping cart icon. ?>
				<?php if ( true == $options['header_shop_cart_icon'] ) { ?>
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="tools-icon shopping-bag-button <?php echo esc_attr( $shop_bag_icon ); ?>">
						<span class="shop-cart-count"><?php echo esc_html( $count ); ?></span>
					</a>
				<?php } ?>

				<?php do_action( 'woostify_site_tool_after_last_item' ); ?>
			<?php } ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_get_page_id' ) ) {
	/**
	 * Get page id
	 *
	 * @return int $page_id Page id
	 */
	function woostify_get_page_id() {
		$page_id = get_queried_object_id();

		if ( class_exists( 'woocommerce' ) && is_shop() ) {
			$page_id = get_option( 'woocommerce_shop_page_id' );
		}

		return $page_id;
	}
}

if ( ! function_exists( 'woostify_view_open' ) ) {
	/**
	 * Open #view
	 */
	function woostify_view_open() {
		?>
		<div id="view">
		<?php
	}
}

if ( ! function_exists( 'woostify_view_close' ) ) {
	/**
	 * Close #view
	 */
	function woostify_view_close() {
		?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_content_open' ) ) {
	/**
	 * Open #content
	 */
	function woostify_content_open() {
		?>
		<div id="content" class="site-content" tabindex="-1">
		<?php
	}
}

if ( ! function_exists( 'woostify_content_close' ) ) {
	/**
	 * Close #content
	 */
	function woostify_content_close() {
		?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woostify_site_container' ) ) {

	/**
	 * Woostify site container
	 *
	 * @return $container The site container
	 */
	function woostify_site_container() {
		$options   = woostify_options( false );
		$container = 'woostify-container';

		// Metabox.
		$page_id           = woostify_get_page_id();
		$metabox_container = woostify_get_metabox( false, 'site-container' );

		if ( 'default' != $metabox_container && 'full-width' == $metabox_container ) {
			$container = 'woostify-container container-fluid';
		} elseif ( 'default' == $metabox_container && 'full-width' == $options['default_container'] ) {
			$container = 'woostify-container container-fluid';
		}

		return $container;
	}
}

if ( ! function_exists( 'woostify_site_header' ) ) {
	/**
	 * Display header
	 */
	function woostify_site_header() {
		$header = woostify_get_metabox( false, 'site-header' );
		if ( 'disabled' == $header ) {
			return;
		}
		?>
			<header id="masthead" <?php woostify_header_class(); ?>>
				<div class="site-header-inner">
					<?php
						/**
						 * Functions hooked into woostify_site_header action
						 *
						 * @hooked woostify_container_open     - 0
						 * @hooked woostify_skip_links         - 5
						 * @hooked woostify_site_branding      - 20
						 * @hooked woostify_primary_navigation - 30
						 * @hooked woostify_header_action      - 50
						 * @hooked woostify_container_close    - 200
						 */
						do_action( 'woostify_site_header' );
					?>
				</div>
			</header>
		<?php
	}
}

if ( ! function_exists( 'woostify_after_header' ) ) {
	/**
	 * After header
	 */
	function woostify_after_header() {
		do_action( 'woostify_after_header' );
	}
}

if ( ! function_exists( 'woostify_before_footer' ) ) {
	/**
	 * After header
	 */
	function woostify_before_footer() {
		do_action( 'woostify_before_footer' );
	}
}

if ( ! function_exists( 'woostify_site_footer' ) ) {

	/**
	 * Woostify footer
	 */
	function woostify_site_footer() {
		// Customize disable footer.
		$options        = woostify_options( false );
		$footer_display = $options['footer_display'];

		// Metabox disable footer.
		$metabox_footer = woostify_get_metabox( false, 'site-footer' );
		if ( 'disabled' == $metabox_footer ) {
			$footer_display = false;
		}

		// Return.
		if ( false == $footer_display ) {
			return;
		}

		?>
			<footer id="colophon" class="site-footer">
				<div class="woostify-container">

					<?php
					/**
					 * Functions hooked in to woostify_footer action
					 *
					 * @hooked woostify_footer_widgets - 10
					 * @hooked woostify_credit         - 20
					 */
					do_action( 'woostify_footer_content' );
					?>

				</div>
			</footer>
		<?php
	}
}

if ( ! function_exists( 'woostify_footer_action' ) ) {
	/**
	 * Footer action
	 */
	function woostify_footer_action() {
		?>
		<div class="footer-action"><?php do_action( 'woostify_footer_action' ); ?></div>
		<?php
	}
}

if ( ! function_exists( 'woostify_after_footer' ) ) {
	/**
	 * After footer
	 */
	function woostify_after_footer() {
		do_action( 'woostify_after_footer' );
	}
}

if ( ! function_exists( 'woostify_scroll_to_top' ) ) {
	/**
	 * Scroll to top
	 */
	function woostify_scroll_to_top() {
		$options = woostify_options( false );
		if ( true != $options['scroll_to_top'] ) {
			return;
		}

		$icon = apply_filters( 'woostify_scroll_to_top_icon', 'ti-angle-up' );
		?>
		<span id="scroll-to-top" class="ft-action-item <?php echo esc_attr( $icon ); ?>" title="<?php esc_attr_e( 'Scroll To Top', 'woostify' ); ?>"></span>
		<?php
	}
}

