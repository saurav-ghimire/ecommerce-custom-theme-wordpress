<?php defined('ABSPATH') || exit;

class WWP_WWS_Youtube {

    /**
     * WC Admin Note unique name
     * @since 1.11.5
     */
    const NOTE_NAME = 'wc-admin-wwp-wws-youtube';

    /**
     * Cron hook to be fired
     * @since 1.11.5
     */
    const CRON_HOOK = 'wwp_wc_admin_note_wws_youtube';

    /**
     * WWP_WWS_Youtube constructor.
     *
     * @since 1.11.5
     * @access public
     */
    public function __construct() {

        add_action(self::CRON_HOOK, array($this, 'follow_wws_youtube'));

    }

    /**
     * Init cron hook to be fired after 30 days since activation.
     *
     * @since 1.11.5
     * @access public
     */
    public static function init_cron_hook() {

        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_single_event(strtotime('+60 days'), self::CRON_HOOK);
        }

    }

    /**
     * Follow WWS Youtube WC Admin Note.
     *
     * @since 1.11.5
     * @access public
     */
    public function follow_wws_youtube() {

        // If WC Admin is not active then don't proceed
        if (!WWP_Helper_Functions::is_wc_admin_active()) {
            return;
        }

        try {

            $data_store = \WC_Data_Store::load('admin-note');

            // We already have this note? Then exit, we're done.
            $note_ids = $data_store->get_notes_with_name(self::NOTE_NAME);
            if (!empty($note_ids)) {
                return;
            }

            $wws_youtube = 'https://www.youtube.com/channel/UCKo77z7250n2AvwLNycweUQ/?sub_confirmation=1';
            $update_url = htmlspecialchars_decode(wp_nonce_url(admin_url() . 'update.php?action=install-plugin&plugin=advanced-coupons-for-woocommerce-free', 'install-plugin_advanced-coupons-for-woocommerce-free'));

            $note_content = __(
                'Get all the wholesale tips & more at the Wholesale Suite Youtube channel. Click here to join.',
                'woocommerce-wholesale-prices'
            );

            $note = WWP_Helper_Functions::wc_admin_note_instance();
            $note->set_title(__('Follow Wholesale Suite on Youtube', 'woocommerce-wholesale-prices'));
            $note->set_content($note_content);
            $note->set_content_data((object) array());
            $note->set_type($note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('woocommerce-admin');
            $note->add_action('wws-youtube', __('Wholesale Suite Youtube Channel', 'woocommerce-wholesale-prices'), $wws_youtube, $note::E_WC_ADMIN_NOTE_ACTIONED, true);
            $note->save();

        } catch (Exception $e) {
            return;
        }

    }

}

return new WWP_WWS_Youtube();
