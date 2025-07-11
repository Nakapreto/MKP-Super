<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksAffiliateLinkModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_affiliateLink']
        );
    }

    public function getModelName(){
        return 'SuperLinksAffiliateLinkModel';
    }

    public function rules()
    {
        return [
            [
                'affiliateUrl', 'required'
            ],
            [
                'affiliateUrl', 'isValidLink'
            ]
        ];
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Url de Afiliado'),
            'idLink' => TranslateHelper::getTranslate('ID do Link'),
            'affiliateUrl' => TranslateHelper::getTranslate('Url de Afiliado'),
            'createdAt' => TranslateHelper::getTranslate('Criado em:'),
            'updatedAt' => TranslateHelper::getTranslate('Atualizado em:')
        );
    }

    public function updateAffiliateLink(){
        $idSuperLink = $this->getAttribute('idLink');

        $affiliateLinks = $this->getAllDataByParam($idSuperLink,'idLink');

        foreach($affiliateLinks as $affiliateLink){
            if(trim($affiliateLink->affiliateUrl) == trim($this->getAttribute('affiliateUrl'))){
                $this->setIsNewRecord(false);
                $this->setAttribute('updatedAt', DateHelper::agora());
            }
        }

        if(!$this->getAttribute('id')){
            $this->setAttribute('createdAt', DateHelper::agora());
        }

        $this->save();
    }
}