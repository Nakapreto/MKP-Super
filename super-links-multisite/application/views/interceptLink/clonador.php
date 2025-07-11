<?php if (!defined('ABSPATH')) {
	die('You are not authorized to access this');
}
header('Access-Control-Allow-Origin: *');

function isMobileDevice() {
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	$mobileDevices = array('iPhone', 'iPad', 'Android', 'webOS', 'BlackBerry', 'Windows Phone');

	foreach ($mobileDevices as $device) {
		if (strpos($userAgent, $device) !== false) {
			return true;
		}
	}

	return false;
}

$isMobileDevice = false;

if (isMobileDevice()) {
	$isMobileDevice = true;
}

$clonouCorretamente = '';

$idPage = isset($this->pageData['id']) ? $this->pageData['id'] : false;
$clonagemNaoAvaliada = isset($this->pageData['opniaoClientePgClonada']) ? $this->pageData['opniaoClientePgClonada'] : 'sim';
$removerPixelPgClonada = isset($this->pageData['removerPixelPgClonada']) ? $this->pageData['removerPixelPgClonada'] : 'enabled';

$uriAtual = $_SERVER['REQUEST_URI'];
$uriAtual = explode('?',$uriAtual);
$parametrosLinkUri = '';
if(isset($uriAtual[1])){
	$parametrosLinkUri = $uriAtual[1];
}

$pageTitle = isset($this->pageData['pageTitle']) ? $this->pageData['pageTitle'] : get_bloginfo('name');
$pageDescription = isset($this->pageData['pageDescription']) ? $this->pageData['pageDescription'] : get_bloginfo('description');

$textLoadPage = isset($this->pageData['textLoadPage']) ? $this->pageData['textLoadPage'] : TranslateHelper::getTranslate('Carregando...');
$showSpinner = isset($this->pageData['showSpinner']) ? $this->pageData['showSpinner'] : 'yes';
$htmlClonePage = isset($this->pageData['htmlClonePage']) ? $this->pageData['htmlClonePage'] : '';
$saveHtmlClone = isset($this->pageData['saveHtmlClone']) ? $this->pageData['saveHtmlClone'] : 'disabled';

$compatibilityMode = isset($this->pageData['compatibilityMode']) ? $this->pageData['compatibilityMode'] : 'disabled';
$forceCompatibility = isset($this->pageData['forceCompatibility']) ? $this->pageData['forceCompatibility'] : 'disabled';
$counterSuperEscassez = isset($this->pageData['counterSuperEscassez']) ? $this->pageData['counterSuperEscassez'] : false;
$alertaConversoes = isset($this->pageData['alertaConversoes']) ? $this->pageData['alertaConversoes'] : false;

$affiliateUrl = $this->pageData['affiliateUrl'];
$url = $this->pageData['url'];

$cloneData = isset($this->pageData['cloneData'])? $this->pageData['cloneData'] : [];

$usarEstrategiaGringa = (isset($this->pageData['usarEstrategiaGringa']) && $this->pageData['usarEstrategiaGringa'] == 'yes')? true : false;

$pgBrancaGringa = isset($this->pageData['pgBrancaGringa'])? $this->pageData['pgBrancaGringa'] : [];

$monitoringModel = isset($this->pageData['monitoringModel'])? $this->pageData['monitoringModel'] : [];

$urlRedirectBtn = isset($this->pageData['urlRedirectBtn'])? $this->pageData['urlRedirectBtn'] : '';


$enableProxy = (isset($this->pageData['enableProxy']) && $this->pageData['enableProxy'] == 'enabled')? true : false;

$urlToGetHtml = $enableProxy? SUPER_LINKS_HELPERS_URL.'/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

$renovarHtml = isset($this->pageData['renovaHtmlClone']) ? $this->pageData['renovaHtmlClone'] : 'disabled';
$updatedAt = isset($this->pageData['updatedAt']) ? $this->pageData['updatedAt'] : '';
$horaAgora = date('Y-m-d H:i:s');

$updatedAt = strtotime($updatedAt);
$horaAgora = strtotime($horaAgora);

$ehParaAtualizarHtml = false;
//1h = 3600
if($updatedAt) {
	if (($horaAgora - $updatedAt) > 3600) {
		$ehParaAtualizarHtml = true;
	}
}else{
	$ehParaAtualizarHtml = true;
}

if($renovarHtml == 'enabled' && $ehParaAtualizarHtml){
	$renovarHtml = true;
}else{
	$renovarHtml = false;
}

function removeLitespeedVary($conteudo = ''){
	if(!$conteudo){
		return '';
	}

	$pattern = '/<script[^>]*>var litespeed_vary[^<]*<\/script>/i';

	$conteudo = preg_replace($pattern, '', $conteudo);

	return $conteudo;
}

function replace_presto_player_url($content, $new_base_url) {
	// Crie uma expressão regular para encontrar a URL contendo "plugins/presto-player/"
	$pattern = '/(https?:\/\/[^\s]+\/)plugins\/presto-player\//';

	// Use uma função de callback para processar a substituição com base na nova URL e na parte após "/presto-player/"
	$updated_content = preg_replace_callback($pattern, function($matches) use ($new_base_url) {
		// Adicione a parte após "/presto-player/" à nova URL base
		return $new_base_url . substr($matches[0], strlen($matches[1]));
	}, $content);

	// Retorne o conteúdo atualizado
	return $updated_content;
}

function corrigeUrlsPresto($conteudo = ''){
	if(!$conteudo){
		return '';
	}

	$new_base_url = SUPER_LINKS_TEMPLATE_URL . '/wp-content/';
	return replace_presto_player_url($conteudo, $new_base_url);
}

function remove_extra_backslashes($html) {
	// Remova as barras invertidas extras usando uma expressão regular e preg_replace()
	$html = preg_replace('/\\\\/', '', $html);

	// Retorne a URL limpa
	return $html;
}


function adicionaCssSuperLinks($conteudo = ''){
	$urlGetPaginaCorrigida = "https://wpsuperlinks.top/paginas-produtores/icones/css/css.php?access_token=mistVAvdCXthnyqMWG5XhJXTc8VHC";
	$existePaginaCorrigida = wp_remote_get($urlGetPaginaCorrigida, [
		'timeout' => 60,
		'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
	]);

	if( !is_wp_error( $existePaginaCorrigida ) ) {
		if (isset($existePaginaCorrigida['body']) && isset($existePaginaCorrigida['body'])) {
			$dataPage = unserialize($existePaginaCorrigida['body']);
			if (isset($dataPage) && $dataPage) {
				$icon = '';
				foreach($dataPage as $cssPage){
					$icon .= ' <link rel="preload" as="font" href="'.$cssPage.'"> ';
				}
				$conteudo = str_replace( '</head>', $icon . ' </head>', $conteudo );
			}
		}
	}

	return $conteudo;
}

function buscaPaginaBibliotecaSuperLinks($affiliateUrl,$idLinkPg){
	$htmlDaPaginaCorrigida = '';
	$urlGetPaginaCorrigida = "https://wpsuperlinks.top/wp-json/spl-light/v1/getClonePage?urlPaginaClonada=$affiliateUrl&access_token=mistVAvdCXthnyqMWG5XhJXTc8VHC";
	$existePaginaCorrigida = wp_remote_get($urlGetPaginaCorrigida, [
		'timeout' => 60,
		'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
	]);

	if( !is_wp_error( $existePaginaCorrigida ) ) {
		if (isset($existePaginaCorrigida['body']) && isset($existePaginaCorrigida['body'])) {
			$dataPageJson = $existePaginaCorrigida['body'];
			$dataPageJson = json_decode($dataPageJson);
			if ($dataPageJson && isset($dataPageJson->data) && $dataPageJson->data) {
				$dataBody = $dataPageJson->data;
				$htmlDaPaginaCorrigida = $dataBody->htmlClonePage;
			}
		}
	}

	$newAddLink = new SuperLinksAddLinkModel();
	$newAddLink->loadDataByID($idLinkPg);
	$newAddLink->setIsNewRecord(false);
	$newAddLink->setAttribute('htmlClonePage', $htmlDaPaginaCorrigida);
	$horaUpdate = date('Y-m-d H:i:s');
	$newAddLink->setAttribute('updatedAt', $horaUpdate);
	$newAddLink->save();

	return $htmlDaPaginaCorrigida;
}

function buscaPaginaProdutor($urlToGetHtml, $idLinkPg){
//	$clonadorHelper = new ClonadorHelper();
//	$htmlNovaClonagem = $clonadorHelper->efetuaClonagem($urlToGetHtml, $idLinkPg,"");
//	$conteudo = $htmlNovaClonagem;

	$resultClone = wp_remote_get($urlToGetHtml, [
		'timeout'    => 60,
		'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
	]);

	$conteudo = '';

	if (is_array($resultClone) && !is_wp_error($resultClone)) {
		$conteudo = $resultClone['body'];
	}

	$newAddLink = new SuperLinksAddLinkModel();
	$newAddLink->loadDataByID($idLinkPg);
	$newAddLink->setIsNewRecord(false);
	$newAddLink->setAttribute('htmlClonePage', $conteudo);
	$horaUpdate = date('Y-m-d H:i:s');
	$newAddLink->setAttribute('updatedAt', $horaUpdate);
	$newAddLink->save();

	return $conteudo;
}

if($saveHtmlClone == 'enabled') {
	if (!$htmlClonePage) {
		$idLinkPg = $this->pageData['idLink'];
		$conteudo = buscaPaginaProdutor($urlToGetHtml,$idLinkPg);
	} else {
		if($renovarHtml){
			$idLinkPg = $this->pageData['idLink'];
			$htmlDaPaginaBiblioteca = buscaPaginaBibliotecaSuperLinks($affiliateUrl,$idLinkPg);

			if(!$htmlDaPaginaBiblioteca){
				$idLinkPg = $this->pageData['idLink'];
				$htmlDaPaginaBiblioteca = buscaPaginaProdutor($urlToGetHtml,$idLinkPg);
			}

			$htmlClonePage = $htmlDaPaginaBiblioteca;
		}

		$conteudo = $htmlClonePage;
	}

}else{

	$userAgentGet = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36';

	if($isMobileDevice){
		$userAgentGet = 'Mozilla/5.0 (Linux; Android 10; SM-A205U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36';
	}

	$resultClone = wp_remote_get($urlToGetHtml, [
		'timeout'    => 60,
		'user-agent' => $userAgentGet,
	]);

	$conteudo = '';

	if (is_array($resultClone) && !is_wp_error($resultClone)) {
		$conteudo = $resultClone['body'];
	}

	$conteudo = adicionaCssSuperLinks($conteudo);
}

// v 3.0.18 remover SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?'
$linkProxySpl = SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?';
$conteudo = str_replace("$linkProxySpl", "", $conteudo);

$linkProxySpl = SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php';
$conteudo = str_replace("$linkProxySpl", "", $conteudo);


$conteudo = str_replace('&#038;', "&", $conteudo);
$conteudo = str_replace('&amp;', "&", $conteudo);

$conteudo = removeLitespeedVary($conteudo);
$conteudo = corrigeUrlsPresto($conteudo);

function remove_tracking_pixels_superLinks($conteudo = '') {

	if(!$conteudo){
		return '';
	}

	$patterngtag = '/<script id="hotmart_launcher_script">.*?<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<meta\s+name="facebook-domain-verification"[^>]+>/i';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = "/<script\s+(?=[^>]*gtag).*?>.*?<\/script>/s";
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<script\s+data-cfasync="[^"]*"\s+data-wpfc-render="[^"]*"[^>]*>.*?<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = "/<script\s+async=['\"]async['\"]\s+src=['\"].*?pixelyoursite.*?['\"]\s+id=['\"]pys-js['\"].*?>.*?<\/script>/s";
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<script[^>]*>[^>]*fbq[^<]*<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<script[^>]*>[^>]*gtag[^<]*<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<script[^>]*>[^>]*connect\.facebook\.net\/[^<]*<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<noscript[^>]*>[^>]*facebook\.com\/tr[^<]*<\/noscript>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<noscript[^>]*>[^>]*googletagmanager\.com\/[^<]*<\/noscript>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<script[^>]*>[^>]*clarity\.ms\/[^<]*<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	$patterngtag = '/<script[^>]*>[^>]*googletagmanager\.com\/[^<]*<\/script>/is';
	$conteudo = preg_replace($patterngtag, "", $conteudo);

	return $conteudo;
}

if($removerPixelPgClonada == 'enabled') {
	$conteudo = remove_tracking_pixels_superLinks( $conteudo );
}

if(!$usarEstrategiaGringa) {
    foreach ($cloneData as $changeItem) {
        $changeItem = get_object_vars($changeItem);
        $pageItem = $changeItem['pageItem'];
        $newItem = $changeItem['newItem'];

        $pageItem = removeParametroIncorretoUrlTroca($pageItem);

        $newItem = str_replace('&#038;', "&", $newItem);
        $newItem = str_replace('&amp;', "&", $newItem);

        if ($pageItem && $newItem) {
            $pageItem = trim($pageItem);
            $newItem = trim($newItem);

            if (preg_match("/http/i", $pageItem)) {
                $newItem = insertParamUriClone($parametrosLinkUri, $newItem);
            }

            $conteudo = str_replace($pageItem, $newItem, $conteudo);
        }
    }
}

// Troca checkout gringa
if($usarEstrategiaGringa && $pgBrancaGringa) {
    $pgBrancaGringa = array_shift($pgBrancaGringa);

    $pgBrancaGringa = get_object_vars($pgBrancaGringa);
    $checkoutProdutor = $pgBrancaGringa['checkoutProdutor'];
    $linkPaginaVenda = $pgBrancaGringa['linkPaginaVenda'];
    $idPaginaVenda = $pgBrancaGringa['id'];
    $idLink = $pgBrancaGringa['idLink'];

    $newItem = SUPER_LINKS_TEMPLATE_URL . '/' . $idLink . '_' . $idPaginaVenda . '_';
    $checkoutProdutor = unserialize($checkoutProdutor);

    foreach ($checkoutProdutor as $key => $checkout) {
        $checkout = removeParametroIncorretoUrlTroca($checkout);

        $checkout = str_replace('&#038;', "&", $checkout);
        $checkout = str_replace('&amp;', "&", $checkout);

        if ($checkout) {
            $checkout = trim($checkout);

            $conteudo = str_replace($checkout, $newItem . $key, $conteudo);
        }
    }
}
// fim Troca checkout gringa


function removeParametroIncorretoUrlTroca($urlCheckoutPgClonada = ''){

	if(!$urlCheckoutPgClonada){
		return '';
	}

	$url_components = parse_url($urlCheckoutPgClonada);

	$query = isset($url_components['query'])? $url_components['query'] : '';

	if(!$query){
		return $urlCheckoutPgClonada;
	}

	$query = explode('&',$query);
	$tamQuery = count($query) - 1;

	$parametroErrado = isset($query[$tamQuery])? $query[$tamQuery] : '';

	if(!$parametroErrado){
		return $urlCheckoutPgClonada;
	}

	$parametroErrado = explode('=',$parametroErrado);

	if(isset($parametroErrado[0]) && $parametroErrado[0] == '_hi'){
		$montaUrlCorreta = $url_components['scheme'] ."://". $url_components['host'] . $url_components['path'];

		$queryCorreta = isset($query[0])? "?".$query[0] : "";

		for($i=1;$i<$tamQuery;$i++){
			$queryCorreta .= "&".$query[$i];
		}

		$montaUrlCorreta .= $queryCorreta;

		return $montaUrlCorreta;
	}

	return $urlCheckoutPgClonada;
}

$faviconBlog = "";
if(get_site_icon_url()){
	$faviconBlog = ' <link rel="shortcut icon" href="'.get_site_icon_url().'" />';
}


$header = '
    <meta http-equiv="cache-control" content="no-store" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Sat, 26 Jul 1997 05:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>

    <meta name="robots" content="noindex">

    <meta itemprop="name" content="'.$pageTitle.'">
    <meta itemprop="description" content="'.$pageDescription.'">
    <meta name="description" content="'.$pageDescription.'">
    
    
';

$header .= $faviconBlog;

if (isset($this->pageData['pageImage'])) {
	$header .= '<meta itemprop="image" content="'.$this->pageData['pageImage'].'">';
}

$header .= '<meta property="og:title" content="'.$pageTitle.'"/>
    <meta property="og:description" content="'.$pageDescription.'">';

if (isset($this->pageData['pageImage'])) {
	list($width, $height, $type, $attr) = getimagesize($this->pageData['pageImage']);
	$header .= ' <meta property="og:image" content="'.$this->pageData['pageImage'].'">
        <meta property="og:image:width" content="'.$width.'">
        <meta property="og:image:height" content="'.$height.'">';
}

$header .= '<meta property="og:url" content="'.$url.'">
    <meta property="og:type" content="website">';

$header .= '<meta name="twitter:title" content="'.$pageTitle.'">
    <meta name="twitter:description" content="'.$pageDescription.'">
    <meta name="twitter:card" content="summary">';

if (isset($this->pageData['pageImage'])) {
	$header .= '<meta name="twitter:image" content="'.$this->pageData['pageImage'].'">';
}

//$header .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

if(isset($monitoringModel['googleMonitoringID']) && !empty($monitoringModel['googleMonitoringID'])){
	$header .= ' ' .SuperLinksInterceptLinkController::getGoogleAnalyticsCode($monitoringModel['googleMonitoringID']) . ' ';
}

if(isset($monitoringModel['trackGoogle']) && !empty($monitoringModel['trackGoogle'])){
	$header .= ' ' . SuperLinksInterceptLinkController::getGoogleEventCode($monitoringModel['trackGoogle']) . ' ';
}

if(isset($monitoringModel['pixelID']) && !empty($monitoringModel['pixelID'])){
	$track = (isset($monitoringModel['track']) && !empty($monitoringModel['track']))? $monitoringModel['track'] : 'PageView';
	$header .= ' ' . SuperLinksInterceptLinkController::getPixelFacebookCode($monitoringModel['pixelID'], $track) . ' ';
}

if(isset($monitoringModel['codeHeadPage']) && !empty($monitoringModel['codeHeadPage'])){
	$header .= ' ' . $monitoringModel['codeHeadPage'] . ' ';
}

$newTitle = '<title>'.$pageTitle.'</title>';


if($compatibilityMode != 'enabled') {
	$conteudo = preg_replace('/<meta itemprop="name" content="(.+?)" (.+?)/sm', "", $conteudo);
	$conteudo = preg_replace('/<meta itemprop="description" content="(.+?)" (.+?)>/sm', "", $conteudo);
	$conteudo = preg_replace('/<meta name="description" content="(.+?)" (.+?)>/sm', "", $conteudo);
}

$urlBase = rtrim($affiliateUrl, '/');

$conteudo = str_replace('<head>', '<head> <base href="'.$urlBase.'" target="_self"> ', $conteudo);
$conteudo = str_replace('<head >', '<head> <base href="'.$urlBase.'" target="_self"> ', $conteudo);

$conteudo = str_replace('</head>', "\n" . $header . '</head>', $conteudo);

$conteudo = str_replace('</head>', $newTitle.' <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"></head>', $conteudo);
$conteudo = str_replace('</head>', "\n<style>.fa::before, .far::before, .fas::before{font-family: 'Font Awesome 5 Free', FontAwesome !important;}</style></head>", $conteudo);

//adiciona elementor icons
$conteudo = str_replace('</head>', "\n<link rel='stylesheet' href='".SUPER_LINKS_TEMPLATE_URL."/wp-content/plugins/elementor/assets/lib/eicons/css/elementor-icons.min.css'></head>", $conteudo);

// substitui icones
$conteudo = str_replace('"eicon-', '"fas fa-', $conteudo);

//Adiciona meta tag de verificação Facebook
if(SUPER_LINKS_FACEBOOK_VERIFICATION) {
	$facebookTagVerification = '<meta name="facebook-domain-verification" content="'.get_option('facebookVerificationSPL').'" />';

	$conteudo = str_replace('</head>', "\n" . $facebookTagVerification . ' </head>', $conteudo);
}

$appendBody = '';
if(isset($monitoringModel['codeBodyPage']) && !empty($monitoringModel['codeBodyPage'])){
	$appendBody .= $monitoringModel['codeBodyPage'];
}

$conteudo = str_replace('</body>',"\n" . $appendBody . ' </body>',$conteudo);

//PERGUNTA SE GOSTOU DA CLONAGEM
$conteudo = str_replace('</body>',"\n" . $clonouCorretamente . ' </body>',$conteudo);

if($counterSuperEscassez && function_exists('getCountersIdForSuperLinks')) {

	$valueCounterSuperEscassez = getCounterForSuperLinks($counterSuperEscassez);

	$contentCounterSuperLinks = '<div style="display:inline-block; text-align: center !important;  margin:20px;">';
	$contentCounterSuperLinks .= getCounterForSuperLinks($counterSuperEscassez);
	$contentCounterSuperLinks .= '</div>';

	$conteudo = str_replace('</body>', "\n" . $contentCounterSuperLinks . ' </body>', $conteudo);
}

if($alertaConversoes && function_exists('getAlertsIdForSuperLinks')) {
	$jsUrlAlert = WP_ALERTA_CONVERSOES_JS_URL . '/wpNotificationAlertConvertSPL.js';
	$getSiteUrl = SUPER_LINKS_TEMPLATE_URL;
	$contentAlertaSuperLinks = '<script>
                                    let siteurl = "'.$getSiteUrl.'";
                                    let idCampaign = "'.$alertaConversoes.'";
                                    let imagesAlertConvert = "'.WP_ALERTA_CONVERSOES_IMAGES_URL.'";
                                </script>';
	$contentAlertaSuperLinks .= '<script type="text/javascript" src="'.$jsUrlAlert.'" id="spl_alerts_js"></script>';

	$conteudo = str_replace('</body>', "\n" . $contentAlertaSuperLinks . ' </body>', $conteudo);
}

$appendFooter = '';
if(isset($monitoringModel['codeFooterPage']) && !empty($monitoringModel['codeFooterPage'])){
	$appendFooter .= $monitoringModel['codeFooterPage'];
}

if($urlRedirectBtn) {
	$appendFooter .= '
                    <script>
                        document.documentElement.addEventListener("mouseleave", function(e){
                            if (e.clientY > 20) { return; }
                            document.location="' . $urlRedirectBtn . '"
                        })
                    </script>				
                ';
	$appendFooter .= '<script>
        history.pushState({}, "", location.href)
        history.pushState({}, "", location.href)
        window.addEventListener("popstate", function(event) {
            setTimeout(function () {
                location.href = "'.$urlRedirectBtn.'"
            }, 1)
        })
    </script>';
}

$cookiesLinks = new SuperLinksCookieLinkController('SuperLinksLinkCookiePageModel');
$cookiesCamu = $cookiesLinks->execCookieSuperLinksCloneCamu($urlRedirectBtn);
$conteudo = str_replace('</body>',"\n" . $cookiesCamu . ' </body>',$conteudo);

$numberWhatsapp = isset($this->pageData['numberWhatsapp'])? $this->pageData['numberWhatsapp'] : false;
$textWhatsapp = isset($this->pageData['textWhatsapp'])? '?text='.$this->pageData['textWhatsapp'] : '';

//popups
$idPopupDesktop = (isset($this->pageData['idPopupDesktop']) && $this->pageData['idPopupDesktop'])? $this->pageData['idPopupDesktop'] : null;
$idPopupMobile = (isset($this->pageData['idPopupMobile']) && $this->pageData['idPopupMobile'])? $this->pageData['idPopupMobile'] : null;

$loadPopupAfterSeconds = (isset($this->pageData['loadPopupAfterSeconds']) && $this->pageData['loadPopupAfterSeconds'])? $this->pageData['loadPopupAfterSeconds'] : 0;

if($idPopupDesktop || $idPopupMobile){
	$popup_content = "<link rel='stylesheet' id='spl_popup_css'  href='".SUPER_LINKS_CSS_URL."/splPop.min.css?ver=".SUPER_LINKS_VERSION."' type='text/css' media='all' />";
	$popup_content .= "<script>
                            let splPop = {
                                mobile: '".$idPopupMobile."',
                                desktop: '".$idPopupDesktop."',
                                loadPopupAfterSeconds: '".$loadPopupAfterSeconds."',
                                exitIntentPopup: '".$this->pageData['exitIntentPopup']."'
                            }
                        </script>";
	$popup_content .= "<script type='text/javascript' src='".SUPER_LINKS_JS_URL."/splPop.min.js?ver=".SUPER_LINKS_VERSION."' id='spl_popup_js'></script>";


	if($idPopupDesktop){
		$popup_content .= get_post_meta($idPopupDesktop, '_superlinks_popup', true);
	}

	if($idPopupMobile){
		$popup_content .= get_post_meta($idPopupMobile, '_superlinks_popup', true);
	}

	$conteudo = str_replace('</body>',"\n" . $popup_content . ' </body>',$conteudo);
}


if($numberWhatsapp) {
	$appendFooter .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <a href="https://wa.me/'.$numberWhatsapp.$textWhatsapp.'" style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#25d366;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 1px 1px 2px #888;
          z-index:10000;" target="_blank">
        <i style="margin-top:16px" class="fa fa-whatsapp"></i>
        </a>
        <script>
            window.addEventListener("load", function(event) {
                document.getElementById("whatsclub-widget").style.display = "none"
            })
        </script>
    ';
}

$conteudo = str_replace('</body>',"\n" . $appendFooter . ' </body>',$conteudo);

function makeHttpToUrlClone($url = '', $urlAffiliate = ''){
	if(isValidUrlSuperLinks($url)){
		return $url;
	}

	$urlRetornada = $url;

	$url = str_replace('./', '', $url);
	$url = str_replace('../', '', $url);

	if($url && $urlAffiliate){

		$splitUrl = explode('/',$urlAffiliate);
		$tam = count($splitUrl) - 1;
		if(!$splitUrl[$tam]){
			unset($splitUrl[$tam]);
		}
		$splitUrl = implode('/',$splitUrl);
		$urlAffiliate = $splitUrl . "/";

		if(parse_url($url, PHP_URL_SCHEME) != "http" && parse_url($url, PHP_URL_SCHEME) != "https" ){
			$urlRetornada = $urlAffiliate . $url;
			if(isValidUrlSuperLinks($urlRetornada)){
				return $urlRetornada;
			}
		}
	}

	return $urlRetornada;
}

function proxifyCSSClonePage($css, $baseURL) {
	$sourceLines = explode("\n", $css);
	$normalizedLines = [];
	foreach ($sourceLines as $line) {
		if (preg_match("/@import\s+url/i", $line)) {
			$normalizedLines[] = $line;
		} else {
			$normalizedLines[] = preg_replace_callback(
				"/(@import\s+)([^;\s]+)([\s;])/i",
				function($matches) use ($baseURL) {
					return $matches[1] . "url(" . $matches[2] . ")" . $matches[3];
				},
				$line);
		}
	}
	$normalizedCSS = implode("\n", $normalizedLines);
	return preg_replace_callback(
		"/url\((.*?)\)/i",
		function($matches) use ($baseURL) {
			$url = $matches[1];
			//Remove any surrounding single or double quotes from the URL so it can be passed to rel2abs - the quotes are optional in CSS
			//Assume that if there is a leading quote then there should be a trailing quote, so just use trim() to remove them
			if (strpos($url, "'") === 0) {
				$url = trim($url, "'");
			}
			if (strpos($url, "\"") === 0) {
				$url = trim($url, "\"");
			}
			if (stripos($url, "data:") === 0) return "url(" . $url . ")"; //The URL isn't an HTTP URL but is actual binary data. Don't proxify it.
			return "url(" . makeHttpToUrlClone($url, $baseURL) . ")";
		},
		$normalizedCSS);
}

$conteudo = trim($conteudo, "\xEF\xBB\xBF");

$scriptCorrecaoAncora = '
<script>
    document.addEventListener("DOMContentLoaded", function() {
     	const links = document.querySelectorAll(\'a[href^="#"], a[href^="/#"]\');

        links.forEach(link => {
            link.addEventListener("click", function(event) {
                event.preventDefault();

                const hrefValue = link.getAttribute("href");
                if (hrefValue.startsWith("/#")) {
                    window.location.href = window.location.origin + hrefValue;
                } else {
					const id = hrefValue.slice(1); // Remove o #
					const targetElement = document.getElementById(id);
					if (targetElement) {
						targetElement.scrollIntoView({ behavior: "smooth" });
                    }
				}
            });
        });
    });
</script>
';

$conteudo = str_replace('</body>',"\n" . $scriptCorrecaoAncora . ' </body>',$conteudo);

if($compatibilityMode == 'enabled'){
	echo $conteudo;
	die();
}

if(!$conteudo){
	echo '.';
	die();
}

$doc = new DomDocument();
@$doc->loadHTML(mb_convert_encoding($conteudo, 'HTML-ENTITIES', 'UTF-8'));
$xpath = new DOMXPath($doc);

if($enableProxy) {

	$linksA = $doc->getElementsByTagName('a');

	foreach ($linksA as $linkA) {

		$old_link = $linkA->getAttribute('href');

		$urlLink = parse_url($old_link);
		$schemeUrl = isset($urlLink['scheme']) ? $urlLink['scheme'] : '';
		$hostUrl = isset($urlLink['host']) ? $urlLink['host'] : '';

		if ($schemeUrl == 'http' || $schemeUrl == 'https') {
			$linkExplode = explode('?', $old_link);
			$replaceLink = isset($linkExplode[0]) ? $linkExplode[0] . '?' : '';
			$old_link = str_replace($replaceLink, '', $old_link);

			$linkA->setAttribute('href', $old_link);

		}

	}

	$links_frame = $doc->getElementsByTagName('iframe');

	foreach ($links_frame as $linkF) {

		$old_link = $linkF->getAttribute('src');

		$urlLink = parse_url($old_link);
		$schemeUrl = isset($urlLink['scheme']) ? $urlLink['scheme'] : '';
		$hostUrl = isset($urlLink['host']) ? $urlLink['host'] : '';

		if ($schemeUrl == 'http' || $schemeUrl == 'https') {
			$linkExplode = explode('?', $old_link);
			$replaceLink = isset($linkExplode[0]) ? $linkExplode[0] . '?' : '';
			$old_link = str_replace($replaceLink, '', $old_link);
			$linkF->setAttribute('src', $old_link);

		}

	}
}else{

	if($forceCompatibility == 'enabled') {
		foreach ($xpath->query("//meta[@http-equiv]") as $element) {
			if (strcasecmp($element->getAttribute("http-equiv"), "refresh") === 0) {
				$content = $element->getAttribute("content");
				if (!empty($content)) {
					$splitContent = preg_split("/=/", $content);
					if (isset($splitContent[1])) {
						$element->setAttribute("content", $splitContent[0] . "=" . makeHttpToUrlClone($splitContent[1], $affiliateUrl));
					}
				}
			}
		}

		foreach ($xpath->query("//style") as $style) {
			$style->nodeValue = proxifyCSSClonePage($style->nodeValue, $affiliateUrl);
		}

		foreach ($xpath->query("//*[@style]") as $element) {
			$element->setAttribute("style", proxifyCSSClonePage($element->getAttribute("style"), $affiliateUrl));
		}

		$proxifyAttributes = ["href", "src", "data-src"];
		foreach ($proxifyAttributes as $attrName) {
			foreach ($xpath->query("//*[@" . $attrName . "]") as $element) { //For every element with the given attribute...
				$attrContent = $element->getAttribute($attrName);
				if ($attrName == "href" && preg_match("/^(about|javascript|magnet|mailto):|#/i", $attrContent)) continue;
				if ($attrName == "src" && preg_match("/^(data):/i", $attrContent)) continue;
				$attrContent = makeHttpToUrlClone($attrContent, $affiliateUrl);
				$element->setAttribute($attrName, $attrContent);
			}
		}

		$proxifyAttributes = ["srcset"];
		foreach ($proxifyAttributes as $attrName) {
			foreach ($xpath->query("//*[@" . $attrName . "]") as $element) { //For every element with the given attribute...
				$element->setAttribute($attrName, '');
			}
		}
	}
}

$linksCheckoutChange = $doc->getElementsByTagName('a');

if(!$usarEstrategiaGringa) {
    foreach ($linksCheckoutChange as $linkChange) {

        $old_link = $linkChange->getAttribute('href');

        foreach ($cloneData as $changeItem) {
            $changeItem = get_object_vars($changeItem);
            $pageItem = $changeItem['pageItem'];
            $newItem = $changeItem['newItem'];

            $pageItem = removeParametroIncorretoUrlTroca($pageItem);

            $newItem = str_replace('&#038;', "&", $newItem);
            $newItem = str_replace('&amp;', "&", $newItem);

            if ($pageItem && $newItem) {
                $pageItem = trim($pageItem);
                $newItem = trim($newItem);

                if (preg_match("/http/i", $pageItem)) {
                    $newItem = insertParamUriClone($parametrosLinkUri, $newItem);
                }

                if ($old_link == $pageItem) {
                    $linkChange->setAttribute('href', $newItem);
                }
            }
        }

    }
}

$linksCheckoutChange = $doc->getElementsByTagName('img');

foreach ($linksCheckoutChange as $linkChange) {

	$old_link = $linkChange->getAttribute('src');

	foreach ($cloneData as $changeItem) {
		$changeItem = get_object_vars($changeItem);
		$pageItem = $changeItem['pageItem'];
		$newItem = $changeItem['newItem'];

		if ($pageItem && $newItem ) {
			$pageItem = trim($pageItem);
			$newItem = trim($newItem);

			if($old_link == $pageItem){
				$linkChange->setAttribute('src', $newItem);
			}
		}
	}

}

function insertParamUriClone($params,$link){

	if(!$params){
		return $link;
	}

	$splitLink = explode('?',$link);

	$inicioLink = '?';
	if(isset($splitLink[1])){
		$inicioLink = '&';
	}

	return $link.$inicioLink.$params;
}

echo $doc->saveHTML();

$rgpd = isset($this->pageData['rgpd']) ? $this->pageData['rgpd'] : false;

if($rgpd && function_exists('active_rgpd_box')){
	require_once("executaRGPD.php");
	rgpdSuperLinks();
}
?>