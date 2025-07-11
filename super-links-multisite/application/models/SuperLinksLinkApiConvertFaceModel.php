<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

class SuperLinksLinkApiConvertFaceModel extends SuperLinksCoreModel {


    public function __construct() {
        parent::__construct();

        $this->setAttributesKeys(
            $this->attributeLabels()
        );

        $this->setTableName(
            $this->tables['spl_apiConvertFacebook']
        );
    }

    public function getModelName(){
        return 'SuperLinksLinkApiConvertFaceModel';
    }

    public function rules()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'id' => TranslateHelper::getTranslate('ID da config'),
            'idLink' => TranslateHelper::getTranslate('ID do Link'),
            'eventNameApiFacebook' => TranslateHelper::getTranslate('Nome do Evento do Facebook (Recomendado ViewContent)'),
            'eventIdApiFacebook' => TranslateHelper::getTranslate('ID do Evento (Opcional)')
        );
    }
}