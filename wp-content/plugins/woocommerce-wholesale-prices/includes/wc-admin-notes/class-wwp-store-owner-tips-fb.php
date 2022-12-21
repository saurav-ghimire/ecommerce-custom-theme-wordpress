<?php defined('ABSPATH') || exit;

class WWP_Store_Owner_Tips {

    /**
     * WC Admin Note unique name
     * @since 1.11.5
     */
    const NOTE_NAME = 'wc-admin-wwp-join-store-owner-tips';

    /**
     * Cron hook to be fired
     * @since 1.11.5
     */
    const CRON_HOOK = 'wwp_wc_admin_note_join_store_owner_tips';

    /**
     * WWP_Store_Owner_Tips constructor.
     *
     * @since 1.11.5
     * @access public
     */
    public function __construct() {

        add_action(self::CRON_HOOK, array($this, 'join_store_owner_tips_note'));

    }

    /**
     * Init cron hook to be fired after 30 days since activation.
     *
     * @since 1.11.5
     * @access public
     */
    public static function init_cron_hook() {

        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_single_event(strtotime('+45 days'), self::CRON_HOOK);
        }

    }

    /**
     * Join Store Owner Tips on FB WC Admin Note.
     *
     * @since 1.11.5
     * @access public
     */
    public function join_store_owner_tips_note() {

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

            $join_store_owner_tips = 'https://www.facebook.com/groups/storeownertips/';

            $note_content = __(
                'Want tips on how to grow your store? Come and join the Store Owner Tips Facebook group. Tips, articles and more, just for store owners.',
                'woocommerce-wholesale-prices'
            );

            $note = WWP_Helper_Functions::wc_admin_note_instance();
            $note->set_title(__('Join Store Owner Tips Facebook Group', 'woocommerce-wholesale-prices'));
            $note->set_content($note_content);
            $note->set_content_data((object) array());
            $note->set_type($note::E_WC_ADMIN_NOTE_INFORMATIONAL);
            $note->set_name(self::NOTE_NAME);
            $note->set_source('woocommerce-admin');
            $note->add_action('join-store-owner-tips', __('Join Store Owner Tips on Facebook', 'woocommerce-wholesale-prices'), $join_store_owner_tips, $note::E_WC_ADMIN_NOTE_ACTIONED, true);
            $note->save();

        } catch (Exception $e) {
            return;
        }

    }

}

return new WWP_Store_Owner_Tips();
