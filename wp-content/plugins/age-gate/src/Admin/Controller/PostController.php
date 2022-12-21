<?php

namespace AgeGate\Admin\Controller;

use AgeGate\Admin\Post\Edit;
use AgeGate\Common\Settings;
use Asylum\Utility\Language;
use AgeGate\Admin\Taxonomy\Term;
use AgeGate\Admin\Post\ListTable;
use AgeGate\Common\Admin\AbstractController;

class PostController extends AbstractController
{
    protected const INIT = true;
    protected $listTable;

    public function register(): void
    {
        $this->listTable = new ListTable($this->view);
        $this->edit = new Edit($this->view);
        $this->term = new Term($this->view);
    }

    public function required(): bool
    {
        return true;
    }

    public function data(): array
    {
        return [];
    }

    public function fields(): array
    {
        return [];
    }

    public function enqueue(): void
    {
        wp_enqueue_script('age-gate-edit', AGE_GATE_URL . '/dist/edit.js', [], AGE_GATE_VERSION, true);

        if ($languages = Language::getInstance()->getLanguages()) {
            $settings = Settings::getInstance();
            $default = Language::getInstance()->getLanguages('default');

            $data = [];

            $data[$default['code']] = $settings->defaultAge;

            foreach ($languages as $code => $language) {
                $data[$code] = $settings->{$code}['defaultAge'] ?? $settings->defaultAge;
            }

            wp_localize_script('age-gate-edit', 'agagemap', $data);
        }
    }
}
