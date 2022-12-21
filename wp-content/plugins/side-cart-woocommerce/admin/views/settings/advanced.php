<?php

$settings = array(

	array(
		'callback' 		=> 'checkbox',
		'title' 		=> 'Refresh Cart',
		'id' 			=> 'm-refresh-cart',
		'section_id' 	=> 'av_main',
		'default' 		=> 'no',
		'desc' 			=> '<b>NOTE - Enable this option only if the cart is not showing correct prices.</b>'
	),

	array(
		'callback' 		=> 'textarea',
		'title' 		=> 'Custom CSS',
		'id' 			=> 'm-custom-css',
		'section_id' 	=> 'av_main',
		'default' 		=> '',
		'args' 			=> array(
			'rows' => 20,
			'cols' => 70
		)
	),

);


return apply_filters( 'xoo_wsc_admin_settings', $settings, 'advanced' );

?>