<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class SuperLinksAddLinkController extends SuperLinksFramework
{

    protected $addLinksModel;
    protected $groupLinkModel;
    protected $affiliateUrlModel;
    protected $monitoringModel;
    protected $cloakModel;
    protected $configSocialModel;
    protected $waitPageModel;
    protected $clonePageModel;
    protected $apiConvertFaceModel;
    protected $pgBrancaGringaModel;

	public $instalarPrestoPlayer;
	public $precisaAtivarCompatibilidade;

    private $toast;
    private $timeToExpire;
    private $urlView;

	public $errorSave;

    public function __construct($model = null, $hooks = [], $filters = [])
    {
        $this->toast = TranslateHelper::getTranslate('O link foi salvo com sucesso!');
        $this->timeToExpire = time() + 60;
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_list_view';

        $this->setScenario('super_links_add');

        $this->setModel($model);
        $this->addLinksModel = $this->loadModel();
		$this->instalarPrestoPlayer = false;
		$this->precisaAtivarCompatibilidade = false;

        $this->init($hooks, $filters);
    }

    public function init($hooks = [], $filters = []){
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

    public function view()
    {
        if($this->isPluginActive()) {
            if(isset($_GET['idCategory']) && $_GET['idCategory']) {
                $idGroup = $_GET['idCategory'];

                if($idGroup == 'none'){
                    $idGroup = null;
                }

                $this->viewLinksByGroup($idGroup);
            }else {

                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar categorias de links existentes');

                $this->groupLinkModel = new SuperLinksGroupLinkModel();
                $allGroups['groups'] = $this->groupLinkModel
                    ->getAllData();

                $allGroups['existCategory'] = $this->existCategory($allGroups['groups']);

                $this->pageData = array_merge($allGroups, $this->pageData);

                $allLinks['links'] = $this->addLinksModel
                    ->getAllDataRedirects();

                $allLinks['existLinkWithoutCategory'] = $this->existLinkwithoutCategory($allLinks['links']);
                $allLinks['existLinks'] = $this->existLinks($allLinks['links']);
                $allLinks['totalAccessThisLink'] = $this->totalAccessLink($allLinks['links']);

                $this->pageData = array_merge($allLinks, $this->pageData);


                $this->render(SUPER_LINKS_VIEWS_PATH . '/links/listView.php');
            }
        }
    }

    private function existLinkwithoutCategory($links = []){
        foreach($links as $link){
            if(!$link->idGroup){
                return true;
            }
        }

        return false;
    }

    private function existCategory($groups = []){
        if($groups){
            return true;
        }

        return false;
    }

    private function existLinks($links = []){
        if($links){
            return true;
        }

        return false;
    }

    private function totalAccessLink($links = []){
        $totalAccess = [];
        foreach($links as $link){
            $affiliate = new SuperLinksAffiliateLinkModel();
            $affiliateData = $affiliate->getAllDataByParam($link->id,'idLink');

            foreach($affiliateData as $affiliateDatum){
                $metrics = new SuperLinksLinkMetricsModel();
                $metricsData = $metrics->getAllDataByParam($affiliateDatum->id,'idAffiliateLink');
                if($metricsData){
                    $metricsData = array_shift($metricsData);
                    $access = 0;

                    if($metricsData){
                        $access = $metricsData->accessTotal;
                    }

                    if(isset($totalAccess[$link->id])) {
                        $totalAccess[$link->id] = $totalAccess[$link->id] + $access;
                    }else{
                        $totalAccess[$link->id] = $access;
                    }
                }else{
                    $totalAccess[$link->id] = 0;
                }
            }
        }

        return $totalAccess;
    }

    private function viewLinksByGroup($idGroup = null){
        $groupName = "Sem categoria";

        if(!is_null($idGroup)){
            $this->groupLinkModel = new SuperLinksGroupLinkModel();
            $this->groupLinkModel->loadDataByID($idGroup);
            $groupName = $this->groupLinkModel->getAttribute('groupName');
        }

        $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar links existentes da Categoria: ') . "<strong>" . $groupName . "</strong>";

        $addLinksModel = new SuperLinksAddLinkModel();
        $this->addLinksModel = $addLinksModel;
        $linksByGroup = $addLinksModel->getLinksByIDGroup($idGroup);

        $allLinks['links'] = $linksByGroup;
        $allLinks['existLinks'] = $this->existLinks($allLinks['links']);
        $allLinks['totalAccessThisLink'] = $this->totalAccessLink($allLinks['links']);

        $this->pageData = array_merge($allLinks, $this->pageData);

        $this->render(SUPER_LINKS_VIEWS_PATH . '/links/listViewLinks.php');
    }

    public function viewLink()
    {
	    $existeVisualizacao = false;
        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $addLinkModel = new SuperLinksAddLinkModel();
                $addLinkModel->loadDataByID($id);

                if (!empty($addLinkModel->getAttributes())) {
	                $existeVisualizacao = true;

                    $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Dados do link');

                    $this->pageData = array_merge($addLinkModel->getAttributes(), $this->pageData);

                    $affiliateLinks = new SuperLinksAffiliateLinkModel();

                    $idLink = $addLinkModel->getAttribute('id');

                    if($addLinkModel->getAttribute('redirectType') == 'facebook'){
                        $internalKeyWord =  $addLinkModel->getAttribute('keyWord') . '/facebook';

                        //pega os dados do link de afiliado corretos
                        $internalLinkModel = new SuperLinksAddLinkModel();

                        $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
                        if($internalLinkData) {
                            $internalLinkData = array_shift($internalLinkData);
                            $internalLinkData = get_object_vars($internalLinkData);
                            $idLink = $internalLinkData['id'];
                        }
                    }

                    $affiliateData = $affiliateLinks->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    $pageDataAffiliate = [];

                    foreach ($affiliateData as $affiliateDatum) {
                        $metricsModel = new SuperLinksLinkMetricsModel();
                        $metricsData = $metricsModel->getAllDataByParam($affiliateDatum->id, 'idAffiliateLink');
                        $pageDataAffiliate[] = ['affiliateData' => $affiliateDatum, 'metrics' => $metricsData];
                    }

                    $this->pageData = array_merge(['affiliate' => $pageDataAffiliate], $this->pageData);
                }

	            $ipModel = new SuperLinksIpModel();
	            $ipsData = $ipModel->getIpsByIdLink($id);
	            $this->pageData = array_merge(['ipsData' => $ipsData], $this->pageData);

	            if($existeVisualizacao) {
		            $this->render(SUPER_LINKS_VIEWS_PATH . '/links/viewLink.php');
	            }
            }
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }
    }

    public function create()
    {
        $savedLink = false;
        if($this->isPluginActive()) {
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Criar novo link');
            $this->groupLinkModel = new SuperLinksGroupLinkModel();
            $this->affiliateUrlModel = new SuperLinksAffiliateLinkModel();
            $this->monitoringModel = new SuperLinksLinkMonitoringModel();
            $this->cloakModel = new SuperLinksLinkCloakModel();
            $this->configSocialModel = new SuperLinksLinkConfigSocialModel();
            $this->waitPageModel = new SuperLinksWaitPageModel();
            $this->clonePageModel = new SuperLinksLinkClonePageModel();
            $this->apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
            $this->pgBrancaGringaModel = new SuperLinksPgBrancaGringaModel();

            $this->addLinksModel->setAttribute('redirectDelay', '0');
            $this->addLinksModel->setAttribute('rgpd', '0');
            $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook', 'ViewContent');

			$keywordAleatorio = $this->addLinksModel->geraKeyWordAleatorio();
            $this->addLinksModel->setAttribute('keyWord', $keywordAleatorio);

            $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', 1);

            if (isset($_POST['scenario'])) {
                $addLinksModel = $this->addLinksModel;
                $affiliateUrlModel = $this->affiliateUrlModel;
                $groupLinkModel = $this->groupLinkModel;

                //verificar se o link é do facebook
                if(isset($_POST[$addLinksModel->getModelName()]['redirectFace'])){
                    $postFacebook = $_POST[$addLinksModel->getModelName()];
                    $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                    $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';

                    unset($postFacebook['redirectFace']);
                    unset($_POST[$addLinksModel->getModelName()]['redirectFace']);

                    $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                    SuperLinksAddLinkModel::saveFacebookLink($postFacebook, $_POST);

                    $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                    $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                    $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                }

                $addLinksModel->setAttributes($_POST[$addLinksModel->getModelName()]);
                $addLinksModel->setAttribute('createdAt', DateHelper::agora());

                $keyWord = $addLinksModel->getAttribute('keyWord');
                $keyWord = strtolower($keyWord);
                $addLinksModel->setAttribute('keyWord', $keyWord);

                $addLinksModel->setAttribute('redirectDelay', $_POST[$addLinksModel->getModelName()]['redirectDelay']);

                if(isset($_POST[$addLinksModel->getModelName()]['redirectBtn'])) {
                    $addLinksModel->setAttribute('redirectBtn', $_POST[$addLinksModel->getModelName()]['redirectBtn']);
                }

                if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'php' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'camuflador'){
                    $addLinksModel->setAttribute('redirectDelay', '0');
                }

                if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'pgBranca'){
                    $addLinksModel->setAttribute('usarEstrategiaGringa', 'yes');
                }else{
                    $addLinksModel->setAttribute('usarEstrategiaGringa', 'no');
                }

                if($_POST[$groupLinkModel->getModelName()]['id']){
                    $addLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                }else{
                    $addLinksModel->setNullToAttribute('idGroup');
                }

	            $faltamDadosParaSalvar = false;
	            $affiliateUrl = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];

	            if (!$affiliateUrl) {
		            // Não existe link de afiliado enviado pelo cliente
		            $faltamDadosParaSalvar = true;
	            }

	            $affiliateUrl = array_shift( $affiliateUrl );
	            $affiliateUrl = trim( $affiliateUrl );

	            if ( ! $affiliateUrl ) {
		            // Não existe link de afiliado enviado pelo cliente
		            $faltamDadosParaSalvar = true;
	            }

	            $idAddLinks = false;
	            $affiliateUrlErro = $affiliateUrl;

	            if($faltamDadosParaSalvar) {
		            //atribui os erros
		            $this->affiliateUrlModel->setErrors('affiliateUrl','required');
	            }

                if (isset($_POST[$addLinksModel->getModelName()]['rgpd']) && !$_POST[$addLinksModel->getModelName()]['rgpd']) {
                    $addLinksModel->setAttribute('rgpd', 0);
                }

	            if(!$faltamDadosParaSalvar) {
		            $idAddLinks = $addLinksModel->save();
	            }

                if ($idAddLinks) {
                    SuperLinksAddLinkModel::saveDependencies($idAddLinks, $_POST, $_POST[$addLinksModel->getModelName()]['redirectType']);
                    $savedLink = true;
                }
            }

            if($savedLink){
                $toast = $this->toast;
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupLinkModel
                    ->getAllData();

	            if(isset($_POST['stayPage']) && $_POST['stayPage']){
		            $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_edit_link&id='.$idAddLinks;
	            }else {
		            // redireciona para a lista de páginas
		            if($addLinksModel->getAttribute('idGroup')) {
			            $urlView = $this->urlView . '&idCategory=' . $addLinksModel->getAttribute('idGroup');
		            }else if($this->existCategory($groups) && !$addLinksModel->getAttribute('idGroup')){
			            $urlView = $this->urlView . '&idCategory=none';
		            }else{
			            $urlView = $this->urlView;
		            }
	            }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }else{
	            $errors = $this->addLinksModel->getErrors();
	            $errorsAfil = $this->affiliateUrlModel->getErrors();

	            $errosAll = array_merge($errors, $errorsAfil);
	            if($errosAll){
		            if(isset($_POST[$this->groupLinkModel->getModelName()])) {
			            $this->groupLinkModel->setAttributes( $_POST[ $this->groupLinkModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->affiliateUrlModel->getModelName() ] ) ) {
			            $this->affiliateUrlModel->setAttribute( 'affiliateUrl', $affiliateUrlErro );
		            }
		            if(isset($_POST[$this->monitoringModel->getModelName()])) {
			            $this->monitoringModel->setAttributes( $_POST[ $this->monitoringModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->cloakModel->getModelName()])) {
			            $this->cloakModel->setAttributes( $_POST[ $this->cloakModel->getModelName() ] );
		            }
					if(isset($_POST[$this->configSocialModel->getModelName()])) {
						$this->configSocialModel->setAttributes( $_POST[ $this->configSocialModel->getModelName() ] );
					}
		            if(isset($_POST[$this->waitPageModel->getModelName()])) {
			            $this->waitPageModel->setAttributes( $_POST[ $this->waitPageModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->apiConvertFaceModel->getModelName()])) {
			            $this->apiConvertFaceModel->setAttributes( $_POST[ $this->apiConvertFaceModel->getModelName() ] );
		            }
					$this->errorSave = $errosAll;
	            }
            }

            $this->render(SUPER_LINKS_VIEWS_PATH . '/links/index.php');
        }
    }

    public function update()
    {
        $savedLink = false;
	    $existeVisualizacao = false;
        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar link');
                $this->groupLinkModel = new SuperLinksGroupLinkModel();
                $this->affiliateUrlModel = new SuperLinksAffiliateLinkModel();
                $this->monitoringModel = new SuperLinksLinkMonitoringModel();
                $this->cloakModel = new SuperLinksLinkCloakModel();
                $this->configSocialModel = new SuperLinksLinkConfigSocialModel();
                $this->waitPageModel = new SuperLinksWaitPageModel();
                $this->clonePageModel = new SuperLinksLinkClonePageModel();
                $this->apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
                $this->pgBrancaGringaModel = new SuperLinksPgBrancaGringaModel();

                $this->addLinksModel->loadDataByID($id);
                $redirectDelay = $this->addLinksModel->getAttribute('redirectDelay');

                if (!$redirectDelay) {
                    $this->addLinksModel->setAttribute('redirectDelay', '0');
                }

                $this->addLinksModel->setIsNewRecord(false);

                if (!empty($this->addLinksModel->getAttributes())) {
                    $confereKeyWord = $this->addLinksModel->getAttribute('keyWord');

					if($confereKeyWord){
						$existeVisualizacao = true;
					}

                    $idLink = $this->addLinksModel->getAttribute('id');
                    $idGroup = $this->addLinksModel->getAttribute('idGroup');

                    $oldRedirectType = $this->addLinksModel->getAttribute('redirectType');

                    $this->groupLinkModel->setAttribute('id', $idGroup);

                    //caso seja redirecionamento do tipo facebook
                    if($this->addLinksModel->getAttribute('redirectType') == 'facebook'){
                        $internalKeyWord =  $this->addLinksModel->getAttribute('keyWord') . '/facebook';

                        //pega os dados do link de afiliado corretos
                        $internalLinkModel = new SuperLinksAddLinkModel();

                        $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
                        if($internalLinkData) {
                            $internalLinkData = array_shift($internalLinkData);
                            $internalLinkData = get_object_vars($internalLinkData);
                            $idLink = $internalLinkData['id'];
                            $this->addLinksModel->setAttribute('redirectFace', $internalLinkData['redirectType']);
                            $this->addLinksModel->setAttribute('redirectDelay', $internalLinkData['redirectDelay']);
                            $this->addLinksModel->setAttribute('idInternalLink', $internalLinkData['id']);
                            $this->addLinksModel->setAttribute('redirectBtn', $internalLinkData['redirectBtn']);
                            $this->addLinksModel->setAttribute('rgpd', $internalLinkData['rgpd']);
                        }
                    }

                    // clone
                    $dataClone = $this->clonePageModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if($dataClone) {

                        $clonePageItem = [];
                        $cloneNewItem = [];
                        $cloneTypeItem = [];
                        foreach ($dataClone as $value) {
                            $clonePageItem[$value->id] = $value->pageItem;
                            $cloneNewItem[$value->id] = $value->newItem;
                            $cloneTypeItem[$value->id] = $value->typeItem;
                        }

                        $this->clonePageModel->setAttribute('pageItem', $clonePageItem);
                        $this->clonePageModel->setAttribute('newItem', $cloneNewItem);
                        $this->clonePageModel->setAttribute('typeItem', $cloneTypeItem);
                    }

                    $eventoApiFacebook = $this->apiConvertFaceModel->getAllDataByParam($idLink,'idLink');
                    if($eventoApiFacebook) {
                        $eventoApiFacebook = array_shift($eventoApiFacebook);
                        $this->apiConvertFaceModel->loadDataByID($eventoApiFacebook->id);
                        if(!$this->apiConvertFaceModel->getAttribute('eventNameApiFacebook')){
                            $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook','ViewContent');
                        }
                    }else{
                        $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook','ViewContent');
                    }

                    // fim clone
                    $dataAffiliate = $this->affiliateUrlModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if ($dataAffiliate) {
                        $dataAffiliateId = $dataAffiliate;
                        $dataAffiliateId = array_shift($dataAffiliateId);
                    }

                    if(isset($dataAffiliateId->id)) {
                        $this->affiliateUrlModel->loadDataByID($dataAffiliateId->id);

                        $affiliateUrl = [];
                        foreach ($dataAffiliate as $affiliate) {
                            $urlSemEspacos = $affiliate->affiliateUrl;
                            $urlSemEspacos = str_replace(' ', "%20", $urlSemEspacos);
                            $affiliateUrl[$affiliate->id] = $urlSemEspacos;
                        }

                        $this->affiliateUrlModel->setAttribute('affiliateUrl', $affiliateUrl);
                    }

                    $dataMonitoring = $this->monitoringModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataMonitoring) {
                        $dataMonitoring = array_shift($dataMonitoring);
                        $this->monitoringModel->loadDataByID($dataMonitoring->id);
                    }

                    $dataCloak = $this->cloakModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataCloak) {
                        $dataCloak = array_shift($dataCloak);
                        $this->cloakModel->loadDataByID($dataCloak->id);
                    }

                    $dataConfigSocial = $this->configSocialModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataConfigSocial) {
                        $dataConfigSocial = array_shift($dataConfigSocial);
                        $this->configSocialModel->loadDataByID($dataConfigSocial->id);
                    }

                    $dataWaitPage = $this->waitPageModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataWaitPage) {
                        $dataWaitPage = array_shift($dataWaitPage);
                        $this->waitPageModel->loadDataByID($dataWaitPage->id);
                    }

                    // Troca link checkout gringa

                    $dataPgGringaClone = $this->pgBrancaGringaModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if($dataPgGringaClone) {
                        $dataPgGringaClone = array_shift($dataPgGringaClone);

                        $checkoutProdutor = $dataPgGringaClone->checkoutProdutor;
                        $linkPaginaVenda = $dataPgGringaClone->linkPaginaVenda;
                        $textoTempoRedirecionamento = $dataPgGringaClone->textoTempoRedirecionamento;
                        $abrirPaginaBranca = $dataPgGringaClone->abrirPaginaBranca;
                        $tempoRedirecionamentoCheckout = $dataPgGringaClone->tempoRedirecionamentoCheckout;

                        if($checkoutProdutor) {
                            $this->pgBrancaGringaModel->setAttribute('checkoutProdutor', $checkoutProdutor);
                            $this->pgBrancaGringaModel->setAttribute('linkPaginaVenda', $linkPaginaVenda);
                            $this->pgBrancaGringaModel->setAttribute('textoTempoRedirecionamento', $textoTempoRedirecionamento);
                            $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', $tempoRedirecionamentoCheckout);
                            $this->pgBrancaGringaModel->setAttribute('abrirPaginaBranca', $abrirPaginaBranca);
                        }
                    }else{
                        $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', 1);
                    }

                }

                if (isset($_POST['scenario'])) {
                    $addLinksModel = $this->addLinksModel;
                    $affiliateUrlModel = $this->affiliateUrlModel;
                    $groupLinkModel = $this->groupLinkModel;

                    $linkBeforeUpdate = new SuperLinksAddLinkModel();
                    $linkBeforeUpdate->loadDataByID($id);


                    //era e continua sendo um link facebook
                    if(isset($_POST[$addLinksModel->getModelName()]['redirectFace'])  && $linkBeforeUpdate->getAttribute('redirectType') == 'facebook'){
                        $postFacebook = $_POST[$addLinksModel->getModelName()];
                        $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                        $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';
                        $postFacebook['id'] = $postFacebook['idInternalLink'];

                        unset($postFacebook['redirectFace']);
                        unset($postFacebook['idInternalLink']);
                        unset($_POST[$addLinksModel->getModelName()]['redirectFace']);
                        unset($_POST[$addLinksModel->getModelName()]['idInternalLink']);

                        $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                        SuperLinksAddLinkModel::updateFacebookLink($postFacebook, $_POST);

                        $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                        $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                        $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                    }elseif(!isset($_POST[$addLinksModel->getModelName()]['redirectFace']) && $linkBeforeUpdate->getAttribute('redirectType') == 'facebook'){
                        //era um link facebook que mudou para outro tipo de link
                        // exclui este link e alterar o keyword do outro link

                        $_POST[$addLinksModel->getModelName()]['keyWord'] = $linkBeforeUpdate->getAttribute('keyWord');
                        $_POST[$addLinksModel->getModelName()]['id'] = $_POST[$addLinksModel->getModelName()]['idInternalLink'];
                        unset($_POST[$addLinksModel->getModelName()]['idInternalLink']);

                        $deleteFacebookLink = new SuperLinksAddLinkModel();
                        $deleteFacebookLink->loadDataByID($linkBeforeUpdate->getAttribute('id'));
                        $deleteFacebookLink->delete();
                    }elseif(isset($_POST[$addLinksModel->getModelName()]['redirectFace']) && $linkBeforeUpdate->getAttribute('redirectType') != 'facebook'){
                        //era um link que mudou para link facebook
                        $postFacebook = $_POST[$addLinksModel->getModelName()];
                        $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                        $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';
                        $postFacebook['id'] = $postFacebook['idInternalLink'];

                        unset($postFacebook['redirectFace']);
                        unset($postFacebook['idInternalLink']);
                        unset($_POST[$addLinksModel->getModelName()]['redirectFace']);
                        unset($_POST[$addLinksModel->getModelName()]['idInternalLink']);

                        $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                        SuperLinksAddLinkModel::saveFacebookLink($postFacebook, $_POST);

                        $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                        $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                        $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                    }

                    $addLinksModel->setAttributes($_POST[$addLinksModel->getModelName()]);
                    $addLinksModel->setAttribute('updatedAt', DateHelper::agora());

                    $keyWord = $addLinksModel->getAttribute('keyWord');
                    $keyWord = strtolower($keyWord);
                    $addLinksModel->setAttribute('keyWord', $keyWord);

                    $addLinksModel->setAttribute('redirectDelay', $_POST[$addLinksModel->getModelName()]['redirectDelay']);
                    $addLinksModel->setAttribute('numberWhatsapp', $_POST[$addLinksModel->getModelName()]['numberWhatsapp']);
                    $addLinksModel->setAttribute('textWhatsapp', $_POST[$addLinksModel->getModelName()]['textWhatsapp']);

                    if(isset($_POST[$addLinksModel->getModelName()]['redirectBtn'])) {
                        $addLinksModel->setAttribute('redirectBtn', $_POST[$addLinksModel->getModelName()]['redirectBtn']);
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'php' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'camuflador'){
                        $addLinksModel->setAttribute('redirectDelay', '0');
                    }

                    $redirectType = $_POST[$addLinksModel->getModelName()]['redirectType'];

                    if($oldRedirectType != $redirectType){
                        $htmlClonePage = "";
                    }else{
                        $htmlClonePage = "";
                        if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' && isset($_POST[$addLinksModel->getModelName()]['htmlClonePage'])) {
                            $htmlClonePage = stripslashes($_POST[$addLinksModel->getModelName()]['htmlClonePage']);
                        }
                    }

                    $addLinksModel->removeAttribute('redirectFace');
                    $addLinksModel->removeAttribute('idInternalLink');
                    $addLinksModel->setAttribute('abLastTest', '0');

                    if($_POST[$groupLinkModel->getModelName()]['id']){
                        $addLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                    }else{
                        $addLinksModel->setNullToAttribute('idGroup');
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador') {
                        if (isset($_POST[$addLinksModel->getModelName()]['htmlClonePage'])) {
                            $addLinksModel->setAttribute('htmlClonePage', $htmlClonePage);
                        } else {
                            $affiliateUrl = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];

                            if ($affiliateUrl && $_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {
                                $affiliateUrl = array_shift($affiliateUrl);

	                            $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSpl($affiliateUrl);
	                            $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
	                            $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

                                $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;

                                $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

                                $resultClone = wp_remote_get($urlToGetHtml, [
                                    'timeout' => 60,
                                    'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
                                ]);

                                $conteudo = '';

                                if (is_array($resultClone) && !is_wp_error($resultClone)) {
                                    $conteudo = stripslashes($resultClone['body']);
                                }

	                            $conteudo = $this->adicionaCssSuperLinks($conteudo);
                                $addLinksModel->setAttribute('htmlClonePage', $conteudo);
                            } else {
                                $addLinksModel->setAttribute('htmlClonePage', '');
                            }
                        }
                    }

	                if (isset($_POST[$addLinksModel->getModelName()]['rgpd']) && !$_POST[$addLinksModel->getModelName()]['rgpd']) {
                        $addLinksModel->setAttribute('rgpd', 0);
                    }

                    $isSavedLink = $addLinksModel->save();


                    if ($isSavedLink) {
                        SuperLinksAddLinkModel::updateDependencies($addLinksModel, $_POST, $_POST[$addLinksModel->getModelName()]['redirectType']);
                        $savedLink = true;
                    }
                }
            }

            if($savedLink){
                $toast = TranslateHelper::getTranslate('O link foi atualizado com sucesso!');
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupLinkModel
                    ->getAllData();

	            if(isset($_POST['stayPage']) && $_POST['stayPage']){
		            $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_edit_link&id='.$id;
	            }else {
		            // redireciona para a lista de páginas
		            if($addLinksModel->getAttribute('idGroup')) {
			            $urlView = $this->urlView . '&idCategory=' . $addLinksModel->getAttribute('idGroup');
		            }else if($this->existCategory($groups) && !$addLinksModel->getAttribute('idGroup')){
			            $urlView = $this->urlView . '&idCategory=none';
		            }else{
			            $urlView = $this->urlView;
		            }
	            }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }else{
	            $errors = $this->addLinksModel->getErrors();
	            if($errors){
		            if(isset($_POST[$this->groupLinkModel->getModelName()])) {
			            $this->groupLinkModel->setAttributes( $_POST[ $this->groupLinkModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->monitoringModel->getModelName()])) {
			            $this->monitoringModel->setAttributes( $_POST[ $this->monitoringModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->cloakModel->getModelName()])) {
			            $this->cloakModel->setAttributes( $_POST[ $this->cloakModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->configSocialModel->getModelName()])) {
			            $this->configSocialModel->setAttributes( $_POST[ $this->configSocialModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->waitPageModel->getModelName()])) {
			            $this->waitPageModel->setAttributes( $_POST[ $this->waitPageModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->apiConvertFaceModel->getModelName()])) {
			            $this->apiConvertFaceModel->setAttributes( $_POST[ $this->apiConvertFaceModel->getModelName() ] );
		            }
		            $this->errorSave = $errors;
	            }
            }

	        if ($id && $existeVisualizacao) {
		        $this->render( SUPER_LINKS_VIEWS_PATH . '/links/update.php' );
	        }
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }
    }

    public function cloneLink()
    {
        $savedLink = false;
	    $existeVisualizacao = false;
        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Duplicar este link');
                $this->groupLinkModel = new SuperLinksGroupLinkModel();
                $this->affiliateUrlModel = new SuperLinksAffiliateLinkModel();
                $this->monitoringModel = new SuperLinksLinkMonitoringModel();
                $this->cloakModel = new SuperLinksLinkCloakModel();
                $this->configSocialModel = new SuperLinksLinkConfigSocialModel();
                $this->waitPageModel = new SuperLinksWaitPageModel();
                $this->clonePageModel = new SuperLinksLinkClonePageModel();
                $this->apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
                $this->pgBrancaGringaModel = new SuperLinksPgBrancaGringaModel();

                $this->addLinksModel->loadDataByID($id);
                $redirectDelay = $this->addLinksModel->getAttribute('redirectDelay');
                $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook', 'ViewContent');

                if (!$redirectDelay) {
                    $this->addLinksModel->setAttribute('redirectDelay', '0');
                }

                if (!empty($this->addLinksModel->getAttributes())) {

                    $idLink = $this->addLinksModel->getAttribute('id');
	                if($idLink){
		                $existeVisualizacao = true;
	                }

                    $idGroup = $this->addLinksModel->getAttribute('idGroup');

                    $this->groupLinkModel->setAttribute('id', $idGroup);

                    //caso seja redirecionamento do tipo facebook
                    if($this->addLinksModel->getAttribute('redirectType') == 'facebook'){
                        $internalKeyWord =  $this->addLinksModel->getAttribute('keyWord') . '/facebook';

                        //pega os dados do link de afiliado corretos
                        $internalLinkModel = new SuperLinksAddLinkModel();

                        $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
                        if($internalLinkData) {
                            $internalLinkData = array_shift($internalLinkData);
                            $internalLinkData = get_object_vars($internalLinkData);
                            $idLink = $internalLinkData['id'];
                            $this->addLinksModel->setAttribute('redirectFace', $internalLinkData['redirectType']);
                            $this->addLinksModel->setAttribute('redirectDelay', $internalLinkData['redirectDelay']);
                            $this->addLinksModel->setAttribute('idInternalLink', $internalLinkData['id']);
                        }
                    }

                    // clone
                    $dataClone = $this->clonePageModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if($dataClone) {

                        $clonePageItem = [];
                        $cloneNewItem = [];
                        $cloneTypeItem = [];
                        foreach ($dataClone as $value) {
                            $clonePageItem[$value->id] = $value->pageItem;
                            $cloneNewItem[$value->id] = $value->newItem;
                            $cloneTypeItem[$value->id] = $value->typeItem;
                        }

                        $this->clonePageModel->setAttribute('pageItem', $clonePageItem);
                        $this->clonePageModel->setAttribute('newItem', $cloneNewItem);
                        $this->clonePageModel->setAttribute('typeItem', $cloneTypeItem);
                    }

                    // fim clone

                    $dataAffiliate = $this->affiliateUrlModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if ($dataAffiliate) {
                        $dataAffiliateId = $dataAffiliate;
                        $dataAffiliateId = array_shift($dataAffiliateId);
                    }

					$idAfiliate = isset($dataAffiliateId->id)? $dataAffiliateId->id : false;

                    $this->affiliateUrlModel->loadDataByID($idAfiliate);

                    $affiliateUrl = [];
                    foreach ($dataAffiliate as $affiliate) {
                        $affiliateUrl[$affiliate->id] = $affiliate->affiliateUrl;
                    }

                    $this->affiliateUrlModel->setAttribute('affiliateUrl', $affiliateUrl);

                    $dataMonitoring = $this->monitoringModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataMonitoring) {
                        $dataMonitoring = array_shift($dataMonitoring);
                        $this->monitoringModel->loadDataByID($dataMonitoring->id);
                    }

                    $dataCloak = $this->cloakModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataCloak) {
                        $dataCloak = array_shift($dataCloak);
                        $this->cloakModel->loadDataByID($dataCloak->id);
                    }

                    $dataConfigSocial = $this->configSocialModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataConfigSocial) {
                        $dataConfigSocial = array_shift($dataConfigSocial);
                        $this->configSocialModel->loadDataByID($dataConfigSocial->id);
                    }

                    $dataWaitPage = $this->waitPageModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataWaitPage) {
                        $dataWaitPage = array_shift($dataWaitPage);
                        $this->waitPageModel->loadDataByID($dataWaitPage->id);
                    }

                    $this->addLinksModel->setAttribute('linkName', '');
                    $this->addLinksModel->setAttribute('keyWord', '');
	                $keywordAleatorio = $this->addLinksModel->geraKeyWordAleatorio();
	                $this->addLinksModel->setAttribute('keyWord', $keywordAleatorio);

                    // Troca link checkout gringa

                    $dataPgGringaClone = $this->pgBrancaGringaModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if($dataPgGringaClone) {
                        $dataPgGringaClone = array_shift($dataPgGringaClone);

                        $checkoutProdutor = $dataPgGringaClone->checkoutProdutor;
                        $linkPaginaVenda = $dataPgGringaClone->linkPaginaVenda;
                        $textoTempoRedirecionamento = $dataPgGringaClone->textoTempoRedirecionamento;
                        $abrirPaginaBranca = $dataPgGringaClone->abrirPaginaBranca;
                        $tempoRedirecionamentoCheckout = $dataPgGringaClone->tempoRedirecionamentoCheckout;

                        if($checkoutProdutor) {
                            $this->pgBrancaGringaModel->setAttribute('checkoutProdutor', $checkoutProdutor);
                            $this->pgBrancaGringaModel->setAttribute('linkPaginaVenda', $linkPaginaVenda);
                            $this->pgBrancaGringaModel->setAttribute('textoTempoRedirecionamento', $textoTempoRedirecionamento);
                            $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', $tempoRedirecionamentoCheckout);
                            $this->pgBrancaGringaModel->setAttribute('abrirPaginaBranca', $abrirPaginaBranca);
                        }
                    }else{
                        $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', 1);
                    }
                }

                if (isset($_POST['scenario'])) {
                    $addLinksModel = new SuperLinksAddLinkModel();
                    $affiliateUrlModel  = new SuperLinksAffiliateLinkModel();
                    $groupLinkModel = new SuperLinksGroupLinkModel();

                    //verificar se o link é do facebook
                    if(isset($_POST[$addLinksModel->getModelName()]['redirectFace'])){
                        $postFacebook = $_POST[$addLinksModel->getModelName()];
                        $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                        $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';

                        unset($postFacebook['redirectFace']);
                        unset($_POST[$addLinksModel->getModelName()]['redirectFace']);

                        $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                        SuperLinksAddLinkModel::saveFacebookLink($postFacebook, $_POST);

                        $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                        $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                        $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                    }

                    $addLinksModel->setAttributes($_POST[$addLinksModel->getModelName()]);
                    $addLinksModel->setAttribute('createdAt', DateHelper::agora());

                    $keyWord = $addLinksModel->getAttribute('keyWord');
                    $keyWord = strtolower($keyWord);
                    $addLinksModel->setAttribute('keyWord', $keyWord);

                    $addLinksModel->setAttribute('redirectDelay', $_POST[$addLinksModel->getModelName()]['redirectDelay']);

                    if(isset($_POST[$addLinksModel->getModelName()]['redirectBtn'])) {
                        $addLinksModel->setAttribute('redirectBtn', $_POST[$addLinksModel->getModelName()]['redirectBtn']);
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'php' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'camuflador'){
                        $addLinksModel->setAttribute('redirectDelay', '0');
                    }

                    $addLinksModel->setAttribute('abLastTest', '0');

                    if($_POST[$groupLinkModel->getModelName()]['id']){
                        $addLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                    }else{
                        $addLinksModel->setNullToAttribute('idGroup');
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador') {
                        if (isset($_POST[$addLinksModel->getModelName()]['htmlClonePage'])) {
                            $htmlClonePage = stripslashes($_POST[$addLinksModel->getModelName()]['htmlClonePage']);
                            $addLinksModel->setAttribute('htmlClonePage', $htmlClonePage);
                        } else {
                            $affiliateUrl = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];
                            if ($affiliateUrl && $_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {
                                $affiliateUrl = array_shift($affiliateUrl);

	                            $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSpl($affiliateUrl);
	                            $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
	                            $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

                                $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;

                                $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

                                $resultClone = wp_remote_get($urlToGetHtml, [
                                    'timeout' => 60,
                                    'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
                                ]);

                                $conteudo = '';

                                if (is_array($resultClone) && !is_wp_error($resultClone)) {
                                    $conteudo = stripslashes($resultClone['body']);
                                }

	                            $conteudo = $this->adicionaCssSuperLinks($conteudo);
                                $addLinksModel->setAttribute('htmlClonePage', $conteudo);
                            } else {
                                $addLinksModel->setAttribute('htmlClonePage', '');
                            }
                        }
                    }

                    $idAddLinks = $addLinksModel->save();


                    if ($idAddLinks) {
                        SuperLinksAddLinkModel::saveDependencies($idAddLinks, $_POST, $_POST[$addLinksModel->getModelName()]['redirectType']);
                        $savedLink = true;
                    }
                }
            }

            if($savedLink){
                $toast = $this->toast;
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupLinkModel
                    ->getAllData();

	            if(isset($_POST['stayPage']) && $_POST['stayPage']){
		            $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_edit_link&id='.$idAddLinks;
	            }else {
		            // redireciona para a lista de páginas
		            if($addLinksModel->getAttribute('idGroup')) {
			            $urlView = $this->urlView . '&idCategory=' . $addLinksModel->getAttribute('idGroup');
		            }else if($this->existCategory($groups) && !$addLinksModel->getAttribute('idGroup')){
			            $urlView = $this->urlView . '&idCategory=none';
		            }else{
			            $urlView = $this->urlView;
		            }
	            }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }else{
	            $errors = $this->addLinksModel->getErrors();
	            if($errors){
		            if(isset($_POST[$this->groupLinkModel->getModelName()])) {
			            $this->groupLinkModel->setAttributes( $_POST[ $this->groupLinkModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->monitoringModel->getModelName()])) {
			            $this->monitoringModel->setAttributes( $_POST[ $this->monitoringModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->cloakModel->getModelName()])) {
			            $this->cloakModel->setAttributes( $_POST[ $this->cloakModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->configSocialModel->getModelName()])) {
			            $this->configSocialModel->setAttributes( $_POST[ $this->configSocialModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->waitPageModel->getModelName()])) {
			            $this->waitPageModel->setAttributes( $_POST[ $this->waitPageModel->getModelName() ] );
		            }
		            if(isset($_POST[$this->apiConvertFaceModel->getModelName()])) {
			            $this->apiConvertFaceModel->setAttributes( $_POST[ $this->apiConvertFaceModel->getModelName() ] );
		            }
		            $this->errorSave = $errors;
	            }
            }

	        if ($id && $existeVisualizacao) {
                $this->render(SUPER_LINKS_VIEWS_PATH . '/links/clone.php');
			}
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }
    }

    public function editGroup()
    {
		$existeVisualizacao = false;

        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar a Categoria');
                $this->groupLinkModel = new SuperLinksGroupLinkModel();
                $this->groupLinkModel->setIsNewRecord(false);

                $this->groupLinkModel->loadDataByID($id);
	            $groupNameSP = $this->groupLinkModel->getAttribute('groupName');

	            if($groupNameSP) {
		            $existeVisualizacao = true;
		            if ( isset( $_POST['scenario'] ) ) {
			            $groupLinkModel = $this->groupLinkModel;
			            $groupLinkModel->setAttributes( $_POST[ $groupLinkModel->getModelName() ] );
			            $idGroupModel = $groupLinkModel->save();

			            if ( $idGroupModel ) {
				            $toast        = TranslateHelper::getTranslate( 'A categoria foi atualizada com sucesso!' );
				            $timeToExpire = $this->timeToExpire;
				            $urlView      = $this->urlView;
				            echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '" . $urlView . "'
                        </script>";
				            exit();
			            } else {
				            $toast        = TranslateHelper::getTranslate( 'Não houve alteração na categoria!' );
				            $timeToExpire = $this->timeToExpire;
				            $urlView      = $this->urlView;
				            echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '" . $urlView . "'
                        </script>";
				            exit();
			            }
		            }
		            $this->render( SUPER_LINKS_VIEWS_PATH . '/links/editGroup.php' );
	            }
            }
        }

		if(!$existeVisualizacao){
			die('.');
		}
    }

    public function isPluginActive(){
        // Versão multisite sempre ativa
        return true;
    }
    // Clone Pages

    public function viewClonePages()
    {
        if($this->isPluginActive()) {
            if(isset($_GET['idCategory']) && $_GET['idCategory']) {
                $idGroup = $_GET['idCategory'];

                if($idGroup == 'none'){
                    $idGroup = null;
                }

                $this->viewClonePagesByGroup($idGroup);
            }else {

                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar categorias de páginas clonadas');

                $this->groupLinkModel = new SuperLinksCloneGroupModel();
                $allGroups['groups'] = $this->groupLinkModel
                    ->getAllData();

                $allGroups['existCategory'] = $this->existCategory($allGroups['groups']);

                $this->pageData = array_merge($allGroups, $this->pageData);

                $allLinks['links'] = $this->addLinksModel
                    ->getAllDataClonador();

                $allLinks['existLinkWithoutCategory'] = $this->existLinkwithoutCategory($allLinks['links']);
                $allLinks['existLinks'] = $this->existLinks($allLinks['links']);
                $allLinks['totalAccessThisLink'] = $this->totalAccessLink($allLinks['links']);

                $this->pageData = array_merge($allLinks, $this->pageData);


                $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/listView.php');
            }
        }
    }

    private function viewClonePagesByGroup($idGroup = null){
        $groupName = "Sem categoria";

        if(!is_null($idGroup)){
            $this->groupLinkModel = new SuperLinksCloneGroupModel();
            $this->groupLinkModel->loadDataByID($idGroup);
            $groupName = $this->groupLinkModel->getAttribute('groupName');
        }

        $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar páginas clonadas na Categoria: ') . "<strong>" . $groupName . "</strong>";

        $addLinksModel = new SuperLinksAddLinkModel();
        $this->addLinksModel = $addLinksModel;
        $linksByGroup = $addLinksModel->getLinksByIDGroupAndClonador($idGroup);

        $allLinks['links'] = $linksByGroup;
        $allLinks['existLinks'] = $this->existLinks($allLinks['links']);
        $allLinks['totalAccessThisLink'] = $this->totalAccessLink($allLinks['links']);

        $this->pageData = array_merge($allLinks, $this->pageData);

        $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/listViewLinks.php');
    }

    public function editGroupClone()
    {
	    $existeVisualizacao = false;
        if($this->isPluginActive()) {
            $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar a Categoria');
                $this->groupLinkModel = new SuperLinksCloneGroupModel();
                $this->groupLinkModel->setIsNewRecord(false);

                $this->groupLinkModel->loadDataByID($id);

				$groupNameSP = $this->groupLinkModel->getAttribute('groupName');

				if($groupNameSP) {
					$existeVisualizacao = true;
					$this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_list_Clones';
					if ( isset( $_POST['scenario'] ) ) {
						$groupLinkModel = $this->groupLinkModel;
						$groupLinkModel->setAttributes( $_POST[ $groupLinkModel->getModelName() ] );
						$idGroupModel = $groupLinkModel->save();

						if ( $idGroupModel ) {
							$toast        = TranslateHelper::getTranslate( 'A categoria foi atualizada com sucesso!' );
							$timeToExpire = $this->timeToExpire;
							$urlView      = $this->urlView;
							echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '" . $urlView . "'
                        </script>";
							exit();
						} else {
							$toast        = TranslateHelper::getTranslate( 'Não houve alteração na categoria!' );
							$timeToExpire = $this->timeToExpire;
							$urlView      = $this->urlView;
							echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '" . $urlView . "'
                        </script>";
							exit();
						}
					}
					$this->render( SUPER_LINKS_VIEWS_PATH . '/clonePages/editGroup.php' );
				}
            }
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }

    }

    public function renderPopupCode($path, array $args)
    {
        ob_start();
        include($path);
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }

    public function getExpirationTime($value)
    {
        if (empty($value)) return 0;
        return $value;
    }

    public function createPopupSuperLinks($popup_id,$popupBackgroundColor = '',$popupAnimation = '')
    {
        $popup_path = SUPER_LINKS_VIEWS_PATH . '/clonePages/popupSuperLinks.php';

        $popup_post = get_post($popup_id);

        if(isset($popup_post->post_content) && $popup_post->post_content) {
            $meta = [
                'id' => $popup_id,
                'animation' => $popupAnimation,
                'background' => $popupBackgroundColor,
                'content' => apply_filters('the_content', $popup_post->post_content)
            ];

            $rendered_popup = $this->renderPopupCode($popup_path, $meta);
            update_post_meta($popup_id, '_superlinks_popup', $rendered_popup);
        }
    }

    public function createClone()
    {
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_list_Clones';
        $savedLink = false;
        if($this->isPluginActive()) {
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Clonar Página');
            $this->groupLinkModel = new SuperLinksCloneGroupModel();
            $this->affiliateUrlModel = new SuperLinksAffiliateLinkModel();
            $this->monitoringModel = new SuperLinksLinkMonitoringModel();
            $this->configSocialModel = new SuperLinksLinkConfigSocialModel();
            $this->waitPageModel = new SuperLinksWaitPageModel();
            $this->clonePageModel = new SuperLinksLinkClonePageModel();
            $this->apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();

            $this->addLinksModel->setAttribute('redirectDelay', '0');
            $this->addLinksModel->setAttribute('redirectType', 'clonador');
            $this->addLinksModel->setAttribute('loadPopupAfterSeconds', '0');
            $this->addLinksModel->setAttribute('popupBackgroundColor', 'rgba(255, 255, 255, 100)');
            $this->addLinksModel->setAttribute('popupAnimation', 'none');
            $this->addLinksModel->setAttribute('rgpd', '0');
            $this->addLinksModel->setAttribute('opniaoClientePgClonada', 'nao');
            $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook', 'ViewContent');

            if (isset($_POST['scenario'])) {
                $addLinksModel = $this->addLinksModel;
                $affiliateUrlModel = $this->affiliateUrlModel;
                $groupLinkModel = $this->groupLinkModel;

                $addLinksModel->setAttributes($_POST[$addLinksModel->getModelName()]);
                $addLinksModel->setAttribute('createdAt', DateHelper::agora());

                $keyWord = $addLinksModel->getAttribute('keyWord');
                $keyWord = strtolower($keyWord);
                $addLinksModel->setAttribute('keyWord', $keyWord);

	            $affiliateUrlErro = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];

				if($affiliateUrlErro) {
					$affiliateUrlErro = array_shift( $affiliateUrlErro );
				}

                if(isset($_POST[$addLinksModel->getModelName()]['redirectBtn'])) {
                    $addLinksModel->setAttribute('redirectBtn', $_POST[$addLinksModel->getModelName()]['redirectBtn']);
                }

                if(isset($_POST[$addLinksModel->getModelName()]['idPage'])) {
                    $addLinksModel->setAttribute('idPage', $_POST[$addLinksModel->getModelName()]['idPage']);
                }else{
                    $addLinksModel->setAttribute('idPage', '');
                }

                if(isset($_POST[$addLinksModel->getModelName()]['idPopupDesktop'])) {
                    $addLinksModel->setAttribute('idPopupDesktop', $_POST[$addLinksModel->getModelName()]['idPopupDesktop']);
                    $popupBackgroundColor = $_POST[$addLinksModel->getModelName()]['popupBackgroundColor'];
                    $popupAnimation = $_POST[$addLinksModel->getModelName()]['popupAnimation'];
                    $this->createPopupSuperLinks($_POST[$addLinksModel->getModelName()]['idPopupDesktop'],$popupBackgroundColor,$popupAnimation);
                }else{
                    $addLinksModel->setAttribute('idPopupDesktop', '');
                }

                if(isset($_POST[$addLinksModel->getModelName()]['idPopupMobile'])) {
                    $addLinksModel->setAttribute('idPopupMobile', $_POST[$addLinksModel->getModelName()]['idPopupMobile']);
                    $popupBackgroundColor = $_POST[$addLinksModel->getModelName()]['popupBackgroundColor'];
                    $popupAnimation = $_POST[$addLinksModel->getModelName()]['popupAnimation'];
                    $this->createPopupSuperLinks($_POST[$addLinksModel->getModelName()]['idPopupMobile'],$popupBackgroundColor,$popupAnimation);
                }else{
                    $addLinksModel->setAttribute('idPopupMobile', '');
                }

                if(isset($_POST[$addLinksModel->getModelName()]['loadPopupAfterSeconds'])) {
                    $addLinksModel->setAttribute('loadPopupAfterSeconds', $_POST[$addLinksModel->getModelName()]['loadPopupAfterSeconds']);
                }else{
                    $addLinksModel->setAttribute('loadPopupAfterSeconds', 0);
                }

                $addLinksModel->setAttribute('redirectDelay', '0');
                $addLinksModel->setAttribute('redirectType', 'clonador');
                $addLinksModel->setAttribute('renovaHtmlClone', 'enabled');

                if($_POST[$groupLinkModel->getModelName()]['id']){
                    $addLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                }else{
                    $addLinksModel->setNullToAttribute('idGroup');
                }

				$faltamDadosParaSalvar = false;
                $affiliateUrl = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];

                $htmlDaPaginaCorrigida = '';

                if (!$affiliateUrl) {
                    // Não existe link de afiliado enviado pelo cliente
	                $faltamDadosParaSalvar = true;
                }

				$affiliateUrl = array_shift( $affiliateUrl );
				$affiliateUrl = trim( $affiliateUrl );

				if ( ! $affiliateUrl ) {
					// Não existe link de afiliado enviado pelo cliente
					$faltamDadosParaSalvar = true;
				}

	            $idAddLinks = false;
	            if($faltamDadosParaSalvar) {
					//atribui os erros
		            $this->affiliateUrlModel->setErrors('affiliateUrl','required');
	            }

	            if(!$faltamDadosParaSalvar) {
		            $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSplClone( $affiliateUrl );

		            if ( ! $affiliateUrl ) {
			            $this->render( SUPER_LINKS_VIEWS_PATH . '/clonePages/erroClonagem.php' );
			            die();
		            }

		            $affiliateUrl                                                = $addLinksModel->removeReferenciaAfiliadoUrlSpl( $affiliateUrl );
		            $_POST[ $affiliateUrlModel->getModelName() ]['affiliateUrl'] = [ '0' => $affiliateUrl ];


		            $urlGetPaginaCorrigida = "https://wpsuperlinks.top/wp-json/spl-light/v1/getClonePage?urlPaginaClonada=$affiliateUrl&access_token=mistVAvdCXthnyqMWG5XhJXTc8VHC";
		            $existePaginaCorrigida = wp_remote_get( $urlGetPaginaCorrigida, [
			            'timeout'    => 60,
			            'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		            ] );

		            if ( ! is_wp_error( $existePaginaCorrigida ) ) {
			            if ( isset( $existePaginaCorrigida['body'] ) && isset( $existePaginaCorrigida['body'] ) ) {
				            $dataPageJson = $existePaginaCorrigida['body'];
				            $dataPageJson = json_decode( $dataPageJson );
				            if ( $dataPageJson && isset( $dataPageJson->data ) && $dataPageJson->data ) {
					            $dataBody              = $dataPageJson->data;
					            $htmlDaPaginaCorrigida = $dataBody->htmlClonePage;
					            $addLinksModel->setAttribute( 'compatibilityMode', $dataBody->compatibilidade );
					            $addLinksModel->setAttribute( 'forceCompatibility', $dataBody->forcar );
					            $addLinksModel->setAttribute( 'enableProxy', $dataBody->proxy );
					            $addLinksModel->setAttribute( 'saveHtmlClone', 'enabled' );
				            }
			            }
		            }

		            if ( ! $htmlDaPaginaCorrigida ) {
			            $conteudo = '';

			            if ( $affiliateUrl ) {

				            $enableProxy = ( $_POST[ $addLinksModel->getModelName() ]['enableProxy'] == 'enabled' ) ? true : false;

				            $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

				            $resultClone = wp_remote_get( $urlToGetHtml, [
					            'timeout'    => 60,
					            'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
				            ] );

				            if ( is_array( $resultClone ) && ! is_wp_error( $resultClone ) ) {
					            $conteudo = stripslashes( $resultClone['body'] );
				            }
			            }
		            } else {
			            $conteudo = $htmlDaPaginaCorrigida;
		            }

		            $conteudo = $this->adicionaCssSuperLinks( $conteudo );
		            $addLinksModel->setAttribute( 'htmlClonePage', $conteudo );


		            $addLinksModel->removeAttribute( 'usarClonagemAvancada' );
		            $idAddLinks = $addLinksModel->save();
	            }

                if ($idAddLinks) {
                    SuperLinksAddLinkModel::saveDependencies($idAddLinks, $_POST, 'clonador');
                    $savedLink = true;

//	                $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSplClone($affiliateUrl);
//
//	                if(!$affiliateUrl){
//		                $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/erroClonagem.php');
//		                die();
//	                }
//
//	                $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
//	                $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

//	                $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;

//	                $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

//	                $clonarModoAvancado = isset($_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'])? $_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'] : 'disabled';
//
//					if($clonarModoAvancado == 'enabled') {
//						$clonadorHelper   = new ClonadorHelper();
//						$htmlNovaClonagem = $clonadorHelper->efetuaClonagem( $urlToGetHtml, $idAddLinks, $conteudo );
//
//						$atualizaHtmlPgClone = new SuperLinksAddLinkModel();
//						$atualizaHtmlPgClone->loadDataByID( $idAddLinks );
//						$atualizaHtmlPgClone->setIsNewRecord( false );
//						$atualizaHtmlPgClone->setAttribute( 'htmlClonePage', $htmlNovaClonagem );
//						$atualizaHtmlPgClone->save();
//					}

	                if (is_plugin_active('super-boost/super-boost.php')) {
						//salva a página wordpress
		                $this->criaPaginaClonadaWordpress($keyWord);
	                }
                }
            }

            if($savedLink){
	            $toast = TranslateHelper::getTranslate('A Página foi salva com sucesso!');
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupLinkModel
                    ->getAllData();

	            if(isset($_POST['stayPage']) && $_POST['stayPage']){
		            $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_edit_clone&id='.$idAddLinks;
	            }else {
		            // redireciona para a lista de páginas
		            if($addLinksModel->getAttribute('idGroup')) {
			            $urlView = $this->urlView . '&idCategory=' . $addLinksModel->getAttribute('idGroup');
		            }else if($this->existCategory($groups) && !$addLinksModel->getAttribute('idGroup')){
			            $urlView = $this->urlView . '&idCategory=none';
		            }else{
			            $urlView = $this->urlView;
		            }
	            }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }else {
	            $errors = $this->addLinksModel->getErrors();
	            $errorsAfil = $this->affiliateUrlModel->getErrors();

				$errosAll = array_merge($errors, $errorsAfil);

	            if ($errosAll) {
		            if ( isset( $_POST[ $this->groupLinkModel->getModelName() ] ) ) {
			            $this->groupLinkModel->setAttributes( $_POST[ $this->groupLinkModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->affiliateUrlModel->getModelName() ] ) ) {
			            $this->affiliateUrlModel->setAttribute( 'affiliateUrl', $affiliateUrlErro );
		            }
		            if ( isset( $_POST[ $this->monitoringModel->getModelName() ] ) ) {
			            $this->monitoringModel->setAttributes( $_POST[ $this->monitoringModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->configSocialModel->getModelName() ] ) ) {
			            $this->configSocialModel->setAttributes( $_POST[ $this->configSocialModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->waitPageModel->getModelName() ] ) ) {
			            $this->waitPageModel->setAttributes( $_POST[ $this->waitPageModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->clonePageModel->getModelName() ] ) ) {
			            $this->clonePageModel->setAttributes( $_POST[ $this->clonePageModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->apiConvertFaceModel->getModelName() ] ) ) {
			            $this->apiConvertFaceModel->setAttributes( $_POST[ $this->apiConvertFaceModel->getModelName() ] );
		            }
		            $this->errorSave = $errosAll;
	            }
            }

            $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/index.php');
        }
    }

	private function criaPaginaClonadaWordpress($page_slug){
		$wp_load_path = ABSPATH . '/wp-load.php';
		if (file_exists($wp_load_path)) {
			require_once( $wp_load_path );

			// Verificar se a página com o slug especificado já existe
			if ( get_page_by_path( $page_slug ) ) {
//				echo 'A página com o slug especificado já existe.';
			} else {
				// Criar um novo objeto de página
				$new_page = array(
					'post_title'  => $page_slug,
					'post_name'   => $page_slug,
					'post_status' => 'publish', // Status da página: publicada
					'post_type'   => 'page' // Tipo do post: página
				);

				// Inserir a nova página no banco de dados
				$page_id = wp_insert_post( $new_page );

//				if ( $page_id ) {
//					echo 'Página criada com sucesso. ID da página: ' . $page_id;
//				} else {
//					echo 'Erro ao criar a página.';
//				}
			}
		}
	}

    public function updateClone()
    {
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_list_Clones';
        $savedLink = false;
	    $existeVisualizacao = false;
        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar página clonada');
                $this->groupLinkModel = new SuperLinksCloneGroupModel();
                $this->affiliateUrlModel = new SuperLinksAffiliateLinkModel();
                $this->monitoringModel = new SuperLinksLinkMonitoringModel();
                $this->configSocialModel = new SuperLinksLinkConfigSocialModel();
                $this->waitPageModel = new SuperLinksWaitPageModel();
                $this->clonePageModel = new SuperLinksLinkClonePageModel();
                $this->apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
                $this->pgBrancaGringaModel = new SuperLinksPgBrancaGringaModel();


                $this->addLinksModel->setAttribute('popupBackgroundColor', 'rgba(255, 255, 255, 100)');
                $this->addLinksModel->setAttribute('popupAnimation', 'none');

                $this->addLinksModel->loadDataByID($id);

	            $confereKeyWord = $this->addLinksModel->getAttribute('keyWord');

	            if($confereKeyWord){
		            $existeVisualizacao = true;
	            }

                $this->addLinksModel->setAttribute('redirectDelay', '0');
                $this->addLinksModel->setAttribute('redirectType', 'clonador');

                $this->addLinksModel->setIsNewRecord(false);

	            $conteudo = $this->addLinksModel->getAttribute('htmlClonePage');

	            if($this->existePrestoPlayerPagina($conteudo)){
		            $presto_path = 'presto-player/presto-player.php';

		            if (!is_plugin_active($presto_path)) {
			            $this->instalarPrestoPlayer = true;
		            }

		            $compatibilityModeAtivado = $this->addLinksModel->getAttribute('compatibilityMode');

					if($compatibilityModeAtivado != 'enabled') {
						$this->precisaAtivarCompatibilidade = true;
					}
	            }

                if (!empty($this->addLinksModel->getAttributes())) {

                    $idLink = $this->addLinksModel->getAttribute('id');
                    $idGroup = $this->addLinksModel->getAttribute('idGroup');

                    if(!$this->addLinksModel->getAttribute('loadPopupAfterSeconds')){
                        $this->addLinksModel->setAttribute('loadPopupAfterSeconds',0);
                    }

                    $oldRedirectType = $this->addLinksModel->getAttribute('redirectType');

                    $this->groupLinkModel->setAttribute('id', $idGroup);

                    //caso seja redirecionamento do tipo facebook
                    if($this->addLinksModel->getAttribute('redirectType') == 'facebook'){
                        $internalKeyWord =  $this->addLinksModel->getAttribute('keyWord') . '/facebook';

                        //pega os dados do link de afiliado corretos
                        $internalLinkModel = new SuperLinksAddLinkModel();

                        $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
                        if($internalLinkData) {
                            $internalLinkData = array_shift($internalLinkData);
                            $internalLinkData = get_object_vars($internalLinkData);
                            $idLink = $internalLinkData['id'];
                            $this->addLinksModel->setAttribute('redirectFace', $internalLinkData['redirectType']);
                            $this->addLinksModel->setAttribute('redirectDelay', $internalLinkData['redirectDelay']);
                            $this->addLinksModel->setAttribute('idInternalLink', $internalLinkData['id']);
                            $this->addLinksModel->setAttribute('redirectBtn', $internalLinkData['redirectBtn']);
                        }
                    }

                    $eventoApiFacebook = $this->apiConvertFaceModel->getAllDataByParam($idLink,'idLink');
                    if($eventoApiFacebook) {
                        $eventoApiFacebook = array_shift($eventoApiFacebook);
                        $this->apiConvertFaceModel->loadDataByID($eventoApiFacebook->id);
                        if(!$this->apiConvertFaceModel->getAttribute('eventNameApiFacebook')){
                            $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook','ViewContent');
                        }
                    }else{
                        $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook','ViewContent');
                    }


	                $dataAffiliate = $this->affiliateUrlModel->getAllDataByParam(
		                $idLink,
		                'idLink'
	                );

	                if ($dataAffiliate) {
		                $dataAffiliateId = $dataAffiliate;
		                $dataAffiliateId = array_shift($dataAffiliateId);
	                }

	                $affiliateUrl = '';

	                if(isset($dataAffiliateId->id)) {
		                $this->affiliateUrlModel->loadDataByID($dataAffiliateId->id);

		                $affiliateUrl = [];
		                foreach ($dataAffiliate as $affiliate) {
			                $affiliateUrl[$affiliate->id] = $affiliate->affiliateUrl;
		                }

		                $this->affiliateUrlModel->setAttribute('affiliateUrl', $affiliateUrl);
	                }

					// Encontra links de checkout na página clonada
	                $htmlParaEncontrarCheckout = '';

	                if(isset($conteudo) && $conteudo){
		                $htmlParaEncontrarCheckout = $conteudo;
	                }else{
						if($affiliateUrl) {
		                    $linkAfiliadoCheck = array_shift($affiliateUrl);
							$resultClone = wp_remote_get( $linkAfiliadoCheck, [
								'timeout'    => 60,
								'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
							] );

							$htmlParaEncontrarCheckout = '';

							if ( is_array( $resultClone ) && ! is_wp_error( $resultClone ) ) {
								$htmlParaEncontrarCheckout = $resultClone['body'];
							}
						}
	                }

	                $pattern = '/<a\s+(?:[^>]*?\s+)?href\s*=\s*["\']?(?!#)([^"\'>\s]+)/i';
	                preg_match_all($pattern, $htmlParaEncontrarCheckout, $matches);
	                $links = $matches[1]; // Contém todos os links encontrados.

	                $words = array('hotmart', 'api.whatsapp', 'monetizze', 'braip', 'tikto', 'kiwify', 'clickbank', 'digistore', 'buygoods', 'mon.net', 'eduzz', '#elementor-');

	                $filteredLinks = array_filter($links, function($link) use ($words) {
		                // Removendo espaços do link
		                $link = str_replace(' ', '', $link);

		                foreach ($words as $word) {
			                if (strpos($link, $word) !== false) {
				                return true;
			                }
		                }
		                return false;
	                });

					// se for página builderall tira as barras invertidas
	                $ehPgBuilderall = false;
	                $patternBuilderall = '/auxPagesConfig\.push/';
					$procuraLinksBuilderall = false;
	                if (preg_match($patternBuilderall, $htmlParaEncontrarCheckout)) {
		                $procuraLinksBuilderall = true;
		                $htmlParaEncontrarCheckout = stripslashes($htmlParaEncontrarCheckout);

		                $configuradoSalvarHtml = $this->addLinksModel->getAttribute('saveHtmlClone');
						if($configuradoSalvarHtml != 'disabled') {
							$ehPgBuilderall = true;
						}else{
							$ehPgBuilderall = false;
						}

		                $configuradoCompatibilidade = $this->addLinksModel->getAttribute('compatibilityMode');
		                if($configuradoCompatibilidade != 'enabled') {
			                $this->precisaAtivarCompatibilidade = true;
		                }else{
			                $this->precisaAtivarCompatibilidade = false;
		                }

	                }

	                $this->pageData['ehPaginaBuilderall'] = $ehPgBuilderall;
	                $linksNaoEncontradosAnteriormente = [];

					//encontrando links da hotmart em páginas do builderall
	                if($procuraLinksBuilderall) {
		                $patternHot = '/[\'"](https?:\/\/[^\'">]*?\.hotmart\.com[^\'">]*?)[\'"]/';

		                preg_match_all( $patternHot, $htmlParaEncontrarCheckout, $matchesHot );

		                $linksNaoEncontradosAnteriormente = $matchesHot[0];
		                $linksNaoEncontradosAnteriormente = array_unique( $linksNaoEncontradosAnteriormente );

		                foreach ( $linksNaoEncontradosAnteriormente as $key => $linksNaoEncontrados ) {
			                $linksNaoEncontrados = str_replace( array(
				                '"',
				                "'"
			                ), '', $linksNaoEncontrados );
			                $linksNaoEncontrados = str_replace( "/", "\\/", $linksNaoEncontrados );
			                $linksNaoEncontradosAnteriormente[ $key ] = $linksNaoEncontrados;
		                }
	                }

	                //fim encontrando links da hotmart em páginas do builderall

	                //encontrando formulario de captura elementor

	                $linksFormularioCaptura = [];
	                $patternElem = '/<a\s+[^>]*?href=(["\'])(#elementor[^"\']*)\1[^>]*?>/i';

	                preg_match_all( $patternElem, $htmlParaEncontrarCheckout, $matchesElem );

	                $linksFormularioCaptura = $matchesElem[2];
	                $linksFormularioCaptura = array_unique( $linksFormularioCaptura );

	                foreach ( $linksFormularioCaptura as $key => $linksFormC ) {
		                $linksFormC = str_replace( ' ', '', $linksFormC );
		                $linksFormularioCaptura[ $key ] = $linksFormC;
	                }

	                $linksFormularioCaptura = array_unique($linksFormularioCaptura);

	                // fim encontrando formulario de captura elementor


	                //verificando se é página wix
	                $patternWix = '/https?:\/\/(?:static\.wixstatic\.com|static\.parastorage\.com)/i';

	                $ehPaginaWix = false;

	                if (preg_match($patternWix, $htmlParaEncontrarCheckout)) {
		                $ehPaginaWix = true;
	                }
	                //fim verificando se é página wix

	                // Removendo links duplicados
	                $filteredLinks = array_unique($filteredLinks);

	                $linksCheckoutProdutor = [];
	                $linksWhatsappProdutor = [];
	                foreach($filteredLinks as $linksNaoSubstituidos){
						$ehWhatsapp = false;
		                $checkoutNaoSubstituido = $linksNaoSubstituidos;

		                if($checkoutNaoSubstituido){
			                $checkoutNaoSubstituido = trim($checkoutNaoSubstituido);
		                }

		                if (strpos($checkoutNaoSubstituido, "api.whatsapp.com/") !== false) {
			                $position = strpos($checkoutNaoSubstituido, '&');
			                if ($position !== false) {
				                $checkoutNaoSubstituido = substr($checkoutNaoSubstituido, 0, $position);
			                }
			                $ehWhatsapp = true;
		                }

		                if (strpos($checkoutNaoSubstituido, "api.whatsapp.com/") !== false) {
			                $position = strpos($checkoutNaoSubstituido, '&');
			                if ($position !== false) {
				                $checkoutNaoSubstituido = substr($checkoutNaoSubstituido, 0, $position);
			                }
			                $ehWhatsapp = true;
		                }

						if($ehWhatsapp){
							$linksWhatsappProdutor[] = $checkoutNaoSubstituido;
						}else {
							$linksCheckoutProdutor[] = $checkoutNaoSubstituido;
						}
	                }

	                // fim encontra links de checkout

	                // limpa os links de checkout

	                foreach ($linksWhatsappProdutor as $index => $link) {
		                $limpaWhatsapp = str_replace('&#038;', '&', $link);
		                $limpaWhatsapp = str_replace('&amp;', '&', $limpaWhatsapp);
		                $linksWhatsappProdutor[$index] = $limpaWhatsapp;
	                }

	                foreach ($linksCheckoutProdutor as $index => $link) {
		                $limpaCheckout = str_replace('&#038;', '&', $link);
		                $limpaCheckout = str_replace('&amp;', '&', $limpaCheckout);
		                $linksCheckoutProdutor[$index] = $limpaCheckout;
	                }

	                foreach ($linksNaoEncontradosAnteriormente as $index => $link) {
		                $limpaCheckoutNE = str_replace('&#038;', '&', $link);
		                $limpaCheckoutNE = str_replace('&amp;', '&', $limpaCheckoutNE);
		                $linksNaoEncontradosAnteriormente[$index] = $limpaCheckoutNE;
	                }

	                foreach ($linksFormularioCaptura as $index => $link) {
		                $limpaCheckoutFC = str_replace('&#038;', '&', $link);
		                $limpaCheckoutFC = str_replace('&amp;', '&', $limpaCheckoutFC);
		                $linksFormularioCaptura[$index] = $limpaCheckoutFC;
	                }

	                // fim limpa os links de checkout

                    // clone
                    $dataClone = $this->clonePageModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    $this->pageData['fezSubstituicaolinksCheckout'] = false;
                    if($dataClone) {
                        $this->pageData['fezSubstituicaolinksCheckout'] = true;
	                    $clonePageItem = [];
	                    $cloneNewItem = [];
	                    $cloneTypeItem = [];

                        foreach ($dataClone as $value) {
	                        $pageItemLimpo = $value->pageItem;
							if($pageItemLimpo){
								$pageItemLimpo = trim($pageItemLimpo);
							}

							if(in_array($pageItemLimpo, $linksFormularioCaptura)){
								$key = array_search($pageItemLimpo, $linksFormularioCaptura);
								if($key !== false){
									unset($linksFormularioCaptura[$key]);
								}
							}

							if(in_array($pageItemLimpo, $linksNaoEncontradosAnteriormente)){
								$key = array_search($pageItemLimpo, $linksNaoEncontradosAnteriormente);
								if($key !== false){
									unset($linksNaoEncontradosAnteriormente[$key]);
								}
							}

							if(in_array($pageItemLimpo, $linksCheckoutProdutor)){
								$key = array_search($pageItemLimpo, $linksCheckoutProdutor);
								if($key !== false){
									unset($linksCheckoutProdutor[$key]);
								}
							}

	                        if(in_array($pageItemLimpo, $linksWhatsappProdutor)){
		                        $key = array_search($pageItemLimpo, $linksWhatsappProdutor);
		                        if($key !== false){
			                        unset($linksWhatsappProdutor[$key]);
		                        }
	                        }

	                        $clonePageItem[$value->id] = $pageItemLimpo;
	                        $cloneNewItem[$value->id] = $value->newItem;
	                        $cloneTypeItem[$value->id] = $value->typeItem;

                        }

	                    $this->clonePageModel->setAttribute('pageItem', $clonePageItem);
	                    $this->clonePageModel->setAttribute('newItem', $cloneNewItem);
	                    $this->clonePageModel->setAttribute('typeItem', $cloneTypeItem);
                    }

	                foreach ($linksNaoEncontradosAnteriormente as $key=>$linksNaoEncontrados){
		                $linksNaoEncontrados = str_replace("\\", "\\\\", $linksNaoEncontrados);
		                $linksNaoEncontradosAnteriormente[$key] = $linksNaoEncontrados;
	                }

	                $linksCheckoutProdutor = array_merge($linksCheckoutProdutor, $linksNaoEncontradosAnteriormente);

	                $this->pageData['linksCheckoutProdutor'] = $linksCheckoutProdutor;
	                $this->pageData['linksWhatsappProdutor'] = $linksWhatsappProdutor;
	                $this->pageData['linksCapturaProdutor'] = $linksFormularioCaptura;

	                $configuradoSalvarHtmlWix = $this->addLinksModel->getAttribute('saveHtmlClone');
	                if($ehPaginaWix && ($configuradoSalvarHtmlWix != 'disabled')) {
		                $ehPaginaWix = true;
	                }else{
		                $ehPaginaWix = false;
	                }

	                $this->pageData['ehPaginaWix'] = $ehPaginaWix;
                    // fim clone

                    // Troca link checkout gringa

                    $dataPgGringaClone = $this->pgBrancaGringaModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    $linksCheckoutGringaFaltandoSubstituir = $linksCheckoutProdutor;

                    if($dataPgGringaClone) {
                        $dataPgGringaClone = array_shift($dataPgGringaClone);

                        $checkoutProdutor = $dataPgGringaClone->checkoutProdutor;
                        $linkPaginaVenda = $dataPgGringaClone->linkPaginaVenda;
                        $textoTempoRedirecionamento = $dataPgGringaClone->textoTempoRedirecionamento;
                        $abrirPaginaBranca = $dataPgGringaClone->abrirPaginaBranca;
                        $tempoRedirecionamentoCheckout = $dataPgGringaClone->tempoRedirecionamentoCheckout;

                        if($checkoutProdutor) {
                            $this->pgBrancaGringaModel->setAttribute('checkoutProdutor', $checkoutProdutor);
                            $this->pgBrancaGringaModel->setAttribute('linkPaginaVenda', $linkPaginaVenda);
                            $this->pgBrancaGringaModel->setAttribute('textoTempoRedirecionamento', $textoTempoRedirecionamento);
                            $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', $tempoRedirecionamentoCheckout);
                            $this->pgBrancaGringaModel->setAttribute('abrirPaginaBranca', $abrirPaginaBranca);
                        }

                        $linksCheckoutGringa = $dataPgGringaClone->checkoutProdutor;
                        if($linksCheckoutGringa){
                            $linksCheckoutGringa = unserialize($linksCheckoutGringa);
                            foreach($linksCheckoutGringa as $valPageItem){
                                if($valPageItem){
                                    if($valPageItem){
                                        $valPageItem = trim($valPageItem);
                                    }

                                    if(in_array($valPageItem, $linksCheckoutGringaFaltandoSubstituir)){
                                        $key = array_search($valPageItem, $linksCheckoutGringaFaltandoSubstituir);
                                        if($key !== false){
                                            unset($linksCheckoutGringaFaltandoSubstituir[$key]);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $this->pgBrancaGringaModel->setAttribute('tempoRedirecionamentoCheckout', 1);
                    }

                    $this->pageData['linksCheckoutProdutorGringa'] = $linksCheckoutGringaFaltandoSubstituir;
                    // fim troca link checkout gringa


                    $dataMonitoring = $this->monitoringModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataMonitoring) {
                        $dataMonitoring = array_shift($dataMonitoring);
                        $this->monitoringModel->loadDataByID($dataMonitoring->id);
                    }


                    $dataConfigSocial = $this->configSocialModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataConfigSocial) {
                        $dataConfigSocial = array_shift($dataConfigSocial);
                        $this->configSocialModel->loadDataByID($dataConfigSocial->id);
                    }

                    $dataWaitPage = $this->waitPageModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataWaitPage) {
                        $dataWaitPage = array_shift($dataWaitPage);
                        $this->waitPageModel->loadDataByID($dataWaitPage->id);
                    }

                }

                if (isset($_POST['scenario'])) {
                    $addLinksModel = $this->addLinksModel;
                    $affiliateUrlModel = $this->affiliateUrlModel;
                    $groupLinkModel = $this->groupLinkModel;

                    $linkBeforeUpdate = new SuperLinksAddLinkModel();
                    $linkBeforeUpdate->loadDataByID($id);


                    //era e continua sendo um link facebook
                    if(isset($_POST[$addLinksModel->getModelName()]['redirectFace'])  && $linkBeforeUpdate->getAttribute('redirectType') == 'facebook'){
                        $postFacebook = $_POST[$addLinksModel->getModelName()];
                        $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                        $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';
                        $postFacebook['id'] = $postFacebook['idInternalLink'];

                        unset($postFacebook['redirectFace']);
                        unset($postFacebook['idInternalLink']);
                        unset($_POST[$addLinksModel->getModelName()]['redirectFace']);
                        unset($_POST[$addLinksModel->getModelName()]['idInternalLink']);

                        $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                        SuperLinksAddLinkModel::updateFacebookLink($postFacebook, $_POST);

                        $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                        $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                        $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                    }elseif(!isset($_POST[$addLinksModel->getModelName()]['redirectFace']) && $linkBeforeUpdate->getAttribute('redirectType') == 'facebook'){
                        //era um link facebook que mudou para outro tipo de link
                        // exclui este link e alterar o keyword do outro link

                        $_POST[$addLinksModel->getModelName()]['keyWord'] = $linkBeforeUpdate->getAttribute('keyWord');
                        $_POST[$addLinksModel->getModelName()]['id'] = $_POST[$addLinksModel->getModelName()]['idInternalLink'];
                        unset($_POST[$addLinksModel->getModelName()]['idInternalLink']);

                        $deleteFacebookLink = new SuperLinksAddLinkModel();
                        $deleteFacebookLink->loadDataByID($linkBeforeUpdate->getAttribute('id'));
                        $deleteFacebookLink->delete();
                    }elseif(isset($_POST[$addLinksModel->getModelName()]['redirectFace']) && $linkBeforeUpdate->getAttribute('redirectType') != 'facebook'){
                        //era um link que mudou para link facebook
                        $postFacebook = $_POST[$addLinksModel->getModelName()];
                        $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                        $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';
                        $postFacebook['id'] = $postFacebook['idInternalLink'];

                        unset($postFacebook['redirectFace']);
                        unset($postFacebook['idInternalLink']);
                        unset($_POST[$addLinksModel->getModelName()]['redirectFace']);
                        unset($_POST[$addLinksModel->getModelName()]['idInternalLink']);

                        $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                        SuperLinksAddLinkModel::saveFacebookLink($postFacebook, $_POST);

                        $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                        $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                        $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                    }


                    $addLinksModel->setAttributes($_POST[$addLinksModel->getModelName()]);
                    $addLinksModel->setAttribute('updatedAt', DateHelper::agora());

                    $keyWord = $addLinksModel->getAttribute('keyWord');
                    $keyWord = strtolower($keyWord);
                    $addLinksModel->setAttribute('keyWord', $keyWord);

                    $addLinksModel->setAttribute('redirectDelay', $_POST[$addLinksModel->getModelName()]['redirectDelay']);
                    $addLinksModel->setAttribute('numberWhatsapp', $_POST[$addLinksModel->getModelName()]['numberWhatsapp']);
                    $addLinksModel->setAttribute('textWhatsapp', $_POST[$addLinksModel->getModelName()]['textWhatsapp']);

                    if(isset($_POST[$addLinksModel->getModelName()]['idPage'])) {
                        $addLinksModel->setAttribute('idPage', $_POST[$addLinksModel->getModelName()]['idPage']);
                    }else{
                        $addLinksModel->setAttribute('idPage', '');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['idPopupDesktop'])) {
                        $addLinksModel->setAttribute('idPopupDesktop', $_POST[$addLinksModel->getModelName()]['idPopupDesktop']);
                        $popupBackgroundColor = $_POST[$addLinksModel->getModelName()]['popupBackgroundColor'];
                        $popupAnimation = $_POST[$addLinksModel->getModelName()]['popupAnimation'];
                        $this->createPopupSuperLinks($_POST[$addLinksModel->getModelName()]['idPopupDesktop'],$popupBackgroundColor,$popupAnimation);
                    }else{
                        $addLinksModel->setAttribute('idPopupDesktop', '');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['idPopupMobile'])) {
                        $addLinksModel->setAttribute('idPopupMobile', $_POST[$addLinksModel->getModelName()]['idPopupMobile']);
                        $popupBackgroundColor = $_POST[$addLinksModel->getModelName()]['popupBackgroundColor'];
                        $popupAnimation = $_POST[$addLinksModel->getModelName()]['popupAnimation'];
                        $this->createPopupSuperLinks($_POST[$addLinksModel->getModelName()]['idPopupMobile'],$popupBackgroundColor,$popupAnimation);
                    }else{
                        $addLinksModel->setAttribute('idPopupMobile', '');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['loadPopupAfterSeconds'])) {
                        $addLinksModel->setAttribute('loadPopupAfterSeconds', $_POST[$addLinksModel->getModelName()]['loadPopupAfterSeconds']);
                    }else{
                        $addLinksModel->setAttribute('loadPopupAfterSeconds', 0);
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['redirectBtn'])) {
                        $addLinksModel->setAttribute('redirectBtn', $_POST[$addLinksModel->getModelName()]['redirectBtn']);
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'php' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'camuflador'){
                        $addLinksModel->setAttribute('redirectDelay', '0');
                    }

                    $redirectType = $_POST[$addLinksModel->getModelName()]['redirectType'];

					if(!isset($oldRedirectType)){
						$oldRedirectType = "";
					}

                    if($oldRedirectType != $redirectType){
                        $htmlClonePage = "";
                    }else{
                        $htmlClonePage = "";
                        if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' && isset($_POST[$addLinksModel->getModelName()]['htmlClonePage'])) {
                            $htmlClonePage = stripslashes($_POST[$addLinksModel->getModelName()]['htmlClonePage']);
                        }
                    }

                    $addLinksModel->removeAttribute('redirectFace');
                    $addLinksModel->removeAttribute('idInternalLink');
                    $addLinksModel->setAttribute('abLastTest', '0');

                    if($_POST[$groupLinkModel->getModelName()]['id']){
                        $addLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                    }else{
                        $addLinksModel->setNullToAttribute('idGroup');
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' && ((isset($_POST[$addLinksModel->getModelName()]['htmlClonePage']) && !$_POST[$addLinksModel->getModelName()]['htmlClonePage']) || $_POST[$addLinksModel->getModelName()]['renovaHtmlClone'] == 'enabled')) {
                        $affiliateUrl = $affiliateUrlModel->getAttribute('affiliateUrl');
                        $affiliateUrl = array_shift($affiliateUrl);

//	                    $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSpl($affiliateUrl);
//	                    $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
	                    $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

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
                                    $addLinksModel->setAttribute('compatibilityMode', $dataBody->compatibilidade);
                                    $addLinksModel->setAttribute('forceCompatibility', $dataBody->forcar);
                                    $addLinksModel->setAttribute('enableProxy', $dataBody->proxy);
                                    $addLinksModel->setAttribute('saveHtmlClone', 'enabled');

//	                                $clonarModoAvancado = isset($_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'])? $_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'] : 'disabled';
//
//	                                if($clonarModoAvancado == 'enabled') {
//		                                $clonadorHelper        = new ClonadorHelper();
//		                                $htmlDaPaginaCorrigida = $clonadorHelper->efetuaClonagem( $affiliateUrl, $id, $htmlDaPaginaCorrigida );
//	                                }
                                }
                            }
                        }

                        if(!$htmlDaPaginaCorrigida) {
                            $existeCodigoHtmlSalvo = isset($_POST[$addLinksModel->getModelName()]['htmlClonePage'])? $_POST[$addLinksModel->getModelName()]['htmlClonePage'] : '';
                            $renovarHtml = false;
                            if($_POST[$addLinksModel->getModelName()]['renovaHtmlClone'] == 'enabled'){
                                $renovarHtml = true;
                            }

                            if ($existeCodigoHtmlSalvo) {

                               if($renovarHtml){
                                   $affiliateUrl = $affiliateUrlModel->getAttribute('affiliateUrl');
                                   $affiliateUrl = array_shift($affiliateUrl);

//	                               $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSpl($affiliateUrl);
//	                               $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
	                               $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

                                   if ($affiliateUrl && $_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {

                                       $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;

                                       $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

//	                                   $clonarModoAvancado = isset($_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'])? $_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'] : 'disabled';
//
//	                                   if($clonarModoAvancado == 'enabled') {
//		                                   $clonadorHelper   = new ClonadorHelper();
//		                                   $htmlNovaClonagem = $clonadorHelper->efetuaClonagem( $urlToGetHtml, $id, "" );
//		                                   $conteudo         = $htmlNovaClonagem;
//
//	                                   }else {
	                                       $resultClone = wp_remote_get($urlToGetHtml, [
	                                           'timeout' => 60,
	                                           'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
	                                       ]);

	                                       $conteudo = '';

	                                       if (is_array($resultClone) && !is_wp_error($resultClone)) {
	                                           $conteudo = stripslashes($resultClone['body']);
	                                       }
//	                                   }

	                                   $conteudo = $this->adicionaCssSuperLinks($conteudo);
                                       $addLinksModel->setAttribute('htmlClonePage', $conteudo);
                                   } else {
                                       $addLinksModel->setAttribute('htmlClonePage', '');
                                   }
                               }else {
                                   if ($_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {
                                       $addLinksModel->setAttribute('htmlClonePage', $htmlClonePage);
                                   } else {
                                       $addLinksModel->setAttribute('htmlClonePage', '');
                                   }
                               }
                            }else {
                                $affiliateUrl = $affiliateUrlModel->getAttribute('affiliateUrl');
                                $affiliateUrl = array_shift($affiliateUrl);

//	                            $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSpl($affiliateUrl);
//	                            $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
	                            $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

                                if ($affiliateUrl && $_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {

                                    $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;

                                    $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

//	                                $clonarModoAvancado = isset($_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'])? $_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'] : 'disabled';
//
//	                                if($clonarModoAvancado == 'enabled') {
//		                                $clonadorHelper   = new ClonadorHelper();
//		                                $htmlNovaClonagem = $clonadorHelper->efetuaClonagem( $urlToGetHtml, $id, "" );
//		                                $conteudo         = $htmlNovaClonagem;
//
//	                                }else {
	                                    $resultClone = wp_remote_get($urlToGetHtml, [
	                                        'timeout' => 60,
	                                        'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
	                                    ]);

	                                    $conteudo = '';

	                                    if (is_array($resultClone) && !is_wp_error($resultClone)) {
	                                        $conteudo = stripslashes($resultClone['body']);
	                                    }
//	                                }

	                                $conteudo = $this->adicionaCssSuperLinks($conteudo);
                                    $addLinksModel->setAttribute('htmlClonePage', $conteudo);
                                } else {
                                    $addLinksModel->setAttribute('htmlClonePage', '');
                                }
                            }
                        }else{
                            $conteudo = $htmlDaPaginaCorrigida;
                            $addLinksModel->setAttribute('htmlClonePage', $conteudo);
                        }
                    }else{
                        if ($_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {
                            $addLinksModel->setAttribute('htmlClonePage', $htmlClonePage);
                        } else {
                            $addLinksModel->setAttribute('htmlClonePage', '');
                        }
                    }

	                if (isset($_POST[$addLinksModel->getModelName()]['counterSuperEscassez']) && !$_POST[$addLinksModel->getModelName()]['counterSuperEscassez']) {
                        $addLinksModel->setAttribute('counterSuperEscassez', 0);
                    }

                    if (isset($_POST[$addLinksModel->getModelName()]['alertaConversoes']) && !$_POST[$addLinksModel->getModelName()]['alertaConversoes']) {
                        $addLinksModel->setAttribute('alertaConversoes', 0);
                    }

	                if (isset($_POST[$addLinksModel->getModelName()]['rgpd']) && !$_POST[$addLinksModel->getModelName()]['rgpd']) {
                        $addLinksModel->setAttribute('rgpd', 0);
                    }

	                $addLinksModel->removeAttribute('usarClonagemAvancada');
                    $isSavedLink = $addLinksModel->save();


                    if ($isSavedLink) {
                        SuperLinksAddLinkModel::updateDependencies($addLinksModel, $_POST, $_POST[$addLinksModel->getModelName()]['redirectType']);
                        $savedLink = true;
                    }
                }
            }

            if($savedLink){
                $toast = TranslateHelper::getTranslate('A Página foi atualizada com sucesso!');
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupLinkModel
                    ->getAllData();

	            if(isset($_POST['stayPage']) && $_POST['stayPage']){
		            $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_edit_clone&id='.$id;
	            }else {
					// redireciona para a lista de páginas
		            if ( $addLinksModel->getAttribute( 'idGroup' ) ) {
			            $urlView = $this->urlView . '&idCategory=' . $addLinksModel->getAttribute( 'idGroup' );
		            } else if ( $this->existCategory( $groups ) && ! $addLinksModel->getAttribute( 'idGroup' ) ) {
			            $urlView = $this->urlView . '&idCategory=none';
		            } else {
			            $urlView = $this->urlView;
		            }
	            }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }else {
	            $errors = $this->addLinksModel->getErrors();
	            if ( $errors ) {
		            if ( isset( $_POST[ $this->groupLinkModel->getModelName() ] ) ) {
			            $this->groupLinkModel->setAttributes( $_POST[ $this->groupLinkModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->affiliateUrlModel->getModelName() ] ) ) {
			            $this->affiliateUrlModel->setAttributes( $_POST[ $this->affiliateUrlModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->monitoringModel->getModelName() ] ) ) {
			            $this->monitoringModel->setAttributes( $_POST[ $this->monitoringModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->configSocialModel->getModelName() ] ) ) {
			            $this->configSocialModel->setAttributes( $_POST[ $this->configSocialModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->waitPageModel->getModelName() ] ) ) {
			            $this->waitPageModel->setAttributes( $_POST[ $this->waitPageModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->clonePageModel->getModelName() ] ) ) {
			            $this->clonePageModel->setAttributes( $_POST[ $this->clonePageModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->apiConvertFaceModel->getModelName() ] ) ) {
			            $this->apiConvertFaceModel->setAttributes( $_POST[ $this->apiConvertFaceModel->getModelName() ] );
		            }
		            $this->errorSave = $errors;
	            }
            }

	        if ($id && $existeVisualizacao) {
		        $this->render( SUPER_LINKS_VIEWS_PATH . '/clonePages/update.php' );
	        }
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }
    }

    public function clonePageCloned()
    {
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_list_Clones';
        $savedLink = false;
	    $existeVisualizacao = false;
        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Duplicar este link');
                $this->groupLinkModel = new SuperLinksCloneGroupModel();
                $this->affiliateUrlModel = new SuperLinksAffiliateLinkModel();
                $this->monitoringModel = new SuperLinksLinkMonitoringModel();
                $this->configSocialModel = new SuperLinksLinkConfigSocialModel();
                $this->waitPageModel = new SuperLinksWaitPageModel();
                $this->clonePageModel = new SuperLinksLinkClonePageModel();
                $this->apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();

                $this->addLinksModel->setAttribute('popupBackgroundColor', 'rgba(255, 255, 255, 100)');
                $this->addLinksModel->setAttribute('popupAnimation', 'none');

                $this->addLinksModel->loadDataByID($id);

                $this->addLinksModel->setAttribute('redirectDelay', '0');
                $this->addLinksModel->setAttribute('redirectType', 'clonador');

                if(!$this->apiConvertFaceModel->getAttribute('eventNameApiFacebook')){
                    $this->apiConvertFaceModel->setAttribute('eventNameApiFacebook', 'ViewContent');
                }

                if (!empty($this->addLinksModel->getAttributes())) {

                    $idLink = $this->addLinksModel->getAttribute('id');

	                if($idLink){
		                $existeVisualizacao = true;
	                }

                    $idGroup = $this->addLinksModel->getAttribute('idGroup');

                    if(!$this->addLinksModel->getAttribute('loadPopupAfterSeconds')){
                        $this->addLinksModel->setAttribute('loadPopupAfterSeconds',0);
                    }

                    $this->groupLinkModel->setAttribute('id', $idGroup);

                    //caso seja redirecionamento do tipo facebook
                    if($this->addLinksModel->getAttribute('redirectType') == 'facebook'){
                        $internalKeyWord =  $this->addLinksModel->getAttribute('keyWord') . '/facebook';

                        //pega os dados do link de afiliado corretos
                        $internalLinkModel = new SuperLinksAddLinkModel();

                        $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
                        if($internalLinkData) {
                            $internalLinkData = array_shift($internalLinkData);
                            $internalLinkData = get_object_vars($internalLinkData);
                            $idLink = $internalLinkData['id'];
                            $this->addLinksModel->setAttribute('redirectFace', $internalLinkData['redirectType']);
                            $this->addLinksModel->setAttribute('redirectDelay', $internalLinkData['redirectDelay']);
                            $this->addLinksModel->setAttribute('idInternalLink', $internalLinkData['id']);
                        }
                    }

                    // clone
                    $dataClone = $this->clonePageModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if($dataClone) {

                        $clonePageItem = [];
                        $cloneNewItem = [];
                        $cloneTypeItem = [];
                        foreach ($dataClone as $value) {
                            $clonePageItem[$value->id] = $value->pageItem;
                            $cloneNewItem[$value->id] = $value->newItem;
                            $cloneTypeItem[$value->id] = $value->typeItem;
                        }

                        $this->clonePageModel->setAttribute('pageItem', $clonePageItem);
                        $this->clonePageModel->setAttribute('newItem', $cloneNewItem);
                        $this->clonePageModel->setAttribute('typeItem', $cloneTypeItem);
                    }

                    // fim clone

                    $dataAffiliate = $this->affiliateUrlModel->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    if ($dataAffiliate) {
                        $dataAffiliateId = $dataAffiliate;
                        $dataAffiliateId = array_shift($dataAffiliateId);
                    }

	                $idAfiliate = isset($dataAffiliateId->id)? $dataAffiliateId->id : false;
                    $this->affiliateUrlModel->loadDataByID($idAfiliate);

                    $affiliateUrl = [];
                    foreach ($dataAffiliate as $affiliate) {
                        $affiliateUrl[$affiliate->id] = $affiliate->affiliateUrl;
                    }

                    $this->affiliateUrlModel->setAttribute('affiliateUrl', $affiliateUrl);

                    $dataMonitoring = $this->monitoringModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataMonitoring) {
                        $dataMonitoring = array_shift($dataMonitoring);
                        $this->monitoringModel->loadDataByID($dataMonitoring->id);
                    }

                    $dataConfigSocial = $this->configSocialModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataConfigSocial) {
                        $dataConfigSocial = array_shift($dataConfigSocial);
                        $this->configSocialModel->loadDataByID($dataConfigSocial->id);
                    }

                    $dataWaitPage = $this->waitPageModel->getAllDataByParam(
                        $this->addLinksModel->getAttribute('id'),
                        'idLink'
                    );

                    if ($dataWaitPage) {
                        $dataWaitPage = array_shift($dataWaitPage);
                        $this->waitPageModel->loadDataByID($dataWaitPage->id);
                    }

                    $this->addLinksModel->setAttribute('linkName', '');
                    $this->addLinksModel->setAttribute('keyWord', '');
                }

                if (isset($_POST['scenario'])) {
                    $addLinksModel = new SuperLinksAddLinkModel();
                    $affiliateUrlModel  = new SuperLinksAffiliateLinkModel();
                    $groupLinkModel = new SuperLinksCloneGroupModel();

                    //verificar se o link é do facebook
                    if(isset($_POST[$addLinksModel->getModelName()]['redirectFace'])){
                        $postFacebook = $_POST[$addLinksModel->getModelName()];
                        $postFacebook['redirectType'] = $postFacebook['redirectFace'];
                        $postFacebook['keyWord'] =  $postFacebook['keyWord'] . '/facebook';

                        unset($postFacebook['redirectFace']);
                        unset($_POST[$addLinksModel->getModelName()]['redirectFace']);

                        $affiliateLinkPhp = SUPER_LINKS_TEMPLATE_URL . '/' . $postFacebook['keyWord']; // este vai ser o link de afiliado do php

                        SuperLinksAddLinkModel::saveFacebookLink($postFacebook, $_POST);

                        $_POST[$addLinksModel->getModelName()]['redirectType'] = 'facebook';
                        $_POST[$addLinksModel->getModelName()]['redirectDelay'] = '0';
                        $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateLinkPhp];
                    }

                    $addLinksModel->setAttributes($_POST[$addLinksModel->getModelName()]);
                    $addLinksModel->setAttribute('createdAt', DateHelper::agora());

                    $keyWord = $addLinksModel->getAttribute('keyWord');
                    $keyWord = strtolower($keyWord);
                    $addLinksModel->setAttribute('keyWord', $keyWord);

                    $addLinksModel->setAttribute('redirectDelay', $_POST[$addLinksModel->getModelName()]['redirectDelay']);

                    if(isset($_POST[$addLinksModel->getModelName()]['redirectBtn'])) {
                        $addLinksModel->setAttribute('redirectBtn', $_POST[$addLinksModel->getModelName()]['redirectBtn']);
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'php' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador' || $_POST[$addLinksModel->getModelName()]['redirectType'] == 'camuflador'){
                        $addLinksModel->setAttribute('redirectDelay', '0');
                    }

                    $addLinksModel->setAttribute('abLastTest', '0');

                    if($_POST[$groupLinkModel->getModelName()]['id']){
                        $addLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                    }else{
                        $addLinksModel->setNullToAttribute('idGroup');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['idPage'])) {
                        $addLinksModel->setAttribute('idPage', $_POST[$addLinksModel->getModelName()]['idPage']);
                    }else{
                        $addLinksModel->setAttribute('idPage', '');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['idPopupDesktop'])) {
                        $addLinksModel->setAttribute('idPopupDesktop', $_POST[$addLinksModel->getModelName()]['idPopupDesktop']);
                        $popupBackgroundColor = $_POST[$addLinksModel->getModelName()]['popupBackgroundColor'];
                        $popupAnimation = $_POST[$addLinksModel->getModelName()]['popupAnimation'];
                        $this->createPopupSuperLinks($_POST[$addLinksModel->getModelName()]['idPopupDesktop'],$popupBackgroundColor,$popupAnimation);
                    }else{
                        $addLinksModel->setAttribute('idPopupDesktop', '');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['idPopupMobile'])) {
                        $addLinksModel->setAttribute('idPopupMobile', $_POST[$addLinksModel->getModelName()]['idPopupMobile']);
                        $popupBackgroundColor = $_POST[$addLinksModel->getModelName()]['popupBackgroundColor'];
                        $popupAnimation = $_POST[$addLinksModel->getModelName()]['popupAnimation'];
                        $this->createPopupSuperLinks($_POST[$addLinksModel->getModelName()]['idPopupMobile'],$popupBackgroundColor,$popupAnimation);
                    }else{
                        $addLinksModel->setAttribute('idPopupMobile', '');
                    }

                    if(isset($_POST[$addLinksModel->getModelName()]['loadPopupAfterSeconds'])) {
                        $addLinksModel->setAttribute('loadPopupAfterSeconds', $_POST[$addLinksModel->getModelName()]['loadPopupAfterSeconds']);
                    }else{
                        $addLinksModel->setAttribute('loadPopupAfterSeconds', 0);
                    }

                    if($_POST[$addLinksModel->getModelName()]['redirectType'] == 'clonador') {
                        if (isset($_POST[$addLinksModel->getModelName()]['htmlClonePage'])) {
                            $htmlClonePage = stripslashes($_POST[$addLinksModel->getModelName()]['htmlClonePage']);
                            $addLinksModel->setAttribute('htmlClonePage', $htmlClonePage);
                        } else {
                            $affiliateUrl = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];
                            if ($affiliateUrl && $_POST[$addLinksModel->getModelName()]['saveHtmlClone'] == 'enabled') {
                                $affiliateUrl = array_shift($affiliateUrl);

	                            $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSpl($affiliateUrl);
	                            $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
	                            $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];

                                $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;

                                $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

                                $resultClone = wp_remote_get($urlToGetHtml, [
                                    'timeout' => 60,
                                    'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
                                ]);

                                $conteudo = '';

                                if (is_array($resultClone) && !is_wp_error($resultClone)) {
                                    $conteudo = stripslashes($resultClone['body']);
                                }

	                            $conteudo = $this->adicionaCssSuperLinks($conteudo);
                                $addLinksModel->setAttribute('htmlClonePage', $conteudo);
                            } else {
                                $addLinksModel->setAttribute('htmlClonePage', '');
                            }
                        }
                    }

                    if (isset($_POST[$addLinksModel->getModelName()]['counterSuperEscassez']) && !$_POST[$addLinksModel->getModelName()]['counterSuperEscassez']) {
                        $addLinksModel->setAttribute('counterSuperEscassez', 0);
                    }

	                if (isset($_POST[$addLinksModel->getModelName()]['alertaConversoes']) && !$_POST[$addLinksModel->getModelName()]['alertaConversoes']) {
                        $addLinksModel->setAttribute('alertaConversoes', 0);
                    }

	                if (isset($_POST[$addLinksModel->getModelName()]['rgpd']) && !$_POST[$addLinksModel->getModelName()]['rgpd']) {
                        $addLinksModel->setAttribute('rgpd', 0);
                    }

	                $addLinksModel->removeAttribute('usarClonagemAvancada');
                    $idAddLinks = $addLinksModel->save();


                    if ($idAddLinks) {
                        SuperLinksAddLinkModel::saveDependencies($idAddLinks, $_POST, $_POST[$addLinksModel->getModelName()]['redirectType']);
                        $savedLink = true;

//	                    $dominioClient = $this->getCurrentUrl();
//	                    $affiliateUrl = $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'];
//	                    $affiliateUrl = array_shift($affiliateUrl);

//	                    $affiliateUrl = $addLinksModel->getUrlOriginalPgVendasProdutorSplClone($affiliateUrl);
//
//	                    if(!$affiliateUrl){
//		                    $this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/erroClonagem.php');
//		                    die();
//	                    }
//
//	                    $affiliateUrl = $addLinksModel->removeReferenciaAfiliadoUrlSpl($affiliateUrl);
//	                    $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] = ['0' => $affiliateUrl];
//
//	                    $enableProxy = ($_POST[$addLinksModel->getModelName()]['enableProxy'] == 'enabled') ? true : false;
//
//	                    $urlToGetHtml = $enableProxy ? SUPER_LINKS_HELPERS_URL . '/super-links-proxy.php?' . $affiliateUrl : $affiliateUrl;

//	                    $clonarModoAvancado = isset($_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'])? $_POST[$addLinksModel->getModelName()]['usarClonagemAvancada'] : 'disabled';
//
//	                    if($clonarModoAvancado == 'enabled') {
//		                    $clonadorHelper   = new ClonadorHelper();
//		                    $htmlNovaClonagem = $clonadorHelper->efetuaClonagem( $urlToGetHtml, $idAddLinks, "" );
//
//		                    $atualizaHtmlPgClone = new SuperLinksAddLinkModel();
//		                    $atualizaHtmlPgClone->loadDataByID( $idAddLinks );
//		                    $atualizaHtmlPgClone->setIsNewRecord( false );
//		                    $atualizaHtmlPgClone->setAttribute( 'htmlClonePage', $htmlNovaClonagem );
//		                    $atualizaHtmlPgClone->save();
//	                    }

	                    if (is_plugin_active('super-boost/super-boost.php')) {
		                    //salva a página wordpress
		                    $this->criaPaginaClonadaWordpress($keyWord);
	                    }
                    }
                }
            }

            if($savedLink){
	            $toast = TranslateHelper::getTranslate('A Página foi salva com sucesso!');
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupLinkModel
                    ->getAllData();

	            if(isset($_POST['stayPage']) && $_POST['stayPage']){
		            $urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_edit_clone&id='.$idAddLinks;
	            }else {
		            // redireciona para a lista de páginas
		            if($addLinksModel->getAttribute('idGroup')) {
			            $urlView = $this->urlView . '&idCategory=' . $addLinksModel->getAttribute('idGroup');
		            }else if($this->existCategory($groups) && !$addLinksModel->getAttribute('idGroup')){
			            $urlView = $this->urlView . '&idCategory=none';
		            }else{
			            $urlView = $this->urlView;
		            }
	            }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }else {
	            $errors = $this->addLinksModel->getErrors();
	            if ( $errors ) {
		            if ( isset( $_POST[ $this->groupLinkModel->getModelName() ] ) ) {
			            $this->groupLinkModel->setAttributes( $_POST[ $this->groupLinkModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->affiliateUrlModel->getModelName() ] ) ) {
			            $this->affiliateUrlModel->setAttributes( $_POST[ $this->affiliateUrlModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->monitoringModel->getModelName() ] ) ) {
			            $this->monitoringModel->setAttributes( $_POST[ $this->monitoringModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->configSocialModel->getModelName() ] ) ) {
			            $this->configSocialModel->setAttributes( $_POST[ $this->configSocialModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->waitPageModel->getModelName() ] ) ) {
			            $this->waitPageModel->setAttributes( $_POST[ $this->waitPageModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->clonePageModel->getModelName() ] ) ) {
			            $this->clonePageModel->setAttributes( $_POST[ $this->clonePageModel->getModelName() ] );
		            }
		            if ( isset( $_POST[ $this->apiConvertFaceModel->getModelName() ] ) ) {
			            $this->apiConvertFaceModel->setAttributes( $_POST[ $this->apiConvertFaceModel->getModelName() ] );
		            }
		            $this->errorSave = $errors;
	            }
            }

	        if ($id && $existeVisualizacao) {
		        $this->render( SUPER_LINKS_VIEWS_PATH . '/clonePages/clone.php' );
	        }
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }
    }

    public function viewClone()
    {
	    $existeVisualizacao = false;
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_list_Clones';
        if($this->isPluginActive()) {
	        $id = isset($_GET['id'])? $_GET['id'] : false;
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $addLinkModel = new SuperLinksAddLinkModel();
                $addLinkModel->loadDataByID($id);

                if (!empty($addLinkModel->getAttributes())) {
	                $existeVisualizacao = true;
                    $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Dados da página clonada');

                    $this->pageData = array_merge($addLinkModel->getAttributes(), $this->pageData);

                    $affiliateLinks = new SuperLinksAffiliateLinkModel();

                    $idLink = $addLinkModel->getAttribute('id');

                    if($addLinkModel->getAttribute('redirectType') == 'facebook'){
                        $internalKeyWord =  $addLinkModel->getAttribute('keyWord') . '/facebook';

                        //pega os dados do link de afiliado corretos
                        $internalLinkModel = new SuperLinksAddLinkModel();

                        $internalLinkData = $internalLinkModel->getAllDataByParam($internalKeyWord,'keyWord');
                        if($internalLinkData) {
                            $internalLinkData = array_shift($internalLinkData);
                            $internalLinkData = get_object_vars($internalLinkData);
                            $idLink = $internalLinkData['id'];
                        }
                    }

                    $affiliateData = $affiliateLinks->getAllDataByParam(
                        $idLink,
                        'idLink'
                    );

                    $pageDataAffiliate = [];

                    foreach ($affiliateData as $affiliateDatum) {
                        $metricsModel = new SuperLinksLinkMetricsModel();
                        $metricsData = $metricsModel->getAllDataByParam($affiliateDatum->id, 'idAffiliateLink');
                        $pageDataAffiliate[] = ['affiliateData' => $affiliateDatum, 'metrics' => $metricsData];
                    }

                    $this->pageData = array_merge(['affiliate' => $pageDataAffiliate], $this->pageData);
                }

				$ipModel = new SuperLinksIpModel();
				$ipsData = $ipModel->getIpsByIdLink($id);
	            $this->pageData = array_merge(['ipsData' => $ipsData], $this->pageData);

				if($existeVisualizacao) {
					$this->render( SUPER_LINKS_VIEWS_PATH . '/clonePages/viewLink.php' );
				}
            }
        }

	    if(!$existeVisualizacao){
		    die('.');
	    }
    }

	public function adicionaCssSuperLinks($conteudo = ''){
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

	private function existePrestoPlayerPagina($content) {
		$pattern = '/(https?:\/\/[^\s]+\/)plugins\/presto-player\//';

		if (preg_match($pattern, $content)) {
			return true;
		}

		return false;
	}

	public function salvaOpniaoClientePgClonada(){
		$this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_opniao_Clone';
		$id = isset($_POST['id'])? $_POST['id'] : false;
		$opcl = isset($_POST['opcl'])? $_POST['opcl'] : false;
		$obsFeedback = isset($_POST['obsFeedback'])? $_POST['obsFeedback'] : false;
		$isSavedLink = false;

		$this->pageData['obsFeedback'] = $obsFeedback;

		if ($id && $opcl) {
			$this->addLinksModel->loadDataByID( $id );

			$opniaoClientePgClonada = $this->addLinksModel->getAttribute('opniaoClientePgClonada');

			if($opcl != 'sim' && $opcl != 'naoGostei'){
				$opcl = 'nao';
			}

			if($opniaoClientePgClonada == 'nao' && !$obsFeedback) {
				$isSavedLink = $this->addLinksModel->saveOpniaoCliente( $opcl, $id );
			}

			$this->pageData['opcl'] = $opcl;
			$this->pageData['id'] = $id;
			$slugPgClone = $this->addLinksModel->getAttribute('keyWord');

			$affiliateLinkModel = new SuperLinksAffiliateLinkModel();
			$affiliateLinkData = $affiliateLinkModel->getAllDataByParam($id, 'idLink', 'ORDER BY id ASC', 'limit 1', 'OFFSET 0');

			$affiliateUrl = false;
			$this->pageData['affiliateUrl'] = false;

			if($affiliateLinkData) {
				$affiliateLinkData = array_shift( $affiliateLinkData );
				$affiliateUrl      = trim( $affiliateLinkData->affiliateUrl );
			}

			if($slugPgClone) {
				$this->pageData['linkPgClonada'] = SUPER_LINKS_TEMPLATE_URL . '/' . $slugPgClone;
			}else{
				$this->pageData['linkPgClonada'] = false;
			}
		}

		if($id && $affiliateUrl){

			$dominio = SUPER_LINKS_TEMPLATE_URL;

			$urlToGetHtml = 'https://wpsuperlinks.top/wp-json/spl-light/v1/saveFeedbackPgClone?access_token=mistVAvdCXthnyqMWG5XhJXTc8VHC&urlPaginaClonada=' . $affiliateUrl . '&dominioCliente=' . $dominio.'&obsFeedback=' . $obsFeedback.'&opcl=' . $opcl;

			wp_remote_get($urlToGetHtml, [
				'timeout' => 60,
				'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
			]);

			$this->pageData['feedbackClone'] = true;
		}

		if($isSavedLink && !$obsFeedback){
			$this->pageData['feedbackClone'] = true;
		}else{
			$this->pageData['feedbackClone'] = false;
		}

		$this->render(SUPER_LINKS_VIEWS_PATH . '/clonePages/opniaoClonagem.php');
	}
}