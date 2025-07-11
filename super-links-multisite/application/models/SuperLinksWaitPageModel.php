<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksWaitPageModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_linkWaitPage']
        );
    }

    public function getModelName(){
        return 'SuperLinksWaitPageModel';
    }

    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da Página de carregamento'),
            'idLink' => TranslateHelper::getTranslate('ID do Link'),
            'textLoadPage' => TranslateHelper::getTranslate('Mensagem de carregamento da página'),
            'showSpinner' => TranslateHelper::getTranslate('Mostrar gif e texto de carregamento?')
        );
    }
}