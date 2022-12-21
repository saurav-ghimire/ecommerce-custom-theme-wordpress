<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly

if ( ! class_exists( 'WWP_Admin_Custom_Fields_Variable_Product' ) ) {

	/**
	 * Model that houses logic  admin custom fields for variable products.
	 *
	 * @since 1.3.0
	 */
	class WWP_Admin_Custom_Fields_Variable_Product {

		/*
		|--------------------------------------------------------------------------
		| Class Properties
		|--------------------------------------------------------------------------
		 */

		/**
		 * Property that holds the single main instance of WWP_Admin_Custom_Fields_Variable_Product.
		 *
		 * @since 1.3.0
		 * @access private
		 * @var WWP_Admin_Custom_Fields_Variable_Product
		 */
		private static $_instance;

		/**
		 * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
		 *
		 * @since 1.3.0
		 * @access private
		 * @var WWP_Wholesale_Roles
		 */
		private $_wwp_wholesale_roles;

		/*
		|--------------------------------------------------------------------------
		| Class Methods
		|--------------------------------------------------------------------------
		 */

		/**
		 * WWP_Admin_Custom_Fields_Variable_Product constructor.
		 *
		 * @param array $dependencies Array of instance objects of all dependencies of WWP_Admin_Custom_Fields_Variable_Product model.
		 *
		 * @since 1.3.0
		 * @access public
		 *
		 */
		public function __construct( $dependencies ) {

			$this->_wwp_wholesale_roles = $dependencies[ 'WWP_Wholesale_Roles' ];

		}

		/**
		 * Ensure that only one instance of WWP_Admin_Custom_Fields_Variable_Product is loaded or can be loaded (Singleton Pattern).
		 *
		 * @param array $dependencies Array of instance objects of all dependencies of WWP_Admin_Custom_Fields_Variable_Product model.
		 *
		 * @return WWP_Admin_Custom_Fields_Variable_Product
		 * @since 1.3.0
		 * @access public
		 *
		 */
		public static function instance( $dependencies ) {

			if ( ! self::$_instance instanceof self ) {
				self::$_instance = new self( $dependencies );
			}

			return self::$_instance;

		}

		/*
		|------------------------------------------------------------------------------------------------------------------
		| Variable Product Custom Bulk Action ( Single Product Page )
		|------------------------------------------------------------------------------------------------------------------
		 */

		/**
		 * Add variation custom bulk action options.
		 *
		 * @since 1.2.3
		 * @since 1.3.0 Refactor codebase and move to its dedicated model.
		 * @access public
		 */
		public function add_variation_custom_wholesale_bulk_action_options() {

			$all_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

			ob_start(); ?>

            <optgroup label="<?php _e( 'Wholesale', 'woocommerce-wholesale-prices' ); ?>">

				<?php foreach ( $all_wholesale_roles as $role_key => $role ) { ?>
                    <option value="<?php echo $role_key; ?>_wholesale_price"><?php echo sprintf( __( 'Set wholesale prices (%1$s)', 'woocommerce-wholesale-prices' ), $role[ 'roleName' ] ); ?></option>
				<?php } ?>

				<?php do_action( 'wwp_custom_variation_bulk_action_options', $all_wholesale_roles ); ?>

            </optgroup>

			<?php do_action( 'wwp_custom_variation_group_bulk_action_options', $all_wholesale_roles ); ?>

			<?php echo ob_get_clean();

		}

		/**
		 * Execute variation custom bulk actions.
		 *
		 * @param string $bulk_action The current bulk action being executed.
		 * @param array $data Array of data passed.
		 * @param int $product_id Variable product id.
		 * @param array $variations Array of variation ids.
		 *
		 * @since 1.3.0 Refactor codebase and move to its own model.
		 * @since 1.6.4 Only set base currency price when setting bulk price set for variations (WWP-155).
		 * @access public
		 *
		 * @since 1.2.3
		 */
		public function execute_variation_custom_wholesale_bulk_actions( $bulk_action, $data, $product_id, $variations ) {

			if ( strpos( $bulk_action, '_wholesale_price' ) !== false && is_array( $variations ) && isset( $data[ 'value' ] ) ) {

				$all_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
				$wholesale_role      = str_replace( '_wholesale_price', '', $bulk_action );
				$wholesale_role_arr  = array( $wholesale_role => $all_wholesale_roles[ $wholesale_role ] );

				$variation_ids    = array();
				$wholesale_prices = array();

				foreach ( $variations as $variationId ) {

					$variation_ids[]    = $variationId;
					$wholesale_prices[] = $data[ 'value' ];

				}

				// We only set the base currency
				$this->save_wholesale_price_fields( $product_id, $wholesale_role_arr, $variation_ids, $wholesale_prices, true );

			}

		}

		/*
		|--------------------------------------------------------------------------
		| Wholesale Price Field
		|--------------------------------------------------------------------------
		 */

		/**
		 * Add wholesale custom price field to variable product edit page (on the variations section).
		 *
		 * @param int $loop Variation loop count.
		 * @param array $variation_data Array of variation data.
		 * @param WP_Post $variation Variation object.
		 *
		 * @since 1.0.0
		 * @since 1.2.0 Add integration with Aelia Currency Switcher Plugin.
		 * @since 1.3.0 Refactor codebase, and move to its own model.
         * @since 2.1.0 Added support for wholesale percentage discount.
		 *
		 */
		public function add_wholesale_price_fields( $loop, $variation_data, $variation ) {

			global $woocommerce, $post, $WOOCS, $woocommerce_wpml;

			$all_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

			// Get the variable product data manually
			// Don't rely on the variation data woocommerce supplied
			// There is a logic change introduced on 2.3 series where they only send variation data (or variation meta)
			// That is built in to woocommerce, so all custom variation meta added to a variable product don't get passed along
			$variable_product_meta = get_post_meta( $variation->ID );

			if ( WWP_ACS_Integration_Helper::aelia_currency_switcher_active() ) {
				?>

                <div class="wholesale-prices-options-group options-group" style="border-top: 1px solid #DDDDDD;">

                    <header class="form-row form-row-full">
                        <h4 style="font-size: 14px; margin: 10px 0;"><?php _e( 'Wholesale Prices', 'woocommerce-wholesale-prices' ); ?></h4>
                        <p style="margin:0; padding:0; line-height: 16px; font-style: italic; font-size: 13px;"><?php _e( 'Wholesale prices are set per role and currency.<br/><br/><b>Note:</b> Wholesale price must be set for the base currency to enable wholesale pricing for that role. The base currency will be used for conversion to other currencies that have no wholesale price explicitly set (Auto).', 'woocommerce-wholesale-prices' ); ?></p>
                    </header>

                    <div class="wholesale-price-per-role-and-country-accordion">
						<?php
						$woocommerce_currencies  = get_woocommerce_currencies(); // Get all woocommerce currencies
						$wacs_enabled_currencies = WWP_ACS_Integration_Helper::enabled_currencies(); // Get all active currencies
						$base_currency           = WWP_ACS_Integration_Helper::get_product_base_currency( $variation->ID ); // Get base currency. Product base currency ( if present ) or shop base currency

						foreach ( $all_wholesale_roles as $role_key => $role ) {
							?>

                            <h4><?php echo $role[ 'roleName' ]; ?></h4>
                            <div class="section-container">

								<?php
								// Get base currency wholesale price
								$wholesale_price = isset( $variable_product_meta[ $role_key . '_wholesale_price' ][ 0 ] ) ? $variable_product_meta[ $role_key . '_wholesale_price' ][ 0 ] : '';

								// Get base currency currency symbol
								$currency_symbol = get_woocommerce_currency_symbol( $base_currency );
								if ( array_key_exists( 'currency_symbol', $role ) && ! empty( $role[ 'currency_symbol' ] ) ) {
									$currency_symbol = $role[ 'currency_symbol' ];
								}

								$field_id    = $role_key . '_wholesale_prices[' . $loop . ']';
								$field_label = $woocommerce_currencies[ $base_currency ] . " (" . $currency_symbol . ") <em><b>Base Currency</b></em>";
								$field_desc  = sprintf( __( 'Only applies to users with the role of %1$s for %2$s currency', 'woocommerce-wholesale-prices' ), $role[ 'roleName' ], $woocommerce_currencies[ $base_currency ] . " (" . $currency_symbol . ")" );

								// Always put the base currency on top of the list
								WWP_Helper_Functions::wwp_woocommerce_wp_text_input( array(
									'id'          => $field_id,
									'class'       => $role_key . '_wholesale_price wholesale_price',
									'label'       => $field_label,
									'placeholder' => '',
									'desc_tip'    => true,
									'description' => $field_desc,
									'data_type'   => 'price',
									'value'       => $wholesale_price,
								) );

								foreach ( $wacs_enabled_currencies as $currency_code ) {

									if ( $currency_code == $base_currency ) {
										continue;
									}
									// Base currency already processed above

									$currency_symbol = get_woocommerce_currency_symbol( $currency_code );

									$wholesale_price_for_specific_currency = isset( $variable_product_meta[ $role_key . '_' . $currency_code . '_wholesale_price' ][ 0 ] ) ? $variable_product_meta[ $role_key . '_' . $currency_code . '_wholesale_price' ][ 0 ] : '';

									$field_id    = $role_key . '_' . $currency_code . '_wholesale_prices[' . $loop . ']';
									$field_label = $woocommerce_currencies[ $currency_code ] . " (" . $currency_symbol . ")";
									$field_desc  = sprintf( __( 'Only applies to users with the role of %1$s for %2$s currency', 'woocommerce-wholesale-prices' ), $role[ 'roleName' ], $woocommerce_currencies[ $currency_code ] . " (" . $currency_symbol . ")" ); ?>
                                    <div class="form-row form-row-full">
										<?php
										WWP_Helper_Functions::wwp_woocommerce_wp_text_input( array(
											'id'          => $field_id,
											'class'       => $role_key . '_wholesale_price wholesale_price',
											'label'       => $field_label,
											'placeholder' => 'Auto',
											'desc_tip'    => true,
											'description' => $field_desc,
											'data_type'   => 'price',
											'value'       => $wholesale_price_for_specific_currency,
										));
										?>
                                    </div>

								<?php } ?>

                            </div><!-- .section-container -->

						<?php } ?>

                    </div><!--.wholesale-price-per-role-and-country-accordion-->

                </div><!--.wholesale-prices-options-group-->

			<?php } else {

				$wwpp_active = is_plugin_active( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php' ) ? true : false; ?>

                <div class="wholesale-prices-options-group options-group" style="border-top: 1px solid #DDDDDD;">

                    <header class="form-row form-row-full">
                        <h4 style="font-size: 14px; margin: 10px 0;"><?php _e( 'Wholesale Prices', 'woocommerce-wholesale-prices' ); ?></h4>
                        <p style="margin:0px; padding:0px; line-height: 16px; font-style: italic; font-size: 13px;">
							<?php _e( 'Set a wholesale price for this product.', 'woocommerce-wholesale-prices' ); ?>
							<?php echo $wwpp_active ? '' : '<a href="#" class="price-levels">Add additional wholesale price levels.</a>'; ?>
                        </p>
                    </header>
					<?php
					foreach ( $all_wholesale_roles as $role_key => $role ) {

						$currency_symbol = get_woocommerce_currency_symbol();
						if ( array_key_exists( 'currency_symbol', $role ) && ! empty( $role[ 'currency_symbol' ] ) ) {
							$currency_symbol = $role[ 'currency_symbol' ];
						} ?>
						<?php
						$field_id    = $role_key . '_wholesale_prices[' . $loop . ']';
						$field_label = sprintf(__('Wholesale Price (%1$s)', 'woocommerce-wholesale-prices'), $currency_symbol);

						$field_desc = sprintf( __('Wholesale price for %1$s customers', 'woocommerce-wholesale-prices'), str_replace(array('Customer','Customers'),'',$role[ 'roleName' ]) );

                        $field_desc_fixed      = $field_desc;
                        $field_desc_percentage = sprintf( __('Wholesale price for %1$s customers <br> Note: Prices are shown up to 6 decimal places but may be calculated and stored at higher precision.', 'woocommerce-wholesale-prices'), str_replace(array('Customer','Customers'),'',$role[ 'roleName' ]) );

						$wholesale_price = isset( $variable_product_meta[ $role_key . '_wholesale_price' ][ 0 ] ) ? $variable_product_meta[ $role_key . '_wholesale_price' ][ 0 ] : '';

						/* Percentage Discount */
                        $wholesale_percentage_discount = get_post_meta( $variation->ID, $role_key . '_wholesale_percentage_discount', true );

                        if(metadata_exists('post', $variation->ID, $role_key.'_wholesale_percentage_discount')){
                            $discount_type = 'percentage';
                            $field_desc    = $field_desc_percentage;
                        }else{
                            $discount_type                 = 'fixed';
                            $wholesale_percentage_discount = '';
                        }?>

                        <div class="form-row form-row-full">
                            <div class="wwp-percentage-pricing variable resp-table">
                                <div class="rest-table-body">
                                    <div class="table-body-cell th" style="display: flex; margin-bottom: 5px;">
                                        <?php echo $role[ 'roleName' ]; ?>
                                    </div>
                                    <div class="table-body-cell variable">
                                        <?php 
                                        if( empty( $WOOCS ) && empty( $woocommerce_wpml ) ){
                                            WWP_Helper_Functions::woocommerce_wp_select( array(
                                                'id'                => "{$role_key}_wholesale_discount_type[{$loop}]",
                                                'class'             => 'wholesale_discount_type select',
                                                'label'             => __( 'Discount Type', 'woocommerce-wholesale-prices' ),
                                                'value'             => $discount_type,
                                                'options'           => array(
                                                    'fixed'      => __( 'Fixed', 'woocommerce-wholesale-prices' ),
                                                    'percentage' => __( 'Percentage', 'woocommerce-wholesale-prices' ),
                                                ),
                                                'desc_tip'          => true,
                                                'description'       => __( 'Choose Price Type <br>Fixed (default) <br> Percentage',
                                                    'woocommerce-wholesale-prices' ),
                                                'custom_attributes' => array(
                                                    'data-wholesale_role' => $role_key,
                                                    'data-loop_id'        => $loop,
                                                ),
                                            ) );
        
                                            WWP_Helper_Functions::wwp_woocommerce_wp_text_input( array(
                                                'id'                => "{$role_key}_wholesale_percentage_discount[{$loop}]",
                                                'class'             => 'wholesale_discount',
                                                'label'             => __( 'Discount (%)', 'woocommerce-wholesale-prices' ),
                                                'placeholder'       => '',
                                                'desc_tip'          => true,
                                                'description'       => __( 'The percentage amount discounted from the regular price', 'woocommerce-wholesale-prices' ),
                                                'data_type'         => 'price',
                                                'value'             => $wholesale_percentage_discount,
                                                'custom_attributes' => array(
                                                    'data-wholesale_role' => $role_key,
                                                    'data-loop_id'        => $loop,
                                                ),
                                            ) );
                                        }

                                        WWP_Helper_Functions::wwp_woocommerce_wp_text_input( array(
                                            'id'          => $field_id,
                                            'class'       => $role_key . '_wholesale_price wholesale_price',
                                            'label'       => $field_label,
                                            'placeholder' => '',
                                            'desc_tip'    => true,
                                            'description' => $field_desc,
                                            'data_type'   => 'price',
                                            'value'       => $wholesale_price,
                                            'custom_attributes' => array(
                                                'data-field_desc_fixed' => html_entity_decode($field_desc_fixed),
                                                'data-field_desc_percentage'=> html_entity_decode($field_desc_percentage),
                                            ),
                                        ) );
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

					<?php } ?>
                </div>

			<?php }

		}

		/**
		 * Save wholesale custom price field on variable products.
		 * Since WooCommerce 2.4.x series, they introduced a new button "Save Changes" on the variation tab of a variable product.
		 * This allows you to save the variations itself even if the main variable product isn't saved yet.
		 *
		 * @param int $post_id Product Id.
		 * @param array|null $wholesale_roles Array of wholesale roles to apply the wholesale price. If null, it will apply to all registered wholesale roles.
		 * @param int|null $variation_ids Variation Id or null.
		 * @param float|null $variation_wholesale_prices Variation wholesale price or null.
		 *
		 * @since 1.6.4 Only set base currency price when setting bulk price set for variations (WWP-155).
		 *
		 * @since 1.0.0
		 * @since 1.2.0 Add Aelia Currency Switcher Plugin Integration.
		 * @since 1.2.3 Add support for custom variations bulk actions.
		 * @since 1.3.0 Refactor codebase and move to dedicated model.
         * @since 2.1.0 Add support for wholesale percentage discount.
		 */
		public function save_wholesale_price_fields( $post_id, $wholesale_roles = null, $variation_ids = null, $variation_wholesale_prices = null, $skip_non_base_currency = false ) {

			if ( is_null( $wholesale_roles ) ) {
				$wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
			}

			if ( ( ! is_null( $variation_ids ) && ! is_null( $variation_wholesale_prices ) ) || ( isset( $_POST[ 'variable_post_id' ] ) && $_POST[ 'variable_post_id' ] ) ) {

				// We delete this meta in the beginning coz we are using add_post_meta, not update_post_meta below
				// If we don't delete this, the values will be stacked with the old values
				// Note: per role
				foreach ( $wholesale_roles as $role_key => $role ) {

					delete_post_meta( $post_id, $role_key . '_variations_with_wholesale_price' );

				}

				$variable_post_id = ! is_null( $variation_ids ) ? $variation_ids : $_POST[ 'variable_post_id' ];
				$max_loop         = max( array_keys( $variable_post_id ) );
				$thousand_sep     = get_option( 'woocommerce_price_thousand_sep' );
				$decimal_sep      = get_option( 'woocommerce_price_decimal_sep' );
                $discount_type    = '';

				$aelia_currency_switcher_active = WWP_ACS_Integration_Helper::aelia_currency_switcher_active();

				if ( $aelia_currency_switcher_active && ! $skip_non_base_currency ) {

					// Get all active currencies
					$wacs_enabled_currencies = WWP_ACS_Integration_Helper::enabled_currencies();

					foreach ( $wholesale_roles as $role_key => $role ) {

						foreach ( $wacs_enabled_currencies as $currency_code ) {

							for ( $i = 0; $i <= $max_loop; $i ++ ) {

								if ( ! isset( $variable_post_id[ $i ] ) ) {
									continue;
								}


								$variation_id = (int) $variable_post_id[ $i ];

                                $discount_type = $_POST[ $role_key . '_wholesale_discount_type' ];
							    $percentage_discount = $_POST[ $role_key . '_wholesale_percentage_discount' ];

								// Get base currency. Product base currency ( if present ) or shop base currency.
								// Note for the variation, note for the parent variable product
								$base_currency = WWP_ACS_Integration_Helper::get_product_base_currency( $variation_id );

								if ( $currency_code == $base_currency ) {

									// Base Currency
									$wholesale_prices = ! is_null( $variation_wholesale_prices )? $variation_wholesale_prices : ( isset( $_POST[ $role_key . '_wholesale_prices' ] ) != false ? $_POST[ $role_key . '_wholesale_prices' ] : $_POST[ $role_key . '_wholesale_price_hidden' ] );

									$wholesale_price_key = $role_key . '_wholesale_price';
									$is_base_currency    = true;

								} else {

									$wholesale_prices    = ! is_null( $variation_wholesale_prices ) ? $variation_wholesale_prices : $_POST[ $role_key . '_' . $currency_code . '_wholesale_prices' ];
									$wholesale_price_key = $role_key . '_' . $currency_code . '_wholesale_price';
									$is_base_currency    = false;

								}

								if ( isset( $wholesale_prices[ $i ] ) ) {
									$this->_save_variable_product_wholesale_price( $post_id, $variation_id, $role_key, $wholesale_prices[ $i ], $wholesale_price_key, $thousand_sep, $decimal_sep, $discount_type[ $i ], $percentage_discount[ $i ], $aelia_currency_switcher_active, $is_base_currency, $currency_code );
								}

							} // for ( $i = 0; $i <= $max_loop; $i++ )

						} // foreach( $wacs_enabled_currencies as $currency_code )

					}

				} else {

					foreach ( $wholesale_roles as $role_key => $role ) {

						$wholesale_prices = ! is_null( $variation_wholesale_prices )
							? $variation_wholesale_prices : $_POST[ $role_key . '_wholesale_prices' ];

						$wholesale_price_key = $role_key . '_wholesale_price';

						for ( $i = 0; $i <= $max_loop; $i ++ ) {

							if ( ! isset( $variable_post_id[ $i ] ) ) {
								continue;
							}

                            $variation_id        = (int) $variable_post_id[ $i ];
							$discount_type       = isset($_POST[ $role_key . '_wholesale_discount_type' ]) ? $_POST[ $role_key . '_wholesale_discount_type' ][ $i ] : null;
							$percentage_discount = isset($_POST[ $role_key . '_wholesale_percentage_discount' ]) ? $_POST[ $role_key . '_wholesale_percentage_discount' ][ $i ] : null;

							if ( isset( $wholesale_prices[ $i ] ) ) {
								$this->_save_variable_product_wholesale_price( $post_id, $variation_id, $role_key, $wholesale_prices[ $i ], $wholesale_price_key, $thousand_sep, $decimal_sep, $discount_type, $percentage_discount );
							}

						}

					}

				}

				/*
				 * The logic here is that we also check those variations that are not currently listed on the current page
				 * WC 2.4.x series introduce variations pagination, now if we don't check those other variations that are not listed
				 * currently coz they are on a different page, what will happen is we will only add on the $role_key . '_variations_with_wholesale_price'
				 * meta the variations that are currently listed on the current page.
				 */
				$main_variable_product = wc_get_product( $post_id );

				// Get other variations that are not currently displayed coz they are on another page
				$other_page_variations = array_diff( $main_variable_product->get_children(), $variable_post_id );

				if ( ! empty( $other_page_variations ) ) {

					foreach ( $wholesale_roles as $role_key => $role ) {

						foreach ( $other_page_variations as $variation_id ) {

							/*
							 * Code below on determining if other paged variations have wholesale pricing is already covers case
							 * if Aelia currency converter plugin is active. When Aelia plugin is active, we only need to check if wholesale price
							 * is set for the base currency to conclude that this variation have a wholesale price. Which the
							 * code below is already doing.
							 */

							$wholesale_price = get_post_meta( $variation_id, $role_key . '_wholesale_price', true );

							if ( is_numeric( $wholesale_price ) && $wholesale_price > 0 ) {

								add_post_meta( $post_id, $role_key . '_variations_with_wholesale_price', $variation_id );
								update_post_meta( $post_id, $role_key . '_have_wholesale_price', 'yes' );

							}

						}

					}

				}

			} // if ( isset( $_POST[ 'variable_post_id' ] ) && $_POST[ 'variable_post_id' ] )

			// Mark parent variable product if having wholesale price or not
			// We need to move this out here coz there will be instances that on variable product save, WooCommerce won't pass variations data if
			// variations tab on single variable product admin page is not opened. Therefore have wholesale price meta of parent variable product
			// wont have proper value (  )
			foreach ( $wholesale_roles as $role_key => $role ) {

				$post_meta = get_post_meta( $post_id, $role_key . '_variations_with_wholesale_price' );

				// WWPP-147 : Delete the meta that is set when setting discount on per product category level
				delete_post_meta( $post_id, $role_key . '_have_wholesale_price_set_by_product_cat' );

				if ( ! empty( $post_meta ) ) {
					update_post_meta( $post_id, $role_key . '_have_wholesale_price', 'yes' );
				} else {
					update_post_meta( $post_id, $role_key . '_have_wholesale_price', 'no' );
					do_action( 'wwp_set_have_wholesale_price_meta_prod_cat_wholesale_discount', $post_id, $role_key );
				}

			}

		}

		/**
		 * Save variable product wholesale price.
		 *
		 * @param int $variable_id Variable Id.
		 * @param int $variation_id Variation Id.
		 * @param string $wholesale_price_key Wholesale price key. Wholesale role key + '_wholesale_price'
		 * @param string $has_wholesale_price_key Has wholesale price key. Wholesale role key + '_have_wholesale_price'
		 * @param string $thousand_sep Thousand separator.
		 * @param string $decimal_sep Decimal separator.
		 * @param boolean $aelia_currency_switcher_active Flag that determines if aelia currency switcher is active or not.
		 * @param boolean $is_base_currency Flag that determines if this is a base currency.
		 * @param mixed $currency_code String of current currency code or null.
		 * @param string $discount_type Determines if price type is percentage or fixed price.
		 *
		 * @since 1.2.0
		 * @since 1.3.0 Refactor codebase and move to its dedicated model.
		 * @since 2.1.0 Added support for wholesale percentage discount
		 *
		 */
		private function _save_variable_product_wholesale_price( $variable_id, $variation_id, $role_key, $wholesale_price, $wholesale_price_key, $thousand_sep, $decimal_sep, $discount_type, $percentage_discount, $aelia_currency_switcher_active = false, $is_base_currency = false, $currency_code = null ) {

			/*
			 * Sanitize and properly format wholesale price.
			 * (This also supports comma as decimal separator currency format).
			 */
			$wholesale_price = trim( esc_attr( $wholesale_price ) );

			if ( $thousand_sep ) {
				$wholesale_price = str_replace( $thousand_sep, '', $wholesale_price );
			}

			if ( $decimal_sep ) {
				$wholesale_price = str_replace( $decimal_sep, '.', $wholesale_price );

                if( ! empty( $percentage_discount ) && $percentage_discount !== null ) {
                    $percentage_discount = str_replace( $decimal_sep, '.', $percentage_discount );
                }
			}

			if ( ! empty( $wholesale_price ) ) {

				if ( ! is_numeric( $wholesale_price ) ) {
					$wholesale_price = '';
				} elseif ( $wholesale_price < 0 ) {
					$wholesale_price = 0;
				} else {
					$wholesale_price = wc_format_decimal( $wholesale_price );
				}

			}

            if ( ! empty( $percentage_discount ) && $percentage_discount !== null ) {

				if ( ! is_numeric( $percentage_discount ) ) {
					$percentage_discount = '';
				} elseif ( $percentage_discount < 0 ) {
					$percentage_discount = 0;
				} else {
					$percentage_discount = wc_format_decimal( $percentage_discount );
				}

			}

			/*
			 * If it has valid wholesale price, attached current variation id to parent product (variable)
			 * $role_key . '_variations_with_wholesale_price' post meta. This meta of the parent variable product
			 * will be used later to determine if the parent variable product has wholesale price or not.
			 */
			if ( $aelia_currency_switcher_active ) {

				/*
				 * Only add current variation id to parent variable product $role_key . '_variations_with_wholesale_price' meta
				 * if this is the base currency. You see due to how Aelia Currency Switcher works, base currency is very important.
				 * Therefore only base currency wholesale price is used to determine if variation has wholesale price or not.
				 */
				if ( $is_base_currency ) {
					if ( is_numeric( $wholesale_price ) && $wholesale_price > 0 ) {
						add_post_meta( $variable_id, $role_key . '_variations_with_wholesale_price', $variation_id );
					}
				}

			} else {

				if ( is_numeric( $wholesale_price ) && $wholesale_price > 0 ) {
					add_post_meta( $variable_id, $role_key . '_variations_with_wholesale_price', $variation_id );
				}

			}

			$wholesale_price = wc_clean( apply_filters( 'wwp_before_save_variation_product_wholesale_price', $wholesale_price, $role_key, $variation_id, $variable_id, $aelia_currency_switcher_active, $is_base_currency, $currency_code ) );

			update_post_meta( $variation_id, $wholesale_price_key, $wholesale_price );

            if( $discount_type === 'percentage' ){
                update_post_meta( $variation_id, $role_key . '_wholesale_percentage_discount', $percentage_discount );
            }else{
                delete_post_meta( $variation_id, $role_key . '_wholesale_percentage_discount' );
            }

		}

		/**
		 * Hook on product ( variation ) deletion. Remove post meta variation id reference and reset have wholesale price on the parent product.
		 *
		 * @param int $variation_id Product ID.
		 *
		 * @since 1.7
         * @since 2.1.0 Added support for wholesale percentage discount
		 *
		 */
		public function variation_deletion( $variation_id ) {

			$product = wc_get_product( $variation_id );

			if ( $product instanceof WC_Product && $product->is_type( 'variation' ) ) {

				$variable_id                = $product->get_parent_id();
				$registered_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

				if ( $registered_wholesale_roles ) {

					foreach ( $registered_wholesale_roles as $role_key => $role ) {

						// Remove trace to variation with wholesale price since it will be deleted
						delete_post_meta( $variable_id, $role_key . '_variations_with_wholesale_price', $variation_id );

                        // Remove trace to variation whith percentage wholesale discount, since it will be deleted
                        delete_post_meta( $variable_id, $role_key . '_variations_with_percentage_discount', $variation_id );

                        // Remove variation percentage discount
                        delete_post_meta( $variation_id, $role_key . '_wholesale_percentage_discount' );

						// Update _have_wholesale_price meta
						$wholesale_variations = get_post_meta( $variable_id, $role_key . '_variations_with_wholesale_price' );
						update_post_meta( $variable_id, $role_key . '_have_wholesale_price', empty( $wholesale_variations ) ? 'no' : 'yes' );

					}

				}

			}

		}

		/**
		 * Execute model.
		 *
		 * @since 1.3.0
		 * @access public
		 */
		public function run() {

			// Variations custom wholesale bulk action
			add_action( 'woocommerce_variable_product_bulk_edit_actions', array($this, 'add_variation_custom_wholesale_bulk_action_options'), 10 );

			add_action( 'woocommerce_bulk_edit_variations', array($this,'execute_variation_custom_wholesale_bulk_actions'), 10, 4 );

			// Variations wholesale price
			add_action( 'woocommerce_product_after_variable_attributes', array($this,'add_wholesale_price_fields'), 10, 3 );

			add_action( 'woocommerce_process_product_meta_variable', array($this,'save_wholesale_price_fields'), 10, 1 );

			add_action( 'woocommerce_ajax_save_product_variations', array($this,'save_wholesale_price_fields'), 10, 1 ); // Via Ajax ( Introduced on WooCommerce 2.4 series )

			// Delete any variation reference from the variable meta
			add_action( 'before_delete_post', array( $this, 'variation_deletion' ), 10, 1 );
		}

	}

}