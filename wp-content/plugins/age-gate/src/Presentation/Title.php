<?php

namespace AgeGate\Presentation;

use AgeGate\Common\Content;
use AgeGate\Common\Settings;

class Title
{
    private $settings;

    private $content;

    public function __construct(Content $content, Settings $settings)
    {
        $this->settings = $settings;
        $this->content = $content;

        if ($settings->method !== 'js' && $settings->switchTitle) {
            add_filter('wpseo_title', [$this, 'returnPageTitle'], 1000, 1);
            add_filter('document_title_parts', [$this, 'changePageTitle'], 1000, 1);
            add_filter('wp_title', [$this, 'changeDefaultTitle'], 10, 3);
        }

    }

    public function getTitle()
    {
        return $this->settings->customTitle . ' - ' . get_bloginfo('name');
    }

    /**
     * Change the page title for themes with theme_support
     * for title-tag
     *
     * @return array
     */
    public function changePageTitle($title = [])
    {
        if ($this->content->isRestricted()) {
            return [
                'title' => $this->settings->customTitle,
                'site' => get_bloginfo('name'),
            ];
        }

        return $title;
    }

    /**
     * Change the title for themes that
     * do not support title tag
     *
     * @param string $title
     * @param string $sep
     * @param string $location
     * @return string
     */
    public function changeDefaultTitle($title, $sep, $location)
    {

        if ($this->content->isRestricted()) {
            return $this->getTitle();
        }

        return $title;
    }

    /**
     * Change the parts sent to Yoast
     *
     * @return void
     */
    public function returnPageTitle($title)
    {
        if ($this->content->isRestricted()) {
            return $this->getTitle();
        }

        return $title;
    }
}
