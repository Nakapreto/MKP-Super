<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksLinkConfigSocialModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_linkConfigSocial']
        );
    }

    public function getModelName(){
        return 'SuperLinksLinkConfigSocialModel';
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
            'textTitle' => TranslateHelper::getTranslate('Título da página'),
            'description' => TranslateHelper::getTranslate('Descrição da página'),
            'image' => TranslateHelper::getTranslate('Imagem que será usada nas redes sociais')
        );
    }
}