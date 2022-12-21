<?php

namespace AgeGate\Controller;

use AgeGate\App\AgeGate;
use Asylum\Utility\View;
use AgeGate\Common\Settings;
use AgeGate\Utility\Encrypt;
use Asylum\Validation\Validator;
use AgeGate\Presentation\Attribute;
use AgeGate\Presentation\ClassNames;
use Asylum\Utility\Facades\Parsedown;
use Asylum\Utility\Facades\StringTemplate;

class ViewController
{
    private $empty = AGE_GATE_PATH . 'src/Resources/views/empty';

    protected $view;

    private static $errors = [];

    public function __construct($settings = null, $content = null)
    {
        $theme = is_dir(get_template_directory() . '/age-gate') ? get_template_directory() . '/age-gate' : $this->empty;
        $settings = Settings::getInstance();

        $map = [
            'y' => 'Year',
            'm' => 'Month',
            'd' => 'Day'
        ];

        $this->view = new View(AGE_GATE_PATH . 'src/Resources/views/public');
        $this->view
            ->addData(
                [
                    'settings' => $settings,
                    'content' => AgeGate::getContent(),
                    'encrypt' => new Encrypt,
                    'errors' => StandardController::$errors,
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
                                'value' => ((int) ($_POST['age_gate'][$key] ?? 0)) ?: apply_filters('age_gate/field/' . strtolower($map[$key]) . '/value', ''),
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
            ->addFunction('mdText', [Parsedown::class, 'text']);

        if (is_dir($theme)) {
            $this->view->addFolder('theme', $theme);
        }
    }


    public function getView()
    {
        return $this->view;
    }

    public function render()
    {
        $this->view
            ->render('age-gate');
    }

    public static function setErrors($errors)
    {
        self::$errors = $errors;
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
}

//
(new ViewController)->render();
