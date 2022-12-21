<?php

namespace AgeGate\Admin\Post;

use Asylum\Utility\Language;
use AgeGate\Common\Settings;
use AgeGate\Common\Immutable\Constants as Immutable;

trait PostTrait
{
    private function restricted(array &$meta)
    {
        $settings = Settings::getInstance();

        $meta['restricted'] = ($settings->restrictionType === 'selected' && $meta['restrict']) || ($settings->restrictionType !== 'selected' && !$meta['bypass']);
        switch ($settings->restrictionType) {
            case 'selected':
                $meta['checked'] = ($meta['restrict']) ? 'checked' : 'unchecked';
                break;

            default:
                $meta['checked'] = ($meta['bypass']) ? 'checked' : 'unchecked';
                break;
        }
    }

    private function getMeta($postId)
    {
        $settings = Settings::getInstance();
        $language = Language::getInstance()->getObjectLanguage($postId);

        $default = $language ? $settings->$language['defaultAge'] ?? $settings->defaultAge : $settings->defaultAge;

        // TODO: Check post default age from Lang in WPML and WP-Multilang
        return [
            'age' => $settings->multiAge ? (get_post_meta($postId, Immutable::META_AGE, true) ?: $default) : $default,
            'bypass' => get_post_meta($postId, Immutable::META_BYPASS, true),
            'restrict' => get_post_meta($postId, Immutable::META_RESTRICT, true),
        ];
    }
}
