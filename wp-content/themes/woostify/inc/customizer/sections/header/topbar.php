<?php
/**
 * Topbar
 *
 * @package woostify
 */

// Default values.
$defaults = woostify_options();

// Topbar color.
$wp_customize->add_setting(
	'woostify_setting[topbar_text_color]',
	array(
		'default'           => $defaults['topbar_text_color'],
		'sanitize_callback' => 'sanitize_hex_color',
		'type'              => 'option',
		'transport'         => 'postMessage',
	)
);
$wp_customize->add_control(
	new WP_Customize_Color_Control(
		$wp_customize,
		'woostify_setting[topbar_text_color]',
		array(
			'label'    => __( 'Text Color', 'woostify' ),
			'section'  => 'woostify_topbar',
			'settings' => 'woostify_setting[topbar_text_color]',
		)
	)
);

// Background color.
$wp_customize->add_setting(
	'woostify_setting[topbar_background_color]',
	array(
		'default'           => $defaults['topbar_background_color'],
		'sanitize_callback' => 'sanitize_hex_color',
		'type'              => 'option',
		'transport'         => 'postMessage',
	)
);
$wp_customize->add_control(
	new Woostify_Color_Control(
		$wp_customize,
		'woostify_setting[topbar_background_color]',
		array(
			'label'    => __( 'Background Color', 'woostify' ),
			'section'  => 'woostify_topbar',
			'settings' => 'woostify_setting[topbar_background_color]',
		)
	)
);

// Space.
$wp_customize->add_setting(
	'woostify_setting[topbar_space]',
	array(
		'default'           => $defaults['topbar_space'],
		'sanitize_callback' => 'absint',
		'type'              => 'option',
		'transport'         => 'postMessage',
	)
);
$wp_customize->add_control(
	new Woostify_Range_Slider_Control(
		$wp_customize,
		'woostify_setting[topbar_space]',
		array(
			'label'    => __( 'Space', 'woostify' ),
			'section'  => 'woostify_topbar',
			'settings' => array(
				'desktop' => 'woostify_setting[topbar_space]',
			),
			'choices' => array(
				'desktop' => array(
					'min'  => apply_filters( 'woostify_topbar_min_step', 0 ),
					'max'  => apply_filters( 'woostify_topbar_max_step', 50 ),
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
		)
	)
);

// Content divider.
$wp_customize->add_setting(
	'topbar_content_divider',
	array(
		'sanitize_callback' => 'sanitize_text_field',
	)
);
$wp_customize->add_control(
	new Woostify_Divider_Control(
		$wp_customize,
		'topbar_content_divider',
		array(
			'section'  => 'woostify_topbar',
			'settings' => 'topbar_content_divider',
			'type'     => 'divider',
		)
	)
);

// Topbar left.
$wp_customize->add_setting(
	'woostify_setting[topbar_left]',
	array(
		'default'           => $defaults['topbar_left'],
		'sanitize_callback' => 'woostify_sanitize_raw_html',
		'type'              => 'option',
		'transport'         => 'postMessage',
	)
);
$wp_customize->add_control(
	new WP_Customize_Control(
		$wp_customize,
		'woostify_setting[topbar_left]',
		array(
			'label'    => __( 'Content Left', 'woostify' ),
			'section'  => 'woostify_topbar',
			'settings' => 'woostify_setting[topbar_left]',
			'type'     => 'textarea',
		)
	)
);

// Topbar center.
$wp_customize->add_setting(
	'woostify_setting[topbar_center]',
	array(
		'default'           => $defaults['topbar_center'],
		'sanitize_callback' => 'woostify_sanitize_raw_html',
		'type'              => 'option',
		'transport'         => 'postMessage',
	)
);
$wp_customize->add_control(
	new WP_Customize_Control(
		$wp_customize,
		'woostify_setting[topbar_center]',
		array(
			'label'    => __( 'Content Center', 'woostify' ),
			'section'  => 'woostify_topbar',
			'settings' => 'woostify_setting[topbar_center]',
			'type'     => 'textarea',
		)
	)
);

// Topbar right.
$wp_customize->add_setting(
	'woostify_setting[topbar_right]',
	array(
		'default'           => $defaults['topbar_right'],
		'sanitize_callback' => 'woostify_sanitize_raw_html',
		'type'              => 'option',
		'transport'         => 'postMessage',
	)
);
$wp_customize->add_control(
	new WP_Customize_Control(
		$wp_customize,
		'woostify_setting[topbar_right]',
		array(
			'label'    => __( 'Content Right', 'woostify' ),
			'section'  => 'woostify_topbar',
			'settings' => 'woostify_setting[topbar_right]',
			'type'     => 'textarea',
		)
	)
);
