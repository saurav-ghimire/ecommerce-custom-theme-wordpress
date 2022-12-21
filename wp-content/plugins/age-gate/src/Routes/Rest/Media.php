<?php

namespace AgeGate\Routes\Rest;

use WP_Query;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Controller;
use AgeGate\Common\Immutable\Constants;

class Media extends WP_REST_Controller
{
    protected $namespace = 'age-gate/v3';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register']);
        add_filter('posts_where', [$this, 'postsWhere'], 10, 2);
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
            '/media',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'response'],
                'permission_callback' => fn () => get_current_user_id() > 0 && current_user_can(Constants::APPEARANCE)
            ]
        );
    }

    public function postsWhere($where, $query)
    {
        global $wpdb;
        if ($title = $query->get('search_title')) {
            $where .= " AND " . $wpdb->posts . ".post_title LIKE '" . esc_sql($wpdb->esc_like($title)) . "%'";
        }


        if ($in = $query->get('current_selection')) {
            $where .= " OR " . $wpdb->posts . ".ID = '" . esc_sql($in) . "'";
        }

        return $where;
    }


    public function response(WP_REST_Request $request)
    {
        $page = $request->get_param('page') ?: 1;
        $perPage = $request->get_param('perPage') ?: 15;
        $in = $request->get_param('in');
        $current = [];

        $args = [
            'post_type' => 'attachment',
            'post_mime_type' => $request->get_param('types') ?: ['image', 'video'],
            'post_status' => 'inherit',
            'posts_per_page' => 15,
            'paged' => $page,
        ];

        if ($in) {
            if ((int) $page === 1) {
                $asset = get_post($in);
                $current = [
                    'id' => $asset->ID,
                    'thumbnail' => wp_get_attachment_image_url($asset->ID, 'thumbnail', true),
                    'full' => wp_get_attachment_url($asset->ID, 'full', true),
                    'type' => get_post_mime_type($asset->ID),
                ];
            } else {
                $args['post__not_in'] = [$in];
            }
        }

        if ($search = $request->get_param('s')) {
            $args['search_title'] = $search;
            $args['posts_per_page'] = -1;
        }


        $results = new WP_Query($args);

        $assets = collect($results->posts)->map(function ($asset) {
            return [
                'id' => $asset->ID,
                'thumbnail' => wp_get_attachment_image_url($asset->ID, 'thumbnail', true),
                'full' => wp_get_attachment_url($asset->ID, 'full', true),
                'type' => get_post_mime_type($asset->ID),
            ];
        })->toArray();


        return new WP_REST_Response([
            'assets' => $assets,
            'next' => ((int) $page < (int) $results->max_num_pages ? rest_url('/age-gate/v3/media?page=' . ((int) $page + 1)) : ''),
            'total' => $results->found_posts,
            'pages' => $results->max_num_pages,
            'page' => (int) $page,
            'perPage' => (int) $perPage,
            'data' => [
                'status' => 200
            ],
        ]);
    }
}
