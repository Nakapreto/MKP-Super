<?php

if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class CoreController extends SuperLinksFramework
{

    protected $superLinksModel;

    public function __construct($model = null, $hooks = [], $filters = [])
    {
        $this->setScenario('super_links');

        $this->setModel($model);
        $this->superLinksModel = $this->loadModel();

        $this->init($hooks, $filters);
    }

    public function init($hooks = [], $filters = [])
    {
        $hooks = array_merge($hooks, $this->basicHooks());
        $filters = array_merge($filters, $this->basicFilters());

        parent::init($hooks, $filters);
    }

    private function basicHooks()
    {
        $installOrUpdatePlugin = $this->superLinksModel
            ->should_install();

        $hooks = [
            ['hook' => 'init', 'function' => array($this, 'installSuperLinks')],
            ['hook' => 'admin_menu', 'function' => array($this, 'add_super_links_menu'), 'priority' => 9],
            ['hook' => 'admin_head', 'function' => array($this, 'removeMenuView')],
            ['hook' => 'the_content', 'function' => [$this, 'implementLinksContent'], 99],
            ['hook' => 'wp_enqueue_scripts', 'function' => [$this, 'load_automatic_link_script']],
            ['hook' => 'wp_footer', 'function' => [$this, 'inputUrl']],
            ['hook' => 'admin_head', 'function' => array($this, 'openAffiliateLinkBlank')],
            ['hook' => 'admin_head', 'function' => array($this, 'openDebugFacebookLinkBlank')],
            ['hook' => 'admin_head', 'function' => array($this, 'openDebugFAQLinkBlank')],
            ['hook' => 'admin_head', 'function' => array($this, 'openAdsLibraryLinkBlank')],
            ['hook' => 'rest_api_init', 'function' => array($this, 'prefix_register_user_routes')],
            ['hook' => 'init', 'function' => array($this, 'create_popup_posts')],
            ['hook' => 'init', 'function' => array($this, 'update_permalinks_config_superlinks')],
            ['hook' => 'init', 'function' => array($this, 'atualiza_git_token')],
            ['hook' => 'init', 'function' => array($this, 'testSplCont')],
//	        ['hook' => 'wp_loaded', 'function' => array($this, 'usuarioLogadoSPLClone')],
//	        ['hook' => 'wp_logout', 'function' => array($this, 'removeSPLCLogout')],
        ];

        if(!$installOrUpdatePlugin){
            $hooks = array_merge($hooks, [['hook' => 'plugins_loaded', 'function' => array($this, 'interceptUrl')]]);
            $hooks = array_merge($hooks, [['hook' => 'wp_footer', 'function' => array($this, 'interceptUrlForCookies')]]);
        }

        if($this->isSuperLinksPage()) {
            $specificHooksSuperLinks = [
                ['hook' => 'admin_enqueue_scripts', 'function' => array($this, 'load_scripts')],
                ['hook' => 'in_admin_header', 'function' => array($this, 'header'), 'priority' => 0],
                ['hook' => 'init', 'function' => array($this, 'superLinksTranslation')],
                ['hook' => 'admin_head', 'function' => array($this, 'removeNoticeAdminSuperlinks')],
            ];
            $hooks = array_merge($hooks, $specificHooksSuperLinks);
        }

        return $hooks;
    }

    private function basicFilters()
    {
        $filters = [];

        if($this->isSuperLinksPage()) {
            $filters = array_merge($filters, [ ['hook' => 'admin_footer_text', 'function' => array($this, 'footer'), 'priority' => 1, 'accepted_args' => 2]]);
        }

        return $filters;
    }

    public function header()
    {
        require_once SUPER_LINKS_VIEWS_PATH . '/header.php';
    }

    public function footer()
    {
        require_once SUPER_LINKS_VIEWS_PATH . '/footer.php';
    }

    public function add_super_links_menu()
    {
        $superLinks = new SuperLinksController();
        $superLinksAddLink = new SuperLinksAddLinkController('SuperLinksAddLinkModel');
        $superLinksAutomaticLink = new SuperLinksAutomaticLinkController('SuperLinksAutomaticLinksModel');
        $superLinksImport = new SuperLinksImportController('SuperLinksImportModel');
        $superLinksCookies = new SuperLinksCookieLinkController('SuperLinksLinkCookiePageModel');

        add_menu_page(
            $this->getMenuLabelBySlug('super_links'),
            $this->getMenuLabelBySlug('super_links'),
            'manage_options',
            'super_links',
            array($superLinks, 'index'),
            'dashicons-admin-links',
            65
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_list_view'),
            $this->getMenuLabelBySlug('super_links_list_view'),
            'manage_options',
            'super_links_list_view',
            array($superLinksAddLink, 'view'),
            1
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_list_Clones'),
            $this->getMenuLabelBySlug('super_links_list_Clones'),
            'manage_options',
            'super_links_list_Clones',
            array($superLinksAddLink, 'viewClonePages'),
            2
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_add'),
            $this->getMenuLabelBySlug('super_links_add'),
            'manage_options',
            'super_links_add',
            array($superLinksAddLink, 'create'),
            3
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_automatic_list_view'),
            $this->getMenuLabelBySlug('super_links_automatic_list_view'),
            'manage_options',
            'super_links_automatic_list_view',
            array($superLinksAutomaticLink, 'view'),
            4
        );


        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_automatic_link'),
            $this->getMenuLabelBySlug('super_links_automatic_link'),
            'manage_options',
            'super_links_automatic_link',
            array($superLinksAutomaticLink, 'create'),
            5
        );

        add_submenu_page(
            'super_links',
//             $this->getMenuLabelBySlug('super_links_activation'),
//             $this->getMenuLabelBySlug('super_links_activation'),
//             'manage_options',
//             'super_links_activation',
//             array($superLinks, 'activation'),
            6
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_view_link',
            array($superLinksAddLink, 'viewLink'),
            7
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_link',
            array($superLinksAddLink, 'update'),
            8
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_clone_link',
            array($superLinksAddLink, 'cloneLink'),
            9
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_automatic_link',
            array($superLinksAutomaticLink, 'update'),
            10
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_clone_automatic_link',
            array($superLinksAutomaticLink, 'cloneLink'),
            11
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_view_automatic_link',
            array($superLinksAutomaticLink, 'viewLink'),
            12
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_cookiePost_view'),
            $this->getMenuLabelBySlug('super_links_cookiePost_view'),
            'manage_options',
            'super_links_cookiePost_view',
            array($superLinksCookies, 'viewCookies'),
            13
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_cookiePost_add'),
            $this->getMenuLabelBySlug('super_links_cookiePost_add'),
            'manage_options',
            'super_links_cookiePost_add',
            array($superLinksCookies, 'addCookie'),
            14
        );
        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_cookiePost_edit'),
            $this->getMenuLabelBySlug('super_links_cookiePost_edit'),
            'manage_options',
            'super_links_cookiePost_edit',
            array($superLinksCookies, 'editCookie'),
            15
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_group',
            array($superLinksAddLink, 'editGroup'),
            16
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_groupClone',
            array($superLinksAddLink, 'editGroupClone'),
            17
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_import_links'),
            $this->getMenuLabelBySlug('super_links_import_links'),
            'manage_options',
            'super_links_import_links',
            array($superLinksImport, 'importLinks'),
            18
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_config'),
            $this->getMenuLabelBySlug('super_links_config'),
            'manage_options',
            "super_links_config",
            array($superLinks, 'config'),
            19
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_debugFacebook'),
            $this->getMenuLabelBySlug('super_links_debugFacebook'),
            'manage_options',
            "https://developers.facebook.com/tools/debug",
            false,
            20
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_affiliate'),
            $this->getMenuLabelBySlug('super_links_affiliate'),
            'manage_options',
            "https://nodz.top/afiliados-materiais-e-afiliacao",
            false,
            21
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_groupCookie',
            array($superLinksCookies, 'editGroupCookie'),
            22
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_addClone'),
            $this->getMenuLabelBySlug('super_links_addClone'),
            'manage_options',
            'super_links_addClone',
            array($superLinksAddLink, 'createClone'),
            23
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_clone',
            array($superLinksAddLink, 'updateClone'),
            24
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_clone_page',
            array($superLinksAddLink, 'clonePageCloned'),
            25
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_view_clone_page',
            array($superLinksAddLink, 'viewClone'),
            26
        );

        add_submenu_page(
            'super_links',
            '',
            '',
            'manage_options',
            'super_links_edit_AutomaticGroup',
            array($superLinksAutomaticLink, 'editGroup'),
            27
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_faq'),
            $this->getMenuLabelBySlug('super_links_faq'),
            'manage_options',
            "https://wpsuperlinks.top/faq",
            false,
            28
        );

        add_submenu_page(
            'super_links',
            $this->getMenuLabelBySlug('super_links_adsLibrary'),
            $this->getMenuLabelBySlug('super_links_adsLibrary'),
            'manage_options',
            "https://www.facebook.com/ads/library",
            false,
            29
        );

	    add_submenu_page(
		    'super_links',
		    $this->getMenuLabelBySlug('super_links_opniao_Clone'),
		    $this->getMenuLabelBySlug('super_links_opniao_Clone'),
		    'manage_options',
		    'super_links_opniao_Clone',
		    array($superLinksAddLink, 'salvaOpniaoClientePgClonada'),
		    30
	    );
    }

    public function create_popup_posts()
    {

        $labels = array(
            'name'                  => 'Super Links Popups',
            'singular_name'         => 'Super Links Popup',
            'menu_name'             => 'Super Links Popups',
            'name_admin_bar'        => 'Super Links Popup',
            'all_items'             => 'Popups Super Links',
            'add_new_item'          => 'Adicionar novo popup',
        );

        $args = array(
            'label'                 => 'Super Links Popup',
            'description'           => 'Adiciona popups em páginas clonadas',
            'labels'                => $labels,
            'supports'              => array('title', 'editor'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => 'super_links',
            'menu_position'         => null,
            'menu_icon'             => 'dashicons-screenoptions',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'rewrite'               => false,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );

        register_post_type('superlinks', $args);
    }


    public function removeMenuView(){
//         remove_submenu_page( 'super_links', 'super_links_activation' );
        remove_submenu_page( 'super_links', 'super_links_add' );
        remove_submenu_page( 'super_links', 'super_links_view_link' );
        remove_submenu_page( 'super_links', 'super_links_edit_link' );
        remove_submenu_page( 'super_links', 'super_links_clone_link' );
        remove_submenu_page( 'super_links', 'super_links_automatic_link' );
        remove_submenu_page( 'super_links', 'super_links_edit_automatic_link' );
        remove_submenu_page( 'super_links', 'super_links_clone_automatic_link' );
        remove_submenu_page( 'super_links', 'super_links_view_automatic_link' );
        remove_submenu_page( 'super_links', 'super_links_edit_group' );
        remove_submenu_page( 'super_links', 'super_links_cookiePost_add' );
        remove_submenu_page( 'super_links', 'super_links_cookiePost_edit' );
        remove_submenu_page( 'super_links', 'super_links_edit_groupCookie' );
        remove_submenu_page( 'super_links', 'super_links_edit_groupClone' );
        remove_submenu_page( 'super_links', 'super_links_addClone' );
        remove_submenu_page( 'super_links', 'super_links_edit_clone' );
        remove_submenu_page( 'super_links', 'super_links_clone_page' );
        remove_submenu_page( 'super_links', 'super_links_view_clone_page' );
        remove_submenu_page( 'super_links', 'super_links_edit_AutomaticGroup' );
        remove_submenu_page( 'super_links', 'super_links_opniao_Clone' );
    }

    public function scenarios(){
        return array_keys($this->menuLabels());
    }

    public function menuLabels(){
        $superLinksModel = new SuperLinksModel();
        return [
            'super_links' => TranslateHelper::getTranslate('Super Links'),
//             'super_links_activation' => $superLinksModel->isPluginActive()? TranslateHelper::getTranslate('Desativar plugin') : TranslateHelper::getTranslate('Ativar plugin'),
            'super_links_add' => TranslateHelper::getTranslate('Novo link'),
            'super_links_intercept' => TranslateHelper::getTranslate('Interceptador de link'),
            'super_links_list_view' => TranslateHelper::getTranslate('Criar Links'),
            'super_links_list_Clones' => TranslateHelper::getTranslate('Clonar Páginas'),
            'super_links_view_link' => TranslateHelper::getTranslate('Visualizar links'),
            'super_links_edit_link' => TranslateHelper::getTranslate('Editar Link'),
            'super_links_clone_link' => TranslateHelper::getTranslate('Duplicar Link'),
            'super_links_automatic_list_view' => TranslateHelper::getTranslate('Links Inteligentes'),
            'super_links_automatic_link' => TranslateHelper::getTranslate('Criar Link Inteligente'),
            'super_links_edit_automatic_link' => TranslateHelper::getTranslate('Editar Links Inteligentes'),
            'super_links_clone_automatic_link' => TranslateHelper::getTranslate('Duplicar Links Inteligentes'),
            'super_links_view_automatic_link' => TranslateHelper::getTranslate('Métricas dos links Inteligentes'),
            'super_links_edit_group' => TranslateHelper::getTranslate('Editar categoria de links'),
            'super_links_import_links' => TranslateHelper::getTranslate('Importar Links'),
            'super_links_affiliate' => TranslateHelper::getTranslate('Seja um afiliado'),
            'super_links_config' => TranslateHelper::getTranslate('Configurações'),
            'super_links_debugFacebook' => TranslateHelper::getTranslate('Testar link (Facebook)'),
            'super_links_cookiePost_view' => TranslateHelper::getTranslate('Ativar Cookies'),
            'super_links_cookiePost_add' => TranslateHelper::getTranslate('Adicionar Cookie'),
            'super_links_cookiePost_edit' => TranslateHelper::getTranslate('Editar Cookie'),
            'super_links_edit_groupCookie' => TranslateHelper::getTranslate('Editar Categoria'),
            'super_links_edit_groupClone' => TranslateHelper::getTranslate('Editar Categoria de páginas Clonadas'),
            'super_links_addClone' => TranslateHelper::getTranslate('Clonar nova página'),
            'super_links_edit_clone' => TranslateHelper::getTranslate('Editar página clonada'),
            'super_links_clone_page' => TranslateHelper::getTranslate('Clonar página clonada'),
            'super_links_view_clone_page' => TranslateHelper::getTranslate('Visualizar página clonada'),
            'super_links_viewPopup' => TranslateHelper::getTranslate('Popups'),
            'super_links_edit_AutomaticGroup' => TranslateHelper::getTranslate('Editar categoria de links inteligentes'),
            'super_links_faq' => TranslateHelper::getTranslate('Perguntas frequentes'),
            'super_links_adsLibrary' => TranslateHelper::getTranslate('Espionar anúncios concorrentes'),
            'super_links_opniao_Clone' => TranslateHelper::getTranslate('Opnião página clonada'),
        ];
    }

    public function getMenuLabelBySlug($slug){
        return $this->menuLabels()[$slug];
    }

    public function load_scripts()
    {
        wp_enqueue_script('super_links_jquery_js', SUPER_LINKS_JS_URL . '/jquery.min.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_script('super_links_bootstrap_js', SUPER_LINKS_BOOTSTRAP_URL . '/js/bootstrap.bundle.min.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_script('spl_notification_js', SUPER_LINKS_JS_URL . '/Notifier.min.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_script('super_links_select2_js', SUPER_LINKS_SELECT2_URL . '/js/select2.min.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_script('super_links_colorpicker_js', SUPER_LINKS_COLORPICKER_URL . '/js/bootstrap-colorpicker.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_script('super_links_js', SUPER_LINKS_JS_URL . '/super-links.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_script('spl_taggle_js', SUPER_LINKS_JS_URL . '/taggle.js', array(), SUPER_LINKS_VERSION, true);
        wp_enqueue_style('super_links_bootstrap_css', SUPER_LINKS_BOOTSTRAP_URL . '/css/bootstrap.min.css', array(), SUPER_LINKS_VERSION);
        wp_enqueue_style('super_links_css', SUPER_LINKS_CSS_URL . '/super-links.css', array(), SUPER_LINKS_VERSION);
        wp_enqueue_style('super_links_fontawesome_css', SUPER_LINKS_CSS_URL . '/all.css', array(), SUPER_LINKS_VERSION);
        wp_enqueue_style('super_links_select2_css', SUPER_LINKS_SELECT2_URL . '/css/select2.min.css', array(), SUPER_LINKS_VERSION);
        wp_enqueue_style('super_links_colorpicker_css', SUPER_LINKS_COLORPICKER_URL . '/css/bootstrap-colorpicker.css', array(), SUPER_LINKS_VERSION);
        wp_enqueue_media();
    }


    public function installSuperLinks()
    {
        @ignore_user_abort(true);
        @set_time_limit(0);

        $this->superLinksModel
             ->superLinks_install();
    }

    private function isSuperLinksPage(){
        $currentPage = $this->getCurrentPage();
        $isSuperPage = false;

        if(!$currentPage){
            return false;
        }

        foreach($this->scenarios() as $scenario){
            if($currentPage == $scenario){
                $isSuperPage = true;
            }
        }

        return $isSuperPage;
    }


    public function superLinksTranslation() {
        load_plugin_textdomain( SUPER_LINKS_PLUGIN_NAME, false, SUPER_LINKS_LANGUAGES_PATH );
    }

    public function interceptUrl(){
        $intercepLink = new SuperLinksInterceptLinkController( 'SuperLinksAddLinkModel' );
        $intercepLink->index();
    }

    public function interceptUrlForCookies(){
        $cookiesLinks = new SuperLinksCookieLinkController('SuperLinksLinkCookiePageModel');
        $cookiesLinks->execCookieSuperLinks();
    }

    public function implementLinksContent($content)
    {
        $superLinksModel = new SuperLinksModel();

        if($superLinksModel->isPluginActive()){
            $links = new SuperLinksAutomaticLinksModel();
            $linkData = $links->getAllDataByParam('1','active');

            $isSamePageRedirect = false;

            $urlPage = $this->getCurrentUrl();

            $urlPage = $this->removeBarraUrlSuperLinks($urlPage);
            $linksParaPostDiferente = array();
            foreach($linkData as $linkDatum){
                $urlLink = $this->removeBarraUrlSuperLinks($linkDatum->url);
                if($urlPage == $urlLink){
                    $isSamePageRedirect = true;
                }else{
	                $linksParaPostDiferente[] = $linkDatum;
                }
            }

            if(!$isSamePageRedirect) {
                return (new TextHelper($content))
                    ->addLinks($linkData)
                    ->getText();
            }else{
	            return (new TextHelper($content))
		            ->addLinks($linksParaPostDiferente)
		            ->getText();
            }
        }else{
            return $content;
        }
    }

    private function removeBarraUrlSuperLinks($url){
        $splitUrl = explode('/',$url);
        $tam = count($splitUrl) - 1;
        if(!$splitUrl[$tam]){
            unset($splitUrl[$tam]);
        }
        $splitUrl = implode('/',$splitUrl);
        return $splitUrl;
    }

    public function testSplCont(){
        $transient_key = 'ultima_atualizacaoSPL';
        if ( false === get_transient( $transient_key ) ) {
            $superlinksModel = new SuperLinksModel();
            $license_key = get_option('spl_code_top');
            $superlinksModel->setAttribute('license_key', $license_key);

            $api_params = array(
                'slm_action' => 'slm_check',
                'secret_key' => $superlinksModel->getSecretKey(),
                'license_key' => $license_key,
            );

            $query = esc_url_raw(add_query_arg($api_params, $superlinksModel->getServerUrl()));
            $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

            if (is_wp_error($response)) {
                $this->pageData['message'] = TranslateHelper::getTranslate(".");
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ($license_data->result == 'success') {

                $statusLic = strtolower($license_data->status);
                if ($statusLic == 'blocked') {
                    $superlinksModel->desativaPlugin();
                }

            }

            set_transient( $transient_key, true, DAY_IN_SECONDS );
        }
    }

    public function inactivateUser($data) {

        $license_key = $data->get_param('license_key');
        $valid_domain = $data->get_param('valid_domain');
        $access_token = $data->get_param('access_token');

        $response = new WP_REST_Response();


        if(!$this->verifyClientSpl($access_token)){
            $response->set_data(['status' => false]);
            $response->set_status(200);
            return $response;
        }

        if(!$license_key || !$valid_domain){
            $response->set_data(['status' => false]);
            $response->set_status(200);
            return $response;
        }

        $superLinksModel = new SuperLinksModel();

        if($superLinksModel->verifyLicense($license_key) && $this->isSameDomain($valid_domain)) {

            $superLinksModel->desativaPlugin();
            $response->set_data(['status' => true]);
            $response->set_status(200);
            return $response;
        }

        $response->set_data(['status' => false]);
        $response->set_status(200);
        return $response;
    }

    private function verifyClientSpl($access_token = ''){
        return $access_token == 'mistVAvdCXthnyqMWG5XhJXTc8VHC';
    }

    private function isSameDomain($url = ''){


        $host = $this->removeHttpsAndWww($_SERVER['HTTP_HOST']);
        $url = $this->removeHttpsAndWww($url);

        if($host == $url){
            return true;
        }else{
            return false;
        }
    }

    private function removeHttpsAndWww($input = ''){

        // in case scheme relative URI is passed, e.g., //www.google.com/
        $input = trim($input, '/');

        // If scheme not included, prepend it
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }

        $urlParts = parse_url($input);

        // remove www
        $domain = preg_replace('/^www\./', '', $urlParts['host']);

        return $domain;
    }

    public function prefix_register_user_routes() {
        // Here we are registering our route for a collection of products and creation of products.
        register_rest_route( 'splpro/api', '/license', array(
            array(
                // By using this constant we ensure that when the WP_REST_Server changes, our create endpoints will work as intended.
                'methods'  => WP_REST_Server::CREATABLE,
                // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
                'callback' => array($this, 'inactivateUser'),
                'permission_callback' => function ($data) {
                    return verifySuperLinksAccess($data); //Corrige bug que pode causar erro na instalação/atualização do plugin
                }
            ),
        ) );

        register_rest_route( 'splpro/spl', '/teste', array(
            array(
                // By using this constant we ensure that when the WP_REST_Server changes, our create endpoints will work as intended.
                'methods'  => WP_REST_Server::READABLE,
                // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
                'callback' => array($this, 'testSplCont'),
                'permission_callback' => function ($data) {
                    return verifySuperLinksAccess($data); //Corrige bug que pode causar erro na instalação/atualização do plugin
                }
            ),
        ) );
    }

    public function atualiza_git_token() {
        $transient_token = 'ultima_atualizacao_token';

        if (get_transient($transient_token) === false) {

            $consultaToken = wp_remote_get('https://wpsuperlinks.top/token-git/getToken.php');

            if ($consultaToken && !is_wp_error($consultaToken)) {
                $status_code = wp_remote_retrieve_response_code($consultaToken);
                if ($status_code === 200) {
                    $response = json_decode(wp_remote_retrieve_body($consultaToken));
                    if (isset($response->token) && !empty($response->token)) {
                        update_option('spl_plus_token_git', $response->token);
                        wp_cache_delete('alloptions', 'options');
                        set_transient( $transient_token, true, DAY_IN_SECONDS );
                    }
                }
            }
        }
    }

    public function load_automatic_link_script()
    {
        wp_enqueue_script('spl_automaticLink_js', SUPER_LINKS_JS_URL . '/automatic-links.js', array(), SUPER_LINKS_VERSION, true);
    }

    public function inputUrl(){
        $url = SUPER_LINKS_TEMPLATE_URL . '/updateAutomaticMetrics';
        global $post;
        $idPost = isset($post->ID)? $post->ID : null;
        echo "<input type='hidden' value='$url' id='splUrlAutomaticLinks'>";
        echo "<input type='hidden' value='$idPost' id='splPostIdAutomaticLinks'>";
    }

    public function openAffiliateLinkBlank() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery( "ul#adminmenu a[href='https://nodz.top/afiliados-materiais-e-afiliacao']" ).attr( 'target', '_blank' );
            });
        </script>
        <?php
    }

    public function openDebugFacebookLinkBlank() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery( "ul#adminmenu a[href='https://developers.facebook.com/tools/debug']" ).attr( 'target', '_blank' );
            });
        </script>
        <?php
    }


    public function openDebugFAQLinkBlank() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery( "ul#adminmenu a[href='https://wpsuperlinks.top/faq']" ).attr( 'target', '_blank' );
                jQuery( "ul#adminmenu a[href='https://wpsuperlinks.top/faq']" ).css( 'color', 'green' );
            });
        </script>
        <?php
    }


    public function openAdsLibraryLinkBlank() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery( "ul#adminmenu a[href='https://www.facebook.com/ads/library']" ).attr( 'target', '_blank' );
            });
        </script>
        <?php
    }

    public function removeNoticeAdminSuperlinks(){
        remove_all_actions( 'admin_notices' );
    }

    public function update_permalinks_config_superlinks() {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();
    }

	public function usuarioLogadoSPLClone() {
		$current_user = wp_get_current_user();
		if ( $current_user->ID != 0 ) {
			setcookie( 'splUserLog', 'logged-in', time() + 3600, '/' );
		} else {
			setcookie( 'splUserLog', '', time() - 3600, '/' );
		}
	}

	function removeSPLCLogout() {

		setcookie( 'splUserLog', '', time() - 3600, '/' );

	}
}