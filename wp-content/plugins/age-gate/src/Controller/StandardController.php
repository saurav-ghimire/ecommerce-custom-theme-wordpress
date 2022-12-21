<?php

namespace AgeGate\Controller;

use AgeGate\Utility\Cookie;
use AgeGate\Common\Content;
use AgeGate\Common\Form\Submit;
use AgeGate\Presentation\ClassNames;
use AgeGate\Common\Controller\AbstractController;
use AgeGate\Common\Interfaces\ControllerInterface;

class StandardController extends AbstractController implements ControllerInterface
{
    use ClassNames;

    public static $errors = [];

    public function __construct(Content $c)
    {
        parent::__construct($c);

        if (isset($_POST['age_gate'])) {
            $response = (new Submit($_POST, $this->settings))->validate();

            if ($response['status'] === true) {

                if ($response['set_cookie']) {
                    Cookie::set($this->settings->getCookieName(), $response['data']['user_age'], $response['cookieLength'] ?? 0);
                    Cookie::destroy($this->settings->getCookieName() . '_failed');
                } else {
                    $_COOKIE[$this->settings->getCookieName()] = $response['data']['user_age'];
                }

                do_action('age_gate/passed', $response);
                wp_safe_redirect(esc_url_raw($response['redirect']), 303, 'AGE-GATE');
                exit;
            }

            global $wp;
            if ($response['set_cookie']) {
                Cookie::set($this->settings->getCookieName() . '_failed', 1);
            }

            if ($wp->request !== $response['redirect']) {
                wp_redirect(esc_url_raw($response['redirect']), 303, 'AGE-GATE');
            }

            if ($this->settings->method === 'standard' && !$this->settings->rechallenge) {
                $this->settings->lockout = true;

                StandardController::$errors = [
                    $this->settings->errorFailed,
                ];
            }

            self::$errors = $response['errors'];
        }
    }

    public function init(): void
    {
        $cookie = $this->settings->getCookieName() . '_failed';

        if ($this->settings->method === 'standard' && !$this->settings->rechallenge && Cookie::get($cookie)) {

            remove_all_actions('age_gate/fields');
            remove_all_actions('age_gate/custom/after');

            $this->settings->lockout = true;

            StandardController::$errors = [
                $this->settings->errorFailed,
            ];
        }


        add_filter('template_include', [$this, 'template']);
    }

    public function assets(): void
    {
        wp_register_script('age-gate-stepped', AGE_GATE_URL . 'dist/stepped.js', [], AGE_GATE_VERSION, true);
        wp_localize_script('age-gate-stepped', 'ag_stepped', ['age' => $this->content->getAge()]);

        if ($this->content->isRestricted()) {
            wp_enqueue_script('age-gate-standard', AGE_GATE_URL . 'dist/standard.js', [], AGE_GATE_VERSION, true);
        }
    }

    public function template($template)
    {
        $show = apply_filters('age_gate/show', true, $this->content);
        if ($this->content->isRestricted() && $show) {
            wp_enqueue_style('age-gate');

            return AGE_GATE_PATH . 'src/Controller/ViewController.php';
        }

        return $template;
    }
}
