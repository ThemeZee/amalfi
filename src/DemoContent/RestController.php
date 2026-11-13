<?php
/**
 * Demo content REST controller.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\DemoContent;

use ThemeZee\Amalfi\Core\ThemeConfig;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Registers the demo-content REST route and delegates to the importer.
 */
class RestController {

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig $config   The theme configuration.
	 * @param Importer    $importer The demo importer.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly Importer $importer,
	) {
	}

	/**
	 * Register the route.
	 */
	public function registerRoutes(): void {
		register_rest_route(
			$this->config->restNamespace(),
			'/demo',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'import' ),
				'permission_callback' => array( $this, 'checkPermission' ),
				'args'                => array(
					'parts' => array(
						'type'     => 'object',
						'required' => true,
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
	 * Run the demo content import.
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response
	 */
	public function import( WP_REST_Request $request ): WP_REST_Response {
		$requested = (array) $request->get_param( 'parts' );
		$parts     = array();

		foreach ( $this->importer->parts() as $part ) {
			$parts[ $part['key'] ] = ! empty( $requested[ $part['key'] ] );
		}

		$results = $this->importer->run( $parts );

		return new WP_REST_Response(
			array(
				'results' => $results,
				'failed'  => wp_list_filter( $results, array( 'status' => 'error' ) ) !== array(),
			)
		);
	}
}
