<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksLinkCookiePageModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_cookieLinks']
        );
    }

    public function getModelName(){
        return 'SuperLinksLinkCookiePageModel';
    }

    public function rules()
    {
        return array(
            [
                'timeCookie', 'naturalNumber'
            ]
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Configuração'),
            'cookieName' => TranslateHelper::getTranslate('Nome da configuração'),
            'idPost' => TranslateHelper::getTranslate('Ativar nos Posts'),
            'idPage' => TranslateHelper::getTranslate('Ativar nas Páginas'),
            'linkSuperLinks' => TranslateHelper::getTranslate('Ativar nos Links do Super Links'),
            'statusCookie' => TranslateHelper::getTranslate('Habilitado'),
            'timeCookie' => TranslateHelper::getTranslate('Tempo para ativação do cookie'),
            'urlCookie' => TranslateHelper::getTranslate('Seu link de afiliado para ativar o cookie'),
            'redirect' => TranslateHelper::getTranslate('Redirecionar ao invés de camuflar?'),
            'urlCamuflada' => TranslateHelper::getTranslate('Url para exibir quando o usuário deixar a página'),
            'qtdAcessos' => TranslateHelper::getTranslate('Exibir a url abaixo ao mesmo usuário'),
            'activeWhen' => TranslateHelper::getTranslate('Ativar a url quando'),
            'idGroup' => TranslateHelper::getTranslate('Categoria'),
        );
    }

    public function naturalNumber($attribute = ''){
        $attributeVal = $this->getAttribute($attribute);
        return ($attributeVal >= 0)? true : false;
    }

    public function getPostsSuperLinksOptions(){

            $allPosts[] = ['selected' => false, 'text' => 'Todos os posts', 'val' => 'allPosts'];


        $posts = $this->findWpSuperLinksPages('post');
        foreach($posts as $post){
            $allPosts[] = ['selected' => false, 'text' => $post['title'], 'val' => $post['id']];
        }
        return $allPosts;
    }

    public function getPagesSuperLinksOptions(){

        $allPosts[] = ['selected' => false, 'text' => 'Todas as páginas', 'val' => 'allPages'];

        $pages = $this->findWpSuperLinksPages('page');
        foreach($pages as $page){
            $allPosts[] = ['selected' => false, 'text' => $page['title'], 'val' => $page['id']];
        }

        return $allPosts;
    }

    private function findWpSuperLinksPages($type = 'page')
    {
        $posts = SuperLinksPosts::query()
            ->select('ID as id, post_title as title, post_type as type')
            ->where('post_type', 'in', [array_values(get_post_types(['public' => true]))])
            ->where('post_type', "$type")
            ->where('post_status', 'publish')
            ->order_by('post_modified', 'desc')
            ->get_results();
        return $posts;
    }

    private function isPostCookie($id)
    {
        $posts = SuperLinksPosts::query()
            ->select('ID as id, post_title as title, post_type as type')
            ->where('post_type', 'in', [array_values(get_post_types(['public' => true]))])
            ->where('ID', "$id")
            ->order_by('post_modified', 'desc')
            ->get_results();

        if($posts){
            $posts = array_shift($posts);
            if($posts['type'] == 'post'){
                return true;
            }
        }

        return false;
    }

    public function getLinksSuperLinksOptions(){

        $allPosts[] = ['selected' => false, 'text' => 'Todos os Links Camuflados ou clonados', 'val' => 'all'];
        $addLinkModel = new SuperLinksAddLinkModel();
        $dataLinks = $addLinkModel->getAllDataByParam('enabled','statusLink');

        //remove links do facebook
        foreach ($dataLinks as $key => $link) {
            $link = get_object_vars($link);
            if($link['redirectType'] == 'facebook'){
                $affiliateUrl = new SuperLinksAffiliateLinkModel();
                $affiliateData = $affiliateUrl->getAllDataByParam($link['id'],'idLink');
                if($affiliateData){
                    $affiliateData = array_shift($affiliateData);
                    $affiliateData = get_object_vars($affiliateData);
                    foreach($dataLinks as $id => $l){
                        $l = get_object_vars($l);
                        if($affiliateData['affiliateUrl'] == (SUPER_LINKS_TEMPLATE_URL . '/' . $l['keyWord']) ){
                            unset($dataLinks[$id]);
                        }
                    }
                }
            }
        }

        foreach($dataLinks as $dataLink){
            if($dataLink->redirectType == 'camuflador' || $dataLink->redirectType == 'clonador') {
                $allPosts[] = ['selected' => false, 'text' => '/' . $dataLink->keyWord, 'val' => $dataLink->id];
            }
        }

        return $allPosts;
    }

    public function getLinksByIDGroup($idGroup = null){
        if(is_null($idGroup)){
            global $wpdb;
            $tableName = $this->getTableName();

            if(is_null($tableName)){
                return [];
            }

            return $wpdb->get_results("SELECT * FROM $tableName where idGroup IS NULL");
        }

        return $this->getAllDataByParam($idGroup,'idGroup');
    }

    public function existConfigForThisPost(){
        $idPost = $this->getAttribute('idPost');
        $oldIdPost = $this->getAttribute('oldIdPost');

        if(!$idPost){
            return false;
        }

        $idPost = explode(",",$idPost);
        $allConfigs = $this->getAllData();
        foreach ($allConfigs as $config){
            $allPosts = $config->idPost? explode(",",$config->idPost) : [];

            if($config->id != $oldIdPost) {
                foreach ($idPost as $id) {
                    if($id == 'allPosts' && (in_array('allPosts', $allPosts))){
                        return true;
                    }
                    if($id == 'allPages' && (in_array('allPages', $allPosts))){
                        return true;
                    }
                    if($id == 'all' && (in_array('all', $allPosts))){
                        return true;
                    }
                    if (in_array($id, $allPosts)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function existConfigForThisPage(){
        $idPost = $this->getAttribute('idPage');
        $oldIdPost = $this->getAttribute('oldIdPost');

        if(!$idPost){
            return false;
        }

        $idPost = explode(",",$idPost);
        $allConfigs = $this->getAllData();
        foreach ($allConfigs as $config){
            $allPosts = $config->idPost? explode(",",$config->idPost) : [];

            if($config->id != $oldIdPost) {
                foreach ($idPost as $id) {
                    if($id == 'allPosts' && (in_array('allPosts', $allPosts))){
                        return true;
                    }
                    if($id == 'allPages' && (in_array('allPages', $allPosts))){
                        return true;
                    }
                    if($id == 'all' && (in_array('all', $allPosts))){
                        return true;
                    }
                    if (in_array($id, $allPosts)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function existConfigForThisLink(){
//        $linkSuperLinks = $this->getAttribute('linkSuperLinks');
//        $oldIdPost = $this->getAttribute('oldIdPost');
//
//        if(!$linkSuperLinks){
//            return false;
//        }
//
//        $linkSuperLinks = explode(",",$linkSuperLinks);
//
//        $allConfigs = $this->getAllData();
//        foreach ($allConfigs as $config){
//            $allLinks = $config->linkSuperLinks? explode(",",$config->linkSuperLinks) : [];
//
//            if($config->id != $oldIdPost) {
//                foreach ($linkSuperLinks as $id) {
//                    if($id == 'all' && (in_array('all', $allLinks))){
//                        return true;
//                    }
//                    if (in_array($id, $allLinks)) {
//                        return true;
//                    }
//                }
//            }
//        }

        return false;
    }
}