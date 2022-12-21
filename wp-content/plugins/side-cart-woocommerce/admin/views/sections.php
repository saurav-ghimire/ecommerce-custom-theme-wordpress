<?php

$sections = array(

	/* General TAB Sections */
	array(
		'title' => 'Main',
		'id' 	=> 'main',
		'tab' 	=> 'general',
	),

	array(
		'title' => 'Side Cart Header',
		'id' 	=> 'sc_head',
		'tab' 	=> 'general',
	),

	array(
		'title' => 'Side Cart Body',
		'id' 	=> 'sc_body',
		'tab' 	=> 'general',
	),


	array(
		'title' => 'Side Cart Footer',
		'id' 	=> 'sc_footer',
		'tab' 	=> 'general',
	),


	array(
		'title' => 'Texts',
		'id' 	=> 'texts',
		'tab' 	=> 'general',
		'desc' 	=> 'Leave text empty to remove element'
	),


	array(
		'title' => 'URLs',
		'id' 	=> 'urls',
		'tab' 	=> 'general',
	),


	array(
		'title' => 'Basket',
		'id' 	=> 'basket',
		'tab' 	=> 'general',
	),

	array(
		'title' => 'Suggested Products',
		'id' 	=> 'suggested_products',
		'tab' 	=> 'general',
		'pro' 	=> 'yes'
	),

	/* Style TAB Sections */
	array(
		'title' => 'Main',
		'id' 	=> 'sc_main',
		'tab' 	=> 'style',
	),

	array(
		'title' => 'Side Cart Basket',
		'id' 	=> 'sc_basket',
		'tab' 	=> 'style',
	),

	array(
		'title' => 'Side Cart Header',
		'id' 	=> 'sc_head',
		'tab' 	=> 'style',
	),

	array(
		'title' => 'Side Cart Body',
		'id' 	=> 'sc_body',
		'tab' 	=> 'style',
	),

	array(
		'title' => 'Side Cart Body ( Product )',
		'id' 	=> 'scb_product',
		'tab' 	=> 'style',
	),


	array(
		'title' => 'Side Cart Body ( Quantity )',
		'id' 	=> 'scb_qty',
		'tab' 	=> 'style',
		'pro' 	=> 'yes'
	),

	array(
		'title' => 'Side Cart Footer',
		'id' 	=> 'sc_footer',
		'tab' 	=> 'style',
	),

	array(
		'title' => 'Suggested Products',
		'id' 	=> 'sc_sug_products',
		'tab' 	=> 'style',
		'pro' 	=> 'yes'
	),

	/* Custom CSS TAB Sections */
	array(
		'title' => 'Main',
		'id' 	=> 'av_main',
		'tab' 	=> 'advanced',
	),
);

return apply_filters( 'xoo_wsc_admin_settings_sections', $sections );