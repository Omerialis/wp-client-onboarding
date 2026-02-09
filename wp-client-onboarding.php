<?php
/**
 * Plugin Name: WP Client Onboarding
 * Description: Embedded user manual for client onboarding
 * Version: 1.0.0
 * Author: Omerialis
 * License: GPLv2
 * Text Domain: wp-client-onboarding
 * Domain Path: /languages
 * Requires PHP: 8.0
 *
 * @package WpClientOnboarding
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

// Define plugin constants.
define('WCOB_VERSION', '1.0.0');
define('WCOB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCOB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCOB_PLUGIN_FILE', __FILE__);

// Load the autoloader.
require_once WCOB_PLUGIN_DIR . 'includes/Autoloader.php';

// Register the autoloader.
\WpClientOnboarding\Autoloader::register();

// Instantiate the plugin.
$plugin = new \WpClientOnboarding\Plugin();
$plugin->run();

// Register activation hook.
register_activation_hook(__FILE__, [\WpClientOnboarding\Capabilities::class, 'activate']);

// Register deactivation hook.
register_deactivation_hook(__FILE__, [\WpClientOnboarding\Capabilities::class, 'deactivate']);
