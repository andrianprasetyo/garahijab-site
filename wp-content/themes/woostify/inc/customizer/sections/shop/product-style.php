<?php
/**
 * Woocommerce shop product style customizer
 *
 * @package woostify
 */

// Default values.
$defaults = woostify_options();

// Header layout.
$wp_customize->add_setting(
	'woostify_setting[product_style]',
	array(
		'default'           => $defaults['product_style'],
		'sanitize_callback' => 'woostify_sanitize_choices',
		'type'              => 'option',
	)
);
$wp_customize->add_control(
	new Woostify_Radio_Image_Control(
		$wp_customize,
		'woostify_setting[product_style]',
		array(
			'label'    => __( 'Layout', 'woostify' ),
			'section'  => 'woostify_product_style',
			'settings' => 'woostify_setting[product_style]',
			'priority' => 5,
			'choices'  => apply_filters(
				'woostify_setting_product_style_choices',
				array(
					'layout-1' => WOOSTIFY_THEME_URI . 'assets/images/customizer/product-style/woostify-product-card-1.jpg',
				)
			),
		)
	)
);

// Always show add to cart button.
$wp_customize->add_setting(
	'woostify_setting[product_style_defaut_add_to_cart]',
	array(
		'type'              => 'option',
		'default'           => $defaults['product_style_defaut_add_to_cart'],
		'sanitize_callback' => 'woostify_sanitize_checkbox',
	)
);
$wp_customize->add_control(
	new WP_Customize_Control(
		$wp_customize,
		'woostify_setting[product_style_defaut_add_to_cart]',
		array(
			'type'     => 'checkbox',
			'label'    => __( 'Always Show Add To Cart', 'woostify' ),
			'section'  => 'woostify_product_style',
			'settings' => 'woostify_setting[product_style_defaut_add_to_cart]',
		)
	)
);
