<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksLinkMetricsModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_linkMetrics']
        );
    }

    public function getModelName(){
        return 'SuperLinksLinkMetricsModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Métrica'),
            'idAffiliateLink' => TranslateHelper::getTranslate('ID do Link de afiliado'),
            'accessTotal' => TranslateHelper::getTranslate('Total de acessos'),
            'uniqueTotalAccesses' => TranslateHelper::getTranslate('Total de acessos únicos')
        );
    }

    public function getMetricsByIdAffiliateLink($idAffiliateLink = null){
        if(is_null($idAffiliateLink)){
            return [];
        }

        $metricsData = $this->getAllDataByParam($idAffiliateLink,'idAffiliateLink');
        if($metricsData){
            $metricsData = array_shift($metricsData);
            $metricsData = get_object_vars($metricsData);
        }

        return $metricsData;
    }

    public function updateMetricsByIDLink($idAffiliateLink = null, $isUniqueAccess = false, $cloakIsActive = false, $isFacebookLink = false){
        if(is_null($idAffiliateLink) || $cloakIsActive){
            return false;
        }

        $metricsData = $this->getMetricsByIdAffiliateLink($idAffiliateLink);

        $this->setAttribute('idAffiliateLink', $idAffiliateLink);

        $accessTotalQtd = 1;

        if($isFacebookLink){
            $accessTotalQtd = 0;
        }

        if(!$metricsData){
            $this->setAttribute('accessTotal', $accessTotalQtd);
            $this->setAttribute('uniqueTotalAccesses', 1);
            return $this->save();
        }

        $this->setIsNewRecord(false);

        $newAccessTotal = $metricsData['accessTotal'] + $accessTotalQtd;

        $uniqueTotalAccesses = $isUniqueAccess? $metricsData['uniqueTotalAccesses'] + 1 : $metricsData['uniqueTotalAccesses'];

        $this->setAttribute('id', $metricsData['id']);
        $this->setAttribute('accessTotal', $newAccessTotal);
        $this->setAttribute('uniqueTotalAccesses', $uniqueTotalAccesses);
        return $this->save();
    }


}