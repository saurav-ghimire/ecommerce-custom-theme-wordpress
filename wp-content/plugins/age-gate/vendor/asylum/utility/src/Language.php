<?php

namespace Asylum\Utility;

class Language
{
    private static $instance = null;

    private $languages = [];
    private $current = null;
    private $default = null;

    private function __construct()
    {
        $this->setLanguages();
        $this->setCurrent();
        $this->setDefault();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Language();
        }

        return self::$instance;
    }

    public function getLanguage()
    {
        if (function_exists('icl_get_current_language')) {
            return icl_get_current_language();
        }

        if (function_exists('wpm_get_user_language')) {
            return wpm_get_user_language();
        }

        return substr(get_locale(), 0, 2);
    }

    public function multilingual()
    {
        if (function_exists('icl_get_current_language')) {
            return 'pll';
        }

        if (function_exists('wpm_get_user_language')) {
            return 'wpm';
        }

        return false;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getLanguages($type = null)
    {
        if ($type === 'default' || $type === 'current') {
            $languages = $this->format($this->languages, $this->default, $this->current, false);
            return $languages[$this->$type] ?? [];
        }

        return $this->format($this->languages, $this->default, $this->current);
    }

    public function setLanguages()
    {
        switch ($this->multilingual()) {
            case 'pll':
                $this->languages = icl_get_languages();
                break;
            case 'wpm':
                $this->languages = wpm_get_languages();
                break;
            default:
                $this->languages = [];
        }


        return $this;
    }

    public function setDefault()
    {
        if (function_exists('icl_get_current_language')) {
            $this->default = icl_get_default_language();
        }

        if (function_exists('wpm_get_languages')) {
            $this->default = wpm_get_default_language();
        }

        return $this;
    }

    public function setCurrent()
    {
        if (function_exists('icl_get_current_language')) {
            $this->current = icl_get_current_language();
        }

        if (function_exists('wpm_get_languages')) {
            $this->current = (!is_admin()) ? wpm_get_language() : wpm_get_user_language();
        }

        return $this;
    }

    private function format($languages, $default, $current, $excludeDefault = true)
    {
        // dd($languages);
        $flagPath = function_exists('wpm_get_flags_dir') ? wpm_get_flags_dir() : '';

        return collect($languages)->mapWithKeys(function ($language, $code) use ($default, $current, $flagPath, $excludeDefault) {
            if ($excludeDefault && $code === $default) {
                return [];
            }

            $flag = false;

            if (class_exists('\\WPML\\Element\\API\\Languages')) {
                $flag = \WPML\Element\API\Languages::getFlagUrl($code);
            }

            return [
                $code => [
                    'code' => $code,
                    'flag' => $flag ?: ($flagPath . ($language['country_flag_url'] ?? $language['flag'] ?? null)),
                    'name' => $language['native_name'] ?? $language['name'] ?? null,
                    'current' => $code === $current,
                    'url' => $language['url'] ?? ''
                ]
            ];
        })->toArray();
    }

    /**
     * Get the language of a piece of content
     * @return string
     */
    public function getObjectLanguage($id, $type = 'post')
    {
        if ($type === 'post') {
            if (function_exists('wpml_get_language_information')) {
                return wpml_get_language_information(null, $id)['language_code'] ?? null;
            } elseif (function_exists('pll_get_post_language')) {
                return pll_get_post_language($id);
            }
        } elseif ($type === 'term') {
            if (function_exists('pll_get_term_language')) {
                return pll_get_term_language($id);
            } elseif (function_exists('wpml_element_language_details_filter')) {
                // TODO: Test this!
                global $sitepress;
                return $sitepress->get_this_lang();
            }
        }

        return substr(get_locale(), 0, 2);
    }
}
