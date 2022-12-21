<?php defined('ABSPATH') || exit;

class WWP_WWS_Review {

    /**
     * WC Admin Note unique name
     * @since 1.11.5
     */
    const NOTE_NAME = 'wc-admin-wwp-wws-review';

    /**
     * WWP_WWS_Review constructor.
     *
     * @since 1.11.5
     * @access public
     */
    public function __construct() {

        add_action('plugins_loaded', array($this, 'review_wws'), 11);

    }

    /**
     * Review WWS WC Admin Note.
     * Condition: Only add note if WWP_REVIEW_REQUEST_RESPONSE is equal to 'never-show' or WWP_REVIEW_REQUEST_RESPONSE is equal to 'review-later'
     *
     * @since 1.11.5
     * @access public
     */
    public function review_wws() {

        // If WC Admin is not active then don't proceed
        if (!WWP_Helper_Functions::is_wc_admin_active()) {
            return;
        }

        if (get_option(WWP_REVIEW_REQUEST_RESPONSE) === 'never-show' || get_option(WWP_REVIEW_REQUEST_RESPONSE) === 'review-later') {

            try {

                $data_store = \WC_Data_Store::load('admin-note');

                // We already have this note? Then exit, we're done.
                $note_ids = $data_store->get_notes_with_name(self::NOTE_NAME);
                if (!empty($note_ids)) {
                    return;
                }

                $review_link = 'https://goo.gl/FVRQcH';
                $note_content = __(
                    'We notice you\'ve been using Wholesale Prices for a couple of weeks now. We\'d love to get your review on our plugin listing! Your review helps give others the confidence to try our plugin.',
                    'woocommerce-wholesale-prices'
                );

                $note = WWP_Helper_Functions::wc_admin_note_instance();
                $note->set_title(__('Review Wholesale Prices', 'woocommerce-wholesale-prices'));
                $note->set_content($note_content);
                $note->set_content_data((object) array());
                $note->set_type($note::E_WC_ADMIN_NOTE_INFORMATIONAL);
                $note->set_name(self::NOTE_NAME);
                $note->set_source('woocommerce-admin');
                $note->add_action('review-wws', __('Review Wholesale Prices', 'woocommerce-wholesale-prices'), $review_link, $note::E_WC_ADMIN_NOTE_ACTIONED, true);
                $note->save();

            } catch (Exception $e) {
                return;
            }

        }

    }

}

return new WWP_WWS_Review();
