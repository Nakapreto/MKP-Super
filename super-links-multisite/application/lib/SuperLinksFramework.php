<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class SuperLinksFramework
{
    protected $model;
    private $hooks;
    private $filters;
    protected $scenario = null;
    protected $pageData = [];
    protected $exceptRules = '';
    protected $expireDaysToUniqueAccessAutomatic = 1;
    protected $expireTimeToCountAccess = 60;
    protected $liberaFuncoesAddons;

    protected function init($hooks = [], $filters = [])
    {
        $this->liberaFuncoesAddons = false;
        if (is_plugin_active('super-links-addons/super-links-addons.php')) {
            $this->liberaFuncoesAddons = true;
        }

        $hooks = array_merge($hooks, $this->basicHooks());
        $this->setHooks($hooks);
        $this->load_hooks();

        $filters = array_merge($filters, $this->basicFilters());
        $this->setFilters($filters);
        $this->load_filters();
    }

    protected function load_hooks()
    {
        foreach ($this->hooks as $hook) {
            $priority = isset($hook['priority']) ? $hook['priority'] : 10;
            $accepted_args = isset($hook['accepted_args']) ? $hook['accepted_args'] : 1;
            add_action($hook['hook'], $hook['function'], $priority, $accepted_args);
        }
    }

    protected function load_filters()
    {
        foreach ($this->filters as $filter) {
            $priority = isset($filter['priority']) ? $filter['priority'] : 10;
            $accepted_args = isset($filter['accepted_args']) ? $filter['accepted_args'] : 1;
            add_action($filter['hook'], $filter['function'], $priority, $accepted_args);
        }
    }

    private function basicHooks()
    {
        return [
            ['hook' => 'plugins_loaded', 'function' => array($this, 'routes')]
        ];
    }

    private function basicFilters()
    {
        return [];
    }

    protected function setHooks($hooks)
    {
        $this->hooks = $hooks;
    }

    protected function setFilters($filters)
    {
        $this->filters = $filters;
    }

    protected function setModel($model)
    {
        $this->model = $model;
    }

    protected function loadModel()
    {
        if (is_null($this->model)) {
            return null;
        }

        return new $this->model;
    }

    protected function render($renderView)
    {
        require_once $renderView;
    }

    protected function setScenario($scenario)
    {
        $this->scenario = $scenario;
    }

    protected function getScenario()
    {
        return $this->scenario;
    }

    protected function getCurrentPage()
    {
        $uri = $_SERVER["REQUEST_URI"];
        $parts = parse_url($uri);

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            return isset($query['page']) ? $query['page'] : null;
        }

        return null;
    }

    protected function getCurrentUrl()
    {
        $uri = $_SERVER['REQUEST_URI'];

        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        $url = $protocol . $_SERVER['HTTP_HOST'] . $uri;
        $uri = parse_url($url, PHP_URL_PATH);
        $url = $protocol . $_SERVER['HTTP_HOST'] . $uri;

        return $url;
    }

    protected function updatePopupSPL($params = [])
    {
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $addLinksModel = new SuperLinksAddLinkModel();
        $addLinksModel->loadDataByID($id);

        if($addLinksModel->getAttribute('idPopupDesktop')) {
            $popupBackgroundColor = $addLinksModel->getAttribute('popupBackgroundColor');
            $popupAnimation = $addLinksModel->getAttribute('popupAnimation');
            $this->createPopupSuperLinks($addLinksModel->getAttribute('idPopupDesktop'),$popupBackgroundColor,$popupAnimation);
        }

        if($addLinksModel->getAttribute('idPopupMobile')) {
            $popupBackgroundColor = $addLinksModel->getAttribute('popupBackgroundColor');
            $popupAnimation = $addLinksModel->getAttribute('popupAnimation');
            $this->createPopupSuperLinks($addLinksModel->getAttribute('idPopupMobile'),$popupBackgroundColor,$popupAnimation);
        }

        $response = ['status' => true];

        echo json_encode($response);
    }

    public function renderPopupCode($path, array $args)
    {
        ob_start();
        include($path);
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }

    public function createPopupSuperLinks($popup_id,$popupBackgroundColor = '',$popupAnimation = '')
    {
        $popup_path = SUPER_LINKS_VIEWS_PATH . '/clonePages/popupSuperLinks.php';

        $popup_post = get_post($popup_id);
        $meta = [
            'id' => $popup_id,
            'animation' => $popupAnimation,
            'background' => $popupBackgroundColor,
            'content' => apply_filters('the_content', $popup_post->post_content)
        ];

        $rendered_popup = $this->renderPopupCode($popup_path, $meta);
        update_post_meta($popup_id, '_superlinks_popup', $rendered_popup);
    }


    protected function validate($params = [])
    {
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);
        $excluiRules = [];

        if(isset($params['exceptRules']) && !empty($params['exceptRules'])){
            $exceptRules = explode(',',$params['exceptRules']);

            foreach($exceptRules as $rule){
                if(!empty($rule)){
                    $excluiRules[] = $rule;
                }
            }

            unset($params['exceptRules']);
        }

        $params = (object)$params;

        $response = [];



        foreach($params as $modelKey => $param){
            if(!isset($this->modelList()[$modelKey])){
                continue;
            }

            $model = new $modelKey();

            $model->setAttributes($param);
            $model->setExceptRules($excluiRules);

            $response = array_merge($response,$model->validate());

        }

        echo json_encode($response);
    }

    protected function delete($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $addLinkModel = new SuperLinksAddLinkModel();
        $addLinkModel->loadDataByID($id);
		$enderecoPagina = $addLinkModel->getAttribute('keyWord');

        //se for link do facebook exclui o outro tbm

        if($addLinkModel->getAttribute('redirectType') == 'facebook'){
            $internalKeyWord =  $addLinkModel->getAttribute('keyWord') . '/facebook';

            //pega os dados do link de afiliado corretos
            $internalLinkModel = new SuperLinksAddLinkModel();

            $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
            if($internalLinkData) {
                $internalLinkData = array_shift($internalLinkData);
                $internalLinkData = get_object_vars($internalLinkData);
                $idLink = $internalLinkData['id'];
                $internalLinkModel->loadDataByID($idLink);
                $internalLinkModel->delete();
            }
        }

		$removeElementosPagina = false;

		if($addLinkModel->getAttribute('redirectType') == 'clonador'){
			$removeElementosPagina = true;
		}

        $addLinkModel->delete();
        $result = $addLinkModel->getLastQueryResult();

        $response = ['status' => true];

        if(isset($result['error']) && $result['error']){
            $response = ['status' => false];
	        $removeElementosPagina = false;
        }

		if($removeElementosPagina){
			$this->deletaPastasElements($id);
		}

	    if (is_plugin_active('super-boost/super-boost.php')) {
		    $this->deletaPaginaWordpressSB( $enderecoPagina );
	    }

        echo json_encode($response);
    }

	private function deletaPastasElements($idPageClone){
		if(!$idPageClone){
			return false;
		}

		$clonadorHelper = new ClonadorHelper();
		return $clonadorHelper->deletaPastasNaExclusaoPagina($idPageClone);
	}

	public function deletaPaginaWordpressSB($page_slug){
		$wp_load_path = ABSPATH . '/wp-load.php';
		if (file_exists($wp_load_path)) {
			require_once($wp_load_path);

			$page = get_page_by_path( $page_slug );

			if ( $page ) {
				// Excluir a página
				$result = wp_delete_post( $page->ID, true, true );
//				if ($result === false) {
//					return 'Erro ao excluir a página. ' .$wp_load_path ;
//				} else {
//					return 'Página excluída com sucesso. ' .$wp_load_path;
//				}
			}else{
//				return 'Não encontrado. ' .$wp_load_path;
			}
		}else{
//			return 'sem wpload. ' .$wp_load_path;
		}
	}

    protected function deleteCookie($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksLinkCookiePageModel();
        $classModel->loadDataByID($id);
        $classModel->delete();
        $result = $classModel->getLastQueryResult();

        $response = ['status' => true];

        if(isset($result['error']) && $result['error']){
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function removeAffiliateLink($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksAffiliateLinkModel();
        $classModel->loadDataByID($id);
        $classModel->delete();
        $result = $classModel->getLastQueryResult();

        $response = ['status' => true];

        if(isset($result['error']) && $result['error']){
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function removeCloneLink($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksLinkClonePageModel();
        $classModel->loadDataByID($id);
        $classModel->delete();
        $result = $classModel->getLastQueryResult();

        $response = ['status' => true];

        if(isset($result['error']) && $result['error']){
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function saveNewGroupLink($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $groupName = $params['groupName'];
        if(!$groupName){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksGroupLinkModel();
        $classModel->setAttribute('groupName', $groupName);
        $idGroup = $classModel->save();

        if($idGroup) {
            $response = ['id' => $idGroup, 'status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function saveNewCloneGroup($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $groupName = $params['groupName'];
        if(!$groupName){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksCloneGroupModel();
        $classModel->setAttribute('groupName', $groupName);
        $idGroup = $classModel->save();

        if($idGroup) {
            $response = ['id' => $idGroup, 'status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function saveNewAutomaticGroup($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $groupName = $params['groupName'];
        if(!$groupName){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksAutomaticGroupModel();
        $classModel->setAttribute('groupName', $groupName);
        $idGroup = $classModel->save();

        if($idGroup) {
            $response = ['id' => $idGroup, 'status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function saveNewGroupLinkCookie($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $groupName = $params['groupName'];
        if(!$groupName){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksCookiePageGroupModel();
        $classModel->setAttribute('groupName', $groupName);
        $idGroup = $classModel->save();

        if($idGroup) {
            $response = ['id' => $idGroup, 'status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    private function slugify($string){
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    protected function updateAutomaticMetrics($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        $idPost = $params['idPost'];
        $keyword = $params['keyword'];

        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $classModel = new SuperLinksAutomaticMetricsModel();

        $classModel->setAttribute('idAutomaticLink',$id);
        $classModel->setAttribute('idPost',$idPost);
        $classModel->setAttribute('keyword',$keyword);

        $ip = $this->getClientIp();

        $keyword = $this->slugify($keyword);
        if(!isset($_COOKIE['ipAutomaticLink_'.$id.'_'.$idPost.'_'.$keyword])) {
            setcookie('ipAutomaticLink_'.$id.'_'.$idPost.'_'.$keyword, $ip, time() + (86400 * $this->expireDaysToUniqueAccessAutomatic), "/");
            $classModel->setIsUniqueAccessTrue();
        }

        if(!isset($_COOKIE['ipAutomaticKeyword_'.$id.'_'.$idPost.'_'.$keyword])) {
            setcookie('ipAutomaticKeyword_'.$id.'_'.$idPost.'_'.$keyword, $ip, time() + ($this->expireTimeToCountAccess), "/");
            $classModel->updateMetricsByIDAutomaticLink();
        }

        $result = $classModel->getLastQueryResult();

        $response = ['status' => true];

        if(isset($result['error']) && $result['error']){
            $response = ['status' => false, 'result' => $result];
        }

        echo json_encode($response);
    }

    protected function deleteAutomaticLink($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $addLinkModel = new SuperLinksAutomaticLinksModel();
        $addLinkModel->loadDataByID($id);
        $addLinkModel->delete();
        $result = $addLinkModel->getLastQueryResult();

        $response = ['status' => true];

        if($result['error']){
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function deleteGroup($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $addLinksModel = new SuperLinksAddLinkModel();
        $linksRedirects = $addLinksModel->getAllDataRedirects();

        $isToDelete = true;
        foreach($linksRedirects as $link){
            if($link->idGroup == $id){
                $isToDelete = false;
            }
        }

        if($isToDelete) {
            $groupLinkModel = new SuperLinksGroupLinkModel();
            $groupLinkModel->loadDataByID($id);
            $result = $groupLinkModel->delete();

            $response = ['status' => true, 'result' => $result];

            if ($result != 1) {
                $response = ['status' => false, 'result' => $result];
            }
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function deleteGroupClone($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $addLinksModel = new SuperLinksAddLinkModel();
        $linksClonadores = $addLinksModel->getAllDataClonador();

        $isToDelete = true;
        foreach($linksClonadores as $link){
            if($link->idGroup == $id){
                $isToDelete = false;
            }
        }

        if($isToDelete) {
            $groupLinkModel = new SuperLinksCloneGroupModel();
            $groupLinkModel->loadDataByID($id);
            $result = $groupLinkModel->delete();

            $response = ['status' => true, 'result' => $result];

            if ($result != 1) {
                $response = ['status' => false, 'result' => $result];
            }
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function deleteGroupCookie($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $groupLinkModel = new SuperLinksCookiePageGroupModel();
        $groupLinkModel->loadDataByID($id);
        $result = $groupLinkModel->delete();

        $response = ['status' => true,'result' => $result];

        if($result != 1){
            $response = ['status' => false,'result' => $result];
        }

        echo json_encode($response);
    }

    protected function deleteAutomaticGroup($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $id = $params['id'];
        if(!$id){
            echo json_encode(['erro']);
            return;
        }

        $addLinksModel = new SuperLinksAutomaticLinksModel();
        $linksRedirects = $addLinksModel->getAllData();

        $isToDelete = true;
        foreach($linksRedirects as $link){
            if($link->idGroup == $id){
                $isToDelete = false;
            }
        }

        if($isToDelete) {
            $groupLinkModel = new SuperLinksAutomaticGroupModel();
            $groupLinkModel->loadDataByID($id);
            $result = $groupLinkModel->delete();

            $response = ['status' => true, 'result' => $result];

            if ($result != 1) {
                $response = ['status' => false, 'result' => $result];
            }
        }else{
            $response = ['status' => false];
        }
        echo json_encode($response);
    }

    protected function zerarClickLink($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $idLink = $params['id'];
        if(!$idLink){
            echo json_encode(['erro']);
            return;
        }

        $addLinkModel = new SuperLinksAddLinkModel();
        $addLinkModel->loadDataByID($idLink);

        //se for link do facebook zera o outro tbm
        if($addLinkModel->getAttribute('redirectType') == 'facebook'){
            $internalKeyWord =  $addLinkModel->getAttribute('keyWord') . '/facebook';

            //pega os dados do link de afiliado corretos
            $internalLinkModel = new SuperLinksAddLinkModel();

            $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
            if($internalLinkData) {
                $internalLinkData = array_shift($internalLinkData);
                $internalLinkData = get_object_vars($internalLinkData);
                $idLinkFacebook = $internalLinkData['id'];

                $classModel = new SuperLinksAffiliateLinkModel();
                $affiliateData = $classModel->getAllDataByParam($idLinkFacebook,'idLink');

                if($affiliateData){
                    foreach($affiliateData as $affiliateDatum){
                        $metrics = new SuperLinksLinkMetricsModel();
                        $metricsData = $metrics->getAllDataByParam($affiliateDatum->id,'idAffiliateLink');
                        foreach($metricsData as $metricsDatum){
                            $zeroMetrics = new SuperLinksLinkMetricsModel();
                            $zeroMetrics->loadDataByID($metricsDatum->id);
                            $zeroMetrics->setIsNewRecord(false);
                            $zeroMetrics->setAttribute('accessTotal',0);
                            $zeroMetrics->setAttribute('accessTotal',0);
                            $zeroMetrics->setAttribute('uniqueTotalAccesses',0);
                            $zeroMetrics->save();
                        }
                    }
                }
            }
        }

        $classModel = new SuperLinksAffiliateLinkModel();
        $affiliateData = $classModel->getAllDataByParam($idLink,'idLink');

        if($affiliateData){
            foreach($affiliateData as $affiliateDatum){
                $metrics = new SuperLinksLinkMetricsModel();
                $metricsData = $metrics->getAllDataByParam($affiliateDatum->id,'idAffiliateLink');
                foreach($metricsData as $metricsDatum){
                    $zeroMetrics = new SuperLinksLinkMetricsModel();
                    $zeroMetrics->loadDataByID($metricsDatum->id);
                    $zeroMetrics->setIsNewRecord(false);
                    $zeroMetrics->setAttribute('accessTotal',0);
                    $zeroMetrics->setAttribute('accessTotal',0);
                    $zeroMetrics->setAttribute('uniqueTotalAccesses',0);
                    $zeroMetrics->save();
                }
            }
            $response = ['status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function colocaLinksEmCategoria($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $idLink = $params['id'];
        $idCategory = $params['idCategory'];
        if(!$idLink){
            echo json_encode(['erro']);
            return;
        }

        $addLinkModel = new SuperLinksAddLinkModel();
        $addLinkModel->loadDataByID($idLink);

        //se for link do facebook categoriza o outro tbm
        if($addLinkModel->getAttribute('redirectType') == 'facebook'){
            $internalKeyWord =  $addLinkModel->getAttribute('keyWord') . '/facebook';

            //pega os dados do link de afiliado corretos
            $internalLinkModel = new SuperLinksAddLinkModel();

            $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
            if($internalLinkData) {
                $internalLinkData = array_shift($internalLinkData);
                $internalLinkData = get_object_vars($internalLinkData);
                $idLink = $internalLinkData['id'];
                $internalLinkModel->loadDataByID($idLink);

                $internalLinkModel->setIsNewRecord(false);
                if($idCategory) {
                    $internalLinkModel->setAttribute('idGroup', $idCategory);
                }else{
                    $internalLinkModel->setNullToAttribute('idGroup');
                }

                $internalLinkModel->save();
            }
        }

        $addLinkModel->setIsNewRecord(false);
        if($idCategory) {
            $addLinkModel->setAttribute('idGroup', $idCategory);
        }else{
            $addLinkModel->setNullToAttribute('idGroup');
        }

        $isSavedLink = $addLinkModel->save();

        if ($isSavedLink) {
            $response = ['status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    protected function colocaLinksEmCategoriaAutomatic($params = []){
        if(empty($params)){
            return ['error' => TranslateHelper::getTranslate('Não foram passados parametros')];
        }

        // só aceita requisições ajax
        if(!isset($params['type']) || $params['type'] != 'ajax'){
            return ['error' => TranslateHelper::getTranslate('Não foi aceita a requisição')];
        }

        unset($params['type']);

        $idLink = $params['id'];
        $idCategory = $params['idCategory'];
        if(!$idLink){
            echo json_encode(['erro']);
            return;
        }

        $addLinkModel = new SuperLinksAutomaticLinksModel();
        $addLinkModel->loadDataByID($idLink);

        $addLinkModel->setIsNewRecord(false);
        if($idCategory) {
            $addLinkModel->setAttribute('idGroup', $idCategory);
        }else{
            $addLinkModel->setNullToAttribute('idGroup');
        }

        $isSavedLink = $addLinkModel->save();

        if ($isSavedLink) {
            $response = ['status' => true];
        }else{
            $response = ['status' => false];
        }

        echo json_encode($response);
    }

    public function routes()
    {
        $routes = [
            ['function' => 'validate'],
            ['function' => 'delete'],
            ['function' => 'removeAffiliateLink'],
            ['function' => 'removeCloneLink'],
            ['function' => 'deleteAutomaticLink'],
            ['function' => 'updateAutomaticMetrics'],
            ['function' => 'saveNewGroupLink'],
            ['function' => 'saveNewCloneGroup'],
            ['function' => 'saveNewGroupLinkCookie'],
            ['function' => 'deleteGroup'],
            ['function' => 'deleteGroupClone'],
            ['function' => 'deleteGroupCookie'],
            ['function' => 'deleteCookie'],
            ['function' => 'zerarClickLink'],
            ['function' => 'colocaLinksEmCategoria'],
            ['function' => 'updatePopupSPL'],
            ['function' => 'saveNewAutomaticGroup'],
            ['function' => 'deleteAutomaticGroup'],
            ['function' => 'colocaLinksEmCategoriaAutomatic'],
        ];

        $url = $this->getCurrentUrl();

        foreach ($routes as $route) {
            if ($url == SUPER_LINKS_TEMPLATE_URL . '/' . $route['function']) {
                $route = (object)$route;
                $function = $route->function;
                $this->$function($_POST);
                exit;
            }
        }
    }

    public function getClientIp(){

	    $ipaddress = '';

	    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
		    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
		    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
		    $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
		    $ipaddress = $_SERVER['REMOTE_ADDR'];
	    } else {
		    $ipaddress = 'UNKNOWN';
	    }

	    return $ipaddress;
    }

    private function modelList(){
        return [
            "SuperLinksAddLinkModel" => '',
            "SuperLinksAffiliateLinkModel" => '',
            "SuperLinksGroupLinkModel" => '',
            "SuperLinksImportHotLinksModel" => '',
            "SuperLinksImportModel" => '',
            "SuperLinksImportPrettyLinksModel" => '',
            "SuperLinksLinkMetricsModel" => '',
            "SuperLinksModel" => '',
            "SuperLinksPosts" => ''
        ];
    }
}