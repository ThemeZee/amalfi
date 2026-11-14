<?php
/**
 * PHPUnit bootstrap for the Amalfi unit tests.
 *
 * These are isolated unit tests: WordPress is not loaded. WordPress functions
 * are mocked per-test with Brain Monkey, and the few WordPress classes and
 * constants the code references are given minimal stand-ins here.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

// Time constants referenced in class-constant expressions (e.g. ThemeUpdater).
if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
	define( 'HOUR_IN_SECONDS', 3600 );
}
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/**
 * Minimal stand-in for WP_Error.
 */
if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {

		/**
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 */
		public function __construct(
			private string $code = '',
			private string $message = '',
		) {
		}

		public function get_error_message(): string {
			return $this->message;
		}

		public function get_error_code(): string {
			return $this->code;
		}
	}
}

/**
 * Minimal stand-in for WP_REST_Request (parameter bag).
 */
if ( ! class_exists( 'WP_REST_Request' ) ) {
	class WP_REST_Request {

		/**
		 * @param array<string, mixed> $params Request parameters.
		 */
		public function __construct( private array $params = array() ) {
		}

		public function get_param( string $key ): mixed {
			return $this->params[ $key ] ?? null;
		}

		public function set_param( string $key, mixed $value ): void {
			$this->params[ $key ] = $value;
		}
	}
}

/**
 * Minimal stand-in for WP_REST_Response (data holder).
 */
if ( ! class_exists( 'WP_REST_Response' ) ) {
	class WP_REST_Response {

		/**
		 * @param mixed $data Response data.
		 */
		public function __construct( public mixed $data = null ) {
		}

		public function get_data(): mixed {
			return $this->data;
		}
	}
}

/**
 * Minimal stand-in for WP_REST_Server (HTTP method constants).
 */
if ( ! class_exists( 'WP_REST_Server' ) ) {
	class WP_REST_Server {
		public const READABLE  = 'GET';
		public const CREATABLE = 'POST';
	}
}

/**
 * Minimal stand-in for the block pattern category registry singleton.
 *
 * Tests assign their own double to {@see self::$instance}.
 */
if ( ! class_exists( 'WP_Block_Pattern_Categories_Registry' ) ) {
	class WP_Block_Pattern_Categories_Registry {
		public static mixed $instance = null;

		public static function get_instance(): mixed {
			return self::$instance;
		}
	}
}

/**
 * Minimal stand-in for the block patterns registry singleton.
 *
 * Tests assign their own double to {@see self::$instance}.
 */
if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
	class WP_Block_Patterns_Registry {
		public static mixed $instance = null;

		public static function get_instance(): mixed {
			return self::$instance;
		}
	}
}
