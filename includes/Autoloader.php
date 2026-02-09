<?php
/**
 * PSR-4 Autoloader for WpClientOnboarding namespace.
 *
 * @package WpClientOnboarding
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

namespace WpClientOnboarding;

/**
 * PSR-4 Autoloader class.
 *
 * Maps WpClientOnboarding\ClassName to includes/ClassName.php
 */
class Autoloader {

	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	public static function register(): void {
		spl_autoload_register([self::class, 'load']);
	}

	/**
	 * Load a class file.
	 *
	 * @param string $class The fully qualified class name.
	 * @return void
	 */
	public static function load(string $class): void {
		// Check if the class is in the WpClientOnboarding namespace.
		if (strpos($class, __NAMESPACE__ . '\\') !== 0) {
			return;
		}

		// Remove the namespace prefix.
		$relative_class = substr($class, strlen(__NAMESPACE__) + 1);

		// Convert namespace separators to file path separators.
		$file = WCOB_PLUGIN_DIR . 'includes/' . str_replace('\\', '/', $relative_class) . '.php';

		// Load the file if it exists.
		if (file_exists($file)) {
			require_once $file;
		}
	}
}
