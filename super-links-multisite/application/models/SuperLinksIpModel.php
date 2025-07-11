<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksIpModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_linkIps']
        );
    }

    public function getModelName(){
        return 'SuperLinksIpModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Métrica'),
            'idLink' => TranslateHelper::getTranslate('ID do Link ou Página'),
            'ipClient' => TranslateHelper::getTranslate('Ip que acessou'),
            'blocked' => TranslateHelper::getTranslate('Total de acessos únicos'),
            'url' => TranslateHelper::getTranslate('Url acessada'),
            'datasAcesso' => TranslateHelper::getTranslate('Datas de acesso'),
        );
    }

    public function getIpsByIdLink($idLink = null){
        if(is_null($idLink)){
            return [];
        }

        $ipsData = $this->getAllDataByParam($idLink,'idLink','ORDER BY ipClient');

        return $ipsData;
    }

    public function getIpsByIp($ipClient = null){
        if(is_null($ipClient)){
            return [];
        }

        $ipsData = $this->getAllDataByParam($ipClient,'ipClient');

        return $ipsData;
    }

    public function updateIpByIDLink($ipClient = 0, $idLink = null, $urlAcessada = ''){
        if(is_null($idLink) || !$urlAcessada || !$ipClient){
            return false;
        }

		$dateHelper = new DateHelper();
		$dataHoraAtual = current_time($dateHelper::SQL_DATETIME_FORMAT);
        $metricsData = $this->getIpsByIp($ipClient);
		$linkData = false;

		if(!$dataHoraAtual){
			return false;
		}

		$urlHelper = new UrlSPLHelper();
	    $urlAcessada = $urlHelper->remove_trailing_slash_spl($urlAcessada);

		foreach($metricsData as $data){
			$urlBd = $urlHelper->remove_trailing_slash_spl($data->url);
			if(($urlBd == $urlAcessada) && ($data->idLink == $idLink)){
				$linkData = $data;
			}
		}

		if(!$linkData){
			$dataAcessada = serialize(array($dataHoraAtual));
			$ipModel = new SuperLinksIpModel();
			$ipModel->setAttribute('idLink', $idLink);
			$ipModel->setAttribute('ipClient', $ipClient);
			$ipModel->setAttribute('url', $urlAcessada);
			$ipModel->setAttribute('datasAcesso', $dataAcessada);
			return $ipModel->save();
		}

		if(!isset($linkData->id) || !$linkData->id){
			return false;
		}

	    $ipModel = new SuperLinksIpModel();
		$ipModel->loadDataByID($linkData->id);
	    $ipModel->setIsNewRecord(false);

		$dataAcessoLink = unserialize($linkData->datasAcesso);
	    $dataAcessoLink = array_merge($dataAcessoLink,array($dataHoraAtual));
	    $dataAcessoLink = serialize($dataAcessoLink);
	    $ipModel->setAttribute('datasAcesso', $dataAcessoLink);

        return $ipModel->save();
    }

}