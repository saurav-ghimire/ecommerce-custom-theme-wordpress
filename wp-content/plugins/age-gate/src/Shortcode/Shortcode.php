<?php

namespace AgeGate\Shortcode;

use Asylum\Utility\View;
use AgeGate\Utility\Cookie;
use AgeGate\Common\Settings;
use AgeGate\Controller\StandardController;
use AgeGate\Utility\Encrypt;
use Asylum\Validation\Validator;
use AgeGate\Presentation\Attribute;
use Asylum\Utility\Facades\Parsedown;
use Asylum\Utility\Facades\StringTemplate;
use Asylum\Common\Shortcode as AbstractShortcode;
use AgeGate\Shortcode\ShortcodeContent as Content;
use AgeGate\Utility\Crawler;

class Shortcode extends AbstractShortcode
{
    protected $shortcode = 'age-gate';

    private static $count = 0;

    public function registerShortcode($atts, $content, $tag)
    {
        $settings = Settings::getInstance();
        self::$count = self::$count + 1;

        $atts = shortcode_atts(
            [
                'age' => $settings->defaultAge,
                'title' => $settings->labelButtons,
                'type' => $settings->inputType,
                'poster' => false,
                'force' => false,
            ],
            $atts,
            'age-gate'
        );

        if ($this->status($settings, $atts) === true || $settings->method === 'standard' && Crawler::isBot()) {
            return $content;
        }

        if (!is_single()) {
            return '';
        }

        $shortcodeSettings = clone($settings);
        $shortcodeSettings->set('inputType', $atts['type']);
        $shortcodeSettings->set('labelButtons', $atts['title']);

        if ($atts['poster'] ?? false) {
            if (is_numeric($atts['poster'])) {
                $shortcodeSettings->set('poster', wp_get_attachment_url($atts['poster']));
            } else {
                $shortcodeSettings->set('poster', $atts['poster']);
            }
        }

        $shortcodeContent = new Content(null, 'shortcode', $shortcodeSettings);
        $shortcodeContent->setAge($atts['age']);

        $view = $this->view($shortcodeSettings, $shortcodeContent);

        if ($settings->method === 'js') {
            wp_enqueue_script('age-gate-shortcode');
            $view->addData([
                'restrictedContent' => base64_encode($content),
                'options' => base64_encode(json_encode(array_merge($atts, ['cookieName' => $settings->getCookieName(), 'cookieDomain' => Cookie::getDomain()]))),
            ]);
        } else {
            $view->addData(['e' => StandardController::$errors, 'c' => self::$count]);
        }
        $markup = $view->compile('shortcode/shortcode-' . $settings->method);

        // remove the attribute
        return $markup;
    }

    private function view($settings, $content)
    {
        $map = [
            'y' => 'Year',
            'm' => 'Month',
            'd' => 'Day'
        ];

        $view = new View(AGE_GATE_PATH . 'src/Resources/views/public');
        $view
            ->addData(
                [
                    'settings' => $settings,
                    'content' => $content,
                    'encrypt' => new Encrypt(),
                    'errors' => [],
                ]
            )
            ->addData(
                [
                    'fields' => collect(explode(' ', $settings->dateFormat))->mapWithKeys(function ($item) use ($map, $settings) {
                        $key = strtolower($item[0]);
                        $label = 'label' . $map[$key];
                        $placeholder = 'placeholder' . $map[$key];

                        return [
                            $key => [
                                'label' => $settings->$label,
                                'placeholder' => $settings->$placeholder,
                                'value' => $_POST['age_gate'][$key] ?? apply_filters('age_gate/field/' . strtolower($map[$key]) . '/value', ''),
                                'errors' => Validator::get_instance()->get_errors_array(),
                                'options' => $settings->inputType === 'selects' ? $this->getOptions($key, $settings) : [],
                            ]
                        ];
                    })->toArray()
                ]
            )
            ->addFunction('attr', [Attribute::class, 'attr'])
            ->addFunction('stringTemplate', [StringTemplate::class, 'render'])
            ->addFunction('mdLine', [Parsedown::class, 'line'])
            ->addFunction('mdText', [Parsedown::class, 'text'])
            ->addFolder('theme', AGE_GATE_PATH . 'src/Resources/views/empty');
        return $view;
    }

    private function getOptions($key, $settings)
    {
        switch ($key) {
            case 'm':
                $display = apply_filters('age_gate/form/select/month/format', 'M');
                $range = range(1, 12);
                return collect($range)->mapWithKeys(function ($month) use ($display) {
                    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
                    return [$month => date_i18n($display, strtotime("2022-$month-01"))];
                })->toArray();

            case 'd':
                return collect(range(1, 31))->mapWithKeys(function ($day) {
                    $day = str_pad($day, 2, "0", STR_PAD_LEFT);
                    return [$day => $day];
                })->toArray();
            case 'y':
                $years = collect(range(apply_filters('age_gate/form/select/year/min', 1900), 2022))->mapWithKeys(function ($year) {
                    return[$year => $year];
                });

                if ($settings->yearOrder === 'high-low') {
                    return $years->reverse()->toArray();
                } else {
                    return $years->toArray();
                }
        }
    }

    private function status($settings, $atts)
    {
        if ($settings->method === 'js') {
            return false;
        }

        $cookie = Cookie::get($settings->getCookieName());

        if ($settings->anonymous && $cookie) {
            return true;
        }

        if ($cookie >= $atts['age']) {
            return true;
        }

        return false;
    }
}
