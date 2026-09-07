<?php
/* CONSTANTS
=============================================== */
$ilist_api_version = 'v2';

/** Method WP_REST_Server
=============================================== */
// CREATABLE / READABLE / EDITABLE / DELETABLE

/** Initialise ENDPOINTS - List
=============================================== */
add_action( 'rest_api_init', 'ilist_api_routes' );
// -----------------------------------------------
if (!function_exists("ilist_api_routes")) {
    function ilist_api_routes() {
        global $ilist_api_version;
        // ====================================================
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        // ====================================================
        add_filter( 'rest_pre_serve_request', function( $value ) {
            header( 'Access-Control-Allow-Origin: *' );
            header( 'Access-Control-Allow-Headers: X-Requested-With' );
            header( 'Access-Control-Allow-Methods: GET, POST, DELETE, PUT' );
            header( 'Access-Control-Allow-Credentials: true' );
            header( 'Access-Control-Expose-Headers: Link', false );
            return $value;
        } );
        // ====================================================
        // WP_REST_Server::READABLE
		// WP_REST_Server::CREATABLE
		// WP_REST_Server::EDITABLE
		// WP_REST_Server::DELETABLE
		// WP_REST_Server::ALLMETHODS
        // ====================================================
        // Registering route for all lists
        // ----------------------------------------------------
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list
        // ====================================================
        register_rest_route( "ilist/$ilist_api_version", "/get-list",
            ['methods' => WP_REST_Server::READABLE, 'callback' => 'ilist_api_get_all', 'permission_callback' => 'ilist_api_get_permissions_check']
        );
        // ====================================================
        // Registering route for single list (By ID or Key)
        // ----------------------------------------------------
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list/2
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list/bc80e648bc75da00c12d71f1056aad
        // ====================================================
        register_rest_route( "ilist/$ilist_api_version", "/get-list/(?P<id>[a-zA-Z0-9-]+)",
            ['methods' => WP_REST_Server::READABLE, 'callback' => 'ilist_api_get_list_infos', 'permission_callback' => 'ilist_api_get_permissions_check']
        );
        // ====================================================
        // Registering route for create a list
        // ----------------------------------------------------
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list/28
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list/bc80e648bc75da00c12d71f1056aad
        // ====================================================
        register_rest_route( "ilist/$ilist_api_version", "/create-list/(?P<id>[a-zA-Z0-9-]+)",
            ['methods' => WP_REST_Server::READABLE, 'callback' => 'ilist_api_get_list_infos', 'permission_callback' => 'ilist_api_get_permissions_check']
        );
        // ====================================================
        // Registering route for all products in a list
        // ----------------------------------------------------
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list-products/28
        // ====================================================
        register_rest_route( "ilist/$ilist_api_version", "/get-list-products/(?P<key>[a-zA-Z0-9-]+)",
            ['methods' => WP_REST_Server::READABLE, 'callback' => 'ilist_api_get_products_by_list', 'permission_callback' => 'ilist_api_get_permissions_check']
        );
        // ====================================================
        // Registering route for nb products in a list
        // ----------------------------------------------------
        // Ex: https://domaine.com/wp-json/ilist/v1/get-list-nbproducts/28
        // ====================================================
        register_rest_route( "ilist/$ilist_api_version", "/get-list-nbproducts/(?P<id>[\d]+)",
            ['methods' => WP_REST_Server::READABLE, 'callback' => 'ilist_api_get_nb_products_by_list_id', 'permission_callback' => 'ilist_api_get_permissions_check']
        );
    }
}

/** Callback function permissions check
=============================================== */
if (!function_exists("ilist_api_get_permissions_check")) {
    function ilist_api_get_permissions_check() {
        // Restrict endpoint to only users who have the xxxxxxxxxx capability
        if ( ! current_user_can( 'edit_posts' ) ) {
            return new WP_Error( 'rest_forbidden', esc_html__( 'OMG you can not view private data.', 'my-text-domain' ), array( 'status' => 401 ) );
        }
        return true;
    }
}

// =====================================================================>
// =====================================================================>
// =====================================================================>
//                                   LIST
// =====================================================================>
// =====================================================================>
// =====================================================================>


/** Get a list infos by ID or keyaccess
=============================================== */
if (!function_exists("ilist_api_get_list_infos")) {
    function ilist_api_get_list_infos( $request ) {
        $id_key = (string) $request['id'];
        if (!$id_key) {
            return new WP_Error( 'rest_id_invalid', esc_html__( 'The list does not exist', ILIST_ID_LANGUAGES ), array( 'status' => 404 ) );
        } else {
            // Get list infos by ID
            $by_id = intval($id_key);
            $list_infos_by_id = ilist_get_lists_search("id = %d", array($by_id));
            if ($list_infos_by_id && array_keys($list_infos_by_id, true)) {
                return rest_ensure_response( $list_infos_by_id );
            } else {
                // Get list infos by KEYACCESS
                $list_infos_by_key = ilist_get_lists_search("keyaccess = %s", array($id_key));
                if (!$list_infos_by_key || !array_keys($list_infos_by_key, true)) {
                    return new WP_Error( 'rest_no_list_infos', esc_html__( 'ID / Key not valid', ILIST_ID_LANGUAGES ), array( 'status' => 404 ) );
                } else {
                    return rest_ensure_response( $list_infos_by_key );
                }
            }
        }
        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong (Get list infos)', ILIST_ID_LANGUAGES ), array( 'status' => 500 ) );
    }
}

/** Get all lists infos
=============================================== */
if (!function_exists("ilist_api_get_all")) {
    function ilist_api_get_all( $request ) {
        $list = ilist_get_lists_search();
        if (!$list || !array_keys($list, true)) {
            return new WP_Error( 'rest_list_empty', esc_html__( 'No list in Database', ILIST_ID_LANGUAGES ), array( 'status' => 404 ) );
        } else {
            return rest_ensure_response($list);
        }
        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong (Get all list)', ILIST_ID_LANGUAGES ), array( 'status' => 500 ) );
    }
}

// =====================================================================>
// =====================================================================>
// =====================================================================>
//                            PRODUCTS
// =====================================================================>
// =====================================================================>
// =====================================================================>
if (!function_exists("ilist_api_get_products_by_list")) {
    function ilist_api_get_products_by_list( $request ) {
        $listkey = ilist_stopXSS( $request['key'] );
        if (!$listkey) {
            return new WP_Error( 'rest_list_products_empty', esc_html__( 'No list products in Database', ILIST_ID_LANGUAGES ), array( 'status' => 404 ) );
        } else {
            $products = ilist_get_woocommerce_product_list_by_key($listkey);
            return rest_ensure_response($products);
        }
        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong (Get all products of list)', ILIST_ID_LANGUAGES ), array( 'status' => 500 ) );
    }
}

if (!function_exists("ilist_api_get_nb_products_by_list_id")) {
    function ilist_api_get_nb_products_by_list_id( $request ) {
        $listid = (int) $request['id'];
        if (!$listid) {
            return new WP_Error( 'rest_list_products_empty', esc_html__( 'No list products in Database', ILIST_ID_LANGUAGES ), array( 'status' => 404 ) );
        } else {
            $nbproducts = ilist_get_nb_products_by_list($listid);
            return rest_ensure_response($nbproducts);
        }
        return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong (Get nb products of list)', ILIST_ID_LANGUAGES ), array( 'status' => 500 ) );
    }
}



/**
 * This is our callback function to return a single product.
 *
 * @param WP_REST_Request $request This function accepts a rest request to process data.
 */
function prefix_get_product( $request ) {
    // In practice this function would fetch the desired data. Here we are just making stuff up.
    $products = array(
        '1' => 'I am product 1',
        '2' => 'I am product 2',
        '3' => 'I am product 3',
    );

    // Here we are grabbing the 'id' path variable from the $request object. WP_REST_Request implements ArrayAccess, which allows us to grab properties as though it is an array.
    $id = (string) $request['id'];

    if ( isset( $products[ $id ] ) ) {
        // Grab the product.
        $product = $products[ $id ];

        // Return the product as a response.
        return rest_ensure_response( $product );
    } else {
        // Return a WP_Error because the request product was not found. In this case we return a 404 because the main resource was not found.
        return new WP_Error( 'rest_product_invalid', esc_html__( 'The product does not exist.', 'my-text-domain' ), array( 'status' => 404 ) );
    }

    // If the code somehow executes to here something bad happened return a 500.
    return new WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
}




?>