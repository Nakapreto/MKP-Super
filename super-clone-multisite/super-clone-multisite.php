<?php
/*
Plugin Name: Super Clone Multisite
Description: Clonador de sites para WordPress Multisite (subdomínios), baseado no super-links.zip.
Version: 1.0.0
Author: Seu Nome
Network: true
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Ativa para toda a rede multisite
global $wpdb;

// Inclui as funções principais de clonagem
define('SCM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SCM_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once SCM_PLUGIN_PATH . 'includes/class-scm-cloner.php';
require_once SCM_PLUGIN_PATH . 'includes/scm-admin-page.php';

// Hooks de ativação/desativação
register_activation_hook(__FILE__, ['SCM_Cloner', 'activate']);
register_deactivation_hook(__FILE__, ['SCM_Cloner', 'deactivate']);