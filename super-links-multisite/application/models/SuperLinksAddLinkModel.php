<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksAddLinkModel extends SuperLinksCoreModel {

    private $defaultSecondsToRedirectDelay = 3; //por causa do rastreamento
	private $tokenClient = 'RESfdh4848fjdKYpYah1591dsa';

    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_link']
        );
    }

    public function getModelName(){
        return 'SuperLinksAddLinkModel';
    }

    public function rules()
    {
        return [
            [
                'linkName, keyWord, redirectType', 'required'
            ],
            [
                'keyWord', 'uniqueLink'
            ],
            [
                'redirectDelay', 'naturalNumber'
            ],
            [
                'redirectDelay', 'delayIsPossible'
            ],
            [
                'redirectBtn', 'isValidUrl'
            ]
        ];
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID Link'),
            'idGroup' => TranslateHelper::getTranslate('ID do grupo'),
            'linkName' => TranslateHelper::getTranslate('Nome do link'),
            'description' => '',
            'keyWord' => TranslateHelper::getTranslate('Endereço do link <small class="text-warning">(Não pode conter espaços nem acentos)</small>'),
            'redirectType' => TranslateHelper::getTranslate('Tipo do redirecionamento'),
            'redirectDelay' => TranslateHelper::getTranslate('Redirecionar após'),
            'statusLink' => TranslateHelper::getTranslate('Status'),
            'abLastTest' => TranslateHelper::getTranslate('Último teste A/B'),
            'createdAt' => TranslateHelper::getTranslate('Criado em:'),
            'updatedAt' => TranslateHelper::getTranslate('Atualizado em:'),
            'redirectBtn' => TranslateHelper::getTranslate('Url de página em branco ou de isca digital para redirecionar o usuário quando ele tentar sair da página'),
            'htmlClonePage' => TranslateHelper::getTranslate('Código HTML da página'),
            'saveHtmlClone' => TranslateHelper::getTranslate('Passo 4: Salvar Html da página clonada'),
            'enableProxy' => TranslateHelper::getTranslate('Passo 3: Habilitar proxy na página clonada?'),
            'enableRedirectJavascript' => TranslateHelper::getTranslate('Habilitar redirecionamento javascript caso a página não possa ser camuflada?'),
            'numberWhatsapp' => TranslateHelper::getTranslate('Número de whatsapp'),
            'textWhatsapp' => TranslateHelper::getTranslate('Texto padrão para envio de mensagem pelo Whatsapp'),
            'idPage' => TranslateHelper::getTranslate('Associar está página clonada a uma página do wordpress'),
            'idPopupDesktop' => TranslateHelper::getTranslate('Selecionar popup para desktop'),
            'idPopupMobile' => TranslateHelper::getTranslate('Selecionar popup para dispositivos móveis'),
            'exitIntentPopup' => TranslateHelper::getTranslate('Quando exibir o popup?'),
            'loadPopupAfterSeconds' => TranslateHelper::getTranslate('Carregar popup depois de'),
            'popupBackgroundColor' => TranslateHelper::getTranslate('Cor de fundo do Popup'),
            'popupAnimation' => TranslateHelper::getTranslate('Animação de entrada do Popup'),
            'compatibilityMode' => TranslateHelper::getTranslate('Passo 1: Ativar modo de compatibilidade?'),
            'forceCompatibility' => TranslateHelper::getTranslate('Passo 2: Forçar clonagem?'),
            'counterSuperEscassez' => TranslateHelper::getTranslate('Contador Super Escassez'),
            'alertaConversoes' => TranslateHelper::getTranslate('Alerta de Conversões'),
            'rgpd' => TranslateHelper::getTranslate('RGPD'),
            'renovaHtmlClone' => TranslateHelper::getTranslate('Sempre renovar Html da página?'),
            'opniaoClientePgClonada' => TranslateHelper::getTranslate('Opnião do cliente sobre a página clonada'),
            'usarClonagemAvancada' => TranslateHelper::getTranslate('Usar Clonagem avançada?'),
            'removerPixelPgClonada' => TranslateHelper::getTranslate('Passo 5: Remover pixel do produtor automaticamente?'),
            'usarEstrategiaGringa' => TranslateHelper::getTranslate('Usar Marcação de Cookie ao Invés da Substituir Checkout?'),

        );
    }

    public function getOptionsRedirectPro(){
        return [
            ['selected' => true, 'text' => TranslateHelper::getTranslate('Redirecionador (Html)'), 'val' => 'html'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (Javascript)'), 'val' => 'javascript'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (PHP)'), 'val' => 'php'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Camuflador'), 'val' => 'camuflador'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Link especial para o Facebook'), 'val' => 'facebook'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Link para Whatsapp e Telegram'), 'val' => 'wpp_tlg'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Marcador de Cookie (Criação de Página Branca)'), 'val' => 'pgBranca'],
        ];
    }

    public function getOptionsRedirectImport($selected = 'html'){
        $values = [
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (Html)'), 'val' => 'html'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (Javascript)'), 'val' => 'javascript'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (PHP)'), 'val' => 'php'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Camuflador'), 'val' => 'camuflador']
        ];

        foreach($values as $key => $value){
            $selectedValue = false;
            if($value['val'] == $selected){
                $selectedValue = true;
            }
            $values[$key] = ['selected' => $selectedValue, 'text' => $value['text'] , 'val' => $value['val']];
        }

        return $values;
    }

    public function getOptionsRedirectLight(){
        return [
            ['selected' => true, 'text' => TranslateHelper::getTranslate('Redirecionador (Html)'), 'val' => 'html'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (Javascript)'), 'val' => 'javascript'],
            ['selected' => false, 'text' => TranslateHelper::getTranslate('Redirecionador (PHP)'), 'val' => 'php']
        ];
    }

    private function helpTextRedirect(){
        return array(
            'html' => 'Redireciona o usuário para seus links de afiliado, podendo ser rastreado.',
            'javascript' => 'Redireciona o usuário para seus links de afiliado, podendo ser rastreado.',
            'wpp_tlg' => 'Redireciona o usuário para seus links de Whatsapp ou Telegram, podendo ser rastreado.',
            'php' => 'Redireciona o usuário para seus links de afiliado. Não é possível fazer rastreamento.',
            'camuflador' => 'Exibe a página referente aos seus links de afiliado sem sair do seu site.',
            'clonador' => 'Faz uma cópia de uma página diretamente para a sua estrutura.',
            'facebook' => 'Link especial para realizar anúncios ou compartilhamentos no Facebook.',
        );
    }

    public function getHelpTextRedirect($redirectType = ''){
        return isset($this->helpTextRedirect()[$redirectType]) ? $this->helpTextRedirect()[$redirectType] : '';
    }

    /**
     * @param string $keyword
     * @return array|bool|object|null
     */
    public function getLinkByPage($url = ''){
        if(empty($url)){
            return [];
        }

        return $this->getAllDataByParam($url,'idPage');
    }

    /**
     * @param string $keyword
     * @return array|bool|object|null
     */
    public function getLinkByKeyword($keyWord = ''){
        if(empty($keyWord)){
            return [];
        }

        return $this->getAllDataByParam($keyWord,'keyWord');
    }

    public function uniqueLink($attribute = ''){
        $attributeVal = $this->getAttribute($attribute);
        return (empty($this->getLinkByKeyword($attributeVal)) || $this->isTheSameLink())? true : false;
    }

    private function isTheSameLink(){

        $keyWord = $this->getAttribute('keyWord');
        $id = $this->getAttribute('id');

        if(!$id) {
            return false;
        }

        $addLinkData = $this->getAllDataByParam($id,'id');
        if($addLinkData) {
            $addLinkData = array_shift($addLinkData);
        }

        if(isset($addLinkData->keyWord) && (trim($addLinkData->keyWord) == trim($keyWord))){
            return true;
        }

        return false;
    }

    public function naturalNumber($attribute = ''){
        $attributeVal = $this->getAttribute($attribute);
        return ($attributeVal >= 0)? true : false;
    }

    public function updateLastTestAb($atualTestAb = 0){
        $this->setIsNewRecord(false);
        $this->setAttribute('abLastTest', $atualTestAb);
        $this->setExceptRules(['uniqueLink']);
        $this->save();
    }

    public function delayIsPossible($attribute = ''){
        $attributeVal = $this->getAttribute('redirectType');
        $delay = $this->getAttribute('redirectDelay');

        // somente redirecionamento tipo php nao tem tempo de espera
        if(($attributeVal == 'php' || $attributeVal == 'clonador' || $attributeVal == 'camuflador') && $delay > 0){
            return false;
        }

        return true;
    }

    public function getDefaultRedirectDelay(){
        $redirectDelay = $this->getAttribute('redirectDelay');

        if($redirectDelay < $this->defaultSecondsToRedirectDelay) {
            return $this->defaultSecondsToRedirectDelay;
        }

        return $redirectDelay;
    }

    public static function saveFacebookLink($postLink,$post){
        $addLinksModelFacebook = new SuperLinksAddLinkModel();
        $groupLinkModel = new SuperLinksGroupLinkModel();

        $addLinksModelFacebook->setAttributes($postLink);
        $addLinksModelFacebook->setAttribute('createdAt', DateHelper::agora());

        $keyWord = $addLinksModelFacebook->getAttribute('keyWord');
        $keyWord = strtolower($keyWord);
        $addLinksModelFacebook->setAttribute('keyWord', $keyWord);
        $addLinksModelFacebook->setAttribute('rgpd', 0);

        $addLinksModelFacebook->setAttribute('redirectDelay', $postLink['redirectDelay']);
        $redirectType = $addLinksModelFacebook->getAttribute('redirectType');

        if(isset($postLink['redirectBtn'])) {
            $addLinksModelFacebook->setAttribute('redirectBtn', $postLink['redirectBtn']);
        }

        if($redirectType == 'php' || $redirectType == 'clonador' || $redirectType == 'camuflador'){
            $addLinksModelFacebook->setAttribute('redirectDelay', '0');
        }

        if($_POST[$groupLinkModel->getModelName()]['id']){
            $addLinksModelFacebook->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
        }else{
            $addLinksModelFacebook->setNullToAttribute('idGroup');
        }

        $idAddLinks = $addLinksModelFacebook->save();

        if ($idAddLinks) {
            return self::saveDependencies($idAddLinks, $post, $redirectType);
        }

        return false;
    }

    public static function saveDependencies($idLink = null, $post = [], $redirectType = ''){
        if(is_null($idLink) || !$post || !$redirectType){
            return false;
        }

        $affiliateUrlModel = new SuperLinksAffiliateLinkModel();
        $monitoringModel = new SuperLinksLinkMonitoringModel();
        $cloakModel = new SuperLinksLinkCloakModel();
        $configSocialModel = new SuperLinksLinkConfigSocialModel();
        $waitPageModel = new SuperLinksWaitPageModel();
        $clonePageModel = new SuperLinksLinkClonePageModel();
        $apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
        $pgBrancaGringaModel = new SuperLinksPgBrancaGringaModel();

        foreach ($post[$affiliateUrlModel->getModelName()]['affiliateUrl'] as $value) {
            $urlSemEspacos = $value;
            $urlSemEspacos = str_replace(' ', "%20", $urlSemEspacos);
            $affiliateUrlModel->setAttribute('affiliateUrl', $urlSemEspacos);
            $affiliateUrlModel->setAttribute('createdAt', DateHelper::agora());
            $affiliateUrlModel->setAttribute('idLink', $idLink);
            $affiliateUrlModel->save();
        }

        if(isset($post[$clonePageModel->getModelName()]['pageItem'])) {
	        $cloneHelperLink = new ClonadorHelper();
            foreach ($post[$clonePageModel->getModelName()]['pageItem'] as $key => $value) {
	            $pitem = "";
                $newItem = $post[$clonePageModel->getModelName()]['newItem'][$key];
                $typeItem = $post[$clonePageModel->getModelName()]['typeItem'][$key];

	            $pitem = $cloneHelperLink->removeParametrosTrackeamentoLink($value);

                $clonePageModel = new SuperLinksLinkClonePageModel();
                $clonePageModel->setAttribute('pageItem', $pitem);
                $clonePageModel->setAttribute('newItem', $newItem);
                $clonePageModel->setAttribute('typeItem', $typeItem);
                $clonePageModel->setAttribute('idLink', $idLink);
                $clonePageModel->save();
            }
        }

        if ($redirectType != 'php') {

            foreach($post[$monitoringModel->getModelName()] as $key => $val){
                $post[$monitoringModel->getModelName()][$key] = stripslashes($val);
            }

            $monitoringModel->setAttributes($post[$monitoringModel->getModelName()]);
            $monitoringModel->setAttribute('idLink', $idLink);
            $monitoringModel->save();
            $isEnabledApiConvert = $post[$monitoringModel->getModelName()]['enableApiFacebook'];

            if($isEnabledApiConvert == 'enabled') {
                $apiConvertFaceModel->setAttributes($post[$apiConvertFaceModel->getModelName()]);
                $apiConvertFaceModel->setAttribute('idLink', $idLink);
                $apiConvertFaceModel->save();
            }
        }

        if ($redirectType == 'pgBranca') {
            if (isset($_POST[$pgBrancaGringaModel->getModelName()]['checkoutProdutor']) && $_POST[$pgBrancaGringaModel->getModelName()]['checkoutProdutor']) {

                $checkoutProdutor = $_POST[$pgBrancaGringaModel->getModelName()]['checkoutProdutor'];
                $linkPaginaVenda = isset($_POST[$pgBrancaGringaModel->getModelName()]['linkPaginaVenda'])? $_POST[$pgBrancaGringaModel->getModelName()]['linkPaginaVenda'] : '';
                $abrirPaginaBranca = $_POST[$pgBrancaGringaModel->getModelName()]['abrirPaginaBranca'];
                $textoTempoRedirecionamento = $_POST[$pgBrancaGringaModel->getModelName()]['textoTempoRedirecionamento'];
                $tempoRedirecionamentoCheckout = $_POST[$pgBrancaGringaModel->getModelName()]['tempoRedirecionamentoCheckout'];

                if ($checkoutProdutor) {
                    $cloneHelperLink = new ClonadorHelper();
                    $checkoutProdutorCorrigido = [];
                    foreach ($checkoutProdutor as $key => $value) {
                        $pitem = "";
                        $pitem = $cloneHelperLink->removeParametrosTrackeamentoLink($value);
                        $pitem = stripslashes($pitem);
                        $checkoutProdutorCorrigido[] = $pitem;
                    }

                    $checkoutProdutorCorrigido = serialize($checkoutProdutorCorrigido);

                    $updateClone = new SuperLinksPgBrancaGringaModel();
                    $updateClone->setAttribute('checkoutProdutor', $checkoutProdutorCorrigido);
                    $updateClone->setAttribute('linkPaginaVenda', $linkPaginaVenda);
                    $updateClone->setAttribute('abrirPaginaBranca', $abrirPaginaBranca);
                    $updateClone->setAttribute('textoTempoRedirecionamento', $textoTempoRedirecionamento);
                    $updateClone->setAttribute('tempoRedirecionamentoCheckout', $tempoRedirecionamentoCheckout);
                    $updateClone->setAttribute('idLink', $idLink);
                    $updateClone->updateLinkPgBranca();
                }
            }
        }

        if(isset($post[$cloakModel->getModelName()])) {
            $cloakModel->setAttributes($post[$cloakModel->getModelName()]);
            $cloakModel->setAttribute('idLink', $idLink);
            $cloakModel->save();
        }

        $configSocialModel->setAttributes($post[$configSocialModel->getModelName()]);
        $configSocialModel->setAttribute('idLink', $idLink);
        $configSocialModel->save();

        $waitPageModel->setAttributes($post[$waitPageModel->getModelName()]);
        $waitPageModel->setAttribute('idLink', $idLink);
        $waitPageModel->save();

        return true;
    }

    public static function updateFacebookLink($postLink,$post){
        $addLinksModelFacebook = new SuperLinksAddLinkModel();
        $groupLinkModel = new SuperLinksGroupLinkModel();

        $addLinksModelFacebook->setIsNewRecord(false);
        $addLinksModelFacebook->setAttributes($postLink);
        $addLinksModelFacebook->setAttribute('createdAt', DateHelper::agora());

        $keyWord = $addLinksModelFacebook->getAttribute('keyWord');
        $keyWord = strtolower($keyWord);
        $addLinksModelFacebook->setAttribute('keyWord', $keyWord);
        $addLinksModelFacebook->setAttribute('rgpd', 0);

        $addLinksModelFacebook->setAttribute('redirectDelay', $postLink['redirectDelay']);
        $redirectType = $addLinksModelFacebook->getAttribute('redirectType');

        if(isset($postLink['redirectBtn'])) {
            $addLinksModelFacebook->setAttribute('redirectBtn', $postLink['redirectBtn']);
        }

        if($redirectType == 'php' || $redirectType == 'clonador' || $redirectType == 'camuflador'){
            $addLinksModelFacebook->setAttribute('redirectDelay', '0');
        }

        if($_POST[$groupLinkModel->getModelName()]['id']){
            $addLinksModelFacebook->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
        }else{
            $addLinksModelFacebook->setNullToAttribute('idGroup');
        }

        $idAddLinks = $addLinksModelFacebook->save();

        if ($idAddLinks) {
            return self::updateDependencies($addLinksModelFacebook, $post, $redirectType);
        }

        return false;
    }

    public static function updateDependencies($addLinksModel = null, $post = [], $redirectType = ''){
        if(is_null($addLinksModel) || !$post || !$redirectType){
            return false;
        }

        $affiliateUrlModel = new SuperLinksAffiliateLinkModel();
        $monitoringModel = new SuperLinksLinkMonitoringModel();
        $cloakModel = new SuperLinksLinkCloakModel();
        $configSocialModel = new SuperLinksLinkConfigSocialModel();
        $dataWaitPage = new SuperLinksWaitPageModel();
        $clonePageModel = new SuperLinksLinkClonePageModel();
        $apiConvertFaceModel = new SuperLinksLinkApiConvertFaceModel();
        $pgBrancaGringaModel = new SuperLinksPgBrancaGringaModel();

        $idSuperLink = $addLinksModel->getAttribute('id');

        if(isset($_POST[$affiliateUrlModel->getModelName()]['affiliateUrl']) && $_POST[$affiliateUrlModel->getModelName()]['affiliateUrl']) {
            foreach ($_POST[$affiliateUrlModel->getModelName()]['affiliateUrl'] as $value) {
                $updateAffiliateLink = new SuperLinksAffiliateLinkModel();

                if ($redirectType == 'facebook') {
                    $updateAffiliateData = $updateAffiliateLink->getAllDataByParam($idSuperLink, 'idLink');
                    if ($updateAffiliateData) {
                        $updateAffiliateData = array_shift($updateAffiliateData);
                        $updateAffiliateLink->setAttribute('id', $updateAffiliateData->id);
                    }
                    $updateAffiliateLink->setIsNewRecord(false);
                }

                $urlSemEspacos = $value;
                $urlSemEspacos = str_replace(' ', "%20", $urlSemEspacos);
                $updateAffiliateLink->setAttribute('affiliateUrl', $urlSemEspacos);

                $updateAffiliateLink->setAttribute('idLink', $idSuperLink);

                $updateAffiliateLink->updateAffiliateLink();
            }
        }

        if ($addLinksModel->getAttribute('redirectType') != 'php') {

            $monitoringData = $monitoringModel->getAllDataByParam($idSuperLink,'idLink');
            if($monitoringData){
                $monitoringData = array_shift($monitoringData);
                $monitoringModel->setAttribute('id', $monitoringData->id);
                $monitoringModel->setIsNewRecord(false);
            }

            foreach($_POST[$monitoringModel->getModelName()] as $key => $val){
                $_POST[$monitoringModel->getModelName()][$key] = stripslashes($val);
            }

            $monitoringModel->setAttributes($_POST[$monitoringModel->getModelName()]);
            $monitoringModel->setAttribute('idLink', $idSuperLink);

            if (!$_POST[$monitoringModel->getModelName()]['googleMonitoringID']) {
                $monitoringModel->setAttribute('googleMonitoringID', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['trackGoogle']) {
                $monitoringModel->setAttribute('trackGoogle', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['pixelID']) {
                $monitoringModel->setAttribute('pixelID', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['track']) {
                $monitoringModel->setAttribute('track', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['codeHeadPage']) {
                $monitoringModel->setAttribute('codeHeadPage', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['codeBodyPage']) {
                $monitoringModel->setAttribute('codeBodyPage', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['codeFooterPage']) {
                $monitoringModel->setAttribute('codeFooterPage', '');
            }

            if (!$_POST[$monitoringModel->getModelName()]['testEventApiFacebook']) {
                $monitoringModel->setAttribute('testEventApiFacebook', '');
            }

            $monitoringModel->save();

            $isEnabledApiConvert = $post[$monitoringModel->getModelName()]['enableApiFacebook'];
            if($isEnabledApiConvert == 'enabled') {
                $eventoApiFacebook = $apiConvertFaceModel->getAllDataByParam($idSuperLink,'idLink');
                if($eventoApiFacebook) {
                    $eventoApiFacebook = array_shift($eventoApiFacebook);
                    $apiConvertFaceModel->loadDataByID($eventoApiFacebook->id);
                    $apiConvertFaceModel->setAttributes($post[$apiConvertFaceModel->getModelName()]);

                    if (!$_POST[$apiConvertFaceModel->getModelName()]['eventIdApiFacebook']) {
                        $apiConvertFaceModel->setAttribute('eventIdApiFacebook', '');
                    }

                    $apiConvertFaceModel->setIsNewRecord(false);
                    $apiConvertFaceModel->save();
                }else{
                    $apiConvertFaceModel->setAttributes($post[$apiConvertFaceModel->getModelName()]);
                    $apiConvertFaceModel->setAttribute('idLink', $idSuperLink);
                    $apiConvertFaceModel->save();
                }
            }
        }

        if(isset($_POST[$clonePageModel->getModelName()]['pageItem']) && $_POST[$clonePageModel->getModelName()]['pageItem']) {

            $pageItem = $_POST[$clonePageModel->getModelName()]['pageItem'];
            $newItem = $_POST[$clonePageModel->getModelName()]['newItem'];
            $typeItem = $post[$clonePageModel->getModelName()]['typeItem'];

	        $cloneHelperLink = new ClonadorHelper();
            foreach ($pageItem as $key => $value) {
	            $pitem = "";
                if(isset($newItem[$key]) && $newItem[$key]){
	                $pitem = $cloneHelperLink->removeParametrosTrackeamentoLink($value);
					$pitem = stripslashes($pitem);
                    $updateClone = new SuperLinksLinkClonePageModel();
                    $updateClone->setAttribute('pageItem', $pitem);
                    $updateClone->setAttribute('newItem', $newItem[$key]);
                    $updateClone->setAttribute('typeItem', $typeItem[$key]);
                    $updateClone->setAttribute('idLink', $idSuperLink);
                    $updateClone->updateCloneLink();
                }
            }
        }

        if(isset($_POST[$pgBrancaGringaModel->getModelName()]['checkoutProdutor']) && $_POST[$pgBrancaGringaModel->getModelName()]['checkoutProdutor']) {

            $checkoutProdutor = $_POST[$pgBrancaGringaModel->getModelName()]['checkoutProdutor'];
            $linkPaginaVenda = isset($_POST[$pgBrancaGringaModel->getModelName()]['linkPaginaVenda'])? $_POST[$pgBrancaGringaModel->getModelName()]['linkPaginaVenda'] : '';
            $abrirPaginaBranca = $_POST[$pgBrancaGringaModel->getModelName()]['abrirPaginaBranca'];
            $textoTempoRedirecionamento = $_POST[$pgBrancaGringaModel->getModelName()]['textoTempoRedirecionamento'];
            $tempoRedirecionamentoCheckout = $_POST[$pgBrancaGringaModel->getModelName()]['tempoRedirecionamentoCheckout'];

            if($checkoutProdutor) {
                $cloneHelperLink = new ClonadorHelper();
                $checkoutProdutorCorrigido = [];
                foreach ($checkoutProdutor as $key => $value) {
                    $pitem = "";
                    $pitem = $cloneHelperLink->removeParametrosTrackeamentoLink($value);
                    $pitem = stripslashes($pitem);
                    $checkoutProdutorCorrigido[] = $pitem;
                }

                $checkoutProdutorCorrigido = serialize($checkoutProdutorCorrigido);

                $updateClone = new SuperLinksPgBrancaGringaModel();
                $updateClone->setAttribute('checkoutProdutor', $checkoutProdutorCorrigido);
                $updateClone->setAttribute('linkPaginaVenda', $linkPaginaVenda);
                $updateClone->setAttribute('abrirPaginaBranca', $abrirPaginaBranca);
                $updateClone->setAttribute('textoTempoRedirecionamento', $textoTempoRedirecionamento);
                $updateClone->setAttribute('tempoRedirecionamentoCheckout', $tempoRedirecionamentoCheckout);
                $updateClone->setAttribute('idLink', $idSuperLink);
                $updateClone->updateLinkPgBranca();
            }
        }

        $cloakData = $cloakModel->getAllDataByParam($idSuperLink,'idLink');
        if($cloakData){
            $cloakData = array_shift($cloakData);
            $cloakModel->setAttribute('id', $cloakData->id);
        }

        if(isset($_POST[$cloakModel->getModelName()])) {
            $cloakModel->setAttributes($_POST[$cloakModel->getModelName()]);
            $cloakModel->setAttribute('idLink', $idSuperLink);
            $cloakModel->setIsNewRecord(false);

            if (!$_POST[$cloakModel->getModelName()]['connectionRedirectUrl']) {
                $cloakModel->setAttribute('connectionRedirectUrl', '');
            }

            if (!$_POST[$cloakModel->getModelName()]['defaultRedirectUrl']) {
                $cloakModel->setAttribute('defaultRedirectUrl', '');
            }

            $cloakModel->save();
        }
        $configSocialData = $configSocialModel->getAllDataByParam($idSuperLink,'idLink');
        if($configSocialData){
            $configSocialData = array_shift($configSocialData);
            $configSocialModel->setAttribute('id', $configSocialData->id);
        }

        $configSocialModel->setAttributes($_POST[$configSocialModel->getModelName()]);
        $configSocialModel->setAttribute('idLink', $idSuperLink);
        $configSocialModel->setIsNewRecord(false);

        if (!$_POST[$configSocialModel->getModelName()]['textTitle']) {
            $configSocialModel->setAttribute('textTitle', '');
        }

        if (!$_POST[$configSocialModel->getModelName()]['description']) {
            $configSocialModel->setAttribute('description', '');
        }

        if (!$_POST[$configSocialModel->getModelName()]['image']) {
            $configSocialModel->setAttribute('image', '');
        }

        $configSocialModel->save();

        $dataWaitData = $dataWaitPage->getAllDataByParam($idSuperLink,'idLink');
        if($dataWaitData){
            $dataWaitData = array_shift($dataWaitData);
            $dataWaitPage->setAttribute('id', $dataWaitData->id);
        }

        $dataWaitPage->setAttributes($_POST[$dataWaitPage->getModelName()]);
        $dataWaitPage->setAttribute('idLink', $idSuperLink);
        $dataWaitPage->setIsNewRecord(false);

        if (!$_POST[$dataWaitPage->getModelName()]['textLoadPage']) {
            $dataWaitPage->setAttribute('textLoadPage', '');
        }

        $dataWaitPage->save();
    }


    public function getLinksByIDGroup($idGroup = null){
        global $wpdb;
        $tableName = $this->getTableName();
        if(is_null($tableName)){
            return [];
        }

        if(is_null($idGroup)){
            return $wpdb->get_results("SELECT * FROM $tableName where redirectType != 'clonador' and idGroup IS NULL");
        }

        return $wpdb->get_results("SELECT * FROM $tableName where redirectType != 'clonador' and idGroup = '".$idGroup."'");
    }


    public function getLinksByIDGroupAndClonador($idGroup = null){
        global $wpdb;
        $tableName = $this->getTableName();
        if(is_null($tableName)){
            return [];
        }

        if(is_null($idGroup)){
            return $wpdb->get_results("SELECT * FROM $tableName where redirectType = 'clonador' and idGroup IS NULL");
        }

        return $wpdb->get_results("SELECT * FROM $tableName where redirectType = 'clonador' and idGroup = '".$idGroup."'");
    }

    public function getAllDataClonador(){
        $tableName = $this->getTableName();

        if(is_null($tableName)){
            return [];
        }

        global $wpdb;

        return $wpdb->get_results(" SELECT * FROM $tableName where redirectType = 'clonador'ORDER BY id ASC");
    }

    public function getAllDataRedirects(){
        $tableName = $this->getTableName();

        if(is_null($tableName)){
            return [];
        }

        global $wpdb;

        return $wpdb->get_results(" SELECT * FROM $tableName where redirectType != 'clonador'ORDER BY id ASC");
    }

    public function getPagesSuperLinksOptions(){

        $allPosts[] = ['selected' => true, 'text' => '-- Não associar -- ', 'val' => ''];
        $allPosts[] = ['selected' => false, 'text' => 'Página inicial', 'val' => get_bloginfo('wpurl')];

        $pages = $this->findWpSuperLinksPages('page');
        foreach($pages as $page){
            $queryUrl = parse_url($page['guid'], PHP_URL_QUERY);
            if(!$queryUrl){
                $queryUrl = "";
            }
            $splitQueryUrl = explode('=',$queryUrl);
            $idPage = 0;

            $permalink = $page['guid'];

            if($splitQueryUrl[0] == 'page_id'){
                $idPage = $splitQueryUrl[1];
            }

            if($idPage){
                $permalink = get_permalink( $idPage );
            }

            $allPosts[] = ['selected' => false, 'text' => $page['post_title'], 'val' => $permalink];
        }

        return $allPosts;
    }

    private function findWpSuperLinksPages($type = 'page')
    {
        $posts = SuperLinksPosts::query()
            ->select('*')
            ->where('post_type', 'in', [array_values(get_post_types(['public' => true]))])
            ->where('post_type', "$type")
            ->where('post_status', 'publish')
            ->order_by('post_modified', 'desc')
            ->get_results();
        return $posts;
    }

    public function getPopupsSuperLinksOptions()
    {
        $allPosts[] = ['selected' => true, 'text' => '-- Não exibir popup -- ', 'val' => ''];

        $posts = get_posts(['post_type' => array('superlinks'), 'numberposts' => '-1', 'posts_per_page' => '-1']);
        foreach($posts as $item){
            $id = strval($item->ID);
            $allPosts[] = ['selected' => false, 'text' =>  $item->post_title, 'val' => $id];
        }

        return $allPosts;
    }

    public function getPopupsShowOptions()
    {
        $allPosts[] = ['selected' => true, 'text' => 'Ao abrir a página', 'val' => 'load'];
        $allPosts[] = ['selected' => false, 'text' => 'Ao sair da página (Exit intention)', 'val' => 'exit'];
        $allPosts[] = ['selected' => false, 'text' => 'Ao abrir e sair da página', 'val' => 'loadExit'];

        return $allPosts;
    }

    public function getPopupsAnimationOptions()
    {

        $animationsOptions = ['bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'headShake', 'swing', 'tada', 'wobble', 'jello', 'bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp', 'bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp', 'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig', 'fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig', 'flipInX', 'flipInY', 'flipOutX', 'flipOutY', 'lightSpeedIn', 'lightSpeedOut', 'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight', 'hinge', 'jackInTheBox', 'rollIn', 'rollOut', 'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp', 'zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp', 'slideInDown', 'slideInLeft', 'slideInRight', 'slideInUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight', 'slideOutUp', 'heartBeat'];
        $allOptions[] = ['selected' => true, 'text' => 'Não utilizar animação', 'val' => 'none'];
        foreach($animationsOptions as $option){
            $allOptions[] = ['selected' => false, 'text' =>  $option, 'val' => $option];
        }
        return $allOptions;
    }

	public function getUrlOriginalPgVendasProdutorSplClone($url){
		$urlEnviar = serialize($url);
		$urlApi = SUPER_LINKS_WEB_API . '/ApiSuperLinks/getUrlOriginalPgVendasProdutor?token='.$this->tokenClient.'&url='.$urlEnviar;

		$resultClone = wp_remote_get($urlApi, [
			'timeout'    => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		$conteudo = '';

		if (is_array($resultClone) && !is_wp_error($resultClone)) {
			$conteudo = $resultClone['body'];
		}

		if($conteudo){
			$conteudo = json_decode($conteudo);
		}

		if(isset($conteudo->possivelClonar) && !$conteudo->possivelClonar){
			return false;
		}

		if(isset($conteudo->urlProdutor)){
			return $conteudo->urlProdutor;
		}

		return $url;
	}

	public function getUrlOriginalPgVendasProdutorSpl($url){
		$urlEnviar = serialize($url);
		$urlApi = SUPER_LINKS_WEB_API . '/ApiSuperLinks/getUrlOriginalPgVendasProdutor?token='.$this->tokenClient.'&url='.$urlEnviar;

		$resultClone = wp_remote_get($urlApi, [
			'timeout'    => 60,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36',
		]);

		$conteudo = '';

		if (is_array($resultClone) && !is_wp_error($resultClone)) {
			$conteudo = $resultClone['body'];
		}

		if($conteudo){
			$conteudo = json_decode($conteudo);
		}

		if(isset($conteudo->urlProdutor)){
			return $conteudo->urlProdutor;
		}

		return $url;
	}

	public function removeReferenciaAfiliadoUrlSpl($url){
		$urlNova = explode('?',$url);
		return $urlNova[0];
	}

	public function geraKeyWordAleatorio($length = 6){
		// Caracteres permitidos no slug
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';

		// Obtenha o número total de caracteres
		$characters_length = strlen($characters);

		// Inicialize o slug
		$random_slug = '';

		// Gere o slug aleatoriamente
		for ($i = 0; $i < $length; $i++) {
			$random_character = $characters[rand(0, $characters_length - 1)];
			$random_slug .= $random_character;
		}

		// Retorne o slug gerado
		return $random_slug;
	}

	public function saveOpniaoCliente($opniao, $id){
		if(($opniao == 'sim' || $opniao == 'nao' || $opniao == 'naoGostei') && $id){
			global $wpdb;

			$result = $wpdb->update(
				$wpdb->prefix . SUPER_LINKS_PLUGIN_SLUG . "_Link",
				['opniaoClientePgClonada' => $opniao],
				['id' => $id]
			);

			return $result;
		}
	}
}