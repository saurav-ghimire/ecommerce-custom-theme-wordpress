<?php

namespace AgeGate\Routes\Rest;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Controller;
use AgeGate\Common\Settings;
use AgeGate\Common\Immutable\Constants;

class Developer extends WP_REST_Controller
{
    protected $namespace = 'age-gate/v3';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        if (!Settings::getInstance()->devEndpoint) {
            return;
        }

        register_rest_route(
            $this->namespace,
            '/developer',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'response'],
                'permission_callback' => '__return_true',
            ]
        );

    }

    /**
     * Return the response
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function response(WP_REST_Request $request)
    {
        $data = [
            'version' => get_option('age_gate_version'),
        ];

        if ($updateVersion = get_option('age_gate_updated_from', false)) {
            $data['updated_from'] = $updateVersion;
        }

        foreach (Constants::AGE_GATE_OPTIONS as $key => $option) {
            $data[$key] = get_option($option);
        }

        return new WP_REST_Response($data);
    }


}
