<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksLinkClonePageModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_clonePageLinks']
        );
    }

    public function getModelName(){
        return 'SuperLinksLinkClonePageModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Configuração'),
            'idLink' => TranslateHelper::getTranslate('ID do Link'),
            'pageItem' => TranslateHelper::getTranslate('Url do item na página a ser clonada'),
            'newItem' => TranslateHelper::getTranslate('Url do novo item para substituir'),
            'typeItem' => TranslateHelper::getTranslate('Tipo do item')
        );
    }


    public function updateCloneLink(){
        $idSuperLink = $this->getAttribute('idLink');

        $cloneLinks = $this->getAllDataByParam($idSuperLink,'idLink');

        foreach($cloneLinks as $cloneLink){
            if(trim($cloneLink->pageItem) == trim($this->getAttribute('pageItem'))){
                $this->setIsNewRecord(false);
                $this->setAttribute('id', $cloneLink->id);
            }
        }

        $this->save();
    }
}