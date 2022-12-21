<?php

namespace AgeGate\Controller;

use AgeGate\Utility\Cookie;
use AgeGate\Common\Controller\AbstractController;
use AgeGate\Common\Interfaces\ControllerInterface;

class JsController extends AbstractController implements ControllerInterface
{

    protected $templateHook = 'wp_footer';

    public function init(): void
    {
        $this->templateHook = $this->settings->inHeader ? 'wp_head' : 'wp_footer';
        add_action($this->templateHook, [$this, 'template'], 2);
        add_action('wp_head', [$this, 'user']);

        add_action('wp_enqueue_scripts', function(){
            if ($this->settings->jsHooks) {
                wp_enqueue_script('age-gate-hooks', AGE_GATE_URL . 'dist/hooks.js', [], AGE_GATE_VERSION, !$this->settings->inHeader);
            }

        }, 1);
    }

    public function assets(): void
    {
        if ($this->content->getRestricted()) {
            wp_register_script('age-gate-stepped', AGE_GATE_URL . 'dist/stepped.js', [], AGE_GATE_VERSION, !$this->settings->inHeader);

            wp_enqueue_style('age-gate');
            wp_enqueue_script('age-gate', AGE_GATE_URL . 'dist/age-gate.js', $this->getScriptDependencies(), AGE_GATE_VERSION, !$this->settings->inHeader);

            $data = [
                'cookieDomain' => Cookie::getDomain(),
                'cookieName' => $this->settings->getCookieName(),
                'age' => $this->content->getAge(),
                'css' => $this->settings->cssType,
                'userAgents' => array_filter(apply_filters('age_gate/settings/bots', preg_split('/\r\n|\r|\n/', $this->settings->userAgents ?: ''))),
                'switchTitle' => false,
                'rechallenge' => $this->settings->rechallenge,
                'error' => $this->settings->errorFailed,
                'generic' => $this->settings->errorGeneric,
                'uri' => rest_url('/age-gate/v3/check'),
            ];

            wp_localize_script('age-gate-stepped', 'ag_stepped', ['age' => $this->content->getAge()]);

            if ($this->settings->switchTitle) {
                $data['customTitle'] = $this->title->getTitle();
            }

            if ($this->settings->viewport) {
                $data['viewport'] = true;
            }

            if (!$this->settings->disableAjaxFallback) {
                $data['fallback'] = esc_url(admin_url('admin-ajax.php'));
            }

            if (!$this->settings->munge) {
                wp_localize_script('age-gate', 'age_gate', $data);
            } else {
                // add_action($this->templateHook, function () use ($data) {
                    if ($data['customTitle'] ?? false) {
                        age_gate_add_attribute('age-gate', 'data-title', $data['customTitle']);
                    }
                    echo '<meta name="age-gate" content="" data-ag-munge="' . base64_encode(json_encode($data)) . '" />';
                // }, 1);
            }
        }
    }

    public function template()
    {
        // add dialog attributes
        age_gate_add_attribute('age-gate', 'role', "dialog");
        age_gate_add_attribute('age-gate', 'aria-modal', "true");
        age_gate_add_attribute('age-gate', 'aria-label', $this->settings->labelAria);

        require_once AGE_GATE_PATH . 'src/Controller/ViewController.php';
    }

    public function user()
    {
        if ($this->settings->ignoreLogged && is_user_logged_in()) {
            echo '<script>var ag_logged_in = true;</script>';
        }
    }

    private function getScriptDependencies()
    {
        $deps = [
        ];

        if ($this->settings->jsHooks) {
            $deps[] = 'age-gate-hooks';
        }
        // if ($this->settings->stepped) {
        //     $deps[] = 'age-gate-stepped';
        // }

        // if ($this->settings->focus) {
        //     $deps[] = 'age-gate-focus';
        // }

        // if ($this->settings->devTools) {
        //     $deps[] = 'age-gate-interaction';
        // }

        // if ($this->settings->inputAutoTab) {
        //     $deps[] = 'age-gate-autotab';
        // }

        return $deps;
    }
}
