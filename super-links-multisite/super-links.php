<?php
/**
 * Plugin Name: Super Links
 * Plugin URI:  https://wpsuperlinks.top/
 * Description: O Super Links permite que você crie links usando seu próprio nome de domínio que redirecionam para seus links de afiliado de forma camuflada ou não.
 * Version:     4.0.28
 * Author:      Carlos Lourenço, Fábio Vasconcelos, Thiago Tedeschi
 * Author URI:  https://wpsuperlinks.top/
 *
 *
 * @link    https://wpsuperlinks.top/
 * @since   1.0.0
 * @package Super_Links
 */

if(!defined('ABSPATH')) { die('You are not authorized to access this'); }

define('SUPER_LINKS_TCF','e782e77a5b577f4f4f6091e01821dadd');
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
define('SUPER_LINKS_VERIFIED_VERSION', '4.0.28');


// Define os atributos declarados no cabeçalho
define('SUPER_LINKS_VERSION', super_links_pro_plugin_info('Version'));
define('SUPER_LINKS_DISPLAY_NAME', super_links_pro_plugin_info('Name'));


if ( is_plugin_active( 'super-links-light/super-links-light.php' ) ) {
    deactivate_plugins('super-links-light/super-links-light.php');
}

if ( is_plugin_active( 'afilia-plug/wp-plug.php' ) ) {
    deactivate_plugins('afilia-plug/wp-plug.php');
}

$facebookVerification = '';
//Adiciona meta tag de verificação Facebook
if(get_option('facebookVerificationSPL')) {
    $facebookVerification = get_option('facebookVerificationSPL');

    function addFacebookVerification()
    {
        echo '<meta name="facebook-domain-verification" content="'.get_option('facebookVerificationSPL').'" />';
    }

    add_action('admin_head', 'addFacebookVerification');
    add_action('wp_head', 'addFacebookVerification');
}

define('SUPER_LINKS_FACEBOOK_VERIFICATION', $facebookVerification);


/**
 * retorna informações da declaração do plugin no cabeçalho
 */
function super_links_pro_plugin_info($field) {
    static $plugin_folder, $plugin_file;

    if( !isset($plugin_folder) or !isset($plugin_file) ) {
        if( ! function_exists( 'get_plugins' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
        $plugin_file = basename( ( __FILE__ ) );
    }

    if(isset($plugin_folder[$plugin_file][$field])) {
        return $plugin_folder[$plugin_file][$field];
    }

    return '';
}

/**
 * carrega automaticamente os arquivos do plugin
 * @param $class
 */
function super_links_pro_autoloader($class) {
    if(preg_match('/^.+Controller$/', $class)) {
        $filepath = SUPER_LINKS_CONTROLLERS_PATH."/{$class}.php";
    }
    else if(preg_match('/^.+Helper$/', $class)) {
        $filepath = SUPER_LINKS_HELPERS_PATH."/{$class}.php";
    }
    else {
        $filepath = SUPER_LINKS_MODELS_PATH."/{$class}.php";

        if(!file_exists($filepath)) {
            $filepath = SUPER_LINKS_LIB_PATH."/{$class}.php";
        }
    }

    if(file_exists($filepath)) {
        require_once($filepath);
    }
}

if(is_array(spl_autoload_functions()) && in_array('__autoload', spl_autoload_functions())) {
    spl_autoload_register('__autoload');
}

spl_autoload_register('super_links_pro_autoloader');

function vapspl( $true, $plugin ) {
    if ( isset( $plugin['plugin'] ) && 'super-links/super-links.php' === $plugin['plugin'] ) {

        $superlinksModel = new SuperLinksModel();
        $ret = $superlinksModel->vapsplmodel();
        if(isset($ret['error']) && isset($ret['msg']) && isset($ret['code']) && $ret['error'] && $ret['msg'] && $ret['code']){
            return new WP_Error($ret['code'], $ret['msg']);
        }
    }

    return $true;
}

add_filter('upgrader_pre_install', 'vapspl', 10, 2);

function diferenceUrlSuperLinks($currentUrl = ''){
    $urlTemplate = parse_url(SUPER_LINKS_TEMPLATE_URL,PHP_URL_PATH);

	if(is_null($urlTemplate)){
		$urlTemplate = '';
	}

    $urlTemplate = explode('/',$urlTemplate);

    $currentUrl = parse_url($currentUrl,PHP_URL_PATH);
    $currentUrl = explode('/',$currentUrl);

    $result = array_diff($currentUrl,$urlTemplate);
    if($result) {
        $keywordSuperLinks = implode('/', $result);
    }else{
        $keywordSuperLinks = "";
    }

    return $keywordSuperLinks;
}
// Função de verificação de acesso removida para versão multisite
// UPDATE - Seção de atualização automática removida para versão multisite

// Carrega o controller principal
require_once(SUPER_LINKS_CONTROLLERS_PATH."/CoreController.php");
$coreController = new CoreController("SuperLinksModel");
