<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

require_once SUPER_LINKS_HELPERS_PATH . '/geolocation/vendor/autoload.php';
use GeoIp2\Database\Reader;

class SuperLinksInterceptLinkController extends SuperLinksFramework
{

    protected $interceptLink;

    public $expireTimeCache = 3;

    public $expireDaysToUniqueAccess = 6;

    public $isUniqueAccess = false;

    public function __construct($model = null, $hooks = [], $filters = [])
    {
        $this->setScenario('super_links_intercept');

        $this->setModel($model);
        $this->interceptLink = $this->loadModel();

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
        return [];
    }

    private function basicFilters()
    {
        return [];
    }

    public function index(){

//        if(get_option('enable_redis_superLinks')) {
//            try {
//                if (extension_loaded('redis')) {
//                    $redis = new Redis();
//                    $redis->connect('localhost', 6379);
//                    $redis->flushAll();
//                }
//            } catch (Throwable $t) {
//                update_option('enable_redis_superLinks', false);
//                wp_cache_delete('alloptions', 'options');
//            } catch (Exception $e) {
//                update_option('enable_redis_superLinks', false);
//                wp_cache_delete('alloptions', 'options');
//            }
//        }

        $url = $this->getCurrentUrl();

        $this->pageData = array_merge(['url' => $url], $this->pageData);

	    if($url == SUPER_LINKS_TEMPLATE_URL . '/phpinfospl'){
		    phpinfo();
			die();
	    }

        $dadosPaginaGringa = $this->getPaginaBrancaGringa($url);

	    if(isset($dadosPaginaGringa['dados']) && $dadosPaginaGringa['dados']){

            $this->pageData = array_merge(["dadosPaginaGringa" => $dadosPaginaGringa['dados'], 'idLinkCheckout' => $dadosPaginaGringa['idLinkCheckout']], $this->pageData);
	        $this->doPaginaBrancaGringaRedirect($dadosPaginaGringa);
	        die();
        }

        $idSuperLink = $this->getIDSuperLink($url);

        if(!$idSuperLink) {
            return;
        }

        $addLinkModel = new SuperLinksAddLinkModel();

        $addLinkModel->loadDataByID($idSuperLink);

	    $redirectType = $addLinkModel->getAttribute('redirectType');

		if($redirectType == 'clonador'){
			if (is_plugin_active('super-boost/super-boost.php')) {
				return;
			}
		}

	    $affiliateLinkModel = new SuperLinksAffiliateLinkModel();
	    $monitoringModel = new SuperLinksLinkMonitoringModel();
	    $cloakModel = new SuperLinksLinkCloakModel();
	    $configSocialModel = new SuperLinksLinkConfigSocialModel();
	    $waitPage = new SuperLinksWaitPageModel();
	    $clonePage = new SuperLinksLinkClonePageModel();
	    $apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
        $paginaBrancaGringaModel = new SuperLinksPgBrancaGringaModel();

        $monitoringData = $monitoringModel->getAllDataByParam($idSuperLink,'idLink');

        $cloakData = $cloakModel->getAllDataByParam($idSuperLink,'idLink');

        $configSocialData = $configSocialModel->getAllDataByParam($idSuperLink,'idLink');

        $waitPageData = $waitPage->getAllDataByParam($idSuperLink,'idLink');

        $clonePageData = $clonePage->getAllDataByParam($idSuperLink,'idLink');

        if($clonePageData){
            $this->pageData = array_merge(['cloneData' => $clonePageData],$this->pageData);
        }

        $pgBrancaGringaData = $paginaBrancaGringaModel->getAllDataByParam($idSuperLink,'idLink');

        if($pgBrancaGringaData){
            $this->pageData = array_merge(['pgBrancaGringa' => $pgBrancaGringaData],$this->pageData);
        }

        $ip = $this->getClientIp();

        if($waitPageData){
            $waitPageData = array_shift($waitPageData);
            $waitPageData = get_object_vars($waitPageData);

            if($waitPageData['textLoadPage']){
                $this->pageData = array_merge(['textLoadPage' => $waitPageData['textLoadPage']],$this->pageData);
            }

            if($waitPageData['showSpinner']){
                $this->pageData = array_merge(['showSpinner' => $waitPageData['showSpinner']],$this->pageData);
            }
        }

        if($configSocialData){
            $configSocialData = array_shift($configSocialData);
            $configSocialData = get_object_vars($configSocialData);

            if($configSocialData['textTitle']){
                 $this->pageData = array_merge(['pageTitle' => $configSocialData['textTitle']],$this->pageData);
            }

            if($configSocialData['description']){
                $this->pageData = array_merge(['pageDescription' => $configSocialData['description']],$this->pageData);
            }

            if($configSocialData['image']){
                $this->pageData = array_merge(['pageImage' => $configSocialData['image']],$this->pageData);
            }
        }

        $redirectDelay = $addLinkModel->getAttribute('redirectDelay');

        if($monitoringData){
            $monitoringData = array_shift($monitoringData);
            $monitoringData = get_object_vars($monitoringData);
            $monitoringData['monitoringModel'] = $monitoringData;

            $this->pageData = array_merge($monitoringData,$this->pageData);

            //existe rastreamento e precisa de setar o redirect default
            if($monitoringData['googleMonitoringID'] || $monitoringData['pixelID'] || $monitoringData['track'] || $monitoringData['codeHeadPage'] || $monitoringData['codeBodyPage'] || $monitoringData['codeFooterPage']){
                $redirectDelay = $addLinkModel->getDefaultRedirectDelay();
            }

            if($monitoringData['enableApiFacebook'] == 'enabled') {
                $pixel = $monitoringData['pixelApiFacebook'];
                $testEvent = $monitoringData['testEventApiFacebook'];
                $token = $monitoringData['tokenApiFacebook'];

                $eventoApiFacebook = $apiConvertFaceModel->getAllDataByParam($idSuperLink,'idLink');
                if($eventoApiFacebook) {
                    $eventoApiFacebook = array_shift($eventoApiFacebook);
                    $event_name = $eventoApiFacebook->eventNameApiFacebook;
                    $event_id = $eventoApiFacebook->eventIdApiFacebook;
                }

                if($token && $event_name && $pixel) {
                    $this->sendPostApiFacebook($ip, $event_name, $event_id, $pixel, $testEvent, $token);
                }
            }
        }

        $this->pageData = array_merge($addLinkModel->getAttributes(),$this->pageData);

        if(!$addLinkModel->getAttributes()){
            return;
        }

        $abLastTest = $addLinkModel->getAttribute('abLastTest');

        if(!$abLastTest){
            $abLastTest = 0;
        }

        $atualTestAb = $abLastTest;

        $lastCacheTime = date("Y-m-d H:i");


        if(!isset($_COOKIE['ipClient'])) {
            setcookie('ipClient', $ip, time() + (86400 * $this->expireDaysToUniqueAccess), "/");
            $this->isUniqueAccess = true;
        }

        if($redirectType != 'facebook' && $redirectType != 'clonador'){
            $atualTestAb = $this->getAtualTestAb($affiliateLinkModel, $idSuperLink, $abLastTest);
            $addLinkModel->updateLastTestAb($atualTestAb);
        }

        $affiliateLinkData = $affiliateLinkModel->getAllDataByParam($idSuperLink, 'idLink', 'ORDER BY id ASC', 'limit 1', 'OFFSET ' . $atualTestAb);
        $affiliateLinkData = array_shift($affiliateLinkData);
        $affiliateUrl = trim($affiliateLinkData->affiliateUrl);

        //Só habilita a passagem de parametros se não for teste AB
        $existeTesteAB = $affiliateLinkModel->getAllDataByParam($idSuperLink, 'idLink', 'ORDER BY id ASC', '', '');
        if(count($existeTesteAB) < 2) {
            $this->ehCamufladorETemParametrosUrl($affiliateUrl, $redirectType);
        }


        $cloakIsActive = false; // seta para true caso a página precise ser aberta com cloak

        if($cloakData){
            $cloakData = array_shift($cloakData);
            $cloakData = get_object_vars($cloakData);

            $urlCloak = $this->getUrlCloak($cloakData, $ip);
            if($urlCloak){
                $affiliateUrl = trim($urlCloak);
                $cloakIsActive = true;
            }
        }

        $this->pageData = array_merge(["affiliateUrl" => $affiliateUrl], $this->pageData);

        // Atualiza métricas
        $isFacebookLink = false;
        if($redirectType == 'facebook') {
            $isFacebookLink = true;
        }

        $metricsModel = new SuperLinksLinkMetricsModel();
        $metricsModel->updateMetricsByIDLink($affiliateLinkData->id, $this->isUniqueAccess, $cloakIsActive, $isFacebookLink);
        setcookie('timeIpClient',$lastCacheTime,time() + $this->expireTimeCache,"/");

		// atualiza ip de acesso
	    if(!isset($_COOKIE['ipClientUrlSpl'])) {
		    setcookie('ipClientUrlSpl', $ip, time() + 10, "/");
		    $urlCompleta = $this->get_current_url_completa();
		    $ipModel = new SuperLinksIpModel();
		    $ipModel->updateIpByIDLink($ip, $idSuperLink, $urlCompleta);
	    }


        if(!$redirectDelay){
            $redirectDelay = 0;
        }

        $this->pageData = array_merge(["redirectDelay" => $redirectDelay], $this->pageData);

        //Redirecionamento no btn voltar
        $redirectBtn = $addLinkModel->getAttribute('redirectBtn');
        if($redirectBtn) {
            $redirectBtn = trim($redirectBtn);
            $this->pageData = array_merge(["urlRedirectBtn" => $redirectBtn], $this->pageData);
        }

        if(!$cloakIsActive && $redirectType == 'facebook'){
            $idLink = $this->getIDSuperLink($affiliateUrl);
            $this->doRedirectUserLinkFacebook($idLink, $affiliateUrl);
            exit;
        }

        switch($redirectType){
            case 'html':
                $this->doHtmlRedirect($affiliateUrl);
                break;
            case 'javascript':
                $this->doJavascriptRedirect($affiliateUrl);
                break;
            case 'camuflador':
                $this->doCamufladorRedirect($affiliateUrl);
                break;
            case 'clonador':
                $this->doClonadorRedirect($affiliateUrl);
                break;
            case 'wpp_tlg':
                $this->doHtmlRedirect($affiliateUrl);
                break;
            case 'pgBranca':
                $this->doPgBranca($affiliateUrl);
                break;
            default:
                $this->doPhpRedirect($affiliateUrl);
                break;
        };

        exit;
    }


    public function sendPostApiFacebook($ip='', $event_name = "ViewContent", $event_id = "", $pixel = "", $testEvent = "", $token = ""){
        $curl = curl_init();

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $actualTime = time();

        $userAgent= $_SERVER['HTTP_USER_AGENT'];

        if($testEvent){
            $testEvent = '"test_event_code": "'.$testEvent.'"';
        }else{
            $testEvent = '';
        }

        $postFields = '
        {
           "data": [
              {
                 "event_name": "'.$event_name.'",
                 "event_time": '.$actualTime.',
                 "event_id": "'.$event_id.'",
                 "event_source_url": "'.$actual_link.'",
                 "user_data": {
                    "client_ip_address": "'.$ip.'",
                    "client_user_agent": "'.$userAgent.'"
                 }
              }
           ],
           '.$testEvent.'
        }
    ';

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://graph.facebook.com/v11.0/$pixel/events?access_token=$token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        ]);

        $result = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    }

    public function doRedirectUserLinkFacebook($idLink = null, $url = ''){

        if(!$idLink || !$url){
            return;
        }

        $this->pageData = array_merge(['url' => $url], $this->pageData);

        $idSuperLink = $idLink;

        $addLinkModel = new SuperLinksAddLinkModel();
        $affiliateLinkModel = new SuperLinksAffiliateLinkModel();
        $monitoringModel = new SuperLinksLinkMonitoringModel();
        $cloakModel = new SuperLinksLinkCloakModel();
        $configSocialModel = new SuperLinksLinkConfigSocialModel();
        $waitPage = new SuperLinksWaitPageModel();

        $addLinkModel->loadDataByID($idSuperLink);

        $monitoringData = $monitoringModel->getAllDataByParam($idSuperLink,'idLink');

        $cloakData = $cloakModel->getAllDataByParam($idSuperLink,'idLink');

        $configSocialData = $configSocialModel->getAllDataByParam($idSuperLink,'idLink');

        $waitPageData = $waitPage->getAllDataByParam($idSuperLink,'idLink');

        if($waitPageData){
            $waitPageData = array_shift($waitPageData);
            $waitPageData = get_object_vars($waitPageData);

            if($waitPageData['textLoadPage']){
                $this->pageData = array_merge(['textLoadPage' => $waitPageData['textLoadPage']],$this->pageData);
            }

            if($waitPageData['showSpinner']){
                $this->pageData = array_merge(['showSpinner' => $waitPageData['showSpinner']],$this->pageData);
            }
        }

        if($configSocialData){
            $configSocialData = array_shift($configSocialData);
            $configSocialData = get_object_vars($configSocialData);

            if($configSocialData['textTitle']){
                $this->pageData = array_merge(['pageTitle' => $configSocialData['textTitle']],$this->pageData);
            }

            if($configSocialData['description']){
                $this->pageData = array_merge(['pageDescription' => $configSocialData['description']],$this->pageData);
            }

            if($configSocialData['image']){
                $this->pageData = array_merge(['pageImage' => $configSocialData['image']],$this->pageData);
            }
        }

        $redirectDelay = $addLinkModel->getAttribute('redirectDelay');

        if($monitoringData){
            $monitoringData = array_shift($monitoringData);
            $monitoringData = get_object_vars($monitoringData);
            $monitoringData['monitoringModel'] = $monitoringData;

            $this->pageData = array_merge($monitoringData,$this->pageData);

            //existe rastreamento e precisa de setar o redirect default
            if($monitoringData['googleMonitoringID'] || $monitoringData['pixelID'] || $monitoringData['track'] || $monitoringData['codeHeadPage'] || $monitoringData['codeBodyPage'] || $monitoringData['codeFooterPage']){
                $redirectDelay = $addLinkModel->getDefaultRedirectDelay();
            }
        }

        $this->pageData = array_merge($addLinkModel->getAttributes(),$this->pageData);

        if(!$addLinkModel->getAttributes()){
            return;
        }

        $redirectType = $addLinkModel->getAttribute('redirectType');
        $abLastTest = $addLinkModel->getAttribute('abLastTest');

        if(!$abLastTest){
            $abLastTest = 0;
        }

        $atualTestAb = $abLastTest;

        $lastCacheTime = date("Y-m-d H:i");
        $ip = $this->getClientIp();

        if(!isset($_COOKIE['ipClient'])) {
            setcookie('ipClient', $ip, time() + (86400 * $this->expireDaysToUniqueAccess), "/");
            $this->isUniqueAccess = true;
        }

        // Só salva o teste atual após a expiração do cache
        if($redirectType != 'facebook'){
            $atualTestAb = $this->getAtualTestAb($affiliateLinkModel, $idSuperLink, $abLastTest);
            $addLinkModel->updateLastTestAb($atualTestAb);
        }

        $affiliateLinkData = $affiliateLinkModel->getAllDataByParam($idSuperLink, 'idLink', 'ORDER BY id ASC', 'limit 1', 'OFFSET ' . $atualTestAb);
        $affiliateLinkData = array_shift($affiliateLinkData);
        $affiliateUrl = trim($affiliateLinkData->affiliateUrl);

        $cloakIsActive = false; // seta para true caso a página precise ser aberta com cloak

        if($cloakData){
            $cloakData = array_shift($cloakData);
            $cloakData = get_object_vars($cloakData);

            $urlCloak = $this->getUrlCloak($cloakData, $ip);
            if($urlCloak){
                $affiliateUrl = trim($urlCloak);
                $cloakIsActive = true;
            }
        }

        $this->pageData = array_merge(["affiliateUrl" => $affiliateUrl], $this->pageData);

        if(!$redirectDelay){
            $redirectDelay = 0;
        }

        $this->pageData = array_merge(["redirectDelay" => $redirectDelay], $this->pageData);

        //Redirecionamento no btn voltar
        $redirectBtn = $addLinkModel->getAttribute('redirectBtn');
        if($redirectBtn) {
            $redirectBtn = trim($redirectBtn);
            $this->pageData = array_merge(["urlRedirectBtn" => $redirectBtn], $this->pageData);
        }

        switch($redirectType){
            case 'html':
                $this->doHtmlRedirect($affiliateUrl);
                break;
            case 'javascript':
                $this->doJavascriptRedirect($affiliateUrl);
                break;
            case 'camuflador':
                $this->doCamufladorRedirect($affiliateUrl);
                break;
            case 'wpp_tlg':
                $this->doHtmlRedirect($affiliateUrl);
                break;
            default:
                $this->doPhpRedirect($affiliateUrl);
                break;
        };

        exit;
    }

    public function getIDSuperLink($url = ''){
        if(empty($url)){
            return false;
        }

        $superLinksModel = new SuperLinksModel();

        if(!$superLinksModel->isPluginActive()){
            return false;
        }

        $addLinkModel = new SuperLinksAddLinkModel();

        $keywordSuperLinks = diferenceUrlSuperLinks($url);

        $keywordSuperLinks = strtolower($keywordSuperLinks);
        $superlink = $addLinkModel->getLinkByKeyword($keywordSuperLinks);
        $superlinkBarra = $addLinkModel->getLinkByKeyword($keywordSuperLinks . "/");

        //verifica se é associacao a uma página do wordpress
        $splitUrl = explode('/',$url);
        $tam = count($splitUrl) - 1;
        if(!$splitUrl[$tam]){
            unset($splitUrl[$tam]);
        }
        $splitUrl = implode('/',$splitUrl);
        $urlBarra = $splitUrl . "/";

        $pageSuperLinks = $addLinkModel->getLinkByPage($splitUrl);
        $pageSuperLinksBarra = $addLinkModel->getLinkByPage($urlBarra);

        if (!$superlink && !$superlinkBarra && !$pageSuperLinks && !$pageSuperLinksBarra) {
            return false;
        }

        $link = "";
        if ($superlink) {
            $link = $superlink;
        } elseif($superlinkBarra) {
            $link = $superlinkBarra;
        }elseif ($pageSuperLinks){
            $link = $pageSuperLinks;
        }elseif ($pageSuperLinksBarra){
            $link = $pageSuperLinksBarra;
        }

        if($link) {
            $link = array_shift($link);
            if($link->statusLink == 'enabled'){
                return $link->id;
            }
        }

        return false;
    }

    public function getPaginaBrancaGringa($url = ''){
        if(empty($url)){
            return false;
        }

        $superLinksModel = new SuperLinksModel();

        if(!$superLinksModel->isPluginActive()){
            return false;
        }

        $addLinkModel = new SuperLinksAddLinkModel();

        $keywordSuperLinks = diferenceUrlSuperLinks($url);

        $keywordSuperLinks = strtolower($keywordSuperLinks);

        $keywordSuperLinks = explode('_',$keywordSuperLinks);
        $idSuperLinks = isset($keywordSuperLinks[0])? $keywordSuperLinks[0] : false;
        $idPgBrancaGringa = isset($keywordSuperLinks[1])? $keywordSuperLinks[1] : false;
        $idLinkCheckout = isset($keywordSuperLinks[2])? $keywordSuperLinks[2] : 0;

        if(!$idSuperLinks || !$idPgBrancaGringa){
            return false;
        }

        $addLinkModel->loadDataByID($idSuperLinks);
        $paginaExisteSuperLinks = $addLinkModel->getAttribute('redirectType');

        if(!$paginaExisteSuperLinks){
            return false;
        }

        $paginaBrancaGringa = new SuperLinksPgBrancaGringaModel();
        $dadosPaginaGringa = $paginaBrancaGringa->getPaginaByIds($idSuperLinks,$idPgBrancaGringa);

        if($dadosPaginaGringa){
            return ['dados' => $dadosPaginaGringa, 'idLinkCheckout' => $idLinkCheckout];
        }

        return false;
    }

    public function doPgBranca($affiliateUrl = ''){
        if(empty($affiliateUrl)){
            return;
        }

        if(isset($this->pageData['pgBrancaGringa'])){
            $dadosPgGringa = array_shift($this->pageData['pgBrancaGringa']);
            $dadosPgGringa->linkPaginaVenda = $this->pageData['affiliateUrl'];
            $this->pageData = array_merge(["dadosPaginaGringa" => $dadosPgGringa, 'idLinkCheckout' => 0], $this->pageData);
            $this->doPaginaBrancaGringaRedirect($dadosPgGringa);
            die();
        }

        header('Cache-Control: max-age='.$this->expireTimeCache);
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?',$uri);

        $uriaffiliateUrl = explode('?',$affiliateUrl);
        $existeParamAfiliado = false;
        if(isset($uriaffiliateUrl[1])){
            $existeParamAfiliado = true;
        }

        if(isset($uri[1])){
            if($existeParamAfiliado){
                header('Location: ' . $affiliateUrl . '&' . $uri[1]);
            }else {
                header('Location: ' . $affiliateUrl . '?' . $uri[1]);
            }
        }else{
            header('Location: ' . $affiliateUrl);
        }
    }

    public function doPhpRedirect($affiliateUrl = ''){
        if(empty($affiliateUrl)){
            return;
        }

        header('Cache-Control: max-age='.$this->expireTimeCache);
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?',$uri);

        $uriaffiliateUrl = explode('?',$affiliateUrl);
        $existeParamAfiliado = false;
        if(isset($uriaffiliateUrl[1])){
            $existeParamAfiliado = true;
        }

        if(isset($uri[1])){
            if($existeParamAfiliado){
                header('Location: ' . $affiliateUrl . '&' . $uri[1]);
            }else {
                header('Location: ' . $affiliateUrl . '?' . $uri[1]);
            }
        }else{
            header('Location: ' . $affiliateUrl);
        }
    }

    public function doHtmlRedirect($affiliateUrl = ''){
        if(empty($affiliateUrl)){
            return;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?',$uri);

        $uriaffiliateUrl = explode('?',$affiliateUrl);
        $existeParamAfiliado = false;
        if(isset($uriaffiliateUrl[1])){
            $existeParamAfiliado = true;
        }

        $todosParametros = '';
        if(isset($uri[1])){
            if($existeParamAfiliado){
                $todosParametros = '&' . $uri[1];
            }else {
                $todosParametros = '?' . $uri[1];
            }
        }

        $this->pageData = array_merge(['paramUrlAfiliate' => $todosParametros], $this->pageData);
        $this->render(SUPER_LINKS_VIEWS_PATH . '/interceptLink/html.php');
    }

    public function doJavascriptRedirect($affiliateUrl = ''){
        if(empty($affiliateUrl)){
            return;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?',$uri);

        $uriaffiliateUrl = explode('?',$affiliateUrl);
        $existeParamAfiliado = false;
        if(isset($uriaffiliateUrl[1])){
            $existeParamAfiliado = true;
        }

        $todosParametros = '';
        if(isset($uri[1])){
            if($existeParamAfiliado){
                $todosParametros = '&' . $uri[1];
            }else {
                $todosParametros = '?' . $uri[1];
            }
        }

        $this->pageData = array_merge(['paramUrlAfiliate' => $todosParametros], $this->pageData);
        $this->render(SUPER_LINKS_VIEWS_PATH . '/interceptLink/javascript.php');
    }

    public function ehCamufladorETemParametrosUrl($affiliateUrl = '', $tipoRedirecionador = ''){

        if(empty($affiliateUrl) || empty($tipoRedirecionador)){
            return;
        }

        if($tipoRedirecionador != 'camuflador'){
            return;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?',$uri);

        $existParametroUrlAtual = false;
        if(isset($uri[1])){
            $existParametroUrlAtual = true;
        }

        $affiliateUrl = explode('?',$affiliateUrl);

        $existParametroUrlAfiliado = false;
        if(isset($affiliateUrl[1])){
            $existParametroUrlAfiliado = true;
        }

        if($existParametroUrlAfiliado && !$existParametroUrlAtual){
            $this->pageData = array_merge(['paramUrlAfiliate' => $affiliateUrl[1]], $this->pageData);
            $this->render(SUPER_LINKS_VIEWS_PATH . '/interceptLink/redirectParamUrl.php');
            die();
        }
    }

    public function doCamufladorRedirect($affiliateUrl = ''){
        if(empty($affiliateUrl)){
            return;
        }

        $this->render(SUPER_LINKS_VIEWS_PATH . '/interceptLink/camuflador.php');
    }

    public function doClonadorRedirect($affiliateUrl = ''){
        if(empty($affiliateUrl)){
            return;
        }

        $this->render(SUPER_LINKS_VIEWS_PATH . '/interceptLink/clonador.php');
    }

    public function doPaginaBrancaGringaRedirect($dadosPgBranca = ''){
        if(empty($dadosPgBranca)){
            return;
        }

        $this->render(SUPER_LINKS_VIEWS_PATH . '/interceptLink/paginaEmBranco.php');
    }

    public function getAtualTestAb($affiliateLinkModel = null, $idSuperLink = null, $abLastTest = 0){
        if(is_null($affiliateLinkModel) || is_null($idSuperLink)){
            return 0;
        }

        $affiliateLinksBySuperLinkID = $affiliateLinkModel->getAllDataByParam($idSuperLink, 'idLink');
        $atualAbTest = $abLastTest + 1;

        if($atualAbTest == count($affiliateLinksBySuperLinkID)){
            $atualAbTest = 0;
        }

        return $atualAbTest;
    }

    public static function getGoogleEventCode($monitoringID = ''){
        if(empty($monitoringID)){
            return '';
        }

        $googleCode = "
            <script>
              gtag('event', 'conversion', {
                  'send_to': '".$monitoringID."',
                  'transaction_id': ''
              });
            </script>
        ";

        return $googleCode;
    }

    public static function getGoogleAnalyticsCode($monitoringID = ''){
        if(empty($monitoringID)){
            return '';
        }

        $googleCode = "
            <!-- Global site tag (gtag.js) - Google ADS -->
            <script async src=\"https://www.googletagmanager.com/gtag/js?id=$monitoringID\"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
            
              gtag('config', '$monitoringID');
        </script>
        ";

        return $googleCode;
    }

    public static function getPixelFacebookCode($pixelID = '', $track = 'PageView'){
        if(empty($pixelID)){
            return '';
        }

        $pixelFacebook = "
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '$pixelID');
            fbq('track', '$track');
            </script>
            <noscript><img height=\"1\" width=\"1\" style=\"display:none\"
            src=\"https://www.facebook.com/tr?id=$pixelID&ev=$track&noscript=1\"
            /></noscript>
            <!-- End Facebook Pixel Code -->
        ";

        return $pixelFacebook;
    }

    public function getUrlCloak($cloakData = [], $ip = null){
        if(!$cloakData || $cloakData['statusCloak'] == 'disabled'){
            return false;
        }

        $blockAccess = false;

        if ($this->isFacebookOrGoogleAccess($ip) || $this->isBloquedIp($cloakData, $ip)) {
            $blockAccess = true;
        }

        return $blockAccess ?  $cloakData['defaultRedirectUrl'] : ($this->useUrlCloak($cloakData) ? $cloakData['connectionRedirectUrl'] : false);
    }

    public function isBloquedIp($cloakData, $ip){
        $ipsBloqueados = SuperLinksLinkCloakModel::getBloquedIps();
        $countrySimbol = self::getCountryCode($ip);

        if(GeoPlugin::isBloquedIp($ip, $ipsBloqueados)){
            return true;
        }

        $freeConnections = [];

        if (!empty($cloakData["connection1"])) $freeConnections[] = $cloakData["connection1"];
        if (!empty($cloakData["connection2"])) $freeConnections[] = $cloakData["connection2"];
        if (!empty($cloakData["connection3"])) $freeConnections[] = $cloakData["connection3"];
        if (!empty($cloakData["connection4"])) $freeConnections[] = $cloakData["connection4"];

        if (($countrySimbol != "IPS" && !is_null($countrySimbol)) && !in_array($countrySimbol, $freeConnections)) {
            return true;
        }

        return false;
    }

    public function isFacebookOrGoogleAccess($ip){
        $hostname = gethostbyaddr($ip);

        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $userAgent = strtolower($userAgent);

        $httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Acesso Direto, sem Referência';

        if (strpos($userAgent, "facebook") !== false || strpos($userAgent, "facebot") !== false || strpos($httpReferer, 'dev.facebook.com') !== false || strpos($userAgent, "google") !== false) {
            return true;
        }

        if (strpos($hostname, "google") !== false || strpos($hostname, "facebook") !== false) {
            return true;
        }

        return false;
    }

    public function useUrlCloak($cloakData = []){
        if(isset($cloakData['connectionRedirectUrl']) && $cloakData['connectionRedirectUrl']){
            return true;
        }

        return false;
    }

    public static function getCountryCode($ip){

        require_once(SUPER_LINKS_HELPERS_PATH . '/geolocation/geoplugin.class.php');


        $reader = new Reader(SUPER_LINKS_HELPERS_PATH . '/geolocation/GeoIP2-Country.mmdb');

        try{
            $record = $reader->country($ip);
            $country = $record->country->isoCode; // simbol
        }catch(Exception $e){
            $country = null;
        }

        if(empty($country)){
            $country = null;

            $geoplugin = new GeoPlugin();
            $geoplugin->locate($ip);
            $countrySimbol = $geoplugin->countryCode;

            if(!empty($countrySimbol)) $country = $countrySimbol;
            unset($geoplugin);
        }

        return $country;

    }

	public function get_current_url_completa() {
		// Verifique se o protocolo é HTTP ou HTTPS
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';

		// Obtenha o nome do host
		$host = $_SERVER['HTTP_HOST'];

		// Obtenha a porta, se presente
		$port = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '';

		// Obtenha o caminho da URL
		$path = $_SERVER['REQUEST_URI'];

		// Construa a URL completa
		$current_url = $protocol . $host . $port . $path;

		// Retorne a URL completa
		return $current_url;
	}

}