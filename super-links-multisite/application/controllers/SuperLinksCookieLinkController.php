<?php if (!defined('ABSPATH')) {
    die('You are not authorized to access this');
}

class SuperLinksCookieLinkController extends SuperLinksFramework
{

    protected $cookiePageModel;
    protected $groupCookieModel;
    private $toast;
    private $timeToExpire;
    private $urlView;

    public function __construct($model = null, $hooks = [], $filters = [])
    {
        $this->toast = TranslateHelper::getTranslate('O cookie foi salvo com sucesso!');
        $this->timeToExpire = time() + 60;
        $this->urlView = SUPER_LINKS_TEMPLATE_URL . '/wp-admin/admin.php?page=super_links_cookiePost_view';

        $this->setScenario('super_links_cookiePost_view');

        $this->setModel($model);
        $this->cookiePageModel = $this->loadModel();

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

    public function viewLinksByGroup($idGroup = null)
    {
        if($this->isPluginActive()) {
            $groupName = "Sem categoria";

            if(!is_null($idGroup)){
                $this->groupCookieModel = new SuperLinksCookiePageGroupModel();
                $this->groupCookieModel->loadDataByID($idGroup);
                $groupName = $this->groupCookieModel->getAttribute('groupName');
            }

            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar cookies e popups de saída da página da Categoria: ') . "<strong>" . $groupName . "</strong>";

            $cookiePageModel = new SuperLinksLinkCookiePageModel();
            $this->cookiePageModel = $cookiePageModel;
            $linksByGroup = $cookiePageModel->getLinksByIDGroup($idGroup);

            $allLinks['cookies'] = $linksByGroup;
            $allLinks['existLinks'] = $this->existLinks($allLinks['cookies']);

            $this->pageData = array_merge($allLinks, $this->pageData);

            $this->render(SUPER_LINKS_VIEWS_PATH . '/cookies/listViewCookies.php');
        }
    }

    public function viewCookies()
    {
        if($this->isPluginActive()) {
            if(isset($_GET['idCategory']) && $_GET['idCategory']) {
                $idGroup = $_GET['idCategory'];

                if($idGroup == 'none'){
                    $idGroup = null;
                }

                $this->viewLinksByGroup($idGroup);
            }else {

                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Visualizar cookies e popups de saída da página');

                $this->groupCookieModel = new SuperLinksCookiePageGroupModel();
                $allGroups['groups'] = $this->groupCookieModel
                    ->getAllData();

                $allGroups['existCategory'] = $this->existCategory($allGroups['groups']);

                $this->pageData = array_merge($allGroups, $this->pageData);

                $allCookies['cookies'] = $this->cookiePageModel
                    ->getAllData();

                $allCookies['existLinkWithoutCategory'] = $this->existLinkwithoutCategory($allCookies['cookies']);
                $allCookies['existLinks'] = $this->existLinks($allCookies['cookies']);

                $this->pageData = array_merge($allCookies, $this->pageData);


                $this->render(SUPER_LINKS_VIEWS_PATH . '/cookies/listView.php');
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

    public function addCookie()
    {
        $savedLink = false;
        if($this->isPluginActive()) {
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Criar novo cookie de afiliado');

            $this->cookiePageModel->setAttribute('timeCookie','0');

            $this->groupCookieModel = new SuperLinksCookiePageGroupModel();

            if (isset($_POST['scenario'])) {
                $groupCookieModel = $this->groupCookieModel;
                $this->cookiePageModel->setAttributes($_POST[$this->cookiePageModel->getModelName()]);

                if(!$this->cookiePageModel->getAttribute('timeCookie')){
                    $this->cookiePageModel->setAttribute('timeCookie', '0');
                }

                if($_POST[$this->cookiePageModel->getModelName()]['timeCookie'] < 0) {
                    $this->cookiePageModel->setAttribute('timeCookie', '0');
                }

                $posts = $this->cookiePageModel->getAttribute('idPost');
                if($posts) {
                    $posts = implode(",", $posts);
                    $this->cookiePageModel->setAttribute('idPost', $posts);
                }

                $paginas = $this->cookiePageModel->getAttribute('idPage');
                if($paginas) {
                    $paginas = implode(",", $paginas);
                    $this->cookiePageModel->setAttribute('idPage', $paginas);
                }

                $splinks = $this->cookiePageModel->getAttribute('linkSuperLinks');
                if($splinks) {
                    $splinks = implode(",", $splinks);
                    $this->cookiePageModel->setAttribute('linkSuperLinks', $splinks);
                }

                $activeWhen = $this->cookiePageModel->getAttribute('activeWhen');
                if($activeWhen) {
                    $activeWhen = implode(",", $activeWhen);
                    $this->cookiePageModel->setAttribute('activeWhen', $activeWhen);
                }

                if($_POST[$groupCookieModel->getModelName()]['id']){
                    $this->cookiePageModel->setAttribute('idGroup', $_POST[$groupCookieModel->getModelName()]['id']);
                }else{
                    $this->cookiePageModel->setNullToAttribute('idGroup');
                }

                $existConfigPost = $this->cookiePageModel->existConfigForThisPost();
                $existConfigPage = $this->cookiePageModel->existConfigForThisPage();
                $existConfigLink = $this->cookiePageModel->existConfigForThisLink();

                if(!$existConfigPost && !$existConfigPage && !$existConfigLink) {
                    $idAddLinks = $this->cookiePageModel->save();

                    if ($idAddLinks) {
                        $savedLink = true;
                    }
                }else{
                    if($existConfigPost) {
                        $toast = 'Já existe uma configuração para este post criada anteriormente';
                        $timeToExpire = $this->timeToExpire;
                        echo "<script>
                              document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                              document.cookie = \"typeToastSPL=warning; expires=$timeToExpire; path=/\";
                            </script>";
                    }
                    if($existConfigPage) {
                        $toast = 'Já existe uma configuração para esta página criada anteriormente';
                        $timeToExpire = $this->timeToExpire;
                        echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.cookie = \"typeToastSPL=warning; expires=$timeToExpire; path=/\";
                        </script>";
                    }
                    if($existConfigLink) {
                        $toast = 'Já existe uma configuração para este link do SuperLinks criado anteriormente';
                        $timeToExpire = $this->timeToExpire;
                        echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.cookie = \"typeToastSPL=warning; expires=$timeToExpire; path=/\";
                        </script>";
                    }
                }

                $paginas = $this->cookiePageModel->getAttribute('idPost');
                $paginas = explode(",",$paginas);
                $this->cookiePageModel->setAttribute('idPost',$paginas);

                $splinks = $this->cookiePageModel->getAttribute('linkSuperLinks');
                $splinks = explode(",",$splinks);
                $this->cookiePageModel->setAttribute('linkSuperLinks',$splinks);

                $activeWhen = $this->cookiePageModel->getAttribute('activeWhen');
                $activeWhen = explode(",",$activeWhen);
                $this->cookiePageModel->setAttribute('activeWhen',$activeWhen);
            }

            if($savedLink){
                $toast = $this->toast;
                $timeToExpire = $this->timeToExpire;

                $groups = $this->groupCookieModel
                    ->getAllData();

                if($this->cookiePageModel->getAttribute('idGroup')) {
                    $urlView = $this->urlView . '&idCategory=' . $this->cookiePageModel->getAttribute('idGroup');
                }else if($this->existCategory($groups) && !$this->cookiePageModel->getAttribute('idGroup')){
                    $urlView = $this->urlView . '&idCategory=none';
                }else{
                    $urlView = $this->urlView;
                }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }

            $this->render(SUPER_LINKS_VIEWS_PATH . '/cookies/index.php');
        }
    }

    public function editCookie()
    {
        $savedLink = false;
        if($this->isPluginActive()) {
            $id = $_GET['id'];
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar cookie de afiliado');
                $this->groupCookieModel = new SuperLinksCookiePageGroupModel();

                $this->cookiePageModel->loadDataByID($id);
                $this->cookiePageModel->setIsNewRecord(false);

                $postsPage = $this->cookiePageModel->getAttribute('idPost');
                $allPages = false;
                if($postsPage) {
                    $postsPage = explode(",", $postsPage);
                    $key = array_search('all',$postsPage);
                    if($key !== false){
                        $postsPage[$key] = "allPosts";
                        $allPages = true;
                        $postsPage = array_unique($postsPage);
                    }
                    $this->cookiePageModel->setAttribute('idPost', $postsPage);
                }else{
                    $this->cookiePageModel->setAttribute('idPost', []);
                }

                $paginas = $this->cookiePageModel->getAttribute('idPage');
                if($paginas) {
                    $paginas = explode(",", $paginas);
                    $this->cookiePageModel->setAttribute('idPage', $paginas);
                }else{
                    if($allPages) {
                        $this->cookiePageModel->setAttribute('idPage', 'allPages');
                    }else{
                        $this->cookiePageModel->setAttribute('idPage',[]);
                    }
                }


                $splinks = $this->cookiePageModel->getAttribute('linkSuperLinks');
                $splinks = explode(",",$splinks);
                $this->cookiePageModel->setAttribute('linkSuperLinks',$splinks);

                $activeWhen = $this->cookiePageModel->getAttribute('activeWhen');
                $activeWhen = explode(",",$activeWhen);
                $this->cookiePageModel->setAttribute('activeWhen',$activeWhen);

                if(!$this->cookiePageModel->getAttribute('timeCookie')){
                    $this->cookiePageModel->setAttribute('timeCookie', '0');
                }

                $idGroup = $this->cookiePageModel->getAttribute('idGroup');

                $this->groupCookieModel->setAttribute('id', $idGroup);
                if (isset($_POST['scenario'])) {
                    $cookiePageModel = $this->cookiePageModel;
                    $groupCookieModel = $this->groupCookieModel;

                    $cookiePageModel->setAttributes($_POST[$cookiePageModel->getModelName()]);

                    $cookiePageModel->setAttribute('qtdAcessos', $_POST[$cookiePageModel->getModelName()]['qtdAcessos']);


                    if(!isset($_POST[$cookiePageModel->getModelName()]['timeCookie']) || !$_POST[$cookiePageModel->getModelName()]['timeCookie']) {
                        $cookiePageModel->setAttribute('timeCookie', '0');
                    }

                    if($_POST[$cookiePageModel->getModelName()]['timeCookie'] < 0) {
                        $cookiePageModel->setAttribute('timeCookie', '0');
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['idPost']) || !$_POST[$cookiePageModel->getModelName()]['idPost']) {
                        $cookiePageModel->setAttribute('idPost', '');
                    }else {
                        $posts = $cookiePageModel->getAttribute('idPost');
                        $posts = implode(",", $posts);
                        $cookiePageModel->setAttribute('idPost', $posts);
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['idPage']) || !$_POST[$cookiePageModel->getModelName()]['idPage']) {
                        $cookiePageModel->setAttribute('idPage', '');
                    }else {
                        $paginas = $cookiePageModel->getAttribute('idPage');
                        $paginas = implode(",", $paginas);
                        $cookiePageModel->setAttribute('idPage', $paginas);
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['linkSuperLinks']) || !$_POST[$cookiePageModel->getModelName()]['linkSuperLinks']) {
                        $cookiePageModel->setAttribute('linkSuperLinks', '');
                    }else {
                        $splinks = $cookiePageModel->getAttribute('linkSuperLinks');
                        $splinks = implode(",", $splinks);
                        $cookiePageModel->setAttribute('linkSuperLinks', $splinks);
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['activeWhen']) || !$_POST[$cookiePageModel->getModelName()]['activeWhen']) {
                        $cookiePageModel->setAttribute('activeWhen', '');
                    }else {
                        $activeWhen = $cookiePageModel->getAttribute('activeWhen');
                        $activeWhen = implode(",", $activeWhen);
                        $cookiePageModel->setAttribute('activeWhen', $activeWhen);
                    }

                    if($_POST[$groupCookieModel->getModelName()]['id']){
                        $cookiePageModel->setAttribute('idGroup', $_POST[$groupCookieModel->getModelName()]['id']);
                    }else{
                        $cookiePageModel->setNullToAttribute('idGroup');
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['urlCookie']) || !$_POST[$cookiePageModel->getModelName()]['urlCookie']) {
                        $cookiePageModel->setAttribute('urlCookie', '');
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['urlCamuflada']) || !$_POST[$cookiePageModel->getModelName()]['urlCamuflada']) {
                        $cookiePageModel->setAttribute('urlCamuflada', '');
                    }

                    if(!isset($_POST[$cookiePageModel->getModelName()]['oldIdPost']) || !$_POST[$cookiePageModel->getModelName()]['oldIdPost']) {
                        $cookiePageModel->setAttribute('oldIdPost', '');
                    }

                    $existConfigPost = $cookiePageModel->existConfigForThisPost();
                    $existConfigPage = $cookiePageModel->existConfigForThisPage();
                    $existConfigLink = $cookiePageModel->existConfigForThisLink();

                    $cookiePageModel->removeAttribute('oldLinkSuperLinks');
                    $cookiePageModel->removeAttribute('oldIdPost');

                    if(!$existConfigPost && !$existConfigPage && !$existConfigLink) {
                        $isSavedLink = $cookiePageModel->save();

                        if ($isSavedLink) {
                            $savedLink = true;
                        }
                    }else{
                        if($existConfigPost) {
                            $toast = 'Já existe uma configuração para este post criada anteriormente';
                            $timeToExpire = $this->timeToExpire;
                            echo "<script>
                              document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                              document.cookie = \"typeToastSPL=warning; expires=$timeToExpire; path=/\";
                            </script>";
                        }
                        if($existConfigPage) {
                            $toast = 'Já existe uma configuração para esta página criada anteriormente';
                            $timeToExpire = $this->timeToExpire;
                            echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.cookie = \"typeToastSPL=warning; expires=$timeToExpire; path=/\";
                        </script>";
                        }
                        if($existConfigLink) {
                            $toast = 'Já existe uma configuração para este link do SuperLinks criado anteriormente';
                            $timeToExpire = $this->timeToExpire;
                            echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.cookie = \"typeToastSPL=warning; expires=$timeToExpire; path=/\";
                        </script>";
                        }
                    }

                    $posts = $this->cookiePageModel->getAttribute('idPost');
                    $posts = explode(",",$posts);
                    $this->cookiePageModel->setAttribute('idPost',$posts);

                    $paginas = $this->cookiePageModel->getAttribute('idPage');
                    $paginas = explode(",",$paginas);
                    $this->cookiePageModel->setAttribute('idPage',$paginas);

                    $splinks = $this->cookiePageModel->getAttribute('linkSuperLinks');
                    $splinks = explode(",",$splinks);
                    $this->cookiePageModel->setAttribute('linkSuperLinks',$splinks);

                    $activeWhen = $this->cookiePageModel->getAttribute('activeWhen');
                    $activeWhen = explode(",",$activeWhen);
                    $this->cookiePageModel->setAttribute('activeWhen',$activeWhen);
                }
            }

            if($savedLink){
                $toast = TranslateHelper::getTranslate('O cookie foi atualizado com sucesso!');
                $timeToExpire = $this->timeToExpire;
                $groups = $this->groupCookieModel
                    ->getAllData();

                if($cookiePageModel->getAttribute('idGroup')) {
                    $urlView = $this->urlView . '&idCategory=' . $cookiePageModel->getAttribute('idGroup');
                }else if($this->existCategory($groups) && !$cookiePageModel->getAttribute('idGroup')){
                    $urlView = $this->urlView . '&idCategory=none';
                }else{
                    $urlView = $this->urlView;
                }

                echo "<script>
                          document.cookie = \"toastSPL=$toast; expires=$timeToExpire; path=/\";
                          document.location = '".$urlView."'
                      </script>";
                exit();
            }
            $this->render(SUPER_LINKS_VIEWS_PATH . '/cookies/update.php');
        }
    }

    public function isPluginActive(){
        // Versão multisite sempre ativa
        return true;
    }

    private function activateCookie($keywordSuperLinks = ''){
        $superLinksModel = new SuperLinksModel();

        if(!$superLinksModel->isPluginActive()){
    private function activateCookie($keywordSuperLinks = ""){
        $superLinksModel = new SuperLinksModel();
        
        // Verificação de ativação removida para versão multisite
        // if(!$superLinksModel->isPluginActive()){
        //     return false;
        // }
        $dataLinks = $cookiePageModel->getAllDataByParam('enabled','statusCookie');

        $currentePageID = get_the_ID();
        $pageOrPost = get_post_type();

        if($currentePageID && $pageOrPost) {
            $isPost = ($pageOrPost == 'post') ? true : false;
            $isPage = ($pageOrPost == 'page') ? true : false;
            $showIn = [];
            foreach ($dataLinks as $dataLink) {
                $posts = $dataLink->idPost;
                $pages = $dataLink->idPage;

                $postsPages = '';
                if($posts && $pages) {
                    $postsPages = $posts . "," . $dataLink->idPage;
                }elseif($posts && !$pages){
                    $postsPages = $posts;
                }elseif(!$posts && $pages){
                    $postsPages = $pages;
                }

                $showIn[$dataLink->id] = explode(',', $postsPages);
            }

            foreach ($showIn as $keyShow => $show) {
                if (in_array($currentePageID, $show)) {
                    return $keyShow;
                }
            }

            foreach ($showIn as $keyShow => $show) {
                if (($isPost && in_array('allPosts', $show)) || ($isPage && in_array('allPages', $show))) {
                    return $keyShow;
                }
            }

            foreach ($showIn as $keyShow => $show) {
                if (in_array('all', $show)) {
                    return $keyShow;
                }
            }

        }

        if(!$currentePageID || !$pageOrPost) {
            if(empty($keywordSuperLinks)){
                return false;
            }

            $addLinkModel = new SuperLinksAddLinkModel();
            $keywordSuperLinks = strtolower($keywordSuperLinks);
            $superlink = $addLinkModel->getLinkByKeyword($keywordSuperLinks);
            $superlinkBarra = $addLinkModel->getLinkByKeyword($keywordSuperLinks . "/");

            if (!$superlink && !$superlinkBarra) {
                return false;
            }

            if ($superlink) {
                $link = $superlink;
            } else {
                $link = $superlinkBarra;
            }

            $link = array_shift($link);
            $idLink = $link->id;

            $showIn = [];
            foreach ($dataLinks as $dataLink) {
                $linkSuperLinks = $dataLink->linkSuperLinks;
                $showIn[$dataLink->id] = explode(',', $linkSuperLinks);
            }

            foreach ($showIn as $keyShow => $show) {
                if (in_array($idLink, $show)) {
                    return $keyShow;
                }
            }

            foreach ($showIn as $keyShow => $show) {
                if (in_array('all', $show)) {
                    return $keyShow;
                }
            }
        }
        return false;
    }

    public function execCookieSuperLinks(){

        $url = $this->getCurrentUrl();

        $keywordSuperLinks = diferenceUrlSuperLinks($url);

        $idActivateCookie = $this->activateCookie($keywordSuperLinks);

        if(!$idActivateCookie) {
            return;
        }

        $cookie = new SuperLinksLinkCookiePageModel();
        $cookie->loadDataByID($idActivateCookie);
        $timeCookie = $cookie->getAttribute('timeCookie');
        $urlCookie = $cookie->getAttribute('urlCookie');

        if($timeCookie == 0 ){
            echo '<iframe src="'.$urlCookie.'"  style="width:0;height:0;border:0"></iframe>';
        }else{
            $timeCookie = $timeCookie * 1000;
            echo '
                    <span id="activateCookieSuperLinks"></span>
                    <script>
                        jQuery(document).ready(function() {
                            setTimeout( function(){ 
                                  let boxActivateCookie = `<iframe src="' . $urlCookie . '" style="width:0;height:0;border:0;"></iframe>`
                                  jQuery("#activateCookieSuperLinks").append(boxActivateCookie)
                              }, ' . $timeCookie . '); 
                        })
                    </script>	
            ';
        }

        $urlCamuflada = $cookie->getAttribute('urlCamuflada');
        $redirect = $cookie->getAttribute('redirect');
        $qtdAcessos = $cookie->getAttribute('qtdAcessos');
        $activeWhen = $cookie->getAttribute('activeWhen');

        if(!$qtdAcessos){
            $qtdAcessos = 0;
        }
        if(!$urlCamuflada){
            return;
        }

        echo "<script>

                    function getCookieWpSpl(cname) {
                      var name = cname + \"=\";
                      var decodedCookie = decodeURIComponent(document.cookie);
                      var ca = decodedCookie.split(';');
                      for(var i = 0; i <ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                          c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                          return c.substring(name.length, c.length);
                        }
                      }
                      return \"\";
                    }
                    
                    function setCookieWpSpl(cname, cvalue, exdays) {
                      var d = new Date();
                      d.setTime(d.getTime() + (exdays*24*60*60*1000));
                      var expires = \"expires=\"+ d.toUTCString();
                      document.cookie = cname + \"=\" + cvalue + \";\" + expires + \";path=/\";
                    }

                    let showUrlCamufladaSPL = 0
                    
                    let qtdAcessos = " . $qtdAcessos . "
                    qtdAcessos = parseInt(qtdAcessos)
              </script>";

        if($qtdAcessos != 0) {
            echo "<script>
                    showUrlCamufladaSPL = getCookieWpSpl('showUrlCamufladaSPL')
                    if(showUrlCamufladaSPL != 1){
                        setCookieWpSpl('showUrlCamufladaSPL', '1', qtdAcessos)
                    }
                  </script>";
        }

        $activeWhen = explode(',', $activeWhen);

        if(in_array('exitPage',$activeWhen)){
            if($redirect != 'enabled') {
                echo "
                    <script>
                            if(qtdAcessos == 0 || showUrlCamufladaSPL != 1){
                                   let mouseleaveBox = getCookieWpSpl('showWpMouseLeave')
                                   setCookieWpSpl('showWpMouseLeave', 1)
                                   document.documentElement.addEventListener('mouseleave', function(e){
                                          mouseleaveBox = getCookieWpSpl('showWpMouseLeave')
                                          if(mouseleaveBox == 1){
                                                if (e.clientY > 20) { return; }
                                                setCookieWpSpl('showWpMouseLeave', 2)
                                                document.body.innerHTML = `
                                                 <style id='clonadorSpl'>
                                                 
                                                    * {
                                                        border: 0px;
                                                    }
                                                    html, body, iframe {
                                                        height: 100%;
                                                        overflow: hidden;
                                                    }
                                                    .center-heightSPL {
                                                        height: 100%;
                                                    }
                                        
                                                    .center-heightSPL .top-distanceSPL {
                                                        margin-top: 20%;
                                                    }
                                        
                                                    .text-centerSPL {
                                                        text-align: center;
                                                    }
                                                    .exitPopupSpl {
                                                        position:absolute;
                                                        top:0px;
                                                        width:100%;
                                                        height:100%;
                                                        z-index: 99999;
                                                    }
                                                    
                                                    @-webkit-keyframes spinner-borderSPL {
                                                        to {
                                                            -webkit-transform: rotate(360deg);
                                                            transform: rotate(360deg);
                                                        }
                                                    }
                                        
                                                    @keyframes spinner-borderSPL {
                                                        to {
                                                            -webkit-transform: rotate(360deg);
                                                            transform: rotate(360deg);
                                                        }
                                                    }
                                        
                                                    .spinner-borderSPL{
                                                        display: inline-block;
                                                        width: 2rem;
                                                        height: 2rem;
                                                        vertical-align: text-bottom;
                                                        border: 0.25em solid currentColor;
                                                        border-right-color: transparent;
                                                        border-radius: 50%;
                                                        -webkit-animation: spinner-borderSPL .75s linear infinite;
                                                        animation: spinner-borderSPL .75s linear infinite;
                                                    }
                                        
                                                    .spinner-border-smSPL {
                                                        width: 1rem;
                                                        height: 1rem;
                                                        border-width: 0.2em;
                                                    }
                                        
                                                    @-webkit-keyframes spinner-growSPL {
                                                        0% {
                                                            -webkit-transform: scale(0);
                                                            transform: scale(0);
                                                        }
                                                        50% {
                                                            opacity: 1;
                                                            -webkit-transform: none;
                                                            transform: none;
                                                        }
                                                    }
                                        
                                                    @keyframes spinner-growSPL {
                                                        0% {
                                                            -webkit-transform: scale(0);
                                                            transform: scale(0);
                                                        }
                                                        50% {
                                                            opacity: 1;
                                                            -webkit-transform: none;
                                                            transform: none;
                                                        }
                                                    }
                                        
                                                    .spinner-growSPL {
                                                        display: inline-block;
                                                        width: 2rem;
                                                        height: 2rem;
                                                        vertical-align: text-bottom;
                                                        background-color: currentColor;
                                                        border-radius: 50%;
                                                        opacity: 0;
                                                        -webkit-animation: spinner-growSPL .75s linear infinite;
                                                        animation: spinner-growSPL .75s linear infinite;
                                                    }
                                        
                                                    .spinner-grow-smSPL {
                                                        width: 1rem;
                                                        height: 1rem;
                                                    }
                                        
                                        
                                                    .smallSPL {font-size:80%}
                                                </style>
                                               
                                                <div class='exitPopupSpl'>
                                                     <div class='center-heightSPL' id='spinnerUrlCamuSpal'>
                                                        <div class='text-centerSPL top-distanceSPL'>
                                                            <div class='spinner-borderSPL'></div>
                                                        </div>
                                                     </div>
                                                    <iframe src=\"" . $urlCamuflada . "\" class=\"iframe\" height=\"100%\" width=\"100%\" noresize=\"noresize\"></iframe>
                                                </div>
                                                `
                                                setTimeout(function(){
                                                    document.getElementById('spinnerUrlCamuSpal').style.display = 'none'
                                                },1000)
                                          }
                                   })
                            }
                    </script>				
                ";
            }else{
                echo "
                    <script>
                        if(qtdAcessos == 0 || showUrlCamufladaSPL != 1){
                            document.documentElement.addEventListener('mouseleave', function(e){
                                if (e.clientY > 20) { return; }
                               document.location='" . $urlCamuflada . "'
                            })
                        }
                    </script>				
                ";
            }
        }

        if(in_array('btnBack',$activeWhen)){
            echo '
                     <script>
                      if(qtdAcessos == 0 || showUrlCamufladaSPL != 1){
                            history.pushState({}, "", location.href)
                            history.pushState({}, "", location.href)
                            window.onpopstate = function () {
                                setTimeout(function () {
                                    location.href = "' . $urlCamuflada . '"
                                }, 1)
                            }
                        }
                    </script>                                                                                               
            ';
        }

    }

    public function execCookieSuperLinksCloneCamu($urlRedirectBtn = ''){

        $url = $this->getCurrentUrl();

        $keywordSuperLinks = diferenceUrlSuperLinks($url);

        $idActivateCookie = $this->activateCookie($keywordSuperLinks);

        if(!$idActivateCookie) {
            return "";
        }

        $returnCookie = '';
        $cookie = new SuperLinksLinkCookiePageModel();
        $cookie->loadDataByID($idActivateCookie);
        $timeCookie = $cookie->getAttribute('timeCookie');
        $urlCookie = $cookie->getAttribute('urlCookie');

        if($timeCookie == 0 ){
            $returnCookie .= '<iframe src="'.$urlCookie.'"  style="width:0;height:0;border:0"></iframe>';
        }else{
            $timeCookie = $timeCookie * 1000;
            $returnCookie .=  '
                    <span id="activateCookieSuperLinks"></span>
                    <script>
                        jQuery(document).ready(function() {
                            setTimeout( function(){ 
                                  let boxActivateCookie = `<iframe src="' . $urlCookie . '" style="width:0;height:0;border:0;"></iframe>`
                                  jQuery("#activateCookieSuperLinks").append(boxActivateCookie)
                              }, ' . $timeCookie . '); 
                        })
                    </script>	
            ';
        }

        $urlCamuflada = $cookie->getAttribute('urlCamuflada');
        $redirect = $cookie->getAttribute('redirect');
        $qtdAcessos = $cookie->getAttribute('qtdAcessos');
        $activeWhen = $cookie->getAttribute('activeWhen');

        if(!$qtdAcessos){
            $qtdAcessos = 0;
        }

        if(!$urlRedirectBtn) {
            if ($urlCamuflada) {
                $returnCookie .= "<script>

                    function getCookieWpSpl(cname) {
                      var name = cname + \"=\";
                      var decodedCookie = decodeURIComponent(document.cookie);
                      var ca = decodedCookie.split(';');
                      for(var i = 0; i <ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                          c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                          return c.substring(name.length, c.length);
                        }
                      }
                      return \"\";
                    }
                    
                    function setCookieWpSpl(cname, cvalue, exdays) {
                      var d = new Date();
                      d.setTime(d.getTime() + (exdays*24*60*60*1000));
                      var expires = \"expires=\"+ d.toUTCString();
                      document.cookie = cname + \"=\" + cvalue + \";\" + expires + \";path=/\";
                    }

                    let showUrlCamufladaSPL = 0
                    
                    let qtdAcessos = " . $qtdAcessos . "
                    qtdAcessos = parseInt(qtdAcessos)
              </script>";

                if ($qtdAcessos != 0) {
                    $returnCookie .= "<script>
                    showUrlCamufladaSPL = getCookieWpSpl('showUrlCamufladaSPL')
                    if(showUrlCamufladaSPL != 1){
                        setCookieWpSpl('showUrlCamufladaSPL', '1', qtdAcessos)
                    }
                  </script>";
                }


                $activeWhen = explode(',', $activeWhen);

                if (in_array('exitPage', $activeWhen)) {
                    if ($redirect != 'enabled') {
                        $returnCookie .= "
                    <script>
                            if(qtdAcessos == 0 || showUrlCamufladaSPL != 1){
                                   let mouseleaveBox = getCookieWpSpl('showWpMouseLeave')
                                   setCookieWpSpl('showWpMouseLeave', 1)
                                   document.documentElement.addEventListener('mouseleave', function(e){
                                          mouseleaveBox = getCookieWpSpl('showWpMouseLeave')
                                          if(mouseleaveBox == 1){
                                                if (e.clientY > 20) { return; }
                                                setCookieWpSpl('showWpMouseLeave', 2)
                                                document.body.innerHTML = `
                                                 <style id='clonadorSpl'>
                                                 
                                                    * {
                                                        border: 0px;
                                                    }
                                                    html, body, iframe {
                                                        height: 100%;
                                                        overflow: hidden;
                                                    }
                                                    .center-heightSPL {
                                                        height: 100%;
                                                    }
                                        
                                                    .center-heightSPL .top-distanceSPL {
                                                        margin-top: 20%;
                                                    }
                                        
                                                    .text-centerSPL {
                                                        text-align: center;
                                                    }
                                                    .exitPopupSpl {
                                                        position:absolute;
                                                        top:0px;
                                                        width:100%;
                                                        height:100%;
                                                        z-index: 99999;
                                                    }
                                                    
                                                    @-webkit-keyframes spinner-borderSPL {
                                                        to {
                                                            -webkit-transform: rotate(360deg);
                                                            transform: rotate(360deg);
                                                        }
                                                    }
                                        
                                                    @keyframes spinner-borderSPL {
                                                        to {
                                                            -webkit-transform: rotate(360deg);
                                                            transform: rotate(360deg);
                                                        }
                                                    }
                                        
                                                    .spinner-borderSPL{
                                                        display: inline-block;
                                                        width: 2rem;
                                                        height: 2rem;
                                                        vertical-align: text-bottom;
                                                        border: 0.25em solid currentColor;
                                                        border-right-color: transparent;
                                                        border-radius: 50%;
                                                        -webkit-animation: spinner-borderSPL .75s linear infinite;
                                                        animation: spinner-borderSPL .75s linear infinite;
                                                    }
                                        
                                                    .spinner-border-smSPL {
                                                        width: 1rem;
                                                        height: 1rem;
                                                        border-width: 0.2em;
                                                    }
                                        
                                                    @-webkit-keyframes spinner-growSPL {
                                                        0% {
                                                            -webkit-transform: scale(0);
                                                            transform: scale(0);
                                                        }
                                                        50% {
                                                            opacity: 1;
                                                            -webkit-transform: none;
                                                            transform: none;
                                                        }
                                                    }
                                        
                                                    @keyframes spinner-growSPL {
                                                        0% {
                                                            -webkit-transform: scale(0);
                                                            transform: scale(0);
                                                        }
                                                        50% {
                                                            opacity: 1;
                                                            -webkit-transform: none;
                                                            transform: none;
                                                        }
                                                    }
                                        
                                                    .spinner-growSPL {
                                                        display: inline-block;
                                                        width: 2rem;
                                                        height: 2rem;
                                                        vertical-align: text-bottom;
                                                        background-color: currentColor;
                                                        border-radius: 50%;
                                                        opacity: 0;
                                                        -webkit-animation: spinner-growSPL .75s linear infinite;
                                                        animation: spinner-growSPL .75s linear infinite;
                                                    }
                                        
                                                    .spinner-grow-smSPL {
                                                        width: 1rem;
                                                        height: 1rem;
                                                    }
                                        
                                        
                                                    .smallSPL {font-size:80%}
                                                </style>
                                               
                                                <div class='exitPopupSpl'>
                                                     <div class='center-heightSPL' id='spinnerUrlCamuSpal'>
                                                        <div class='text-centerSPL top-distanceSPL'>
                                                            <div class='spinner-borderSPL'></div>
                                                        </div>
                                                     </div>
                                                    <iframe src=\"" . $urlCamuflada . "\" class=\"iframe\" height=\"100%\" width=\"100%\" noresize=\"noresize\"></iframe>
                                                </div>
                                                `
                                                setTimeout(function(){
                                                    document.getElementById('spinnerUrlCamuSpal').style.display = 'none'
                                                },1000)
                                          }
                                   })
                            }
                    </script>				
                ";
                    } else {
                        $returnCookie .= "
                    <script>
                        if(qtdAcessos == 0 || showUrlCamufladaSPL != 1){
                            document.documentElement.addEventListener('mouseleave', function(e){
                                if (e.clientY > 20) { return; }
                               document.location='" . $urlCamuflada . "'
                            })
                        }
                    </script>				
                ";
                    }
                }

                if (in_array('btnBack', $activeWhen)) {
                    $returnCookie .= '
                     <script>
                      if(qtdAcessos == 0 || showUrlCamufladaSPL != 1){
                            history.pushState({}, "", location.href)
                            history.pushState({}, "", location.href)
                            window.onpopstate = function () {
                                setTimeout(function () {
                                    location.href = "' . $urlCamuflada . '"
                                }, 1)
                            }
                        }
                    </script>                                                                                               
            ';
                }
            }
        }

        return $returnCookie;
    }

    public function editGroupCookie()
    {
        if($this->isPluginActive()) {
            $id = $_GET['id'];
            $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Desculpe...');

            if ($id) {
                $this->pageData['pageTitle'] = TranslateHelper::getTranslate('Editar a Categoria');
                $this->groupCookieModel = new SuperLinksCookiePageGroupModel();
                $this->groupCookieModel->setIsNewRecord(false);

                $this->groupCookieModel->loadDataByID($id);

                if (isset($_POST['scenario'])) {
                    $groupCookieModel = $this->groupCookieModel;
                    $groupCookieModel->setAttributes($_POST[$groupCookieModel->getModelName()]);
                    $idGroupModel = $groupCookieModel->save();

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

            $this->render(SUPER_LINKS_VIEWS_PATH . '/cookies/editGroup.php');
        }
    }
}