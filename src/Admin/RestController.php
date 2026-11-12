<?php
/**
 * REST API controller for the theme setup page.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Registers the REST routes that power the React admin page.
 *
 * Routes live under the `amalfi/v1` namespace and delegate to the existing
 * services. Every route requires the theme capability.
 */
class RestController {

	/**
	 * The REST namespace.
	 */
	private const NAMESPACE = 'amalfi/v1';

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig    $config  The theme configuration.
	 * @param DemoImporter   $demo    The demo importer.
	 * @param LicenseManager $license The license manager.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly DemoImporter $demo,
		private readonly LicenseManager $license,
	) {
	}

	/**
	 * The REST namespace used by the admin page.
	 */
	public function namespaceRoot(): string {
		return self::NAMESPACE;
	}

	/**
	 * Register the routes.
	 */
	public function registerRoutes(): void {
		$permission = array( $this, 'checkPermission' );

		register_rest_route(
			self::NAMESPACE,
			'/demo',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'importDemo' ),
				'permission_callback' => $permission,
				'args'                => array(
					'parts' => array(
						'type'     => 'object',
						'required' => true,
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/license',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'manageLicense' ),
				'permission_callback' => $permission,
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
	 * Permission check for every route.
	 */
	public function checkPermission(): bool {
		return current_user_can( $this->config->capability() );
	}

	/**
	 * Run the demo content import.
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response
	 */
	public function importDemo( WP_REST_Request $request ): WP_REST_Response {
		$requested = (array) $request->get_param( 'parts' );
		$parts     = array();

		foreach ( $this->demo->parts() as $part ) {
			$parts[ $part['key'] ] = ! empty( $requested[ $part['key'] ] );
		}

		$results = $this->demo->run( $parts );

		return new WP_REST_Response(
			array(
				'results' => $results,
				'failed'  => wp_list_filter( $results, array( 'status' => 'error' ) ) !== array(),
			)
		);
	}

	/**
	 * Activate or deactivate the license.
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response
	 */
	public function manageLicense( WP_REST_Request $request ): WP_REST_Response {
		$action = (string) $request->get_param( 'action' );

		if ( 'deactivate' === $action ) {
			return new WP_REST_Response( $this->license->deactivate() );
		}

		$key = sanitize_text_field( (string) $request->get_param( 'key' ) );

		return new WP_REST_Response( $this->license->activate( $key ) );
	}
}
