<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class SuperLinksAutomaticLinkController extends SuperLinksFramework
{

    protected $automaticLinksModel;
    protected $groupLinkModel;
    private $toast;
    private $timeToExpire;
    private $urlView;

    public function __construct($model = null, $hooks = [], $filters = [])
    {
        $this->toast = TranslateHelper::getTranslate('O link foi salvo com sucesso!');
        $this->timeToExpire = time() + 60;
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_automatic_list_view';

        $this->setScenario('super_links_automatic_link');

        $this->setModel($model);
        $this->automaticLinksModel = $this->loadModel();

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

                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar categorias de links inteligentes existentes');

                $this->groupLinkModel = new SuperLinksAutomaticGroupModel();
                $allGroups['groups'] = $this->groupLinkModel
                    ->getAllData();

                $allGroups['existCategory'] = $this->existCategory($allGroups['groups']);

                $this->pageData = array_merge($allGroups, $this->pageData);

                $allLinks['links'] = $this->automaticLinksModel
                    ->getAllData();

                $allLinks['existLinkWithoutCategory'] = $this->existLinkwithoutCategory($allLinks['links']);
                $allLinks['existLinks'] = $this->existLinks($allLinks['links']);

                $this->pageData = array_merge($allLinks, $this->pageData);

                $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/listView.php');
            }
        }
    }

    private function viewLinksByGroup($idGroup = null){
        $groupName = "Sem categoria";

        if(!is_null($idGroup)){
            $this->groupLinkModel = new SuperLinksAutomaticGroupModel();
            $this->groupLinkModel->loadDataByID($idGroup);
            $groupName = $this->groupLinkModel->getAttribute('groupName');
        }

        $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar links existentes da Categoria: ') . "<strong>" . $groupName . "</strong>";

        $automaticLinksModel = new SuperLinksAutomaticLinksModel();
        $this->automaticLinksModel = $automaticLinksModel;
        $linksByGroup = $automaticLinksModel->getLinksByIDGroup($idGroup);

        $allLinks['links'] = $linksByGroup;
        $allLinks['existLinks'] = $this->existLinks($allLinks['links']);

        $this->pageData = array_merge($allLinks, $this->pageData);

        $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/listViewLinks.php');
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

    public function viewLink()
    {
        if($this->isPluginActive()) {
            $id = $_GET['id'];
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $automaticLinksModel = new SuperLinksAutomaticLinksModel();
                $automaticLinksModel->loadDataByID($id);

                if (!empty($automaticLinksModel->getAttributes())) {
                    $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Dados do link');

                    $this->pageData = array_merge($automaticLinksModel->getAttributes(), $this->pageData);

                    $idAutomaticLink = $automaticLinksModel->getAttribute('id');

                    $metricsModel = new SuperLinksAutomaticMetricsModel();
                    $metricsData = $metricsModel->getAllDataByParam($idAutomaticLink, 'idAutomaticLink');

                    $this->pageData = array_merge(['metrics' => $metricsData], $this->pageData);
                }
            }
            $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/viewLink.php');
        }
    }

    public function create()
    {
        $savedLink = false;
        if($this->isPluginActive()) {
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Criar novo link inteligente');

            $automaticLinksModel = $this->automaticLinksModel;

            $automaticLinksModel->setAttribute('num','1');

            $this->groupLinkModel = new SuperLinksAutomaticGroupModel();

            if (isset($_POST['scenario'])) {
                $groupLinkModel = $this->groupLinkModel;
                $automaticLinksModel->setAttributes($_POST[$automaticLinksModel->getModelName()]);

                $automaticLinksModel->setAttribute('partly_match', $_POST[$automaticLinksModel->getModelName()]['partly_match']);

                if(!$_POST[$automaticLinksModel->getModelName()]['num']) {
                    $automaticLinksModel->setAttribute('num', -1);
                }

                $automaticLinksModel->setAttribute('page_id', '-1');

                if($_POST[$groupLinkModel->getModelName()]['id']){
                    $automaticLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                }else{
                    $automaticLinksModel->setNullToAttribute('idGroup');
                }

                $idAddLinks = $automaticLinksModel->save();


                if ($idAddLinks) {
                    $savedLink = true;
                }
            }

            if($savedLink){
                $toast = $this->toast;
                $timeToExpire = $this->timeToExpire;
                $urlView = $this->urlView;
                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }

            $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/index.php');
        }
    }

    public function update()
    {
        $savedLink = false;
        if($this->isPluginActive()) {
            $id = $_GET['id'];
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');
            $this->groupLinkModel = new SuperLinksAutomaticGroupModel();

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar link inteligente');

                $this->automaticLinksModel->loadDataByID($id);
                $partly_match = $this->automaticLinksModel->getAttribute('partly_match');

                if (!$partly_match) {
                    $this->automaticLinksModel->setAttribute('partly_match', '0');
                }

                if($this->automaticLinksModel->getAttribute('num') < 0) {
                    $this->automaticLinksModel->setAttribute('num', 0);
                }

                $active = $this->automaticLinksModel->getAttribute('active');

                if (!$active) {
                    $this->automaticLinksModel->setAttribute('active', '0');
                }

                $this->automaticLinksModel->setIsNewRecord(false);

                $idGroup = $this->automaticLinksModel->getAttribute('idGroup');

                $this->groupLinkModel->setAttribute('id', $idGroup);

                if (isset($_POST['scenario'])) {
                    $automaticLinksModel = $this->automaticLinksModel;
                    $groupLinkModel = $this->groupLinkModel;

                    $automaticLinksModel->setAttributes($_POST[$automaticLinksModel->getModelName()]);
                    $automaticLinksModel->setAttribute('page_id', '-1');
                    $automaticLinksModel->setAttribute('partly_match', $_POST[$automaticLinksModel->getModelName()]['partly_match']);
                    $automaticLinksModel->setAttribute('active', $_POST[$automaticLinksModel->getModelName()]['active']);
                    $automaticLinksModel->setExceptRules(['required']);

                    if(!$_POST[$automaticLinksModel->getModelName()]['num']) {
                        $automaticLinksModel->setAttribute('num', -1);
                    }

                    if($_POST[$groupLinkModel->getModelName()]['id']){
                        $automaticLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                    }else{
                        $automaticLinksModel->setNullToAttribute('idGroup');
                    }

                    $isSavedLink = $automaticLinksModel->save();

                    if ($isSavedLink) {
                        $savedLink = true;
                    }
                }
            }

            if($savedLink){
                $toast = TranslateHelper::getTranslate('O link foi atualizado com sucesso!');
                $timeToExpire = $this->timeToExpire;
                $urlView = $this->urlView;
                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }
            $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/update.php');
        }
    }

    public function cloneLink()
    {
        $savedLink = false;
        if($this->isPluginActive()) {
            $id = $_GET['id'];
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');
            $this->groupLinkModel = new SuperLinksAutomaticGroupModel();
            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Duplicar este link');
                $this->automaticLinksModel->loadDataByID($id);
                $partly_match = $this->automaticLinksModel->getAttribute('partly_match');

                if (!$partly_match) {
                    $this->automaticLinksModel->setAttribute('partly_match', '0');
                }

                if($this->automaticLinksModel->getAttribute('num') < 0) {
                    $this->automaticLinksModel->setAttribute('num', 0);
                }

                $active = $this->automaticLinksModel->getAttribute('active');

                if (!$active) {
                    $this->automaticLinksModel->setAttribute('active', '0');
                }

                $this->automaticLinksModel->setIsNewRecord(false);

                $idGroup = $this->automaticLinksModel->getAttribute('idGroup');

                $this->groupLinkModel->setAttribute('id', $idGroup);

                if (isset($_POST['scenario'])) {
                    $automaticLinksModel = new SuperLinksAutomaticLinksModel();
                    $groupLinkModel = new SuperLinksAutomaticGroupModel();

                    $automaticLinksModel->setAttributes($_POST[$automaticLinksModel->getModelName()]);

                    $automaticLinksModel->setAttribute('partly_match', $_POST[$automaticLinksModel->getModelName()]['partly_match']);

                    $automaticLinksModel->setAttribute('page_id', '-1');

                    if(!$_POST[$automaticLinksModel->getModelName()]['num']) {
                        $automaticLinksModel->setAttribute('num', -1);
                    }

                    if($_POST[$groupLinkModel->getModelName()]['id']){
                        $automaticLinksModel->setAttribute('idGroup', $_POST[$groupLinkModel->getModelName()]['id']);
                    }else{
                        $automaticLinksModel->setNullToAttribute('idGroup');
                    }

                    $idAddLinks = $automaticLinksModel->save();

                    if ($idAddLinks) {
                        $savedLink = true;
                    }
                }
            }

            if($savedLink){
                $toast = $this->toast;
                $timeToExpire = $this->timeToExpire;
                $urlView = $this->urlView;
                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }

            $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/clone.php');
        }
    }

    public function editGroup()
    {
        if($this->isPluginActive()) {
            $id = $_GET['id'];
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar a Categoria');
                $this->groupLinkModel = new SuperLinksAutomaticGroupModel();
                $this->groupLinkModel->setIsNewRecord(false);

                $this->groupLinkModel->loadDataByID($id);

                if (isset($_POST['scenario'])) {
                    $groupLinkModel = $this->groupLinkModel;
                    $groupLinkModel->setAttributes($_POST[$groupLinkModel->getModelName()]);
                    $idGroupModel = $groupLinkModel->save();

                    if ($idGroupModel) {
                        $toast = TranslateHelper::getTranslate('A categoria foi atualizada com sucesso!');
                        $timeToExpire = $this->timeToExpire;
                        $urlView = $this->urlView;
                        echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '" . $urlView . "'
                        </script>";
                        exit();
                    }else{
                        $toast = TranslateHelper::getTranslate('Não houve alteração na categoria!');
                        $timeToExpire = $this->timeToExpire;
                        $urlView = $this->urlView;
                        echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '" . $urlView . "'
                        </script>";
                        exit();
                    }
                }
            }

            $this->render(SUPER_LINKS_VIEWS_PATH . '/automaticLinks/editGroup.php');
        }
    }

    public function isPluginActive(){
        // Versão multisite sempre ativa
        return true;
    }
}