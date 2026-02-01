<?php
/**
 * REST API Class
 *
 * @package ZU_Custom_TShirt_Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ZU_CTSD_REST_API
 * Handles REST API endpoints
 */
class ZU_CTSD_REST_API {

    /**
     * Namespace for REST API
     */
    const NAMESPACE = 'zu-ctsd/v1';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes
     */
    public function register_routes(): void {
        // Design routes
        register_rest_route(self::NAMESPACE, '/designs', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'create_design'],
                'permission_callback' => [$this, 'check_logged_in_permissions'],
            ],
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_designs'],
                'permission_callback' => [$this, 'check_logged_in_permissions'],
            ],
        ]);

        register_rest_route(self::NAMESPACE, '/designs/(?P<id>\d+)', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_design'],
                'permission_callback' => [$this, 'check_design_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update_design'],
                'permission_callback' => [$this, 'check_design_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
            [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => [$this, 'delete_design'],
                'permission_callback' => [$this, 'check_design_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
        ]);

        // Upload route
        register_rest_route(self::NAMESPACE, '/upload', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'upload_image'],
                'permission_callback' => [$this, 'check_logged_in_permissions'],
            ],
        ]);

        // Price calculation route
        register_rest_route(self::NAMESPACE, '/calculate-price', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'calculate_price'],
                'permission_callback' => '__return_true',
            ],
        ]);

        // Template routes
        register_rest_route(self::NAMESPACE, '/templates', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_templates'],
                'permission_callback' => '__return_true',
            ],
        ]);

        register_rest_route(self::NAMESPACE, '/templates/(?P<id>\d+)', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_template'],
                'permission_callback' => '__return_true',
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
        ]);

        // Export route
        register_rest_route(self::NAMESPACE, '/designs/(?P<id>\d+)/export', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'export_design'],
                'permission_callback' => [$this, 'check_design_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
        ]);

        // Share route
        register_rest_route(self::NAMESPACE, '/designs/(?P<id>\d+)/share', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'share_design'],
                'permission_callback' => [$this, 'check_design_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
        ]);

        // Reorder route
        register_rest_route(self::NAMESPACE, '/designs/(?P<id>\d+)/reorder', [
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'reorder_design'],
                'permission_callback' => [$this, 'check_design_permissions'],
                'args' => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ],
                ],
            ],
        ]);

        // Pricing options route
        register_rest_route(self::NAMESPACE, '/pricing-options', [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'get_pricing_options'],
                'permission_callback' => '__return_true',
            ],
        ]);
    }

    /**
     * Check logged in permissions
     */
    public function check_logged_in_permissions(): bool {
        return is_user_logged_in();
    }

    /**
     * Check design permissions
     */
    public function check_design_permissions(WP_REST_Request $request): bool {
        if (!is_user_logged_in()) {
            return false;
        }

        $design_id = intval($request->get_param('id'));
        $design = ZU_CTSD_Database::get_design($design_id);

        if (!$design) {
            return false;
        }

        // Allow if user owns the design or is admin
        return $design->user_id == get_current_user_id() || current_user_can('manage_zu_tshirt');
    }

    /**
     * Create design
     */
    public function create_design(WP_REST_Request $request): WP_REST_Response {
        $params = $request->get_params();
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->save_design($params);

        if ($result['success']) {
            return new WP_REST_Response($result, 201);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Get designs
     */
    public function get_designs(WP_REST_Request $request): WP_REST_Response {
        $user_id = get_current_user_id();
        $args = [
            'limit' => intval($request->get_param('limit') ?? 20),
            'offset' => intval($request->get_param('offset') ?? 0),
            'status' => sanitize_text_field($request->get_param('status') ?? ''),
        ];

        $designs = ZU_CTSD_Database::get_designs_by_user($user_id, $args);

        return new WP_REST_Response([
            'success' => true,
            'designs' => $designs,
        ], 200);
    }

    /**
     * Get single design
     */
    public function get_design(WP_REST_Request $request): WP_REST_Response {
        $design_id = intval($request->get_param('id'));
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $design = $design_handler->get_design($design_id);

        if ($design) {
            return new WP_REST_Response([
                'success' => true,
                'design' => $design,
            ], 200);
        }

        return new WP_REST_Response([
            'success' => false,
            'message' => __('Design not found.', 'zu-custom-tshirt'),
        ], 404);
    }

    /**
     * Update design
     */
    public function update_design(WP_REST_Request $request): WP_REST_Response {
        $design_id = intval($request->get_param('id'));
        $params = $request->get_params();
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->update_design($design_id, $params);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Delete design
     */
    public function delete_design(WP_REST_Request $request): WP_REST_Response {
        $design_id = intval($request->get_param('id'));
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->delete_design($design_id);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Upload image
     */
    public function upload_image(WP_REST_Request $request): WP_REST_Response {
        $files = $request->get_file_params();

        if (empty($files['image'])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('No image file provided.', 'zu-custom-tshirt'),
            ], 400);
        }

        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->upload_image($files['image']);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Calculate price
     */
    public function calculate_price(WP_REST_Request $request): WP_REST_Response {
        $params = $request->get_params();
        
        $product_id = intval($params['product_id'] ?? 0);
        $design_data = $params['design_data'] ?? [];

        $product = wc_get_product($product_id);
        $base_price = $product ? $product->get_price() : 0;

        $pricing_engine = new ZU_CTSD_Pricing();
        $price_data = $pricing_engine->get_live_price($base_price, $design_data);

        return new WP_REST_Response([
            'success' => true,
            'price_data' => $price_data,
        ], 200);
    }

    /**
     * Get templates
     */
    public function get_templates(WP_REST_Request $request): WP_REST_Response {
        $templates = ZU_CTSD_Database::get_templates();

        return new WP_REST_Response([
            'success' => true,
            'templates' => $templates,
        ], 200);
    }

    /**
     * Get single template
     */
    public function get_template(WP_REST_Request $request): WP_REST_Response {
        $template_id = intval($request->get_param('id'));
        
        $template = ZU_CTSD_Database::get_template($template_id);

        if ($template) {
            // Parse JSON fields
            $template->printable_area_front = json_decode($template->printable_area_front, true);
            $template->printable_area_back = json_decode($template->printable_area_back, true);
            $template->printable_area_left_sleeve = json_decode($template->printable_area_left_sleeve, true);
            $template->printable_area_right_sleeve = json_decode($template->printable_area_right_sleeve, true);

            return new WP_REST_Response([
                'success' => true,
                'template' => $template,
            ], 200);
        }

        return new WP_REST_Response([
            'success' => false,
            'message' => __('Template not found.', 'zu-custom-tshirt'),
        ], 404);
    }

    /**
     * Export design
     */
    public function export_design(WP_REST_Request $request): WP_REST_Response {
        $design_id = intval($request->get_param('id'));
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->export_design($design_id);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Share design
     */
    public function share_design(WP_REST_Request $request): WP_REST_Response {
        $design_id = intval($request->get_param('id'));
        $params = $request->get_params();
        $is_public = !empty($params['is_public']);
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->share_design($design_id, $is_public);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Reorder design
     */
    public function reorder_design(WP_REST_Request $request): WP_REST_Response {
        $design_id = intval($request->get_param('id'));
        
        $design_handler = new ZU_CTSD_Design_Handler();
        $result = $design_handler->reorder_design($design_id);

        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        }

        return new WP_REST_Response($result, 400);
    }

    /**
     * Get pricing options
     */
    public function get_pricing_options(WP_REST_Request $request): WP_REST_Response {
        $options = ZU_CTSD_Pricing::get_pricing_options();

        return new WP_REST_Response([
            'success' => true,
            'pricing_options' => $options,
        ], 200);
    }
}

// Initialize REST API
new ZU_CTSD_REST_API();
