<?php
if (!defined('ABSPATH')) exit;

// Include Plugin Update Checker Library
require plugin_dir_path(__FILE__) . '../vendor/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updater = PucFactory::buildUpdateChecker(
    'https://github.com/vaibhav-pratap/qr-code-generator/',
    __FILE__,
    'qr-codes-generator'
);

// Set branch (default: master)
$updater->setBranch('master');
?>
