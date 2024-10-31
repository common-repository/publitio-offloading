<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://publit.io
 * @since             1.0.0
 * @package           Publitio
 *
 * @wordpress-plugin
 * Plugin Name:       Publitio Offloading
 * Plugin URI:        https://publit.io/
 * Description:       This WordPress plugin offloads your media library to Publitio
 * Version:           1.2.2
 * Author:            Publitio
 * Author URI:        https://publit.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       publitio
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    die;
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}
define('PUBLITIO_OFFLOADING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PUBLITIO_OFFLOADING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PUBLITIO_OFFLOADING_PLUGIN', plugin_basename(__FILE__));
define('PUBLITIO_OFFLOADING_PUBLITIO_MEDIA', 'https://media.publit.io/file/');
define('PUBLITIO_OFFLOADING_PLUGIN_NAME_VERSION', '1.2.2');

/**
 * The code that runs during plugin activation.
 */
function activate_publitio_offloading()
{
    require_once PUBLITIO_OFFLOADING_PLUGIN_PATH . 'includes/class-publitio-offloading-activator.php';
    Publitio_Offloading_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_publitio_offloading()
{
    require_once PUBLITIO_OFFLOADING_PLUGIN_PATH . 'includes/class-publitio-offloading-deactivator.php';
    Publitio_Offloading_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_publitio_offloading');
register_deactivation_hook(__FILE__, 'deactivate_publitio_offloading');

require PUBLITIO_OFFLOADING_PLUGIN_PATH . 'includes/class-publitio-offloading-init.php';

/**
 * Load all necessary classes
 */
PWPO_Init::pwpo_get_services();
