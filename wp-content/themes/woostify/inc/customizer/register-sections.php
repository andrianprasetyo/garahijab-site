<?php
/**
 * Register customizer panels & sections.
 *
 * @package     Woostify
 */

// LAYOUT.
$layout_sections = apply_filters(
	'woostify_customizer_layout_sections',
	array(
		'woostify_topbar'             => __( 'Topbar', 'woostify' ),
		'woostify_header'             => __( 'Normal Header', 'woostify' ),
		'woostify_header_transparent' => __( 'Header Transparent', 'woostify' ),
		'woostify_page_header'        => __( 'Page Header', 'woostify' ),
		'woostify_blog'               => __( 'Blog', 'woostify' ),
		'woostify_blog_single'        => __( 'Blog Single', 'woostify' ),
		'woostify_sidebar'            => __( 'Sidebar', 'woostify' ),
		'woostify_footer'             => __( 'Footer', 'woostify' ),
		'woostify_error'              => __( '404', 'woostify' ),
	)
);

$wp_customize->add_panel(
	'woostify_layout',
	array(
		'title'      => __( 'Layout', 'woostify' ),
		'priority'   => 30,
	)
);

foreach ( $layout_sections as $id => $name ) {
	$wp_customize->add_section(
		$id,
		array(
			'title' => $name,
			'panel' => 'woostify_layout',
		)
	);
}

// COLORS.
$wp_customize->add_section(
	'woostify_color',
	array(
		'title'    => __( 'Color', 'woostify' ),
		'priority' => 30,
	)
);

// BUTTONS.
$wp_customize->add_section(
	'woostify_buttons',
	array(
		'title'    => __( 'Buttons', 'woostify' ),
		'priority' => 30,
	)
);

// TYPOGRAPHY.
$wp_customize->add_panel(
	'woostify_typography',
	array(
		'title'      => __( 'Typography', 'woostify' ),
		'priority'   => 35,
	)
);

// Body.
$wp_customize->add_section(
	'body_font_section',
	array(
		'title'      => __( 'Body', 'woostify' ),
		'panel'      => 'woostify_typography',
	)
);

// Primary menu.
$wp_customize->add_section(
	'menu_font_section',
	array(
		'title'      => __( 'Primary menu', 'woostify' ),
		'panel'      => 'woostify_typography',
	)
);

// Heading.
$wp_customize->add_section(
	'heading_font_section',
	array(
		'title'      => __( 'Heading', 'woostify' ),
		'panel'      => 'woostify_typography',
	)
);

// WOOCOMMERCE.
$wp_customize->add_section(
	'woostify_product_style',
	array(
		'title'      => __( 'Product Style', 'woostify' ),
		'panel'      => 'woocommerce',
	)
);

$wp_customize->add_section(
	'woostify_shop_page',
	array(
		'title'      => __( 'Shop Archive', 'woostify' ),
		'panel'      => 'woocommerce',
	)
);

$wp_customize->add_section(
	'woostify_shop_single',
	array(
		'title'      => __( 'Product Single', 'woostify' ),
		'panel'      => 'woocommerce',
	)
);
