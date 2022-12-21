<?php

namespace AgeGate\Routes\Rest;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Controller;
use AgeGate\Common\Settings;
use AgeGate\Common\Form\Submit;

class Check extends WP_REST_Controller
{
    protected $namespace = 'age-gate/v3';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * Register the rest route
     *
     * @return void
     */
    public function register()
    {
        register_rest_route(
            $this->namespace,
            '/check',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'response'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function response(WP_REST_Request $request)
    {
        // return Settings::getInstance();
        return new WP_REST_Response(
            (new Submit($request->get_params(), Settings::getInstance()))->validate()
        );
    }
}
