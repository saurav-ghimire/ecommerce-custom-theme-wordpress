<?php
if (!defined('ABSPATH')) {
  exit;
}
// Exit if accessed directly

class WWP_Product_Visibility
{


  /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

  /**
   * Property that holds the single main instance of WWP_Product_Visibility.
   *
   * @since 1.14
   * @access private
   * @var WWP_Product_Visibility
   */
  private static $_instance;

  /**
   * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
   *
   * @since 1.14
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
   * WWP_Product_Visibility constructor.
   *
   * @since 1.14
   * @access public
   *
   * @param array $dependencies Array of instance objects of all dependencies of WWP_Product_Visibility model.
   */
  public function __construct($dependencies = array())
  {

    if (isset($dependencies['WWP_Wholesale_Roles'])) {
      $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];
    }
  }

  /**
   * Ensure that only one instance of WWP_Product_Visibility is loaded or can be loaded (Singleton Pattern).
   *
   * @since 1.14
   * @access public
   *
   * @param array $dependencies Array of instance objects of all dependencies of WWP_Product_Visibility model.
   * @return WWP_Product_Visibility
   */
  public static function instance($dependencies)
  {

    if (!self::$_instance instanceof self) {
      self::$_instance = new self($dependencies);
    }

    return self::$_instance;
  }

  /**
   * Embed custom metabox with fields relating to wholesale role filter into the single product admin page.
   * This is to educate the user about the expanded feature for product visibility that we have in Premium.
   *
   * @since 1.14
   * @access public
   */
  public function add_product_wholesale_role_visibility_filter_fields()
  {
    if (!WWP_Helper_Functions::is_wwpp_active()) {
      global $post;
      $all_registered_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

      if ($post->post_type == 'product') {
        require_once WWP_VIEWS_PATH . 'backend/product/single/view-wwp-product-wholesale-role-visibility-filter.php';
      }
    }
  }

  /**
   * Ensure that only one instance of WWP_Product_Visibility is loaded or can be loaded (Singleton Pattern).
   *
   * @since 1.14
   * @access public
   * @deprecated: Will be remove on future versions
   *
   * @param array $dependencies Array of instance objects of all dependencies of WWP_Product_Visibility model.
   * @return WWP_Product_Visibility
   */
  public static function getInstance()
  {

    if (!self::$_instance instanceof self) {
      self::$_instance = new self;
    }

    return self::$_instance;
  }



  /*
    |--------------------------------------------------------------------------
    | Execute Model
    |--------------------------------------------------------------------------
     */

  /**
   * Execute model.
   *
   * @since 1.14
   * @access public
   */
  public function run()
  {

    // Add product visibility fields to the single product edit screen
    add_action('post_submitbox_misc_actions', array($this, 'add_product_wholesale_role_visibility_filter_fields'), 100);
  }
}
