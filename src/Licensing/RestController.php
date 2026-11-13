<?php
/**
 * License REST controller.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Licensing;

use ThemeZee\Amalfi\Core\ThemeConfig;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Registers the license REST route and delegates to the license client.
 */
class RestController {

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig   $config  The theme configuration.
	 * @param LicenseClient $license The license client.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly LicenseClient $license,
	) {
	}

	/**
	 * Register the route.
	 */
	public function registerRoutes(): void {
		register_rest_route(
			$this->config->restNamespace(),
			'/license',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'manage' ),
				'permission_callback' => array( $this, 'checkPermission' ),
				'args'                => array(
					'action' => array(
						'type'     => 'string',
						'required' => true,
						'enum'     => array( 'activate', 'deactivate' ),
					),
					'key'    => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);
	}

	/**
	 * Permission check for the route.
	 */
	public function checkPermission(): bool {
		return current_user_can( $this->config->capability() );
	}

	/**
	 * Activate or deactivate the license.
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response
	 */
	public function manage( WP_REST_Request $request ): WP_REST_Response {
		$action = (string) $request->get_param( 'action' );

		if ( 'deactivate' === $action ) {
			return new WP_REST_Response( $this->license->deactivate() );
		}

		$key = sanitize_text_field( (string) $request->get_param( 'key' ) );

		return new WP_REST_Response( $this->license->activate( $key ) );
	}
}
