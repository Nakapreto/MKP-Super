<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksAutomaticMetricsModel extends SuperLinksCoreModel {

    protected $isUniqueAccess = false;

    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_automaticMetrics']
        );
    }

    public function getModelName(){
        return 'SuperLinksAutomaticMetricsModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Métrica'),
            'idAutomaticLink' => TranslateHelper::getTranslate('ID do Link de afiliado'),
            'idPost' => TranslateHelper::getTranslate('Post'),
            'keyword' => TranslateHelper::getTranslate('Palavra-chave'),
            'accessTotal' => TranslateHelper::getTranslate('Total de acessos'),
            'uniqueTotalAccesses' => TranslateHelper::getTranslate('Total de acessos únicos')
        );
    }

    public function setIsUniqueAccessTrue(){
        $this->isUniqueAccess = true;
    }

    public function getIsUniqueAccess(){
        return $this->isUniqueAccess;
    }

    public function getMetricsByIdAutomaticLink($idAutomaticLink = null, $idPost = null, $keyword = null){
        if(is_null($idAutomaticLink) || is_null($idPost) || is_null($keyword)){
            return [];
        }

        global $wpdb;
        $tableName = $this->getTableName();

        $metricsData =  $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName where idAutomaticLink = %d and idPost = %d and keyword = %s", $idAutomaticLink, $idPost, $keyword));
        if($metricsData){
            $metricsData = array_shift($metricsData);
            $metricsData = get_object_vars($metricsData);
        }

        return $metricsData;
    }

    public function updateMetricsByIDAutomaticLink(){

        $idAutomaticLink = $this->getAttribute('idAutomaticLink');
        $idPost = $this->getAttribute('idPost');
        $keyword = $this->getAttribute('keyword');

        $isUniqueAccess = $this->getIsUniqueAccess();

        if(!$idAutomaticLink || !$idPost || !$keyword){
            return false;
        }

        $metricsData = $this->getMetricsByIdAutomaticLink($idAutomaticLink, $idPost, $keyword);

        if(!$metricsData){
            $this->setAttribute('accessTotal', 1);
            $this->setAttribute('uniqueTotalAccesses', 1);
            return $this->save();
        }

        $this->setIsNewRecord(false);

        $newAccessTotal = $metricsData['accessTotal'] + 1;

        $uniqueTotalAccesses = $isUniqueAccess? $metricsData['uniqueTotalAccesses'] + 1 : $metricsData['uniqueTotalAccesses'];

        $this->setAttribute('id', $metricsData['id']);
        $this->setAttribute('accessTotal', $newAccessTotal);
        $this->setAttribute('uniqueTotalAccesses', $uniqueTotalAccesses);
        return $this->save();
    }


}