<?php
/*
 * Plugin Name: 42videobricks
 * Plugin URI: https://42videobricks.com
 * Description: <a target="_blank" href="https://42videobricks.com">42videobricks</a> handles the complexity of video for you: no infrastructure required, no CapEx, no complexity! Simply add our embed code to your WordPress site or service to add video.
 * Version: 1.0.0
 * Author: 42videobricks
 */

define ('VIDEOBRICKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
require_once VIDEOBRICKS_PLUGIN_DIR . 'includes/42videobricks_pages.php';
require_once VIDEOBRICKS_PLUGIN_DIR . 'includes/42videobricks_functions.php';
require_once VIDEOBRICKS_PLUGIN_DIR . 'includes/42videobricks_add_new_video.php';
require_once VIDEOBRICKS_PLUGIN_DIR . 'includes/42videobricks_settings.php';
require_once VIDEOBRICKS_PLUGIN_DIR . 'includes/42videobricks_admin_list_table.php';
?>
