<?php
/**
 * Plugin Name: Super Links Multisite
 * Plugin URI:  https://wpsuperlinks.top/
 * Description: O Super Links Multisite permite que você crie links usando seu próprio nome de domínio que redirecionam para seus links de afiliado de forma camuflada ou não. Versão configurada para WordPress Multisite com subdomínios.
 * Version:     4.0.28-multisite
 * Author:      Carlos Lourenço, Fábio Vasconcelos, Thiago Tedeschi
 * Author URI:  https://wpsuperlinks.top/
 * Network:     true
 *
 *
 * @link    https://wpsuperlinks.top/
 * @since   1.0.0
 * @package Super_Links_Multisite
 */

if(!defined('ABSPATH')) { die('You are not authorized to access this'); }

// Remove a constante TCF que é usada para ativação
// define('SUPER_LINKS_TCF','e782e77a5b577f4f4f6091e01821dadd');
define('SUPER_LINKS_PLUGIN_SLUG','superLinks');
define('SUPER_LINKS_PLUGIN_NAME','super-links-multisite');
define('SUPER_LINKS_PATH',WP_PLUGIN_DIR.'/'.SUPER_LINKS_PLUGIN_NAME);
define('SUPER_LINKS_CONTROLLERS_PATH',SUPER_LINKS_PATH.'/application/controllers');
define('SUPER_LINKS_MODELS_PATH',SUPER_LINKS_PATH.'/application/models');
define('SUPER_LINKS_HELPERS_PATH',SUPER_LINKS_PATH.'/application/helpers');
define('SUPER_LINKS_VIEWS_PATH',SUPER_LINKS_PATH.'/application/views');
define('SUPER_LINKS_LIB_PATH',SUPER_LINKS_PATH.'/application/lib');
define('SUPER_LINKS_CSS_PATH',SUPER_LINKS_PATH.'/assets/css');
define('SUPER_LINKS_JS_PATH',SUPER_LINKS_PATH.'/assets/js');
define('SUPER_LINKS_IMAGES_PATH',SUPER_LINKS_PATH.'/assets/images');
define('SUPER_LINKS_BOOTSTRAP_PATH',SUPER_LINKS_PATH.'/assets/bootstrap');
define('SUPER_LINKS_LANGUAGES_PATH',SUPER_LINKS_PATH.'/languages');
define('SUPER_LINKS_ELEMENTS_PATH',SUPER_LINKS_PATH.'/elements');

define('SUPER_LINKS_URL',plugins_url($path = '/'.SUPER_LINKS_PLUGIN_NAME));
define('SUPER_LINKS_CONTROLLERS_URL',SUPER_LINKS_URL.'/application/controllers');
define('SUPER_LINKS_MODELS_URL',SUPER_LINKS_URL.'/application/models');
define('SUPER_LINKS_HELPERS_URL',SUPER_LINKS_URL.'/application/helpers');
define('SUPER_LINKS_VIEWS_URL',SUPER_LINKS_URL.'/application/views');
define('SUPER_LINKS_LIB_URL',SUPER_LINKS_URL.'/application/lib');
define('SUPER_LINKS_CSS_URL',SUPER_LINKS_URL.'/assets/css');
define('SUPER_LINKS_JS_URL',SUPER_LINKS_URL.'/assets/js');
define('SUPER_LINKS_IMAGES_URL',SUPER_LINKS_URL.'/assets/images');
define('SUPER_LINKS_BOOTSTRAP_URL',SUPER_LINKS_URL.'/assets/bootstrap');
define('SUPER_LINKS_LANGUAGES_URL',SUPER_LINKS_URL.'/languages');
define('SUPER_LINKS_SELECT2_URL',SUPER_LINKS_URL.'/assets/select2');
define('SUPER_LINKS_COLORPICKER_URL',SUPER_LINKS_URL.'/assets/colorpicker');
define('SUPER_LINKS_ELEMENTS_URL',SUPER_LINKS_URL.'/elements');

define('SUPER_LINKS_WEB_API', 'https://app.wpsuperlinks.top');

$linkPaginaWp = get_bloginfo('wpurl');
define('SUPER_LINKS_TEMPLATE_URL', $linkPaginaWp);

define('SUPER_LINKS_PROBLEM_SSL', false);

//Verifica se os links permanentes estão configurados como nome do post
$isPostNameAtivo = true;
if ( get_option('permalink_structure') != '/%postname%/' ) {
    $isPostNameAtivo = true;
}

define('SUPER_LINKS_PERMALINK_OK', $isPostNameAtivo);
define('SUPER_LINKS_PAGE_INICIAL', SUPER_LINKS_TEMPLATE_URL.'/wp-admin/admin.php?page=super_links');


// Versão do banco de dados atual
define('SUPER_LINKS_DB_VERSION', '1.0.40');
define('SUPER_LINKS_VERIFIED_VERSION', '4.0.28-multisite');